<?php

namespace Tests;

use App\Models\Formation;
use App\Models\Level;
use App\Models\Pengawakan;
use App\Models\RefWorkPosition;
use App\Models\User;
use App\Models\Work_Position;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // use DatabaseTransactions;

    // use RefreshDatabase;

    protected $connectionsToTransact = ['mysql', 'dupak'];

    public function define_account($is_admin = false, $is_new = false, $email_verif = false, $email = 'admin@telkomuniversity.ac.id', $is_active = true, $is_sdm = false)
    {
        $date_verif_email = $email_verif == true ? now() : null;
        $user = User::factory()->create([
            'nama_lengkap' => 'Admin',
            'email_institusi' => $email,
            // 'email_pribadi' => 'mardiahresti@gmail.com',
            // 'password' => 'CobaIni',
            'is_admin' => $is_admin,
            'is_new' => $is_new,
            'is_active' => $is_active,
            'email_verified_at' => $date_verif_email,
        ]);

        if ($is_sdm == true) {
            $level = Level::factory()->create([
                'nama_level' => 'Anggota',
                'urut' => 5,
            ]);

            $refbagian = RefWorkPosition::where('position_name','Bagian')->first();
            if(!$refbagian){
                $refbagian = RefWorkPosition::factory()->create([
                    'position_name' => 'Bagian',
                ]);
            }

            $bagian = Work_Position::where('position_name','Sumber Daya Manusia')->first();
            if(!$bagian){
                $bagian = Work_Position::factory()->create([
                    'type_work_position' => $refbagian['position_name'],
                    'position_name' => 'Sumber Daya Manusia',

                ]);
            }

            // dd($bagian);
            $formasi = Formation::where('nama_formasi', 'Anggota SDM')->first();
            if(!$formasi){
                $formasi = Formation::factory()->create([
                    'level_id' => $level['id'],
                    'nama_formasi' => 'Anggota SDM',
                    'work_position_id' => $bagian['id'],
                ]);
            }

            // dd($level);

            $pengawakan = Pengawakan::factory()->create([
                'formasi_id' => $formasi['id'],
                'users_id' => $user['id'],
                'tmt_mulai' => now()->subDays(5),
                'tmt_selesai' => now()->addDays(30),
            ]);
        }

        return $user;
    }
}
