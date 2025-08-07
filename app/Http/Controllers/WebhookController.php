<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Site;
use App\Models\Asset;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WebhookController extends Controller
{
    /**
     * Method pribadi untuk memeriksa token keamanan.
     * Ini adalah "satpam" untuk endpoint API kita.
     */
    private function checkToken(Request $request)
    {
        $secretToken = $request->header('X-Secret-Token');
        
        // PENTING: Ganti 'TOKEN_RAHASIA_ANDA' dengan token yang Anda buat sendiri.
        // Token ini harus sama persis dengan yang Anda atur di Make.com.
        if ($secretToken !== '1|fUxTDAao7lUAr9Mf4mnMrpwGtJ6lEuCkNCUEsWel9457f202') { 
            Log::warning('Akses webhook ditolak: Token tidak valid.');
            abort(401, 'Unauthorized');
        }
    }

    /**
     * Menangani sinkronisasi data Site.
     */
    public function handleSiteSync(Request $request)
    {
        $this->checkToken($request);
        $data = $request->all();
        Log::info('Webhook Site diterima:', $data);

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'location_code' => 'required|string|max:10',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validasi webhook Site gagal:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Site::updateOrCreate(
            ['location_code' => $data['location_code']],
            ['name' => $data['name'], 'address' => $data['address']]
        );

        return response()->json(['message' => 'Site synchronized!'], 200);
    }

    /**
     * Menangani sinkronisasi data Aset.
     */
    public function handleAssetSync(Request $request)
    {
        $this->checkToken($request);
        $data = $request->all();
        Log::info('Webhook Aset diterima:', $data);

        $validator = Validator::make($data, [
            'serial_number' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'status' => 'required|string',
            'location_code' => 'required|exists:sites,location_code',
        ]);

        if ($validator->fails()) {
            Log::error('Validasi webhook Aset gagal:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $site = Site::where('location_code', $data['location_code'])->first();

        Asset::updateOrCreate(
            ['serial_number' => $data['serial_number']],
            [
                'site_id' => $site->id,
                'category' => $data['category'],
                'name' => $data['name'],
                'status' => $data['status'],
            ]
        );

        return response()->json(['message' => 'Asset synchronized!'], 200);
    }

    /**
     * Menangani sinkronisasi data Insiden.
     */
    public function handleIncidentSync(Request $request)
    {
        $this->checkToken($request);
        $data = $request->all();
        Log::info('Webhook Insiden diterima:', $data);

        $validator = Validator::make($data, [
            'title' => 'required|string',
            'reporter_email' => 'required|email|exists:users,email',
            'site_location_code' => 'required|exists:sites,location_code',
            'specific_location' => 'required|string',
            'chronology' => 'required|string',
            'involved_asset_sn' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validasi webhook Insiden gagal:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $data['reporter_email'])->first();
        $site = Site::where('location_code', $data['site_location_code'])->first();

        $incident = Incident::create([
            'user_id' => $user->id,
            'site_id' => $site->id,
            'title' => $data['title'],
            'location' => $data['specific_location'],
            'chronology' => $data['chronology'],
            'status' => 'Open',
        ]);

        if (!empty($data['involved_asset_sn'])) {
            $serialNumbers = array_map('trim', explode(',', $data['involved_asset_sn']));
            $assetIds = Asset::whereIn('serial_number', $serialNumbers)->pluck('id');
            $incident->assets()->attach($assetIds);
        }

        return response()->json(['message' => 'Incident created!'], 201);
    }
}