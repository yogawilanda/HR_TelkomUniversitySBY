<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AkumulasiKinerja;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AkumulasiKinerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->info('No users found. Please seed users first.');
            return;
        }

        $this->command->info('Seeding AkumulasiKinerja for ' . $users->count() . ' users...');

        $monthsToSeed = 4;
        $now = Carbon::now();

        foreach ($users as $user) {
            for ($i = 0; $i < $monthsToSeed; $i++) {
                $date = $now->copy()->subMonths($i);
                
                // Realistic random data
                $kehadiran = rand(18, 22);
                $tepatWaktu = rand(15, $kehadiran);
                $tidakTapPulang = rand(0, 2);
                $jamKerja = round(rand(140, 180) + (rand(0, 99) / 100), 2);

                AkumulasiKinerja::create([
                    'id' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'employee_id' => $user->nik ?? 'EMP-' . rand(1000, 9999),
                    'fullname' => $user->nama_lengkap,
                    'year' => $date->year,
                    'month' => $date->month,
                    'jam_kerja' => $jamKerja,
                    'kehadiran' => $kehadiran,
                    'tepat_waktu' => $tepatWaktu,
                    'tidak_tap_pulang' => $tidakTapPulang,
                ]);
            }
        }

        $this->command->info('AkumulasiKinerjaSeeder completed!');
    }
}
