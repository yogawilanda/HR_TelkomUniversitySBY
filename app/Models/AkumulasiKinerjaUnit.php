<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel akumulasi_kinerja_unit.
 * Menyimpan rekap kinerja unit per bulan.
 */
class AkumulasiKinerjaUnit extends Model
{
    protected $table = 'akumulasi_kinerja_unit';

    protected $fillable = [
        'unit_id',
        'bulan',
        'tahun',
        'total_menit',
        'persentase_capaian',
    ];

    /**
     * Relasi ke Unit.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
