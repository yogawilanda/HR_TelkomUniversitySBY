<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontrakUnit extends Model
{
    protected $table = 'kontrak_unit';

    protected $fillable = [
        'kontrak_manajemen_id',
        'nama_unit',
        'pekerjaan',
        'kontrak_type',
        'result',
        'jumlah',
        'waktu_minutes',
        'is_active',
        'bobot',
        'start',
        'end',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    /**
     * Relasi many to one dengan KontrakManajemen
     */
    public function kontrakManajemen()
    {
        return $this->belongsTo(KontrakManajemen::class, 'kontrak_manajemen_id');
    }

    /**
     * Relasi one to one dengan KinerjaUnit
     */
    public function kinerjaUnit()
    {
        return $this->hasOne(KinerjaUnit::class, 'kontrak_unit_id');
    }

    /**
     * Relasi many to many dengan User (pegawai)
     */
    public function pegawai()
    {
        return $this->belongsToMany(User::class, 'kontrak_unit_pegawai', 'kontrak_unit_id', 'user_id')
            ->withPivot('tanggal_mulai', 'tanggal_selesai', 'status', 'catatan')
            ->withTimestamps();
    }
}
