<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TargetKinerjaHarian;
use App\Models\TargetKinerja;
use Carbon\Carbon;

class TargetKinerjaHarianSeeder extends Seeder
{
    public function run()
    {
        $targets = TargetKinerja::take(3)->get();

        foreach ($targets as $t) {
            TargetKinerjaHarian::create([
                'pekerjaan' => 'Rapat Koordinasi ' . $t->nama,
                'kontrak_type' => $t->status,
                'target_kinerja_id' => $t->id,
                'result' => 'Dokumen notulen',
                'jumlah' => 1,
                'waktu_minutes' => 60,
                'is_active' => 1,
                'bobot' => intval($t->bobot / 3),
                'start' => Carbon::now()->toDateTimeString(),
                'end' => Carbon::now()->addHour()->toDateTimeString(),
            ]);
        }
    }
}
