<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KinerjaUnit extends Model
{
    protected $table = 'kinerja_unit';

    protected $fillable = [
        'kontrak_unit_id',
        'status',
        'realisasi_percent',
        'catatan',
        'total_realisasi_jumlah',
        'total_realisasi_waktu_minutes',
    ];

    protected $casts = [
        'realisasi_percent' => 'decimal:2',
    ];

    /**
     * Relasi one to one dengan KontrakUnit (many to one dari sisi KinerjaUnit)
     */
    public function kontrakUnit()
    {
        return $this->belongsTo(KontrakUnit::class, 'kontrak_unit_id');
    }

    /**
     * Relasi one to many dengan PelaporanPekerjaan (many to one dari sisi PelaporanPekerjaan)
     */
    public function pelaporanPekerjaan()
    {
        return $this->hasMany(PelaporanPekerjaan::class, 'kinerja_unit_id');
    }
}
