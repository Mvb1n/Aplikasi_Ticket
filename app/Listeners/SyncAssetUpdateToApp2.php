<?php

namespace App\Listeners;

use App\Events\AssetUpdated;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncAssetUpdateToApp2 implements ShouldQueue
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
    public function handle(AssetUpdated $event): void
    {
        $asset = $event->asset;
        // Kirim HTTP PUT/PATCH ke API Aplikasi 2
        Http::withToken($this->apiToken)
            ->put($this->apiUrl . '/api/v1/assets/' . $asset->serial_number, $asset->toArray());
    }
}
