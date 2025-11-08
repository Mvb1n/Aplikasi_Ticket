<?php
namespace App\Http\Controllers;
use App\Models\Site;
use App\Models\User;
use App\Models\Asset;
use App\Models\Incident;
use App\Events\AssetUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{

    public function storeAsset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:assets,serial_number',
            'category' => 'required|string',
            'status' => 'required|string',
            'site_location_code' => 'required|exists:sites,location_code',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $site = Site::where('location_code', $request->site_location_code)->first();

        $asset = Asset::create([
            'site_id' => $site->id,
            'name' => $request->name,
            'serial_number' => $request->serial_number,
            'category' => $request->category,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Asset created successfully via API!',
            'data' => $asset
        ], 201);
    }

    public function updateAsset(Request $request, $serial_number)
    {
        $asset = Asset::where('serial_number', $serial_number)->firstOrFail();
        // Lakukan update tanpa memicu event untuk mencegah infinite loop
        Asset::withoutEvents(function () use ($asset, $request) {
            $asset->update($request->all());
        });
        return response()->json(['message' => 'Asset updated in App 1']);
    }

    public function deleteAsset(Request $request, $serial_number)
    {
        $asset = Asset::where('serial_number', $serial_number)->firstOrFail();
        // Lakukan update tanpa memicu event untuk mencegah infinite loop
        Asset::withoutEvents(function () use ($asset) {
            $asset->delete();
        });
        return response()->json(['message' => 'Asset delete in App 1']);
    }

    public function storeIncident(Request $request)
    {
        // 1. Validasi data (TERMASUK FILE BARU)
        $validator = Validator::make($request->all(), [
            'uuid'                => 'required|uuid', // Hapus unique, kita pakai updateOrCreate
            'title'               => 'required|string|max:255',
            'reporter_email'      => 'required|email|exists:users,email',
            'site_location_code'  => 'required|string|exists:sites,location_code',
            'specific_location'   => 'required|string',
            'chronology'          => 'required|string',
            'involved_asset_sn'   => 'nullable|array', 
            'involved_asset_sn.*' => 'string',
            
            // Validasi file terstruktur
            'incident_files'   => 'nullable|array',
            'incident_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
            
            'asset_files'   => 'nullable|array',
            'asset_files.*'   => 'nullable|array',
            'asset_files.*.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Cari data terkait (User dan Site)
        $user = User::where('email', $request->reporter_email)->firstOrFail();
        $site = Site::where('location_code', $request->site_location_code)->firstOrFail();

        // 3. Temukan insiden ATAU buat baru
        $incident = Incident::firstOrNew(
            ['uuid' => $request->uuid]
        );

        // 4. Ambil struktur path yang ada (jika ada)
        $paths = json_decode($incident->attachment_paths, true) ?? [];
        $paths['incident_files'] = $paths['incident_files'] ?? [];
        $paths['asset_files'] = $paths['asset_files'] ?? [];

        // 5. PROSES UPLOAD FILE BARU
        
        // 5a. Proses "Incident Files"
        if (!empty($request->file('incident_files'))) {
            foreach ($request->file('incident_files') as $file) {
                // Ambil nama file asli (yang sudah unik) dari Listener
                $safeName = $file->getClientOriginalName();
                $path = $file->storeAs('attachments', $safeName, 'public');
                $paths['incident_files'][] = $path;
            }
        }
        
        // 5b. Proses "Asset Files"
        if (!empty($request->file('asset_files'))) {
            foreach ($request->file('asset_files') as $serialNumber => $files) {
                // Cari asset_id berdasarkan serial_number
                $asset = Asset::where('serial_number', $serialNumber)->first();
                
                // Kita akan simpan pakai ASSET_ID sebagai key, bukan S/N
                // Ini jauh lebih baik untuk relasi di Aplikasi Ticket
                $assetId = $asset ? $asset->id : $serialNumber; // Fallback ke S/N jika aset tdk ketemu
                
                if (!isset($paths['asset_files'][$assetId])) {
                    $paths['asset_files'][$assetId] = [];
                }
                
                foreach ($files as $file) {
                    $safeName = $file->getClientOriginalName();
                    $path = $file->storeAs('attachments', $safeName, 'public');
                    $paths['asset_files'][$assetId][] = $path;
                }
            }
        }
        
        // 6. Isi data insiden dan SIMPAN
        $incident->fill([
            'user_id'    => $user->id,
            'site_id'    => $site->id,
            'title'      => $request->title,
            'location'   => $request->specific_location,
            'chronology' => $request->chronology,
            'status'     => 'Open',
            'attachment_paths' => json_encode($paths), // Simpan JSON baru
        ]);
        $incident->save();

        // 7. Logika Otomatisasi Status Aset
        $serialNumbers = array_filter($request->input('involved_asset_sn', []));

        if (!empty($serialNumbers)) {
            Asset::whereIn('serial_number', $serialNumbers)
                ->update(['status' => 'Stolen/Lost']);
            
            $assetIds = Asset::whereIn('serial_number', $serialNumbers)->pluck('id');
            
            if ($assetIds->isNotEmpty()) {
                $incident->assets()->syncWithoutDetaching($assetIds);
            }
        }

        // 8. Kirim respons sukses.
        return response()->json([
            'message' => 'Incident processed successfully via API!',
            'incident_uuid' => $incident->uuid
        ], 201);
    }

    public function getStatuses(Request $request)
    {
        $request->validate(['uuids' => 'required|array']);

        // Cari semua insiden yang UUID-nya ada di dalam daftar yang diminta, dan muat relasi 'site'
        $incidents = Incident::with('site')->whereIn('uuid', $request->uuids)->get();

        // Ubah hasilnya menjadi format [uuid => ['status' => ..., 'site_name' => ...]]
        $latestData = $incidents->mapWithKeys(function ($incident) {
            return [
                $incident->uuid => [
                    'status' => $incident->status,
                    'site_name' => $incident->site?->name ?? 'N/A'
                ]
            ];
        });

        return response()->json($latestData);
    }

    public function showIncident(Incident $incident)
    {
        // $incident otomatis ditemukan oleh Laravel via binding {incident:uuid}
        
        // Eager load semua relasi yang dibutuhkan oleh view 'show' di Aplikasi 1
        $incident->load(['user', 'site', 'assets', 'comments.user', 'assignedTo', 'attachments']);

        // Kembalikan data lengkap sebagai JSON
        return response()->json($incident);
    }

    public function updateIncident(Request $request, $uuid)
    {
        $incident = Incident::where('uuid', $uuid)->firstOrFail();
        
        $newStatus = $request->input('status');
        $oldStatus = $incident->status;

        // Cek apakah statusnya BARU SAJA diubah menjadi "Cancelled"
        if ($newStatus === 'Cancelled' && $oldStatus !== 'Cancelled') {
            
            $assetIds = $incident->assets()->pluck('id');

            if ($assetIds->isNotEmpty()) {
                // Lakukan Mass Update untuk mengembalikan status aset
                Asset::whereIn('id', $assetIds)->update(['status' => 'In Use']);

                // Picu event manual agar status aset sinkron kembali ke Aplikasi 1
                $updatedAssets = Asset::whereIn('id', $assetIds)->get();
                foreach ($updatedAssets as $asset) {
                    event(new AssetUpdated($asset));
                }
            }
        }
        
        // Update data insiden tanpa memicu event (mencegah deadlock)
        Incident::withoutEvents(function () use ($incident, $request) {
            $incident->update($request->all());
        });
        
        return response()->json(['message' => 'Incident updated successfully.']);
    }

    public function deleteIncident(Incident $incident, $uuid)
    {
        $incident = Incident::where('uuid', $uuid)->firstOrFail();
        // Lakukan update tanpa memicu event untuk mencegah infinite loop
        Incident::withoutEvents(function () use ($incident) {
            $incident->delete();
        });
        return response()->json(['message' => 'Incident delete in App 1']);
    }

    public function getSites()
    {
        // Ambil semua site, urutkan berdasarkan nama, dan pilih hanya kolom yang dibutuhkan
        $sites = Site::orderBy('name')->get(['id', 'name', 'location_code']);
        return response()->json($sites);
    }

    public function getAssetsBySite(Site $site)
    {
        // Ambil hanya aset yang statusnya "In Use" di site yang dipilih
        $assets = $site->assets()->where('status', 'In Use')->get(['id', 'name', 'serial_number']);

        // Kembalikan data dalam format JSON
        return response()->json($assets);
    }
}