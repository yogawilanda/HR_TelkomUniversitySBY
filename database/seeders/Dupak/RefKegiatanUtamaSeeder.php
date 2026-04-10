<?php

namespace Database\Seeders\Dupak;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefKegiatanUtamaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id' => 1, 'nama' => 'UNSUR UTAMA: PENDIDIKAN'],
            ['id' => 2, 'nama' => 'UNSUR UTAMA: PELAKSANAAN PENDIDIKAN'],
            ['id' => 3, 'nama' => 'UNSUR UTAMA: PELAKSANAAN PENELITIAN'],
            ['id' => 4, 'nama' => 'UNSUR UTAMA: PELAKSANAAN PENGABDIAN MASYARAKAT'],
            ['id' => 5, 'nama' => 'UNSUR PENUNJANG: TUGAS PENUNJANG DOSEN'],
        ];

        foreach ($data as $item) {
            DB::connection('dupak')->table('ref_kegiatan_utama')->updateOrInsert(
                ['id' => $item['id']],
                [
                    'nama' => $item['nama'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
