<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The database connection that should be used by the migration.
     */
    protected $connection = 'dupak';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection($this->connection)->create('ref_jenis_input', function (Blueprint $table) {

            // Primary Key
            // Menggunakan unsignedSmallInteger karena ID-nya relatif kecil
            $table->unsignedSmallInteger('id')->primary()->autoIncrement();

            // Foreign Key ke ref_kegiatan_komponen
            $table->unsignedBigInteger('idKomponen')->comment('Foreign key ke ref_kegiatan_komponen (sub-kegiatan)');
            $table->foreign('idKomponen')->references('id')->on('ref_kegiatan_komponen')->onDelete('cascade');

            // Data Input Jenis
            $table->string('nama', 100)->comment('Nama spesifik jenis input (e.g., Jurnal Internasional Bereputasi, Magister S2 linier)');

            // Nilai Angka Kredit / Bobot Baku
            // Menggunakan decimal(5, 3) untuk fleksibilitas AK, meskipun data awal Anda integer.
            $table->decimal('nilai_baku', 6, 3)->comment('Nilai Angka Kredit baku atau bobot yang ditetapkan');

            // Jenis Input (Klasifikasi untuk frontend/logic)
            $table->unsignedTinyInteger('jenisInput')->comment('Jenis klasifikasi input (e.g., 1: Publikasi, 2: Pendidikan)');

            // Timestamps
            $table->timestamps();
        });

        // --- Seeding Data Awal ---
        DB::connection($this->connection)->table('ref_jenis_input')->insert([
            // UTAMA 1: PENDIDIKAN
            // idKomponen 1: Pendidikan Formal
            ['id' => 101, 'idKomponen' => 1, 'nama' => 'Sarjana (S1)', 'nilai_baku' => 100.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 104, 'idKomponen' => 1, 'nama' => 'Magister (S2) linier', 'nilai_baku' => 50.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 105, 'idKomponen' => 1, 'nama' => 'Magister (S2) non linier', 'nilai_baku' => 15.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 106, 'idKomponen' => 1, 'nama' => 'Doktor (S3) linier', 'nilai_baku' => 50.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 107, 'idKomponen' => 1, 'nama' => 'Doktor (S3) non linier', 'nilai_baku' => 15.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],
            // idKomponen 2: Diklat Prajabatan
            ['id' => 201, 'idKomponen' => 2, 'nama' => 'Prajabatan Golongan III', 'nilai_baku' => 3.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],

            // UTAMA 2: PELAKSANAAN PENDIDIKAN
            // idKomponen 3: Melaksanakan Perkuliahan
            ['id' => 301, 'idKomponen' => 3, 'nama' => 'Melaksanakan perkuliahan (Per SKS)', 'nilai_baku' => 1.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],
            // idKomponen 4: Membimbing Seminar
            ['id' => 401, 'idKomponen' => 4, 'nama' => 'Membimbing Seminar Mahasiswa', 'nilai_baku' => 1.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],
            // idKomponen 6: Membimbing Tugas Akhir (Disertasi, Tesis, Skripsi)
            ['id' => 601, 'idKomponen' => 6, 'nama' => 'Bimbingan Disertasi', 'nilai_baku' => 12.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 602, 'idKomponen' => 6, 'nama' => 'Bimbingan Tesis', 'nilai_baku' => 8.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 603, 'idKomponen' => 6, 'nama' => 'Bimbingan Skripsi/Laporan Akhir', 'nilai_baku' => 5.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],

            // UTAMA 3: PENELITIAN
            // idKomponen 16: Menghasilkan Karya Ilmiah
            ['id' => 1601, 'idKomponen' => 16, 'nama' => 'Jurnal Internasional Bereputasi', 'nilai_baku' => 40.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 1602, 'idKomponen' => 16, 'nama' => 'Jurnal Nasional Terakreditasi', 'nilai_baku' => 25.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // UTAMA 5: PENUNJANG
            // idKomponen 26: Panitia/Badan PT
            ['id' => 2601, 'idKomponen' => 26, 'nama' => 'Menjadi Anggota Senat Universitas', 'nilai_baku' => 5.000, 'jenisInput' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('ref_jenis_input');
    }
};
