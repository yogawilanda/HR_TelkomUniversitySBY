<?php

namespace App\Models\Dupak;

use Illuminate\Notifications\DatabaseNotification as BaseDatabaseNotification;
use Illuminate\Support\Str;

class NotifikasiDupakModel extends BaseDatabaseNotification
{
    // Mengarahkan ke koneksi DB DUPAK
    protected $connection = 'dupak'; 
    protected $table = 'notifications';

    // Cast data JSON & tanggal
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // Helper method static biar panggilnya simpel
    public static function send($user, string $title, string $message, ?string $url = null)
    {
        return static::create([
            'id' => (string) Str::uuid(),
            'type' => 'App\Notifications\DupakNotification',
            'notifiable_type' => get_class($user),
            'notifiable_id' => $user->id,
            'data' => [
                'title' => $title,
                'message' => $message,
                'url' => $url ?? '#',
            ],
            'read_at' => null,
        ]);
    }
}