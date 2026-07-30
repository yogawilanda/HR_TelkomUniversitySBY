<?php

namespace Database\Seeders\Dupak;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PengajuanSeeder extends Seeder
{
    public function run(): void
    {
        $DB = DB::connection();
        $DBDupak = DB::connection('dupak');

        $columnName = Schema::hasColumn('users', 'name') ? 'name' : 'nama_lengkap';

        // 1. Ambil JFA Dummy Valid (Pencegah error Integrity Constraint 1048 Column cannot be null)
        $defaultJfaId = $DB->table('ref_jabatan_fungsional_akademiks')->value('id') 
            ?? 'b467678d-8e9f-4453-bb76-f0cba91468dc'; // Fallback UUID jika DB kosong

        // 2. Pastikan Dosen SYSTEM / Dummy Khusus Tersedia
        $systemUser = $DB->table('users')->where("users.$columnName", 'SYSTEM_MASTER')->first();
        
        if (!$systemUser) {
            $userId = (string) Str::uuid();
            $dosenId = (string) Str::uuid();

            $DB->table('users')->insert([
                'id' => $userId,
                $columnName => 'SYSTEM_MASTER',
                'email' => 'system.master@dupak.local',
                'password' => bcrypt('system_password'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $DB->table('dosens')->insert([
                'id' => $dosenId,
                'users_id' => $userId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $idSystemDosen = $dosenId;
        } else {
            $idSystemDosen = $DB->table('dosens')->where('users_id', $systemUser->id)->value('id');
        }

        // 3. Sisipkan Record Master TPAK Mandiri (ID: 9999) menggunakan Dosen System & JFA Valid
        $DBDupak->table('pengajuan')->updateOrInsert(
            ['id' => 9999],
            [
                'idDosen'               => $idSystemDosen,
                'start'                 => '2020-01-01',
                'end'                   => '2099-12-31',
                'TahunAjaranAjuanAwal'  => 'MASTER',
                'TahunAjaranAjuanAkhir' => 'MASTER',
                'semesterAjuan'         => 'Ganjil',
                'jfaAsal'                => $defaultJfaId, // Terisi UUID valid agar tidak diprotes MySQL
                'jfaTujuan'              => $defaultJfaId, // Terisi UUID valid
                'status'                => 'MASTER_TPAK',
                'created_at'            => Carbon::now(),
                'updated_at'            => Carbon::now(),
            ]
        );

        // --- Logika Dosen Real Siti & Budi (untuk data testing biasa) ---
        $idSiti = $DB->table('dosens')
            ->join('users', 'dosens.users_id', '=', 'users.id')
            ->where("users.$columnName", 'like', '%Siti Nurhaliza%')
            ->value('dosens.id');

        $idBudi = $DB->table('dosens')
            ->join('users', 'dosens.users_id', '=', 'users.id')
            ->where("users.$columnName", 'like', '%Budi Santoso%')
            ->value('dosens.id');

        if (! $idSiti || ! $idBudi) {
            $fallbackIds = $DB->table('dosens')->where('id', '!=', $idSystemDosen)->limit(2)->pluck('id');
            $idSiti = $idSiti ?? ($fallbackIds[0] ?? null);
            $idBudi = $idBudi ?? ($fallbackIds[1] ?? null);
        }

        if ($idSiti && $idBudi) {
            $DBDupak->table('pengajuan')->insert([
                [
                    'idDosen'               => $idSiti,
                    'start'                 => '2023-09-01',
                    'end'                   => '2024-02-28',
                    'TahunAjaranAjuanAwal'  => '2023/2024',
                    'TahunAjaranAjuanAkhir' => '2023/2024',
                    'semesterAjuan'         => 'Ganjil',
                    'jfaAsal'                => 'b467678d-8e9f-4453-bb76-f0cba91468dc',
                    'jfaTujuan'              => 'f6890047-b0ea-4b45-a9f9-b0584c65bdd6',
                    'status'                => 'Pending',
                    'created_at'            => Carbon::now(),
                    'updated_at'            => Carbon::now(),
                ],
                [
                    'idDosen'               => $idBudi,
                    'start'                 => '2024-03-01',
                    'end'                   => '2024-08-31',
                    'TahunAjaranAjuanAwal'  => '2023/2024',
                    'TahunAjaranAjuanAkhir' => '2023/2024',
                    'semesterAjuan'         => 'Genap',
                    'jfaAsal'                => 'f6890047-b0ea-4b45-a9f9-b0584c65bdd6',
                    'jfaTujuan'              => '21ac00aa-1f19-4347-84c1-9e70413209ab',
                    'status'                => 'Draft',
                    'created_at'            => Carbon::now(),
                    'updated_at'            => Carbon::now(),
                ],
            ]);
        }
    }
}