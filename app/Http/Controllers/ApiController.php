<?php
namespace App\Http\Controllers;
use App\Models\Site;
use App\Models\User;
use App\Models\Asset;
use App\Models\Incident;
use Illuminate\Http\Request;
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
        // 1. Validasi data yang masuk.
        // Aturan validasi diubah untuk menerima array nomor seri.
        $validator = Validator::make($request->all(), [
            'uuid'                => 'required|uuid|unique:incidents,uuid',
            'title'               => 'required|string|max:255',
            'reporter_email'      => 'required|email|exists:users,email',
            'site_location_code'  => 'required|string|exists:sites,location_code',
            'specific_location'   => 'required|string',
            'chronology'          => 'required|string',
            'involved_asset_sn'   => 'nullable|array', // Diubah menjadi array
            'involved_asset_sn.*' => 'string', // Setiap item dalam array harus string
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Cari data terkait (User dan Site)
        $user = User::where('email', $request->reporter_email)->firstOrFail();
        $site = Site::where('location_code', $request->site_location_code)->firstOrFail();

        // 3. Buat tiket insiden baru.
        // Menggunakan updateOrCreate untuk menangani kemungkinan UUID yang sama dikirim ulang.
        $incident = Incident::updateOrCreate(
            ['uuid' => $request->uuid], // Kunci untuk mencari
            [
                'user_id'    => $user->id,
                'site_id'    => $site->id,
                'title'      => $request->title,
                'location'   => $request->specific_location,
                'chronology' => $request->chronology,
                'status'     => 'Open',
            ]
        );

        // 4. Logika Otomatisasi Status Aset jika ada aset yang terlibat.
        $serialNumbers = array_filter($request->input('involved_asset_sn', []));

        if (!empty($serialNumbers)) {
            // a. Update status semua aset yang terlibat menjadi 'Stolen/Lost' dalam satu query.
            // Ini jauh lebih efisien daripada menggunakan loop.
            Asset::whereIn('serial_number', $serialNumbers)
                 ->update(['status' => 'Stolen/Lost']);

            // b. Dapatkan ID dari aset yang baru saja diupdate untuk ditautkan.
            $assetIds = Asset::whereIn('serial_number', $serialNumbers)->pluck('id');

            // c. Tautkan aset-aset ini ke insiden menggunakan sync untuk menghindari duplikasi.
            if ($assetIds->isNotEmpty()) {
                $incident->assets()->sync($assetIds);
            }
        }

        // 5. Kirim respons sukses.
        return response()->json([
            'message' => 'Incident processed successfully via API!',
            'incident_uuid' => $incident->uuid
        ], 201);
    }

    public function updateIncident(Request $request, Incident $incident, $uuid)
    {
        $incident = Incident::where('uuid', $uuid)->firstOrFail();
        // Lakukan update tanpa memicu event untuk mencegah infinite loop
        Incident::withoutEvents(function () use ($incident, $request) {
            $incident->update($request->all());
        });
        return response()->json(['message' => 'Incident updated in App 1']);
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

    public function cancelIncident(Incident $incident)
    {
        // 1. Ambil semua ID dari aset yang terhubung dengan insiden ini.
        // Method pluck() sangat efisien untuk hanya mengambil satu kolom.
        $assetIds = $incident->assets()->pluck('id');

        // 2. Jika ada aset yang terhubung, jalankan satu perintah massal ke database.
        // Ini adalah cara yang jauh lebih cepat daripada loop.
        if ($assetIds->isNotEmpty()) {
            Asset::whereIn('id', $assetIds)->update(['status' => 'In Use']);
        }

        // 3. Ubah status insiden menjadi "Cancelled"
        // (Anda mungkin perlu menambahkan 'Cancelled' ke enum status di migrasi incidents)
        $incident->status = 'Cancelled'; 
        $incident->save();

        return response()->json(['message' => 'Incident cancelled and assets restored.']);
    }
}