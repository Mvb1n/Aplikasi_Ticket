<?php

namespace App\Listeners;

use App\Events\AssetUpdatedInApp1;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAssetUpdateToApp2
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AssetUpdatedInApp1 $event): void
    {
        $asset = $event->asset;
        $dataToSync = [
            'name' => $asset->name,
            'serial_number' => 'required|string|max:255|unique:assets,serial_number,' . $asset->id,
            'category' => $asset->category,
            'status' => $asset->status,
            'site_location_code' => $asset->site->location_code, // Ambil dari relasi
        ];
        $apiUrl = config('services.sumber_data.url') . '/api/v1/assets/' . $asset->serial_number;
        $apiToken = config('services.sumber_data.token');

        try {
            $response = Http::withToken($apiToken)->acceptJson()->put($apiUrl, $dataToSync);
            if ($response->failed()) {
                Log::error('Gagal sinkronisasi update ke Aplikasi 2:', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::critical('Koneksi API untuk sinkronisasi update gagal:', ['error' => $e->getMessage()]);
        }
    }
}
