<?php

namespace App\Models\Dupak;

/**
 * Model untuk menyimpan hasil verifikasi dokumen (Admin) dan penilaian angka kredit (TPAK).
 */
class HasilEvaluasi extends DupakModel
{
    protected $table = 'hasil_evaluasi';

    protected $fillable = [
        'detail_pengajuan_id',
        'idUserPemeriksa',
        'peran_pemeriksa',
        'status_evaluasi',
        'catatan',
        'nilai_angka_kredit',
    ];

    /**
     * Relasi kembali ke detail butir kegiatan
     */
    public function detail()
    {
        return $this->belongsTo(DetailPengajuan::class, 'detail_pengajuan_id');
    }

    // Catatan: idUserPemeriksa merujuk pada tabel users di database utama (sdm_tus).
    // Karena lintas database, pemanggilan user disarankan menggunakan ID secara langsung di Controller.
}
