<?php

namespace App\Listeners;

use App\Events\IncidentCreated;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\NewIncidentReported;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendNewIncidentNotification
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
    public function handle(IncidentCreated $event): void
    {
        // Ambil data insiden dari event
        $incident = $event->incident;

        // Kirim notifikasi ke channel default (Telegram)
        Notification::route('telegram', config('services.telegram-bot-api.chat_id'))
                    ->notify(new NewIncidentReported($incident));
    }

}

