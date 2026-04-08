<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelaporanPekerjaan extends Model
{
    protected $table = 'pelaporan_pekerjaan';

    protected $fillable = [
        'target_harian_id',
        'realisasi',
        'referensi_set_target_id',
        'realisasi_jumlah',
        'realisasi_waktu_minutes',
        'approved_jumlah',
        'approved_waktu_minutes',
        'status',
        'pencapaian_percent',
        'evidence',
        'created_by',
        'approved_by',
    ];

    public function targetHarian()
    {
        return $this->belongsTo(TargetKinerjaHarian::class, 'target_harian_id');
    }

    public function getEffectiveJumlahAttribute()
    {
        return $this->approved_jumlah !== null ? $this->approved_jumlah : $this->realisasi_jumlah;
    }

    public function getEffectiveWaktuMinutesAttribute()
    {
        return $this->approved_waktu_minutes !== null ? $this->approved_waktu_minutes : $this->realisasi_waktu_minutes;
    }
}
