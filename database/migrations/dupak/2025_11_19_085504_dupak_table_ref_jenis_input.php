<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
            ['id' => 1101, 'idKomponen' => 1, 'nama' => 'Sarjana (S1)', 'nilai_baku' => 100.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 1104, 'idKomponen' => 1, 'nama' => 'Magister (S2) linier', 'nilai_baku' => 50.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 1105, 'idKomponen' => 1, 'nama' => 'Magister (S2) non linier', 'nilai_baku' => 15.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 1106, 'idKomponen' => 1, 'nama' => 'Doktor (S3) linier', 'nilai_baku' => 50.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 1107, 'idKomponen' => 1, 'nama' => 'Doktor (S3) non linier', 'nilai_baku' => 15.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],
            // idKomponen 2: Diklat Prajabatan
            ['id' => 1201, 'idKomponen' => 2, 'nama' => 'Prajabatan Golongan III', 'nilai_baku' => 3.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],

            // UTAMA 2: PELAKSANAAN PENDIDIKAN
            // idKomponen 3: Melaksanakan Perkuliahan
            ['id' => 2301, 'idKomponen' => 3, 'nama' => 'Melaksanakan perkuliahan (Per SKS)', 'nilai_baku' => 1.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],

            // idKomponen 4: Membimbing Seminar
            ['id' => 2401, 'idKomponen' => 4, 'nama' => 'Membimbing Seminar Mahasiswa', 'nilai_baku' => 1.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],

            // idKomponen 5:
            ['id' => 2501, 'idKomponen' => 5, 'nama' => 'Membimbing KKN/PKL/PKN/PKL per semester', 'nilai_baku' => 1.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],

            // idKomponen 6: Membimbing Tugas Akhir (Disertasi, Tesis, Skripsi)
            ['id' => 2601, 'idKomponen' => 6, 'nama' => 'Bimbingan Disertasi', 'nilai_baku' => 12.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2602, 'idKomponen' => 6, 'nama' => 'Bimbingan Tesis', 'nilai_baku' => 8.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2603, 'idKomponen' => 6, 'nama' => 'Bimbingan Skripsi/Laporan Akhir', 'nilai_baku' => 5.000, 'jenisInput' => 2, 'created_at' => now(), 'updated_at' => now()],

            // idKomponen 7: Bertugas sebagai penguji pada Ujian Akhir
            ['id' => 2721, 'idKomponen' => 7, 'nama' => 'Ketua Penguji - Bertugas di Ujian Akhir', 'nilai_baku' => 1.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2722, 'idKomponen' => 7, 'nama' => 'Anggota Penguji - Bertugas di Ujian Akhir', 'nilai_baku' => 0.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // idKomponen 8: Membina kegiatan mahasiswa (Limit 4x pengajuan Per Semester)
            ['id' => 2811, 'idKomponen' => 8, 'nama' => 'Membina kegiatan mahasiswa per semester', 'nilai_baku' => 2.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // idKomponen 9: Mengembangkan program kuliah (Limit 2x Per Semester)
            ['id' => 2911, 'idKomponen' => 9, 'nama' => 'Mengembangkan program kuliah per semester', 'nilai_baku' => 2.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // idKomponen 10: Mengembangkan bahan pengajaran (No Limit)
            ['id' => 2101, 'idKomponen' => 10, 'nama' => 'Buku Pertahun', 'nilai_baku' => 20.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2102, 'idKomponen' => 10, 'nama' => 'Buku Pertahun', 'nilai_baku' => 5.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // idKomponen 11: Menyampaikan Orasi Ilmiah (Limit 10 Per periode)
            ['id' => 2111, 'idKomponen' => 11, 'nama' => 'Buku Pertahun', 'nilai_baku' => 5.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // idKomponen 12: Menduduki jabatan pimpinan perguruan  (No Limit)
            ['id' => 2121, 'idKomponen' => 12, 'nama' => 'Rektor', 'nilai_baku' => 6.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2122, 'idKomponen' => 12, 'nama' => 'Pembantu Rektor / Dekan / Direktur Pascasarjana', 'nilai_baku' => 5.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2123, 'idKomponen' => 12, 'nama' => 'Ketua Sekolah Tinggi / Pembantu Dekan / Asisten Direktur Pasca / Direktur Politeknik', 'nilai_baku' => 4.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2124, 'idKomponen' => 12, 'nama' => 'Pembantu Ketua Sekolah Tinggi / Pembantu Direktur Politeknik', 'nilai_baku' => 4.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2125, 'idKomponen' => 12, 'nama' => 'Direktur Akademi', 'nilai_baku' => 4.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2126, 'idKomponen' => 12, 'nama' => 'Pembantu Direktur Akademi / Ketua Jurusan / Ketua Bagian (Univ/Inst/ST)', 'nilai_baku' => 3.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2127, 'idKomponen' => 12, 'nama' => 'Ketua Jurusan (Poltek/Akademi) / Sekretaris Jurusan (Univ/Inst/ST)', 'nilai_baku' => 3.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2128, 'idKomponen' => 12, 'nama' => 'Sekretaris Jurusan (Poltek/Akademi) / Kepala Laboratorium', 'nilai_baku' => 3.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // idKomponen 13: Membimbing Dosen yang Lebih Rendah Untuk lektor kepala dan jabatan selanjutnya saja (No Limit)
            ['id' => 2311, 'idKomponen' => 13, 'nama' => 'Jabatan Pimpinan (Rektor)', 'nilai_baku' => 6.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2312, 'idKomponen' => 13, 'nama' => 'Pembantu rektor/dekan/direktur pasca', 'nilai_baku' => 5.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2313, 'idKomponen' => 13, 'nama' => 'Ketua ST/Pd.Dekan/As.Dir Pasca/Dir Poltek', 'nilai_baku' => 4.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2314, 'idKomponen' => 13, 'nama' => 'Pembantu ketua ST/Pd.Dir Poltek', 'nilai_baku' => 4.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2315, 'idKomponen' => 13, 'nama' => 'Direktur akademi', 'nilai_baku' => 4.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2316, 'idKomponen' => 13, 'nama' => 'Pd.Dir Akad/Ketua Jurusan/Bagian Univ/Inst/ST', 'nilai_baku' => 3.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2317, 'idKomponen' => 13, 'nama' => 'Ketua Jur Poltek/Akad/Sekr Jur/Bag Univ/Inst/ST', 'nilai_baku' => 3.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2318, 'idKomponen' => 13, 'nama' => 'Sekr Jur Poltek/Akad/Kepala Lab', 'nilai_baku' => 3.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // idKomponen 14: Datasering dan pencangkokan di luar institusi tempat bekerja Untuk lektor kepala dan jabatan selanjutnya saja (No Limit)
            ['id' => 2411, 'idKomponen' => 14, 'nama' => 'Detasering Per Semester', 'nilai_baku' => 4.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2412, 'idKomponen' => 14, 'nama' => 'Pencangkokan Per Semester', 'nilai_baku' => 3.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // idKomponen 15: Pengembangan diri untuk meningkatkan potensi (No Limit)
            // 1 Lamanya > 960 jam = 15 AK
            ['id' => 2511, 'idKomponen' => 15, 'nama' => 'Lamanya > 960 jam', 'nilai_baku' => 15.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2512, 'idKomponen' => 15, 'nama' => 'Lamanya 641 - 960 jam', 'nilai_baku' => 9.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2513, 'idKomponen' => 15, 'nama' => 'Lamanya 481 - 640 jam', 'nilai_baku' => 6.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2514, 'idKomponen' => 15, 'nama' => 'Lamanya 161 - 480 jam', 'nilai_baku' => 3.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2515, 'idKomponen' => 15, 'nama' => 'Lamanya 81 - 160 jam', 'nilai_baku' => 2.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2516, 'idKomponen' => 15, 'nama' => 'Lamanya 31 - 80 jam', 'nilai_baku' => 1.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2517, 'idKomponen' => 15, 'nama' => 'Lamanya 10 - 30 jam', 'nilai_baku' => 0.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // UTAMA 3: PENELITIAN
            // idKomponen 16: Menghasilkan Karya Ilmiah
            ['id' => 3601, 'idKomponen' => 16, 'nama' => 'Jurnal Internasional Bereputasi', 'nilai_baku' => 40.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3602, 'idKomponen' => 16, 'nama' => 'Jurnal Nasional Terakreditasi', 'nilai_baku' => 25.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // idKomponen 17: Menerjemahkan/Menyadur Buku Ilmiah
            ['id' => 3701, 'idKomponen' => 17, 'nama' => '1 Penulis', 'nilai_baku' => 15.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3702, 'idKomponen' => 17, 'nama' => '2 Penulis (ke-1)', 'nilai_baku' => 7.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3703, 'idKomponen' => 17, 'nama' => '2 Penulis (ke-2)', 'nilai_baku' => 7.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3704, 'idKomponen' => 17, 'nama' => '3 Penulis (ke-1)', 'nilai_baku' => 4.995, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3705, 'idKomponen' => 17, 'nama' => '3 Penulis (ke-2)', 'nilai_baku' => 4.995, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3706, 'idKomponen' => 17, 'nama' => '3 Penulis (ke-3)', 'nilai_baku' => 4.995, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3707, 'idKomponen' => 17, 'nama' => '4 Penulis (ke-1)', 'nilai_baku' => 3.750, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3708, 'idKomponen' => 17, 'nama' => '4 Penulis (ke-2)', 'nilai_baku' => 3.750, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3709, 'idKomponen' => 17, 'nama' => '4 Penulis (ke-3)', 'nilai_baku' => 3.750, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3710, 'idKomponen' => 17, 'nama' => '4 Penulis (ke-4)', 'nilai_baku' => 3.750, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // idKomponen 18: Mengedit Menyunting Karya Ilmiah
            ['id' => 3801, 'idKomponen' => 18, 'nama' => '1 Penulis', 'nilai_baku' => 10.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3802, 'idKomponen' => 18, 'nama' => '2 Penulis (ke-1)', 'nilai_baku' => 5.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3803, 'idKomponen' => 18, 'nama' => '2 Penulis (ke-2)', 'nilai_baku' => 5.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3804, 'idKomponen' => 18, 'nama' => '3 Penulis (ke-1)', 'nilai_baku' => 3.330, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3805, 'idKomponen' => 18, 'nama' => '3 Penulis (ke-2)', 'nilai_baku' => 3.330, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3806, 'idKomponen' => 18, 'nama' => '3 Penulis (ke-3)', 'nilai_baku' => 3.330, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3807, 'idKomponen' => 18, 'nama' => '4 Penulis (ke-1)', 'nilai_baku' => 2.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3808, 'idKomponen' => 18, 'nama' => '4 Penulis (ke-2)', 'nilai_baku' => 2.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3809, 'idKomponen' => 18, 'nama' => '4 Penulis (ke-3)', 'nilai_baku' => 2.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3810, 'idKomponen' => 18, 'nama' => '4 Penulis (ke-4)', 'nilai_baku' => 2.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // idKomponen 19: Paten & HaKI
            // Kategori a: Internasional Industri (60 AK)
            ['id' => 3901, 'idKomponen' => 19, 'nama' => 'a. Internasional Industri (1 Penulis)', 'nilai_baku' => 60.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3902, 'idKomponen' => 19, 'nama' => 'a. Internasional Industri (2 Penulis ke-1)', 'nilai_baku' => 30.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3903, 'idKomponen' => 19, 'nama' => 'a. Internasional Industri (2 Penulis ke-2)', 'nilai_baku' => 30.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3904, 'idKomponen' => 19, 'nama' => 'a. Internasional Industri (3 Penulis ke-1)', 'nilai_baku' => 19.980, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3905, 'idKomponen' => 19, 'nama' => 'a. Internasional Industri (3 Penulis ke-2)', 'nilai_baku' => 19.980, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3906, 'idKomponen' => 19, 'nama' => 'a. Internasional Industri (3 Penulis ke-3)', 'nilai_baku' => 19.980, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3907, 'idKomponen' => 19, 'nama' => 'a. Internasional Industri (4 Penulis ke-1)', 'nilai_baku' => 15.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3908, 'idKomponen' => 19, 'nama' => 'a. Internasional Industri (4 Penulis ke-2)', 'nilai_baku' => 15.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3909, 'idKomponen' => 19, 'nama' => 'a. Internasional Industri (4 Penulis ke-3)', 'nilai_baku' => 15.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3910, 'idKomponen' => 19, 'nama' => 'a. Internasional Industri (4 Penulis ke-4)', 'nilai_baku' => 15.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Kategori b: Internasional (50 AK)
            ['id' => 3911, 'idKomponen' => 19, 'nama' => 'b. Internasional (1 Penulis)', 'nilai_baku' => 50.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3912, 'idKomponen' => 19, 'nama' => 'b. Internasional (2 Penulis ke-1)', 'nilai_baku' => 25.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3913, 'idKomponen' => 19, 'nama' => 'b. Internasional (2 Penulis ke-2)', 'nilai_baku' => 25.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3914, 'idKomponen' => 19, 'nama' => 'b. Internasional (3 Penulis ke-1)', 'nilai_baku' => 16.650, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3915, 'idKomponen' => 19, 'nama' => 'b. Internasional (3 Penulis ke-2)', 'nilai_baku' => 16.650, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3916, 'idKomponen' => 19, 'nama' => 'b. Internasional (3 Penulis ke-3)', 'nilai_baku' => 16.650, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3917, 'idKomponen' => 19, 'nama' => 'b. Internasional (4 Penulis ke-1)', 'nilai_baku' => 12.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3918, 'idKomponen' => 19, 'nama' => 'b. Internasional (4 Penulis ke-2)', 'nilai_baku' => 12.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3919, 'idKomponen' => 19, 'nama' => 'b. Internasional (4 Penulis ke-3)', 'nilai_baku' => 12.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3920, 'idKomponen' => 19, 'nama' => 'b. Internasional (4 Penulis ke-4)', 'nilai_baku' => 12.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Kategori c: Nasional Industri (40 AK)
            ['id' => 3921, 'idKomponen' => 19, 'nama' => 'c. Nasional Industri (1 Penulis)', 'nilai_baku' => 40.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3922, 'idKomponen' => 19, 'nama' => 'c. Nasional Industri (2 Penulis ke-1)', 'nilai_baku' => 20.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3923, 'idKomponen' => 19, 'nama' => 'c. Nasional Industri (2 Penulis ke-2)', 'nilai_baku' => 20.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3924, 'idKomponen' => 19, 'nama' => 'c. Nasional Industri (3 Penulis ke-1)', 'nilai_baku' => 13.320, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3925, 'idKomponen' => 19, 'nama' => 'c. Nasional Industri (3 Penulis ke-2)', 'nilai_baku' => 13.320, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3926, 'idKomponen' => 19, 'nama' => 'c. Nasional Industri (3 Penulis ke-3)', 'nilai_baku' => 13.320, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3927, 'idKomponen' => 19, 'nama' => 'c. Nasional Industri (4 Penulis ke-1)', 'nilai_baku' => 10.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3928, 'idKomponen' => 19, 'nama' => 'c. Nasional Industri (4 Penulis ke-2)', 'nilai_baku' => 10.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3929, 'idKomponen' => 19, 'nama' => 'c. Nasional Industri (4 Penulis ke-3)', 'nilai_baku' => 10.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3930, 'idKomponen' => 19, 'nama' => 'c. Nasional Industri (4 Penulis ke-4)', 'nilai_baku' => 10.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Kategori d: Nasional (30 AK)
            ['id' => 3931, 'idKomponen' => 19, 'nama' => 'd. Nasional (1 Penulis)', 'nilai_baku' => 30.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3932, 'idKomponen' => 19, 'nama' => 'd. Nasional (2 Penulis ke-1)', 'nilai_baku' => 15.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3933, 'idKomponen' => 19, 'nama' => 'd. Nasional (2 Penulis ke-2)', 'nilai_baku' => 15.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3934, 'idKomponen' => 19, 'nama' => 'd. Nasional (3 Penulis ke-1)', 'nilai_baku' => 9.990, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3935, 'idKomponen' => 19, 'nama' => 'd. Nasional (3 Penulis ke-2)', 'nilai_baku' => 9.990, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3936, 'idKomponen' => 19, 'nama' => 'd. Nasional (3 Penulis ke-3)', 'nilai_baku' => 9.990, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3937, 'idKomponen' => 19, 'nama' => 'd. Nasional (4 Penulis ke-1)', 'nilai_baku' => 7.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3938, 'idKomponen' => 19, 'nama' => 'd. Nasional (4 Penulis ke-2)', 'nilai_baku' => 7.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3939, 'idKomponen' => 19, 'nama' => 'd. Nasional (4 Penulis ke-3)', 'nilai_baku' => 7.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3940, 'idKomponen' => 19, 'nama' => 'd. Nasional (4 Penulis ke-4)', 'nilai_baku' => 7.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Kategori e: Paten Sederhana (20 AK)
            ['id' => 3941, 'idKomponen' => 19, 'nama' => 'e. Paten Sederhana (1 Penulis)', 'nilai_baku' => 20.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3942, 'idKomponen' => 19, 'nama' => 'e. Paten Sederhana (2 Penulis ke-1)', 'nilai_baku' => 10.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3943, 'idKomponen' => 19, 'nama' => 'e. Paten Sederhana (2 Penulis ke-2)', 'nilai_baku' => 10.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3944, 'idKomponen' => 19, 'nama' => 'e. Paten Sederhana (3 Penulis ke-1)', 'nilai_baku' => 6.660, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3945, 'idKomponen' => 19, 'nama' => 'e. Paten Sederhana (3 Penulis ke-2)', 'nilai_baku' => 6.660, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3946, 'idKomponen' => 19, 'nama' => 'e. Paten Sederhana (3 Penulis ke-3)', 'nilai_baku' => 6.660, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3947, 'idKomponen' => 19, 'nama' => 'e. Paten Sederhana (4 Penulis ke-1)', 'nilai_baku' => 5.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3948, 'idKomponen' => 19, 'nama' => 'e. Paten Sederhana (4 Penulis ke-2)', 'nilai_baku' => 5.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3949, 'idKomponen' => 19, 'nama' => 'e. Paten Sederhana (4 Penulis ke-3)', 'nilai_baku' => 5.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3950, 'idKomponen' => 19, 'nama' => 'e. Paten Sederhana (4 Penulis ke-4)', 'nilai_baku' => 5.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Kategori f: Karya Ciptaan (15 AK)
            ['id' => 3951, 'idKomponen' => 19, 'nama' => 'f. Karya Ciptaan (1 Penulis)', 'nilai_baku' => 15.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3952, 'idKomponen' => 19, 'nama' => 'f. Karya Ciptaan (2 Penulis ke-1)', 'nilai_baku' => 7.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3953, 'idKomponen' => 19, 'nama' => 'f. Karya Ciptaan (2 Penulis ke-2)', 'nilai_baku' => 7.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3954, 'idKomponen' => 19, 'nama' => 'f. Karya Ciptaan (3 Penulis ke-1)', 'nilai_baku' => 4.995, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3955, 'idKomponen' => 19, 'nama' => 'f. Karya Ciptaan (3 Penulis ke-2)', 'nilai_baku' => 4.995, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3956, 'idKomponen' => 19, 'nama' => 'f. Karya Ciptaan (3 Penulis ke-3)', 'nilai_baku' => 4.995, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3957, 'idKomponen' => 19, 'nama' => 'f. Karya Ciptaan (4 Penulis ke-1)', 'nilai_baku' => 3.750, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3958, 'idKomponen' => 19, 'nama' => 'f. Karya Ciptaan (4 Penulis ke-2)', 'nilai_baku' => 3.750, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3959, 'idKomponen' => 19, 'nama' => 'f. Karya Ciptaan (4 Penulis ke-3)', 'nilai_baku' => 3.750, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3960, 'idKomponen' => 19, 'nama' => 'f. Karya Ciptaan (4 Penulis ke-4)', 'nilai_baku' => 3.750, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // idKomponen 20: Membuat rancangan dan karya teknologi yang Tidak dipatenkan atau Tidak terdaftar HKI tetapi telah dipresentasikan pada Forum Teragenda
            // idKomponen 20: Karya Teknologi Tidak Dipatenkan (Forum Teragenda)
            // Kategori a: Internasional (20 AK)
            ['id' => 4001, 'idKomponen' => 20, 'nama' => 'a. Internasional (1 Penulis)', 'nilai_baku' => 20.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4002, 'idKomponen' => 20, 'nama' => 'a. Internasional (2 Penulis ke-1)', 'nilai_baku' => 10.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4003, 'idKomponen' => 20, 'nama' => 'a. Internasional (2 Penulis ke-2)', 'nilai_baku' => 10.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4004, 'idKomponen' => 20, 'nama' => 'a. Internasional (3 Penulis ke-1)', 'nilai_baku' => 6.660, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4005, 'idKomponen' => 20, 'nama' => 'a. Internasional (3 Penulis ke-2)', 'nilai_baku' => 6.660, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4006, 'idKomponen' => 20, 'nama' => 'a. Internasional (3 Penulis ke-3)', 'nilai_baku' => 6.660, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4007, 'idKomponen' => 20, 'nama' => 'a. Internasional (4 Penulis ke-1)', 'nilai_baku' => 5.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4008, 'idKomponen' => 20, 'nama' => 'a. Internasional (4 Penulis ke-2)', 'nilai_baku' => 5.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4009, 'idKomponen' => 20, 'nama' => 'a. Internasional (4 Penulis ke-3)', 'nilai_baku' => 5.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4010, 'idKomponen' => 20, 'nama' => 'a. Internasional (4 Penulis ke-4)', 'nilai_baku' => 5.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Kategori b: Nasional (15 AK)
            ['id' => 4011, 'idKomponen' => 20, 'nama' => 'b. Nasional (1 Penulis)', 'nilai_baku' => 15.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4012, 'idKomponen' => 20, 'nama' => 'b. Nasional (2 Penulis ke-1)', 'nilai_baku' => 7.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4013, 'idKomponen' => 20, 'nama' => 'b. Nasional (2 Penulis ke-2)', 'nilai_baku' => 7.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4014, 'idKomponen' => 20, 'nama' => 'b. Nasional (3 Penulis ke-1)', 'nilai_baku' => 4.995, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4015, 'idKomponen' => 20, 'nama' => 'b. Nasional (3 Penulis ke-2)', 'nilai_baku' => 4.995, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4016, 'idKomponen' => 20, 'nama' => 'b. Nasional (3 Penulis ke-3)', 'nilai_baku' => 4.995, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4017, 'idKomponen' => 20, 'nama' => 'b. Nasional (4 Penulis ke-1)', 'nilai_baku' => 3.750, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4018, 'idKomponen' => 20, 'nama' => 'b. Nasional (4 Penulis ke-2)', 'nilai_baku' => 3.750, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4019, 'idKomponen' => 20, 'nama' => 'b. Nasional (4 Penulis ke-3)', 'nilai_baku' => 3.750, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4020, 'idKomponen' => 20, 'nama' => 'b. Nasional (4 Penulis ke-4)', 'nilai_baku' => 3.750, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Kategori c: Lokal (10 AK)
            ['id' => 4821, 'idKomponen' => 20, 'nama' => 'c. Lokal (1 Penulis)', 'nilai_baku' => 10.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4822, 'idKomponen' => 20, 'nama' => 'c. Lokal (2 Penulis ke-1)', 'nilai_baku' => 5.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4823, 'idKomponen' => 20, 'nama' => 'c. Lokal (2 Penulis ke-2)', 'nilai_baku' => 5.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4824, 'idKomponen' => 20, 'nama' => 'c. Lokal (3 Penulis ke-1)', 'nilai_baku' => 3.330, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4825, 'idKomponen' => 20, 'nama' => 'c. Lokal (3 Penulis ke-2)', 'nilai_baku' => 3.330, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4826, 'idKomponen' => 20, 'nama' => 'c. Lokal (3 Penulis ke-3)', 'nilai_baku' => 3.330, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4827, 'idKomponen' => 20, 'nama' => 'c. Lokal (4 Penulis ke-1)', 'nilai_baku' => 2.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4828, 'idKomponen' => 20, 'nama' => 'c. Lokal (4 Penulis ke-2)', 'nilai_baku' => 2.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4829, 'idKomponen' => 20, 'nama' => 'c. Lokal (4 Penulis ke-3)', 'nilai_baku' => 2.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4830, 'idKomponen' => 20, 'nama' => 'c. Lokal (4 Penulis ke-4)', 'nilai_baku' => 2.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // idKomponen 21: Membuat rancangan dan karya Seni / Seni Pertunjukan yang tidak terdaftar di HaKI Angka Kreditnya
            // WARNING: Konflik ID dengan 21 Menduduki Jabatan, maka sementara saya jadikan idnya 99 biar ngga chain error/bug
            // idKomponen 99: Karya Seni Monumental / Pertunjukan (Temporary ID)

            // Kategori a: Internasional (20 AK)
            ['id' => 9901, 'idKomponen' => 99, 'nama' => 'a. Internasional (1 Penulis)', 'nilai_baku' => 20.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9902, 'idKomponen' => 99, 'nama' => 'a. Internasional (2 Penulis)', 'nilai_baku' => 10.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9903, 'idKomponen' => 99, 'nama' => 'a. Internasional (3 Penulis)', 'nilai_baku' => 6.660, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9904, 'idKomponen' => 99, 'nama' => 'a. Internasional (4 Penulis)', 'nilai_baku' => 5.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Kategori b: Nasional (15 AK)
            ['id' => 9911, 'idKomponen' => 99, 'nama' => 'b. Nasional (1 Penulis)', 'nilai_baku' => 15.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9912, 'idKomponen' => 99, 'nama' => 'b. Nasional (2 Penulis)', 'nilai_baku' => 7.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9913, 'idKomponen' => 99, 'nama' => 'b. Nasional (3 Penulis)', 'nilai_baku' => 4.995, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9914, 'idKomponen' => 99, 'nama' => 'b. Nasional (4 Penulis)', 'nilai_baku' => 3.750, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Kategori c: Lokal (10 AK)
            ['id' => 9921, 'idKomponen' => 99, 'nama' => 'c. Lokal (1 Penulis)', 'nilai_baku' => 10.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9922, 'idKomponen' => 99, 'nama' => 'c. Lokal (2 Penulis)', 'nilai_baku' => 5.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9923, 'idKomponen' => 99, 'nama' => 'c. Lokal (3 Penulis)', 'nilai_baku' => 3.330, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9924, 'idKomponen' => 99, 'nama' => 'c. Lokal (4 Penulis)', 'nilai_baku' => 2.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // UTAMA 4: PENGABDIAN
            // idKomponen 21: Menduduki jabatan pimpinan
            ['id' => 4021, 'idKomponen' => 21, 'nama' => 'Menduduki jabatan pimpinan', 'nilai_baku' => 5.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            // idKomponen 22: Melaksanakan pengembangan hasil pendidikan & penelitian
            ['id' => 4022, 'idKomponen' => 22, 'nama' => 'Melaksanakan pengembangan hasil pendidikan & penelitian', 'nilai_baku' => 3.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            // idKomponen 23: Melaksanakan pengembangan hasil pendidikan & penelitian
            // idKomponen 23 dipecah menjadi 7 record sesuai aturan sistematis (4231 - 4237)
            ['id' => 4231, 'idKomponen' => 23, 'nama' => 'Memberi latihan/penyuluhan (Terjadwal >= 1 Sem - Internasional)', 'nilai_baku' => 4.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4232, 'idKomponen' => 23, 'nama' => 'Memberi latihan/penyuluhan (Terjadwal >= 1 Sem - Nasional)', 'nilai_baku' => 3.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4233, 'idKomponen' => 23, 'nama' => 'Memberi latihan/penyuluhan (Terjadwal >= 1 Sem - Lokal)', 'nilai_baku' => 2.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4234, 'idKomponen' => 23, 'nama' => 'Memberi latihan/penyuluhan (Terjadwal 1 Bln-1 Sem - Internasional)', 'nilai_baku' => 3.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4235, 'idKomponen' => 23, 'nama' => 'Memberi latihan/penyuluhan (Terjadwal 1 Bln-1 Sem - Nasional)', 'nilai_baku' => 2.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4236, 'idKomponen' => 23, 'nama' => 'Memberi latihan/penyuluhan (Terjadwal 1 Bln-1 Sem - Lokal)', 'nilai_baku' => 1.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4237, 'idKomponen' => 23, 'nama' => 'Memberi latihan/penyuluhan (Insidental)', 'nilai_baku' => 1.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // idKomponen 24: Memberi pelayanan kepada masyarakat atau kegiatan lain menunjang pelaksanaan tugas umum pemerintah dan pembangunan
            ['id' => 4441, 'idKomponen' => 24, 'nama' => 'Pelayanan Masyarakat (Berdasarkan bidang keahlian)', 'nilai_baku' => 1.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4442, 'idKomponen' => 24, 'nama' => 'Pelayanan Masyarakat (Berdasarkan penugasan lembaga PT)', 'nilai_baku' => 1.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4443, 'idKomponen' => 24, 'nama' => 'Pelayanan Masyarakat (Berdasarkan fungsi dan jabatan)', 'nilai_baku' => 0.500, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // idKomponen 25
            ['id' => 4501, 'idKomponen' => 25, 'nama' => 'Membuat/menulis karya pengabdian', 'nilai_baku' => 3.000, 'jenisInput' => 1, 'created_at' => now(), 'updated_at' => now()],

            // UTAMA 5: PENUNJANG
            // idKomponen 26: Panitia/Badan PT
            ['id' => 5101, 'idKomponen' => 26, 'nama' => 'Menjadi Anggota Senat Universitas', 'nilai_baku' => 5.000, 'jenisInput' => 4, 'created_at' => now(), 'updated_at' => now()],
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
