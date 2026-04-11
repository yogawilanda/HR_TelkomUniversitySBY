<?

namespace App\Models\Dupak;

/**
 * Model untuk menyimpan data penunjukan TPAK pada setiap pengajuan.
 * Tabel ini menyimpan relasi antara dosen yang ditunjuk sebagai TPAK dengan pengajuan yang diajukan.
 */
class PenunjukanTPAKModel extends DupakModel {
	protected $table = 'penunjukan_tpaks';

	protected $fillable = [
		'pengajuan_id',
		'idDosenTpak',
		'bukti_penunjukan',
		'nilai_angka_kredit',
		'catatan',
	];

	/**
	 * Relasi ke Pengajuan
	 */
	public function pengajuan()
	{
		return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
	}

}