<?php

namespace App\Notifications;

use App\Models\Dupak\NotifikasiDupakModel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DupakNotification extends Notification
{
    use Queueable;

    protected string $title;
    protected string $message;
    protected string $url;

    public function __construct(string $title, string $message, string $url = '#')
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'url'     => $this->url,
        ];
    }

    // Paksa simpan ke DB DUPAK via NotifikasiDupakModel
    public function toDatabase($notifiable): NotifikasiDupakModel
    {
        return new NotifikasiDupakModel([
            'id'      => $this->id,
            'type'    => static::class,
            'data'    => $this->toArray($notifiable),
            'read_at' => null,
        ]);
    }
}