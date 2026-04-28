<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\Tpa;
use App\Models\RefJenjangPendidikan;
use App\Models\RiwayatJenjangPendidikan;
use Illuminate\Database\Seeder;

class RiwayatJenjangPendidikanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenjangs = RefJenjangPendidikan::all()->keyBy('jenjang_pendidikan');
        
        if ($jenjangs->isEmpty()) {
            $this->command->warn('RefJenjangPendidikan table is empty. Please run RefJenjangPendidikanSeeder first.');
            return;
        }

        // 1. Process Dosen
        Dosen::all()->each(function ($dosen) use ($jenjangs) {
            $userId = $dosen->users_id;
            
            // Dosen must have at least S2
            // We still seed S1 for completeness of history
            $this->createEducation($userId, $jenjangs['S1']->id, 2010);
            $this->createEducation($userId, $jenjangs['S2']->id, 2015);
            
            // 30% of Dosens have S3
            if (fake()->boolean(30)) {
                $this->createEducation($userId, $jenjangs['S3']->id, 2020);
            }
        });

        // 2. Process TPA
        Tpa::all()->each(function ($tpa) use ($jenjangs) {
            $userId = $tpa->users_id;
            
            // TPA distribution: 50% D3, 50% S1
            if (fake()->boolean(50)) {
                $this->createEducation($userId, $jenjangs['D3']->id, 2012);
            } else {
                $this->createEducation($userId, $jenjangs['S1']->id, 2014);
                
                // 10% of S1 TPAs have S2
                if (fake()->boolean(10)) {
                    $this->createEducation($userId, $jenjangs['S2']->id, 2018);
                }
            }
        });
    }

    private function createEducation($userId, $jenjangId, $baseYear)
    {
        RiwayatJenjangPendidikan::factory()->create([
            'users_id' => $userId,
            'jenjang_pendidikan_id' => $jenjangId,
            'tahun_lulus' => $baseYear + fake()->numberBetween(0, 3),
        ]);
    }
}
