<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel UNITS (ERD).
 * Mewakili unit/bagian dalam organisasi yang menjadi
 * penerima distribusi Kontrak Manajemen KPI.
 */
class Unit extends Model
{
    protected $table = 'units';

    protected $fillable = [
        'nama_unit',
        'kode_unit',
    ];

    /**
     * Relasi ke users yang bernaung di unit ini.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'unit_id');
    }

    /**
     * Relasi ke KPI yang didistribusikan ke unit ini (melalui kontrak_unit).
     */
    public function kontrakUnit()
    {
        return $this->hasMany(KontrakUnit::class, 'unit_id');
    }

    /**
     * Relasi many-to-many ke TargetKinerja melalui pivot kontrak_unit.
     */
    public function targetKinerja()
    {
        return $this->belongsToMany(TargetKinerja::class, 'kontrak_unit', 'unit_id', 'km_id')
            ->withPivot('target_angka')
            ->withTimestamps();
    }

    /**
     * Akumulasi kinerja bulanan dari unit ini.
     */
    public function akumulasiKinerja()
    {
        return $this->hasMany(AkumulasiKinerjaUnit::class, 'unit_id');
    }
}
