<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage; // Import class TelegramMessage
use App\Models\Incident; // Import model Incident

class NewIncidentReported extends Notification
{
    use Queueable;

    protected $incident;

    /**
     * Create a new notification instance.
     */
    public function __construct(Incident $incident)
    {
        $this->incident = $incident;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['telegram'];
    }

    /**
     * Get the Telegram representation of the notification.
     */
        public function toTelegram(object $notifiable)
        {
            // Ambil nama site dari relasi. Gunakan nullsafe operator untuk keamanan.
            $siteName = $this->incident->site?->name ?? 'Tidak diketahui';

            // Buat URL untuk tombol
            $url = route('incidents.show', $this->incident->id);

            return TelegramMessage::create()
                ->to(config('services.telegram-bot-api.chat_id'))
                ->content("⚠️ **NOTIFIKASI INSIDEN BARU** ⚠️\n\n"
                    . "*Judul:* " . $this->incident->title . "\n"
                    . "*Site:* " . $siteName . "\n" // TAMBAHKAN BARIS INI
                    . "*Lokasi:* " . $this->incident->location . "\n"
                    . "*Pelapor:* " . $this->incident->user->name)
                ->button('Lihat Detail Insiden', $url);
                }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}