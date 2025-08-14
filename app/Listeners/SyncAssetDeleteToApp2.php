<?php

namespace App\Listeners;

use App\Events\AssetDeleted;
use Illuminate\Support\Facades\Log;
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
        // Ambil data aset yang baru saja dihapus
        $asset = $event->asset;

        // Siapkan URL dan Token untuk Aplikasi 2
        $apiUrl = config('services.sumber_data.url') . '/api/v1/assets/' . $asset->serial_number;
        $apiToken = config('services.sumber_data.token');

        try {
            $response = Http::withToken($apiToken)
                            ->acceptJson()
                            ->delete($apiUrl);

            if ($response->failed()) {
                Log::error('Gagal mengirim notifikasi HAPUS ke Aplikasi 2:', ['response' => $response->body()]);
            } else {
                Log::info('Notifikasi HAPUS untuk aset #' . $asset->serial_number . ' berhasil dikirim ke Aplikasi 2.');
            }
        } catch (\Exception $e) {
            Log::critical('Koneksi API ke Aplikasi 2 GAGAL TOTAL saat mengirim notifikasi HAPUS:', ['error' => $e->getMessage()]);
        }
    }
}
