<?php

namespace Database\Seeders\Dupak;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class PengajuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // TRICK: Cari ID Dosen secara dinamis berdasarkan Nama atau User ID dari database tim
        // Gunakan koneksi default karena sdm_new.sql di-load ke sana di DatabaseSeeder
        // koneksi ke sdm_tus untuk mendapatkan id Dosen yang sudah di acak di dari database utama.
        $DB = DB::connection();

        // koneksi ke db dupak untuk input kedalam pengajuan
        $DBDupak = DB::connection('dupak'); // Koneksi ke database dupak

        // Cek kolom nama yang tersedia di tabel users (name atau nama)
        // Ini untuk mengatasi error "Unknown column users.name"
        $columnName = Schema::hasColumn('users', 'name') ? 'name' : 'nama_lengkap';

        $idSiti = $DB->table('dosens')
            ->join('users', 'dosens.users_id', '=', 'users.id')
            // Gunakan Siti Nurhaliza sesuai data di sdm_new.sql
            ->where("users.$columnName", 'like', '%Siti Nurhaliza%')
            ->value('dosens.id');

        $idBudi = $DB->table('dosens')
            ->join('users', 'dosens.users_id', '=', 'users.id')
            ->where("users.$columnName", 'like', '%Budi Santoso%')
            ->value('dosens.id');

        // Jika pencarian gagal, kita ambil 2 dosen pertama sebagai fallback agar seeder tidak macet
        if (!$idSiti || !$idBudi) {
            $fallbackIds = $DB->table('dosens')->limit(2)->pluck('id');
            $idSiti = $idSiti ?? ($fallbackIds[0] ?? null);
            $idBudi = $idBudi ?? ($fallbackIds[1] ?? null);
        }

        if (!$idSiti || !$idBudi) {
            return;
        }

        $DBDupak->table('pengajuan')->insert([
            [
                // Menggunakan ID hasil lookup dinamis
                'idDosen' => $idSiti,
                'start' => '2023-09-01',
                'end' => '2024-02-28',
                'TahunAjaranAjuanAwal' => '2023/2024',
                'TahunAjaranAjuanAkhir' => '2023/2024',
                'semesterAjuan' => 'Ganjil',
                'jfaAsal' => 'b467678d-8e9f-4453-bb76-f0cba91468dc', // Asisten Ahli
                'jfaTujuan' => 'f6890047-b0ea-4b45-a9f9-b0584c65bdd6', // Lektor
                'status' => 'Pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'idDosen' => $idBudi,
                'start' => '2024-03-01',
                'end' => '2024-08-31',
                'TahunAjaranAjuanAwal' => '2023/2024',
                'TahunAjaranAjuanAkhir' => '2023/2024',
                'semesterAjuan' => 'Genap',
                'jfaAsal' => 'f6890047-b0ea-4b45-a9f9-b0584c65bdd6', // Lektor
                'jfaTujuan' => '21ac00aa-1f19-4347-84c1-9e70413209ab', // Lektor Kepala
                'status' => 'Draft',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}

