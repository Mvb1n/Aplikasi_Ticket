<?php

namespace App\Listeners;

use App\Events\AssetDeleted;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncAssetDeleteToApp2
{
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
    public function handle(AssetDeleted $event): void
    {
        $asset = $event->asset;
        // Kirim HTTP DELETE ke API Aplikasi 2
        Http::withToken($this->apiToken)
            ->delete($this->apiUrl . '/api/v1/assets/' . $asset->serial_number);
    }
}
