<?php

namespace App\Listeners;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Events\IncidentUpdatedInApp1;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendIncidentUpdateToApp2
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
    public function handle(IncidentUpdatedInApp1 $event): void
    {
        $incident = $event->incident;
        $dataToSync = [
            'title' => 'required|string',
            'reporter_email' => 'required|email',
            'site_location_code' => 'required|string',
            'specific_location' => 'required|string',
            'chronology' => 'required|string',
            'involved_asset_sn' => 'nullable|string',
        ];
        $apiUrl = config('services.sumber_data.url') . '/api/v1/incidents/' . $incident->serial_number;
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
