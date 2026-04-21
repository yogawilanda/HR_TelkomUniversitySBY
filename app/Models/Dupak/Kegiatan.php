<?php

namespace App\Models\Dupak;

class Kegiatan extends DupakModel
{
    protected $table = 'kegiatan';
    protected $fillable = [
        'kategori',
        'sub_kategori',
        'nama',
        'satuan_hasil',
        'angka_kredit'
    ];

    public function details()
    {
        return $this->hasMany(DetailPengajuan::class);
    }
}