<?php

namespace App\Models\Dupak;

use App\Models\refJabatanFungsionalAkademik;

class RefTargetJabatanPengajuan extends DupakModel
{
    protected $table = 'ref_target_jabatan_pengajuan';

    protected $fillable = [
        'jfaAsal',
        'jfaTujuan',
        // update request Pak Dahliar.
        'limit_lampiran_1',
        'limit_lampiran_2',
        'limit_lampiran_3',
        'limit_lampiran_4',
        'limit_lampiran_5',
        'klausa_lampiran_1',
        'klausa_lampiran_2',
        'klausa_lampiran_3',
        'klausa_lampiran_4',
        'klausa_lampiran_5',
        'kumTarget',
        'isActive'
    ];

    public function jabatanAsal()
    {
        return $this->belongsTo(refJabatanFungsionalAkademik::class, 'jfaAsal');
    }

    public function jabatanTujuan()
    {
        return $this->belongsTo(refJabatanFungsionalAkademik::class, 'jfaTujuan');
    }
}