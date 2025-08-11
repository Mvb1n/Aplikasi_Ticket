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

    public function updateIncident(Request $request, Incident $incident)
    {
        // Logika validasi dan update...
        $validatedData = $request->validate(['title' => 'required|string']);
        $incident->update($validatedData);
        return response()->json(['message' => 'Incident updated successfully!'], 200);
    }

    public function destroyIncident(Incident $incident)
    {
        $incident->delete();
        return response()->json(null, 204);
    }

    /**
     * Menerima dan menyimpan data aset baru dari API.
     */
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

    public function storeIncident(Request $request)
    {
        // 1. Validasi data yang masuk dari Aplikasi 2
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'reporter_email' => 'required|email|exists:users,email',
            'site_location_code' => 'required|exists:sites,location_code',
            'specific_location' => 'required|string',
            'chronology' => 'required|string',
            'involved_asset_sn' => 'nullable|string', // Nomor seri dipisah koma
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Cari data terkait (User dan Site)
        $user = User::where('email', $request->reporter_email)->first();
        $site = Site::where('location_code', $request->site_location_code)->first();

        // 3. Buat tiket insiden baru dengan status 'Open'
        $incident = Incident::create([
            'user_id' => $user->id,
            'site_id' => $site->id,
            'title' => $request->title,
            'location' => $request->specific_location,
            'chronology' => $request->chronology,
            'status' => 'Open',
        ]);

        // 4. Logika Otomatisasi Status Aset
        if (!empty($request->involved_asset_sn)) {
            // Pisahkan nomor seri yang dipisah koma menjadi array
            $serialNumbers = array_map('trim', explode(',', $request->involved_asset_sn));
            
            // Cari semua aset yang cocok dengan nomor seri tersebut
            $assets = Asset::whereIn('serial_number', $serialNumbers)->get();
            
            if ($assets->isNotEmpty()) {
                // a. Tautkan aset-aset ini ke insiden yang baru dibuat
                $incident->assets()->attach($assets->pluck('id'));

                // b. Loop melalui setiap aset dan ubah statusnya menjadi 'Stolen/Lost'
                foreach ($assets as $asset) {
                    $asset->status = 'Stolen/Lost';
                    $asset->save();
                }
            }
        }

        // 5. Kirim respons sukses.
        // Event 'IncidentCreated' akan otomatis terpicu oleh Model dan mengirim notifikasi.
        return response()->json(['message' => 'Incident created and assets status updated successfully via API!'], 201);
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