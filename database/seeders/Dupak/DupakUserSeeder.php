<?php

namespace Database\Seeders\Dupak;

use App\Models\Dosen;
use App\Models\Tpa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DupakUserSeeder extends Seeder
{
    public function run(): void
    {
        $seedUsers = [
            [
                'id' => 'dup-uid-admin-tpak-0001',
                'nama_lengkap' => 'Admin DUPAK (TPAK)',
                'email_institusi' => 'admin.tpak@telkomuniversity.ac.id',
                'email_pribadi' => 'admin.tpak.dup@local.test',
                'is_admin' => 1,
                'tipe_pegawai' => 'Tpa',
                'telepon' => '081234567891', // Tambahkan telepon unik
                'password' => '321',
                'make_role_models' => ['tpa' => true],
            ],
            [
                'id' => 'dup-uid-admin-0002',
                'nama_lengkap' => 'Admin DUPAK (SDM)',
                'email_institusi' => 'admin.sdm@telkomuniversity.ac.id',
                'email_pribadi' => 'admin.sdm.dup@local.test',
                'is_admin' => 1,
                'tipe_pegawai' => 'Tpa',
                'telepon' => '081234567892', // Tambahkan telepon unik
                'password' => '321',
                'make_role_models' => [],
            ],
            // Case : Dosen valid, namun belum memiliki data NIDN, JFA, dan NIK
            [
                'id' => 'dup-uid-dosen-0003',
                'nama_lengkap' => 'Dosen DUPAK',
                'email_institusi' => 'datadosenkosong@telkomuniversity.ac.id',
                'email_pribadi' => 'dosen.dupak@local.test',
                'is_admin' => 0,
                'tipe_pegawai' => 'Dosen',
                'telepon' => '081234567893', // Tambahkan telepon unik
                'password' => '321',
                'make_role_models' => ['dosen' => true],
            ],
            [
                'id' => 'dup-uid-tpa-0004',
                'nama_lengkap' => 'TPAK (non-admin)',
                'email_institusi' => 'testdupaktpa@telkomuniversity.ac.id',
                'email_pribadi' => 'tpa.dupak@local.test',
                'is_admin' => 0,
                'tipe_pegawai' => 'Tpa',
                'telepon' => '081234567894', // Tambahkan telepon unik
                'password' => '321',
                'make_role_models' => ['tpa' => true],
            ],
        ];

        foreach ($seedUsers as $u) {
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
                    'telepon' => $u['telepon'], // Bind ke insert
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
                    'telepon' => $u['telepon'], // Bind ke update
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'password' => Hash::make($u['password'] ?? '321'),
                ]);
            }

            $makeRole = $u['make_role_models'] ?? [];

            if (($makeRole['dosen'] ?? false) === true) {
                Dosen::query()->firstOrCreate(['users_id' => $user->id], []);
            }

            if (($makeRole['tpa'] ?? false) === true) {
                Tpa::query()->firstOrCreate(['users_id' => $user->id], []);
            }
        }
    }
}