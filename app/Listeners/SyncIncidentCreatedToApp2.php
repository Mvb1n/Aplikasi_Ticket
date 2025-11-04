<?php

namespace App\Listeners;

use App\Events\IncidentCreated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncIncidentCreatedToApp2
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
    public function handle(IncidentCreated $event): void
    {
            $incident = $event->incident->load(['user', 'site', 'assets']);

            // Siapkan data untuk dikirim
            $dataToSync = [
                'uuid' => $incident->uuid,
                'title' => $incident->title,
                'reporter_email' => $incident->user->email,
                'site_location_code' => $incident->site->location_code,
                'specific_location' => $incident->location,
                'chronology' => $incident->chronology,
                'status' => $incident->status,
                'involved_asset_sn' => $incident->assets->pluck('serial_number')->implode(','),
            ];

            // Kirim data ke API Aplikasi 2
            try {
                $response = Http::withToken(config('services.sumber_data.token'))
                                ->acceptJson()
                                ->post(config('services.sumber_data.url') . '/api/v1/incidents', $dataToSync);

                if ($response->failed()) {
                    Log::error('Gagal sinkronisasi insiden ke Aplikasi 2:', ['response' => $response->body()]);
                }
            } catch (\Exception $e) {
                Log::critical('Koneksi API ke Aplikasi 2 gagal:', ['error' => $e->getMessage()]);
            }
        }
}
