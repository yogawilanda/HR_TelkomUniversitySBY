<?php

namespace App\Models\Dupak;

use App\Models\User;
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
        // Ambil ID user dengan aman (baik jika dipassing Objek Model maupun String ID)
        $userId = $user instanceof User ? $user->id : (is_object($user) ? $user->id : $user);

        return static::create([
            'id'              => (string) Str::uuid(),
            'type'            => 'App\Notifications\DupakNotification',
            'notifiable_type' => User::class, // Patenkan ke Model User utama
            'notifiable_id'   => (string) $userId,
            'data'            => [
                'title'   => $title,
                'message' => $message,
                'url'     => $url ?? '#',
            ],
            'read_at'         => null,
        ]);
    }
}