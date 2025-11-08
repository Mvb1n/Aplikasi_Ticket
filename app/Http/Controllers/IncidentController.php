<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\User;
use App\Models\Asset;
use App\Models\Incident;
use Illuminate\Support\Arr;
use App\Events\AssetUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Notifications\NewIncidentReported;
use Illuminate\Support\Facades\Notification;

class IncidentController extends Controller
{

    private string $apiUrl;
    private string $apiToken;

    public function __construct()
    {
        // Ambil konfigurasi dari file config/services.php yang terhubung ke .env
        $this->apiUrl = config('services.sumber_data.url');
        $this->apiToken = config('services.sumber_data.token');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $incidents = Incident::with(['user', 'site', 'assignedTo'])->latest()->paginate(10);
        return view('incidents.index', compact('incidents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $assets = Asset::where('status', 'In Use')->orderBy('name')->get();
        $sites = Site::orderBy('name')->get(); // Ambil semua data site
        return view('incidents.create', compact('assets', 'sites')); // Kirim data sites ke view
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'site_id' => 'required|exists:sites,id',
            'location' => 'required|string|max:255',
            'chronology' => 'required|string',
            'asset_ids' => 'nullable|array',
            'asset_ids.*' => 'exists:assets,id',
        ]);

        // Buat insiden baru
        $incident = new Incident();
        $incident->user_id = Auth::id();
        $incident->site_id = $validatedData['site_id'];
        $incident->title = $validatedData['title'];
        $incident->location = $validatedData['location'];
        $incident->chronology = $validatedData['chronology'];
        $incident->status = 'Open';
        $incident->save();

        // Tautkan aset dan langsung ubah statusnya
        if (!empty($validatedData['asset_ids'])) {
            // Tautkan aset ke insiden
            $incident->assets()->attach($validatedData['asset_ids']);

            // Ubah status aset yang baru saja ditautkan
            Asset::whereIn('id', $validatedData['asset_ids'])->update(['status' => 'Stolen/Lost']);
        }

        // Event 'IncidentCreated' akan otomatis terpicu oleh Model dan mengirim notifikasi

        return redirect()->route('incidents.index')->with('success', 'Laporan insiden berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Incident $incident)
    {
        $incident->load(['assets', 'site', 'user', 'comments.user', 'assignedTo', 'attachments']);

        // Ambil semua user yang memiliki peran 'security' untuk dropdown penugasan
        $securityTeam = User::whereHas('roles', function ($query) {
            $query->where('name', 'security');
        })->get();

        return view('incidents.show', compact('incident', 'securityTeam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Incident $incident)
    {
        $this->authorize('edit', $incident);
        $sites = Site::orderBy('name')->get();
        // Kita tidak perlu mengirim aset dari sini, karena akan diambil oleh JavaScript
        return view('incidents.edit', compact('incident', 'sites'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Incident $incident)
    {
        // 1. Otorisasi (Dari kode Anda)
        $this->authorize('update', $incident);

        // 2. Validasi (Gabungan)
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'site_id' => 'sometimes|required|exists:sites,id',
            'location' => 'sometimes|required|string|max:255',
            'chronology' => 'sometimes|required|string',
            'asset_ids' => 'nullable|array',
            'investigation_notes' => 'nullable|string',
            'status' => 'required|in:Open,In Progress,Resolved,Closed,Cancelled',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            
            // --- MODIFIKASI ---
            // Mengganti 'attachment' (singular) menjadi 'attachments' (plural, array)
            // Ini untuk mendukung upload file baru dari form 'Investigasi & Aksi'
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240', // 10MB
        ]);

        // 3. Update model (Hanya field non-relasi & non-file)
        // Kita gunakan Arr::except untuk membuang 'asset_ids' dan 'attachments'
        // agar 'update()' tidak error.
        $incident->update(Arr::except($validatedData, ['asset_ids', 'attachments']));
        
        // --- TAMBAHAN BARU: Logika File ---
        // 4. Proses file attachment (jika ada)
        $newAttachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {

                // --- MENJADI INI ---
                $originalName = $file->getClientOriginalName();
                $safeName = preg_replace("/[^A-Za-z0-9\._-]/", '', $originalName);
                $finalName = uniqid() . '_' . $safeName;
                $path = $file->storeAs('attachments', $finalName, 'public'); 
                // --- AKHIR PERUBAHAN ---
                
                $newAttachmentPaths[] = $path;
            }
        }
        // Gabungkan file lama (dari sync) dengan file baru (dari upload ini)
        $existingPaths = json_decode($incident->attachment_paths, true) ?? [];
        $allPaths = array_merge($existingPaths, $newAttachmentPaths);

        // Simpan array path yang sudah digabung
        $incident->attachment_paths = !empty($allPaths) ? json_encode($allPaths) : null;
        $incident->save(); // Simpan perubahan attachment_paths
        // --- AKHIR TAMBAHAN BARU ---

        // 5. Sinkronisasi Aset (Dari kode Anda)
        if ($request->has('asset_ids')) {
            $incident->assets()->sync($request->asset_ids ?? []);
        }
        
        // 6. Logika status aset (Dari kode Anda - SANGAT PENTING)
        $newStatus = null;
        if (in_array($incident->status, ['Resolved', 'Closed'])) {
            $newStatus = 'Stolen/Lost';
        } elseif (in_array($incident->status, ['Cancelled'])) {
            $newStatus = 'In Use';
        }

        if ($newStatus) {
            $assetIds = $incident->assets()->pluck('assets.id');
            if ($assetIds->isNotEmpty()) {
                Asset::whereIn('id', $assetIds)->update(['status' => $newStatus]);
                
                // Picu event untuk sinkronisasi aset (Dari kode Anda)
                $updatedAssets = Asset::whereIn('id', $assetIds)->get();
                foreach ($updatedAssets as $asset) {
                    event(new AssetUpdated($asset));
                }
            }
        }

        // 7. Redirect (Dari kode Anda)
        return redirect()->route('incidents.show', $incident->id)
                        ->with('success', 'Detail insiden berhasil diperbarui.');
    }


    /**
     * FUNGSI BARU UNTUK "BATALKAN"
     * Menangani logika pembatalan secara bersih.
     */
    public function cancel(Incident $incident)
    {
        // 1. Otorisasi (jika perlu, misalnya hanya admin)
        // $this->authorize('cancel', $incident);

        // 2. Cek apakah sudah dibatalkan
        if ($incident->status !== 'Cancelled') {
            
            // 3. Ambil ID aset yang terhubung
            $assetIds = $incident->assets()->pluck('id');

            if ($assetIds->isNotEmpty()) {
                // 4. Lakukan Mass Update untuk mengembalikan status aset
                Asset::whereIn('id', $assetIds)->update(['status' => 'In Use']);

                // 5. (SANGAT PENTING) Picu event untuk setiap aset
                //    agar Listener SyncAssetUpdateToApp2 berjalan (di queue)
                $updatedAssets = Asset::whereIn('id', $assetIds)->get();
                foreach ($updatedAssets as $asset) {
                    event(new AssetUpdated($asset));
                }
            }
        }
        
        // 6. Ubah status insiden lokal
        $incident->status = "Cancelled";

        // 7. Simpan. Ini akan memicu event 'IncidentUpdated'
        //    dan ditangani oleh 'SyncIncidentUpdate' (di queue)
        $incident->save(); 

        return back()->with('success', 'Laporan berhasil dibatalkan! Sinkronisasi berjalan.');
    }


    /**
     */
    public function destroy(Incident $incident)
    {
        // Otorisasi (jika perlu)
        // $this->authorize('delete', $incident);
        
        // (OPSIONAL) Kembalikan aset sebelum dihapus
        $assetIds = $incident->assets()->pluck('id');
        if ($assetIds->isNotEmpty()) {
             Asset::whereIn('id', $assetIds)->update(['status' => 'In Use']);
             // Picu event sinkronisasi aset jika perlu
        }

        // Hapus insiden
        $incident->delete();

        // Event 'IncidentDeleted' akan terpicu dan
        // 'SyncIncidentDelete' (di queue) akan menanganinya.
        return back()->with('success', 'Laporan berhasil dihapus! Sinkronisasi berjalan.');
    }
}
