<?php

namespace App\Models\Dupak;

use App\Models\Dosen;

/**
 * Model untuk menyimpan data penunjukan TPAK pada setiap pengajuan.
 * Tabel ini menyimpan relasi antara dosen yang ditunjuk sebagai TPAK dengan pengajuan yang diajukan.
 */
class PenunjukanTPAKModel extends DupakModel {
	protected $table = 'penunjukan_tpak';

	protected $fillable = [
		'pengajuan_id',
		'idDosenTpak',
		'bukti_penunjukan',
		'nilai_angka_kredit',
		'catatan',
		'created_by',
	];

	/**
	 * Relasi ke Pengajuan
	 */
	public function pengajuan()
	{
		return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
	}

	/**
	 * Relasi ke Dosen (Database sdm_tus)
	 */
	public function dosenTpak()
	{
		return $this->belongsTo(Dosen::class, 'idDosenTpak');
	}

	/**
	 * Relasi ke User (Admin yang menunjuk)
	 */
	public function creator()
	{
		return $this->belongsTo(\App\Models\User::class, 'created_by');
	}
}
