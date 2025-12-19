<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontrakManajemen extends Model
{
    protected $table = 'kontrak_manajemen';

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
        'periode',
        'start',
        'end',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'target_percent' => 'decimal:2',
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    /**
     * Relasi one to many dengan KontrakUnit
     */
    public function kontrakUnit()
    {
        return $this->hasMany(KontrakUnit::class, 'kontrak_manajemen_id');
    }
}
