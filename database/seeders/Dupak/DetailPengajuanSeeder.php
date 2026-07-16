<?php

namespace Database\Seeders\Dupak;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetailPengajuanSeeder extends Seeder
{
    public function run(): void
    {
        $dbDupak = DB::connection('dupak');
        $listPengajuan = $dbDupak->table('pengajuan')->get();

        foreach ($listPengajuan as $pengajuan) {
            // Tambahkan 2 contoh kegiatan per pengajuan
            $dbDupak->table('detail_pengajuan')->insert([
                [
                    'pengajuan_id' => $pengajuan->id,
                    'idKomponen' => 3, // Melaksanakan perkuliahan
                    'deskripsi_kegiatan' => 'Mengajar Mata Kuliah Pemrograman Web di Semester Ganjil',
                    'volume' => 3.00,
                    'angka_kredit_murni' => 1.00,
                    'angka_kredit_total' => 3.00,
                    'status' => 'Pending',
                    'link_bukti_pendukung' => 'https://drive.google.com/sample-bukti-ajar',
                    'created_at' => now(),
                ],
                [
                    'pengajuan_id' => $pengajuan->id,
                    'idKomponen' => 16, // Menghasilkan Karya Ilmiah
                    'deskripsi_kegiatan' => 'Publikasi Jurnal Internasional: Deep Learning for HR',
                    'volume' => 1.00,
                    'angka_kredit_murni' => 20.00,
                    'angka_kredit_total' => 20.00,
                    'status' => 'Pending',
                    'link_bukti_pendukung' => 'https://jurnal.org/deep-learning-hr',
                    'created_at' => now(),
                ]
            ]);
        }
    }
}

