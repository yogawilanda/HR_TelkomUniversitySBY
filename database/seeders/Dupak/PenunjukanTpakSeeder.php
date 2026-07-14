<?php

namespace Database\Seeders\Dupak;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenunjukanTpakSeeder extends Seeder
{
    public function run(): void
    {
        $dbMain = DB::connection(); // sdm_tus
        $dbDupak = DB::connection('dupak');

        // 1. Ambil semua pengajuan
        $listPengajuan = $dbDupak->table('pengajuan')->get();

        // 2. Ambil beberapa Dosen kandidat TPAK (Pastikan mereka bukan pemilik pengajuan)
        // Kita ambil 5 dosen secara acak untuk diputar sebagai TPAK
        $dosenKandidat = $dbMain->table('dosens')->limit(5)->pluck('id')->toArray();

        if (empty($dosenKandidat) || $listPengajuan->isEmpty()) {
            return;
        }

        foreach ($listPengajuan as $pengajuan) {
            // Pilih 2 dosen secara acak dari kandidat, pastikan bukan pengaju itu sendiri
            $tpaks = collect($dosenKandidat)
                ->reject(fn($id) => $id === $pengajuan->idDosen)
                ->random(min(2, count($dosenKandidat)));

            foreach ($tpaks as $idDosenTpak) {
                $dbDupak->table('penunjukan_tpak')->updateOrInsert(
                    [
                        'pengajuan_id' => $pengajuan->id,
                        'idDosenTpak' => $idDosenTpak
                    ],
                    [
                        'bukti_penunjukan' => 'SK-TPAK-' . $pengajuan->id . '.pdf',
                        'nilai_angka_kredit' => null, // Belum dinilai
                        'catatan' => 'Ditugaskan untuk meninjau pengajuan ini.',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
