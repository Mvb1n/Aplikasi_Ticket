<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\User;
use App\Models\Asset;
use App\Models\Incident;
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
        // 1. Otorisasi: Pastikan pengguna punya izin untuk meng-update
        $this->authorize('update', $incident);

        // 2. Validasi semua field yang MUNGKIN datang dari KEDUA form
        // Aturan 'sometimes' berarti "validasi hanya jika field ini ada di dalam request".
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'site_id' => 'sometimes|required|exists:sites,id',
            'location' => 'sometimes|required|string|max:255',
            'chronology' => 'sometimes|required|string',
            'asset_ids' => 'nullable|array',
            'investigation_notes' => 'nullable|string',
            'status' => 'required|in:Open,In Progress,Resolved,Closed,Cancelled',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'assigned_to_user_id' => 'nullable|exists:users,id',
        ]);

        // 3. Update model dengan semua data yang valid dalam satu kali operasi
        $incident->update($validatedData);

        // Jika ini adalah update dari form Edit Admin yang bisa mengubah aset
        if ($request->has('asset_ids')) {
            $incident->assets()->sync($request->asset_ids ?? []);
        }

        // 4. Logika untuk mengunggah file jika ada
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('attachments/incidents', 'public');
            $incident->attachments()->create([
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }

        // 5. Logika otomatisasi untuk mengubah status aset
        // Pastikan kita menggunakan status terbaru setelah update
        if (in_array($incident->status, ['Resolved', 'Closed'])) {
            foreach ($incident->assets as $asset) {
                $asset->status = 'Stolen/Lost';
                $asset->save();
            }
        }

        if (in_array($incident->status, ['Cancelled'])) {
            foreach ($incident->assets as $asset) {
                $asset->status = 'In Use';
                $asset->save();
            }
        }

        // 6. Arahkan kembali ke halaman detail dengan pesan sukses
        return redirect()->route('incidents.show', $incident->id)
                         ->with('success', 'Detail insiden berhasil diperbarui.');
    }

    /**
     * Membatalkan laporan insiden dari database (khusus Admin).
     */
    public function destroy(Incident $incident)
    {
        // Kirim permintaan cancel ke API Aplikasi 1 tanpa menghapus data lokal
        $response = Http::withToken($this->apiToken)
            ->timeout(240)
            ->put("{$this->apiUrl}/api/v1/incidents/{$incident->uuid}/cancel");

        $incident->status = "Cancelled"; // Update status lokal sebelum menghapus
        $incident->save(); // Simpan perubahan status di database lokal

        if ($response->successful()) {
            return back()->with('success', 'Laporan berhasil dibatalkan! Proses sinkronisasi berjalan di latar belakang.');
        } else {
            // Tangani error jika permintaan gagal
            $errorMessage = $response->json('message') ?? 'Terjadi kesalahan saat membatalkan laporan insiden.';
            return back()->with('error', $errorMessage);
        }
    }
}
