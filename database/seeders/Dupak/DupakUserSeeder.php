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
            // 1. Admin DUPAK sekaligus TPA
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

            // 2. Admin SDM / Kepegawaian
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

            // 3. Case Negatif: Dosen Baru (Tanpa NIDN, NIK, JFA)
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
                    // Kosongkan flag JFA biar memicu alert "Profile Incomplete"
                ],
            ],

            // 4. Case: TPA non-admin
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

            // 5. Case Positif: Dosen dengan berkas JFA lengkap (Asisten Ahli / Lektor)
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
                    'has_complete_profile' => true, // Menandakan dia butuh JFA Lektor/Asisten Ahli
                ],
            ],

            // 6. Case Limitatif: Dosen Guru Besar (JFA Tertinggi)
            [
                'id' => 'dup-uid-dosen-0006',
                'nama_lengkap' => 'Prof. Dr. Dosen Senior',
                'email_institusi' => 'dosen.gurubesar@telkomuniversity.ac.id',
                'email_pribadi' => 'dosen.gurubesar@local.test',
                'is_admin' => 0,
                'tipe_pegawai' => 'Dosen',
                'telepon' => '081234567896',
                'password' => '321',
                'make_role_models' => [
                    'dosen' => true,
                    'jfa_level' => 'Guru Besar', // Menandakan wajib dapat JFA Guru Besar
                ],
            ],

            // 7. Case Kompleks: Dosen Aktif + TPAK
            [
                'id' => 'dup-uid-dosen-tpak-0007',
                'nama_lengkap' => 'Dr. Dosen Penilai TPAK',
                'email_institusi' => 'dosen.tpak@telkomuniversity.ac.id',
                'email_pribadi' => 'dosen.tpak@local.test',
                'is_admin' => 0,
                'tipe_pegawai' => 'Dosen',
                'telepon' => '081234567897',
                'password' => '321',
                'make_role_models' => [
                    'dosen' => true,
                    'has_complete_profile' => true, // Kasih JFA aktif biar bisa dinilai & menilai
                    'tpak' => true, 
                ],
            ],
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
                    'password' => Hash::make($u['password'] ?? '321'),
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
                    'password' => Hash::make($u['password'] ?? '321'),
                ]);
            }

            $makeRole = $u['make_role_models'] ?? [];

            // 3. Logika Pembuatan Model Dosen & Riwayat JFA
            if (($makeRole['dosen'] ?? false) === true) {
                // Buat data dosen dasar (untuk memicu NIDN)
                $dosen = Dosen::query()->firstOrCreate(
                    ['users_id' => $user->id],
                    [
                        'nidn' => (($makeRole['has_complete_profile'] ?? false) || isset($makeRole['jfa_level'])) 
                                  ? fake()->unique()->numerify('##########') // Hanya isi NIDN jika profil diset lengkap
                                  : null,
                    ]
                );

                // Update NIDN jika ternyata sebelumnya null tapi di seeder sekarang harus lengkap
                if ((($makeRole['has_complete_profile'] ?? false) || isset($makeRole['jfa_level'])) && !$dosen->nidn) {
                    $dosen->update(['nidn' => fake()->unique()->numerify('##########')]);
                }

                // Tambahkan NIK pada User jika profil lengkap
                if ((($makeRole['has_complete_profile'] ?? false) || isset($makeRole['jfa_level'])) && !$user->nik) {
                    $user->update(['nik' => fake()->unique()->numerify('################')]);
                }

                // --- PROSES SEEDING RIWAYAT JFA ---
                $targetJfa = null;

                if (isset($makeRole['jfa_level'])) {
                    // Cari JFA Guru Besar sesuai flag
                    $targetJfa = $refJFA->first(function ($jfa) use ($makeRole) {
                        return str_contains(strtolower($jfa->nama_jabatan), strtolower($makeRole['jfa_level'])) 
                            || str_contains(strtolower($jfa->nama_jfa ?? ''), strtolower($makeRole['jfa_level']));
                    });
                } elseif (($makeRole['has_complete_profile'] ?? false) === true) {
                    // Cari JFA Lektor atau Asisten Ahli untuk dosen valid biasa
                    $targetJfa = $refJFA->first(function ($jfa) {
                        return str_contains(strtolower($jfa->nama_jabatan), 'lektor') 
                            || str_contains(strtolower($jfa->nama_jfa ?? ''), 'lektor');
                    }) ?? $refJFA->first(); // fallback ke data JFA pertama jika tidak ada text "lektor"
                }

                // Jika target JFA ditemukan, buatkan riwayatnya
                if ($targetJfa) {
                    // Buat SK penunjang (Meniru format SK tim)
                    $skLLDIKTI = SK::factory()->lldikti()->create([
                        'tipe_sk' => 'LLDIKTI',
                        'keterangan' => 'Penetapan JFA untuk keperluan simulasi DUPAK.',
                    ]);

                    $skYPT = SK::factory()->ypt()->create([
                        'keterangan' => 'Pengakuan internal SK YPT atas SK LLDIKTI.',
                    ]);

                    // Gunakan updateOrCreate agar data riwayat tidak terduplikasi saat re-seed
                    RiwayatJabatanFungsionalAkademik::query()->updateOrCreate(
                        ['dosen_id' => $dosen->id],
                        [
                            'ref_jfa_id' => $targetJfa->id,
                            'sk_llkdikti_id' => $skLLDIKTI->id,
                            'sk_pengakuan_ypt_id' => $skYPT->id,
                            'tmt_mulai' => now()->subYears(2), // diset 2 tahun lalu agar realistis untuk naik pangkat
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