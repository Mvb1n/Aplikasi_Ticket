<?php

namespace App\Listeners;

use App\Events\AssetDeletedInApp1;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Events\IncidentDeletedInApp1;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendIncidentDeleteToApp2
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
    public function handle(IncidentDeletedInApp1 $event): void
    {
        $incident = $event->incident;

        // Konfigurasi ini perlu Anda siapkan di config/services.php dan .env Aplikasi 1
        $apiUrl = config('services.sumber_data.url') . '/api/v1/incidents/' . $incident->serial_number;
        $apiToken = config('services.sumber_data.token');

        try {
            $response = Http::withToken($apiToken)->acceptJson()->delete($apiUrl);
            if ($response->failed()) {
                Log::error('Gagal sinkronisasi hapus ke Aplikasi 2:', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::critical('Koneksi API untuk sinkronisasi hapus gagal:', ['error' => $e->getMessage()]);
        }
    }
}
