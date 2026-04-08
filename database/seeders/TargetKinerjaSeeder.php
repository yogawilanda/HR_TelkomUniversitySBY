<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TargetKinerja;

class TargetKinerjaSeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'nama' => 'Peningkatan Publikasi Internasional',
                'keterangan' => 'Meningkatkan jumlah publikasi pada jurnal bereputasi',
                'bobot' => 30,
                'is_active' => 1,
                'responsibility' => 'Publikasi dan penelitian',
                'satuan' => 'Publikasi',
                'target_percent' => 20,
                'pencapaian_percent' => 0,
                'status' => 'institusi',
                'unit_penanggung_jawab' => 'Fakultas Teknologi Informasi',
                'evidence' => 'https://repo.example.com/publikasi',
                'periode' => '2025',
            ],
            [
                'nama' => 'Kegiatan Pengabdian Masyarakat',
                'keterangan' => 'Pelaksanaan kegiatan pengabdian masyarakat oleh prodi',
                'bobot' => 20,
                'is_active' => 1,
                'responsibility' => 'Pengabdian',
                'satuan' => 'Kegiatan',
                'target_percent' => 10,
                'pencapaian_percent' => 0,
                'status' => 'unit',
                'unit_penanggung_jawab' => 'Prodi Sistem Informasi',
                'evidence' => '',
                'periode' => '2025',
            ],
            [
                'nama' => 'Kinerja Pribadi Dosen',
                'keterangan' => 'Target kinerja individu dosen',
                'bobot' => 50,
                'is_active' => 1,
                'responsibility' => 'Dosen',
                'satuan' => 'Unit',
                'target_percent' => 50,
                'pencapaian_percent' => 0,
                'status' => 'pribadi',
                'unit_penanggung_jawab' => 'Dosen yang bersangkutan',
                'evidence' => '',
                'periode' => '2025',
            ],
        ];

        foreach ($items as $data) {
            TargetKinerja::create($data);
        }
    }
}
