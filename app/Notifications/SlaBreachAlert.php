<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;
use App\Models\Incident;

class SlaBreachAlert extends Notification
{
    use Queueable;
    protected $incident;

    public function __construct(Incident $incident)
    {
        $this->incident = $incident;
    }

    public function via(object $notifiable): array
    {
        return ['telegram'];
    }

    public function toTelegram(object $notifiable)
    {
        $url = route('incidents.show', $this->incident->id);
        return TelegramMessage::create()
            ->to(config('services.telegram-bot-api.chat_id'))
            ->content("‼️ **PERINGATAN PELANGGARAN SLA** ‼️\n\n"
                . "Tiket insiden berikut belum ditangani:\n\n"
                . "*Judul:* " . $this->incident->title . "\n"
                . "*Site:* " . $this->incident->site?->name . "\n"
                . "*Waktu Laporan:* " . $this->incident->created_at->format('d M Y, H:i'))
            ->button('Segera Tangani', $url);
    }
}