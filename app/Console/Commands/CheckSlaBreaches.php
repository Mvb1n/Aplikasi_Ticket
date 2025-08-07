<?php

namespace App\Console\Commands;

use Carbon\Carbon;

use App\Models\Incident;
use Illuminate\Console\Command;
use App\Notifications\SlaBreachAlert;
use Illuminate\Support\Facades\Notification;

class CheckSlaBreaches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-sla-breaches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Periksa tiket insiden yang melanggar SLA dan kirim notifikasi';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memeriksa pelanggaran SLA...');

        // Aturan SLA: 1 jam (3600 detik). Anda bisa mengubahnya.
        $slaTimeInSeconds = 3600; 

        // Cari insiden yang:
        // 1. Statusnya 'Open'
        // 2. Dibuat lebih dari 1 jam yang lalu
        // 3. Belum pernah dikirimi notifikasi pelanggaran (sla_breached_at masih kosong)
        $breachedIncidents = Incident::where('status', 'Open')
            ->where('created_at', '<', Carbon::now()->subSeconds($slaTimeInSeconds))
            ->whereNull('sla_breached_at')
            ->get();

        if ($breachedIncidents->isEmpty()) {
            $this->info('Tidak ada pelanggaran SLA ditemukan.');
            return;
        }

        $this->info("Ditemukan {$breachedIncidents->count()} pelanggaran SLA. Mengirim notifikasi...");

        foreach ($breachedIncidents as $incident) {
            // Kirim notifikasi ke channel default (Telegram)
            Notification::route('telegram', config('services.telegram-bot-api.chat_id'))
                        ->notify(new SlaBreachAlert($incident));

            // Tandai bahwa notifikasi sudah dikirim agar tidak dikirim lagi
            $incident->sla_breached_at = Carbon::now();
            $incident->save();

            $this->info("Notifikasi untuk insiden #{$incident->id} telah dikirim.");
        }

        $this->info('Semua notifikasi pelanggaran SLA telah dikirim.');
    }
}
