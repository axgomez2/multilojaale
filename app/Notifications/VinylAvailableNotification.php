<?php

namespace App\Notifications;

use App\Models\VinylMaster;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VinylAvailableNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $vinylMaster;

    /**
     * Create a new notification instance.
     */
    public function __construct(VinylMaster $vinylMaster)
    {
        $this->vinylMaster = $vinylMaster;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('site.vinyl.show', [
            'artistSlug' => $this->vinylMaster->artists->first()->slug ?? 'artista',
            'titleSlug' => $this->vinylMaster->slug,
        ]);

        $artistNames = $this->vinylMaster->artists->pluck('name')->join(', ');

        return (new MailMessage)
            ->subject('Vinil Disponível: ' . $this->vinylMaster->title)
            ->view('emails.vinyl.available', [
                'user' => $notifiable,
                'vinyl' => $this->vinylMaster,
                'artistNames' => $artistNames,
                'url' => $url,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'vinyl_master_id' => $this->vinylMaster->id,
            'vinyl_title' => $this->vinylMaster->title,
            'message' => 'O vinil "' . $this->vinylMaster->title . '" agora está disponível para compra!'
        ];
    }
}
