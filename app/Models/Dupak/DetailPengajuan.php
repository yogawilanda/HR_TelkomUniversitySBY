<?php

namespace App\Models\Dupak;

/**
 * Model untuk menyimpan detail setiap butir kegiatan yang diajukan dalam DUPAK.
 * Tabel ini menyimpan deskripsi kegiatan dan angka kredit (KUM) yang diinput dosen.
 */
class DetailPengajuan extends DupakModel
{
	protected $table = 'detail_pengajuan';

	protected $fillable = [
		'pengajuan_id',
		'idKomponen',
		'idJenisInput',
		'deskripsi_kegiatan',
		'angka_kredit_murni',
		'angka_kredit_total',
		'volume',
		'link_bukti_pendukung',
		'is_verified',
		'catatan_pemeriksa',
	];

	/**
	 * Relasi ke Header Pengajuan (Parent)
	 */
	public function pengajuan()
	{
		return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
	}

	/**
	 * Relasi ke Master Komponen Kegiatan (Referensi)
	 */
	public function komponen()
	{
		return $this->belongsTo(RefKegiatanKomponen::class, 'idKomponen');
	}

	/**
	 * Relasi ke Hasil Evaluasi (Feedback dari Admin dan TPAK)
	 */
	public function evaluations()
	{
		return $this->hasMany(HasilEvaluasi::class, 'detail_pengajuan_id');
	}
}
