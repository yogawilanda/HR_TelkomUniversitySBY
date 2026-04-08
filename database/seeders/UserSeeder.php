<?php

namespace Database\Seeders;

use App\Models\Emergency_contact;
use App\Models\formation;
use App\Models\Level;
use App\Models\pengawakan;
use App\Models\ref_work_position;
use App\Models\riwayatJenjangPendidikan;
use App\Models\RiwayatNip;
use App\Models\SK;
use App\Models\Tpa;
use App\Models\User;
use App\Models\work_position;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        // Create admin user
        // $user1 = User::factory()->admin()->create([
        //     'nama_lengkap' => 'Admin Telkom University',
        //     'email_institusi' => 'admin@telkomuniversity.ac.id',
        // ]);



        // // Create test user accounts
        // $user2 = User::factory()->create([
        //     'nama_lengkap' => 'Budi Santoso',
        //     'email_institusi' => 'budi.santoso@telkomuniversity.ac.id',
        // ]);

        // $user3 = User::factory()->create([
        //     'nama_lengkap' => 'Siti Nurhaliza',
        //     'email_institusi' => 'siti.nurhaliza@telkomuniversity.ac.id',
        // ]);

        // User::create([
        //     'nama_lengkap' => 'Siti Nurhaliza',
        //     'telepon' => '087895732155',
        //     'email_institusi' => 'siti.nurhaliza@telkomuniversity.ac.id',
        //     'password' => 'password123',
        // ]);

        // User::create([
        //     'nama_lengkap' => 'Budi Santoso',
        //     'telepon' => '084553776814',
        //     'email_institusi' => 'budi.santoso@telkomuniversity.ac.id',
        //     'password' => 'password123',
        // ]);

        User::factory()->create([
            'nama_lengkap' => 'Admin Telkom University',
            // 'telepon' => '088193176424',
            'email_institusi' => 'admin@telkomuniversity.ac.id',
            // 'password' => 'password123',
            'is_admin' => 1,
        ]);
        User::factory()->count(30)->create([
            'is_admin'=>0,
        ]);
        // User::factory();

        $refJenjangPendidikan = \App\Models\RefJenjangPendidikan::all();
        $refPangkatGolongan = \App\Models\RefPangkatGolongan::all();
        $refStatusPegawai = \App\Models\RefStatusPegawai::all();
        $refJFA = \App\Models\RefJabatanFungsionalAkademik::all();
        $refJFK = \App\Models\RefJabatanFungsionalKeahlian::all();
        $refFormasi = \App\Models\formation::all();

        $refProdi = work_position::where('type_work_position', 'Program Studi')->get();
        $refbagian = ref_work_position::all();

        // dd($refbagian);
        // dd(count($refProdi));
        // dd($refProdi[0]);


        $users = User::all();
        // dd($users);
        foreach ($users as $user) {
            // dd($user->id);


            $indexRefStatusPegawai = fake()->numberBetween(0, count($refStatusPegawai) - 1);
            // dd($refStatusPegawai[$indexRefStatusPegawai]);

            //buat 2 emergency contact
            emergency_contact::factory(2)->create([
                'users_id' => $user->id,
            ]);

            // Buat history NIP
            RiwayatNip::factory()->create([
                'users_id' => $user->id,
                'nip' => fake()->unique()->numerify('##############'),
                'status_pegawai_id' => $refStatusPegawai[$indexRefStatusPegawai]['id'],
                // 'status_pegawai_id' => (string) data_get($refStatusPegawai[$indexRefStatusPegawai], 'id'),

            ]);

            if (fake()->boolean()) {
                riwayatJenjangPendidikan::factory()->create([
                    'users_id' => $user->id,
                    'jenjang_pendidikan_id' => $refJenjangPendidikan[fake()->numberBetween(0, count($refJenjangPendidikan) - 1)]->id,
                ]);

                if (fake()->boolean()) {
                    riwayatJenjangPendidikan::factory()->create([
                        'users_id' => $user->id,
                        'jenjang_pendidikan_id' => $refJenjangPendidikan[fake()->numberBetween(0, count($refJenjangPendidikan) - 1)]->id,
                    ]);
                }
            }

            $create_dosen_or_tpa = null;
            if ($user->tipe_pegawai === 'Dosen') {
                $indexRefPangkatGolongan = fake()->numberBetween(0, count($refPangkatGolongan) - 1);
                $indexRefJFA = fake()->numberBetween(0, count($refJFA) - 1);
                $indexProdi =  fake()->numberBetween(0, count($refProdi) - 1);
                // dd($refProdi[$indexProdi]->id);
                $dosen = \App\Models\Dosen::factory()->create([
                    'users_id' => $user->id,
                    'prodi_id' => $refProdi[$indexProdi]->id,

                ]);

                // Assign kelompok keahlian ke dosen
                $kelompokKeahlian = \App\Models\KelompokKeahlian::all();
                if ($kelompokKeahlian->isNotEmpty()) {
                    $numAssign = fake()->numberBetween(1, min(3, $kelompokKeahlian->count()));
                    $assignedKK = $kelompokKeahlian->random($numAssign);
                    foreach ($assignedKK as $kk) {
                        $dosen->kelompokKeahlian()->attach($kk->id);
                    }
                }

                $skLLKDIKTI = SK::factory()->create([
                    'users_id' => $user->id,
                    'tipe_sk' => 'LLDIKTI',
                ]);

                $skYPT = SK::factory()->create([
                    'users_id' => $user->id,
                    'tipe_sk' => 'Pengakuan YPT',
                ]);

                // dd($refPangkatGolongan[$indexRefPangkatGolongan]->id);
                \App\Models\riwayatPangkatGolongan::factory()->create([
                    'dosen_id' => $dosen->id,
                    'pangkat_golongan_id' => $refPangkatGolongan[$indexRefPangkatGolongan]->id,
                    'sk_llkdikti_id' => $skLLKDIKTI->id,
                    // 'sk_pengakuan_ypt_id' => $skYPT->id,
                ]);

                \App\Models\riwayatJabatanFungsionalAkademik::factory()->create([
                    'dosen_id' => $dosen->id,
                    'ref_jfa_id' => $refJFA[$indexRefJFA]->id,
                    'sk_llkdikti_id' => $skLLKDIKTI->id,
                    'sk_pengakuan_ypt_id' => $skYPT->id,
                    'tmt_mulai' => now(),
                ]);
            } else {
                $skLLKDIKTI = SK::factory()->create([
                    'users_id' => $user->id,
                    'tipe_sk' => 'Pengakuan YPT',
                ]);
                $tpa_models = Tpa::factory()->create([
                    'users_id' => $user->id,
                    'nitk' => fake()->unique()->numerify('#############'),
                ]);
                // dd($tpa_models);

                $indexRefJFK = fake()->numberBetween(0, count($refJFK) - 1);
                // dD($indexRefJFK);
                $boolRandom = fake()->boolean();
                $sk = $boolRandom == true ? $skLLKDIKTI->id : null;
                \App\Models\riwayatJabatanFungsionalKeahlian::factory()->create([
                    'tpa_id' => $tpa_models->id,
                    'ref_jfk_id' => $refJFK[$indexRefJFK]->id,
                    // 'sk_llkdikti_id' => $skLLKDIKTI->id,
                    'sk_pengakuan_ypt_id' => $sk,
                    'tmt_mulai' => now(),
                    'tmt_selesai' => null,
                ]);
            }


            $formasi = [];
            // $bagian = [];
            $count = fake()->numberBetween(1, 3);
            for ($i = 0; $i < $count; $i++) {

                // $bagian = $refbagian[fake()->numberBetween(0, count($refbagian)-1)];
                // dd($bagian->position_name);
                $formations = Formation::whereHas('bagian', function ($q) {
                    $q->where('type_work_position', fake()->randomElement(['Bagian', 'Fakultas', 'Program Studi']));
                })->get();

                $indexFormation = fake()->numberBetween(0, count($formations) - 1);
                $formasi[] = $formations[$indexFormation];
            }

            // dd($formasi);
            for ($i = 0; $i < count($formasi); $i++) {
                $skYPT = SK::factory()->create([
                    'users_id' => $user->id,
                    'tipe_sk' => 'Pengakuan YPT',
                ]);

                $pemetaan = pengawakan::factory()->create([
                    'users_id' => $user->id,
                    'formasi_id' => $formasi[$i]->id,
                    'sk_ypt_id' => $skYPT->id,
                    'tmt_selesai' => today(),
                ]);
            }

            $formasi = [];
            // $bagian = [];
            $count = fake()->numberBetween(1, 3);
            for ($i = 0; $i < $count; $i++) {

                // $bagian = $refbagian[fake()->numberBetween(0, count($refbagian)-1)];
                // dd($bagian->position_name);
                $formations = Formation::whereHas('bagian', function ($q) {
                    $q->where('type_work_position', fake()->randomElement(['Bagian', 'Fakultas', 'Program Studi']));
                })->get();

                $indexFormation = fake()->numberBetween(0, count($formations) - 1);
                $formasi[] = $formations[$indexFormation];
            }

            // dd($formasi);
            for ($i = 0; $i < count($formasi); $i++) {
                $skYPT = SK::factory()->create([
                    'users_id' => $user->id,
                    'tipe_sk' => 'Pengakuan YPT',
                ]);

                $pemetaan = pengawakan::factory()->create([
                    'users_id' => $user->id,
                    'formasi_id' => $formasi[$i]->id,
                    'sk_ypt_id' => $skYPT->id,
                    'tmt_selesai' => null,
                ]);
            }



            // $indexRefFormasi = fake()->numberBetween(0, count($refFormasi)-1);
        }
    }
}
