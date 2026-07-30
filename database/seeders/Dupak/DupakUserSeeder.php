<?php

namespace Database\Seeders\Dupak;

use App\Models\Dosen;
use App\Models\Tpa;
use App\Models\User;
use App\Models\SK;
use App\Models\RefJabatanFungsionalAkademik;
use App\Models\RiwayatJabatanFungsionalAkademik;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DupakUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil Data Referensi JFA dari database tim
        $refJFA = RefJabatanFungsionalAkademik::all();

        $seedUsers = [
            // a. Role: Dosen Yang Memiliki Data Absah
            [
                'id' => 'dup-uid-dosen-0005',
                'nama_lengkap' => 'Dosen Utama DUPAK (Valid)',
                'email_institusi' => 'dosen.valid@telkomuniversity.ac.id',
                'email_pribadi' => 'dosen.valid@local.test',
                'is_admin' => 0,
                'tipe_pegawai' => 'Dosen',
                'telepon' => '081234567895',
                'password' => '321',
                'make_role_models' => [
                    'dosen' => true,
                    'has_complete_profile' => true,
                ],
            ],

            // b. Role: Dosen Yang Belum Lengkap Datanya
            [
                'id' => 'dup-uid-dosen-0003',
                'nama_lengkap' => 'Dosen DUPAK (Profil Kosong)',
                'email_institusi' => 'datadosenkosong@telkomuniversity.ac.id',
                'email_pribadi' => 'dosen.dupak@local.test',
                'is_admin' => 0,
                'tipe_pegawai' => 'Dosen',
                'telepon' => '081234567893',
                'password' => '321',
                'make_role_models' => [
                    'dosen' => true,
                ],
            ],

            // c. Role: TPA yang merangkap sebagai Admin di sistem
            [
                'id' => 'dup-uid-admin-tpak-0001',
                'nama_lengkap' => 'Admin DUPAK (TPA)',
                'email_institusi' => 'admin.tpa@telkomuniversity.ac.id',
                'email_pribadi' => 'admin.tpadup@local.test',
                'is_admin' => 1,
                'tipe_pegawai' => 'Tpa',
                'telepon' => '081234567891',
                'password' => '321',
                'make_role_models' => ['tpa' => true],
            ],

            // d. Role: TPA Non Admin
            [
                'id' => 'dup-uid-tpa-0004',
                'nama_lengkap' => 'TPA (non-admin)',
                'email_institusi' => 'testdupaktpa@telkomuniversity.ac.id',
                'email_pribadi' => 'tpa.dupak@local.test',
                'is_admin' => 0,
                'tipe_pegawai' => 'Tpa',
                'telepon' => '081234567894',
                'password' => '321',
                'make_role_models' => ['tpa' => true],
            ],

            // e. Role: Dosen dengan Jabatan Fungsional Akademik Tertinggi
            [
                'id' => 'dup-uid-dosen-0006',
                'nama_lengkap' => 'Dosen Guru Besar',
                'email_institusi' => 'dosen.gurubesar@telkomuniversity.ac.id',
                'email_pribadi' => 'dosen.gurubesar@local.test',
                'is_admin' => 0,
                'tipe_pegawai' => 'Dosen',
                'telepon' => '081234567896',
                'password' => '321',
                'make_role_models' => [
                    'dosen' => true,
                    'jfa_level' => 'Guru Besar',
                ],
            ],

            // f. Role: Dosen yang memiliki role sebagai Admin
            [
                'id' => 'dup-uid-admin-dosen-0008',
                'nama_lengkap' => 'Admin DUPAK (Dosen)',
                'email_institusi' => 'admin.dosen@telkomuniversity.ac.id',
                'email_pribadi' => 'admin.dosen.dup@local.test',
                'is_admin' => 1,
                'tipe_pegawai' => 'Dosen',
                'telepon' => '081234567898',
                'password' => 'password123', // Password sesuai lampiran UAT
                'make_role_models' => [
                    'dosen' => true,
                    'has_complete_profile' => true, // Opsional: Diberi profil lengkap agar bisa test ajukan data juga
                ],
            ],
            
            // Tambahan Tambahan (Admin SDM Asli tetap dipertahankan jika dibutuhkan)
            [
                'id' => 'dup-uid-admin-0002',
                'nama_lengkap' => 'Admin DUPAK (SDM)',
                'email_institusi' => 'admin.sdm@telkomuniversity.ac.id',
                'email_pribadi' => 'admin.sdm.dup@local.test',
                'is_admin' => 1,
                'tipe_pegawai' => 'Tpa',
                'telepon' => '081234567892',
                'password' => '321',
                'make_role_models' => [],
            ],
            // Tambahan
        ];

        foreach ($seedUsers as $u) {
            // 2. Buat atau Update User
            $user = User::query()->where('id', $u['id'])->first();

            if (!$user) {
                $user = User::create([
                    'id' => $u['id'],
                    'nama_lengkap' => $u['nama_lengkap'],
                    'email_institusi' => $u['email_institusi'],
                    'email_pribadi' => $u['email_pribadi'],
                    'is_admin' => (int) ($u['is_admin'] ?? 0),
                    'is_new' => 0,
                    'tipe_pegawai' => $u['tipe_pegawai'],
                    'telepon' => $u['telepon'] ?? null,
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'password' => Hash::make($u['password']),
                ]);
            } else {
                $user->update([
                    'nama_lengkap' => $u['nama_lengkap'],
                    'email_institusi' => $u['email_institusi'],
                    'email_pribadi' => $u['email_pribadi'],
                    'is_admin' => (int) ($u['is_admin'] ?? 0),
                    'tipe_pegawai' => $u['tipe_pegawai'],
                    'telepon' => $u['telepon'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'password' => Hash::make($u['password']),
                ]);
            }

            $makeRole = $u['make_role_models'] ?? [];

            // 3. Logika Pembuatan Model Dosen & Riwayat JFA
            if (($makeRole['dosen'] ?? false) === true) {
                $dosen = Dosen::query()->firstOrCreate(
                    ['users_id' => $user->id],
                    [
                        'nidn' => (($makeRole['has_complete_profile'] ?? false) || isset($makeRole['jfa_level'])) 
                                  ? fake()->unique()->numerify('##########') 
                                  : null,
                    ]
                );

                if ((($makeRole['has_complete_profile'] ?? false) || isset($makeRole['jfa_level'])) && !$dosen->nidn) {
                    $dosen->update(['nidn' => fake()->unique()->numerify('##########')]);
                }

                if ((($makeRole['has_complete_profile'] ?? false) || isset($makeRole['jfa_level'])) && !$user->nik) {
                    $user->update(['nik' => fake()->unique()->numerify('################')]);
                }

                // --- PROSES SEEDING RIWAYAT JFA ---
                $targetJfa = null;

                if (isset($makeRole['jfa_level'])) {
                    $targetJfa = $refJFA->first(function ($jfa) use ($makeRole) {
                        return str_contains(strtolower($jfa->nama_jabatan), strtolower($makeRole['jfa_level'])) 
                            || str_contains(strtolower($jfa->nama_jfa ?? ''), strtolower($makeRole['jfa_level']));
                    });
                } elseif (($makeRole['has_complete_profile'] ?? false) === true) {
                    $targetJfa = $refJFA->first(function ($jfa) {
                        return str_contains(strtolower($jfa->nama_jabatan), 'lektor') 
                            || str_contains(strtolower($jfa->nama_jfa ?? ''), 'lektor');
                    }) ?? $refJFA->first();
                }

                if ($targetJfa) {
                    $skLLDIKTI = SK::factory()->lldikti()->create([
                        'tipe_sk' => 'LLDIKTI',
                        'keterangan' => 'Penetapan JFA untuk keperluan simulasi DUPAK.',
                    ]);

                    $skYPT = SK::factory()->ypt()->create([
                        'keterangan' => 'Pengakuan internal SK YPT atas SK LLDIKTI.',
                    ]);

                    RiwayatJabatanFungsionalAkademik::query()->updateOrCreate(
                        ['dosen_id' => $dosen->id],
                        [
                            'ref_jfa_id' => $targetJfa->id,
                            'sk_llkdikti_id' => $skLLDIKTI->id,
                            'sk_pengakuan_ypt_id' => $skYPT->id,
                            'tmt_mulai' => now()->subYears(2),
                        ]
                    );
                }
            }

            // 4. Logika Pembuatan Model TPA
            if (($makeRole['tpa'] ?? false) === true) {
                Tpa::query()->firstOrCreate(['users_id' => $user->id], [
                    'nitk' => fake()->unique()->numerify('#############')
                ]);
            }
        }
    }
}