<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelaporanPekerjaan extends Model
{
    protected $table = 'pelaporan_pekerjaan';

    protected $fillable = [
        'target_harian_id',
        'kinerja_unit_id',
        'tpa_id',
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

    /**
     * Relasi dengan TargetKinerjaHarian (untuk backward compatibility)
     */
    public function targetHarian()
    {
        return $this->belongsTo(TargetKinerjaHarian::class, 'target_harian_id');
    }

    /**
     * Relasi many to one dengan KinerjaUnit
     */
    public function kinerjaUnit()
    {
        return $this->belongsTo(KinerjaUnit::class, 'kinerja_unit_id');
    }

    /**
     * Relasi dengan TPA (pelaporan berasal dari TPA)
     */
    public function tpa()
    {
        return $this->belongsTo(Tpa::class, 'tpa_id');
    }

    /**
     * Relasi dengan user yang membuat pelaporan
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi dengan user yang menyetujui pelaporan
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
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
