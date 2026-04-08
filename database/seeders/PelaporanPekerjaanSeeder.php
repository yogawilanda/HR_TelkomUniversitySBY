<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PelaporanPekerjaan;
use App\Models\TargetKinerjaHarian;

class PelaporanPekerjaanSeeder extends Seeder
{
    public function run()
    {
        $harian = TargetKinerjaHarian::take(3)->get();

        foreach ($harian as $h) {
            PelaporanPekerjaan::create([
                'target_harian_id' => $h->id,
                'realisasi' => 'Telah dilakukan kegiatan: ' . $h->pekerjaan,
                'referensi_set_target_id' => $h->id,
                'realisasi_jumlah' => $h->jumlah ?? 1,
                'realisasi_waktu_minutes' => $h->waktu_minutes ?? 0,
                'approved_jumlah' => null,
                'approved_waktu_minutes' => null,
                'created_by' => null,
                'approved_by' => null,
            ]);
        }
    }
}
