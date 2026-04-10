<?php

namespace Database\Seeders\Dupak;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HasilEvaluasiSeeder extends Seeder
{
    public function run(): void
    {
        $dbMain = DB::connection(); // sdm_tus
        $dbDupak = DB::connection('dupak');

        // Ambil Admin pertama untuk simulasi verifikasi admin
        $adminId = $dbMain->table('users')->where('is_admin', 1)->value('id');
        
        // Ambil detail pengajuan
        $details = $dbDupak->table('detail_pengajuan')->get();

        foreach ($details as $detail) {
            // 1. Tambahkan Evaluasi dari Admin (Verifikasi Dokumen)
            $dbDupak->table('hasil_evaluasi')->insert([
                'detail_pengajuan_id' => $detail->id,
                'idUserPemeriksa' => (string) $adminId, // ID Admin (cast ke string jika UUID di DB)
                'peran_pemeriksa' => 'Admin',
                'status_evaluasi' => 'Verified',
                'catatan' => 'Dokumen bukti pendukung sudah sesuai dan terbaca jelas.',
                'created_at' => now(),
            ]);

            // 2. Tambahkan Evaluasi dari TPAK (Penilaian Angka Kredit)
            // Cari TPAK yang ditunjuk untuk pengajuan ini
            $tpakAssigned = $dbDupak->table('penunjukan_tpak')
                ->where('pengajuan_id', $detail->pengajuan_id)
                ->first();

            if ($tpakAssigned) {
                $dbDupak->table('hasil_evaluasi')->insert([
                    'detail_pengajuan_id' => $detail->id,
                    'idUserPemeriksa' => $tpakAssigned->idDosenTpak,
                    'peran_pemeriksa' => 'TPAK',
                    'status_evaluasi' => 'Verified',
                    'nilai_angka_kredit' => $detail->angka_kredit_total, // TPAK menyetujui nilai penuh
                    'catatan' => 'Kontribusi kegiatan sangat relevan dengan bidang ilmu.',
                    'created_at' => now(),
                ]);
            }
        }
    }
}
