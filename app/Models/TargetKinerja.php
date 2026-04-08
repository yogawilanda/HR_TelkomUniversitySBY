<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetKinerja extends Model
{
    protected $table = 'target_kinerja';

    protected $fillable = [
        'nama',
        'keterangan',
        'bobot',
        'is_active',
        'responsibility',
        'satuan',
        'target_percent',
        'status',
        'unit_penanggung_jawab',
        // 'evidence' and 'pencapaian' moved to pelaporan_pekerjaan per requirements
        'periode',
        'start', // Added new field
        'end',   // Added new field
    ];

    public function pegawai()
    {
        return $this->belongsToMany(User::class, 'target_kinerja_pegawai', 'target_kinerja_id', 'user_id')
            ->withPivot('tanggal_mulai', 'tanggal_selesai', 'status', 'catatan')
            ->withTimestamps();
    }
}
