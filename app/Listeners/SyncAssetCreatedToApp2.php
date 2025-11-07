<?php

namespace App\Listeners;

use App\Events\AssetCreated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncAssetCreatedToApp2 implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    private string $apiUrl;
    private string $apiToken;
    
    public function __construct()
    {
        // Ambil konfigurasi dari file config/services.php yang terhubung ke .env
        $this->apiUrl = config('services.sumber_data.url');
        $this->apiToken = config('services.sumber_data.token');
    }

    /**
     * Handle the event.
     */
    public function handle(AssetCreated $event): void
    {
        $asset = $event->asset;

        // Siapkan data ASET untuk dikirim
        $dataToSync = [
            'name' => $asset->name,
            'serial_number' => $asset->serial_number,
            'category' => $asset->category,
            'status' => $asset->status,
            'site_id' => $asset->site_id,
            'site_location_code' => $asset->site_location_code,
        ];

        try {
            $response = Http::withToken($this->apiToken)
                            ->acceptJson()
                            ->post($this->apiUrl . '/api/v1/assets', $dataToSync);

            if ($response->failed()) {
                Log::error('Gagal sinkronisasi asset ke Aplikasi 2:', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::critical('Koneksi API ke Aplikasi 2 gagal:', ['error' => $e->getMessage()]);
        }
    }
}
