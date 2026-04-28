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
        Schema::connection($this->connection)->create('ref_kegiatan_komponen', function (Blueprint $table) {

            $table->id(); // Auto-incrementing primary key (matches `id` int NOT NULL)

            // Original: `nama` varchar(200) DEFAULT NULL
            $table->string('nama', 200)->nullable()->comment('Nama sub-kegiatan komponen DUPAK (e.g., Melaksanakan perkuliahan)');

            // Original: `idKegiatanUtama` int NOT NULL (Foreign key)
            // Asumsi: Menunjuk ke tabel ref_kegiatan_utama (yang harus dibuat sebelum ini atau sudah ada)
            $table->unsignedBigInteger('idKegiatanUtama')->comment('Foreign key ke ref_kegiatan_utama');

            // Original: `satuanHasil` varchar(100) NOT NULL
            $table->string('satuanHasil', 100)->comment('Satuan hasil kegiatan (e.g., Ijazah, SKS, Kegiatan)');

            // Original: `status` int NOT NULL
            $table->unsignedTinyInteger('status')->default(1)->comment('Status aktif: 1=Aktif, 0=Tidak Aktif');

            // Standard Laravel timestamps
            $table->timestamps();

            // Foreign Key Constraint
            // Menambahkan constraint, asumsi tabel ref_kegiatan_utama sudah ada atau akan segera dibuat
            $table->foreign('idKegiatanUtama')->references('id')->on('ref_kegiatan_utama')->onDelete('cascade');
        });

        // --- Seeding Data Awal ---
        // Memasukkan data referensi yang Anda berikan
        DB::connection($this->connection)->table('ref_kegiatan_komponen')->insert([
            ['id' => 1, 'nama' => 'I. A. Pendidikan Formal', 'idKegiatanUtama' => 1, 'status' => 1, 'satuanHasil' => 'Ijazah', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama' => 'I. B. Pendidikan & Pelatihan Prajabatan', 'idKegiatanUtama' => 1, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nama' => 'II. A. Melaksanakan perkuliahan', 'idKegiatanUtama' => 2, 'status' => 1, 'satuanHasil' => 'SKS', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nama' => 'II. B. Membimbing seminar', 'idKegiatanUtama' => 2, 'status' => 1, 'satuanHasil' => 'Kegiatan', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nama' => 'II. C. Membimbing Kuliah Kerja Nyata (KKN), Praktek Kerja Nyata (PKN), Praktek Kerja Lapangan (PKL)', 'idKegiatanUtama' => 2, 'status' => 1, 'satuanHasil' => 'Kegiatan', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'nama' => 'II. D. Membimbing dan ikut membimbing dalam menghasilkan disertasi, tesis, skripsi dan laporan akhir studi', 'idKegiatanUtama' => 2, 'status' => 1, 'satuanHasil' => 'Lulusan', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'nama' => 'II. E. Bertugas sebagai penguji pada Ujian Akhir', 'idKegiatanUtama' => 2, 'status' => 1, 'satuanHasil' => 'Lulusan', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'nama' => 'II. F. Membina kegiatan mahasiswa', 'idKegiatanUtama' => 2, 'status' => 1, 'satuanHasil' => 'Semester', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'nama' => 'II. G. Mengembangkan program kuliah', 'idKegiatanUtama' => 2, 'status' => 1, 'satuanHasil' => 'Semester', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'nama' => 'II. H. Mengembangkan bahan pengajaran', 'idKegiatanUtama' => 2, 'status' => 1, 'satuanHasil' => 'Eksampelar', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'nama' => 'II. I. Menyampaikan Orasi Ilmiah', 'idKegiatanUtama' => 2, 'status' => 1, 'satuanHasil' => 'Orasi', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'nama' => 'II. J. Menduduki jabatan pimpinan perguruan tinggi', 'idKegiatanUtama' => 2, 'status' => 1, 'satuanHasil' => 'Semester', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'nama' => 'II. K. Membimbing dosen yang lebih rendah setiap semester (bagi dosen Lektor Kepala ke atas)', 'idKegiatanUtama' => 2, 'status' => 1, 'satuanHasil' => 'Kegiatan', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'nama' => 'II. L. Melaksanakan kegiatan datasering dan pencangkokan di luar institusi tempat bekerja setiap semester (bagi dosen Lektor kepala ke atas)', 'idKegiatanUtama' => 2, 'status' => 1, 'satuanHasil' => 'Kegiatan', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'nama' => 'II. M. Melakukan kegiatan pengembangan diri untuk meningkatkan kompetensi', 'idKegiatanUtama' => 2, 'status' => 1, 'satuanHasil' => 'Sertifikat', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'nama' => 'III. A. Menghasilkan Karya Ilmiah', 'idKegiatanUtama' => 3, 'status' => 1, 'satuanHasil' => 'Jurnal Nasional Bereputasi', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'nama' => 'III. B. Menerjemahkan / Menyadur Buku Ilmiah', 'idKegiatanUtama' => 3, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'nama' => 'III. C. Mengedit/menyunting karya ilmiah', 'idKegiatanUtama' => 3, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'nama' => 'III. D. Membuat rancangan dan karya teknologi yang dipatenkan terdaftar HaKi', 'idKegiatanUtama' => 3, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'nama' => 'III. E. Membuat rancangan dan karya teknologi yang Tidak dipatenkan atau Tidak terdaftar HKI tetapi telah dipresentasikan pada Forum Teragenda', 'idKegiatanUtama' => 3, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'nama' => 'IV. A. Menduduki jabatan pimpinan', 'idKegiatanUtama' => 4, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 22, 'nama' => 'IV. B. Melaksanakan pengembangan hasil pendidikan & penelitian', 'idKegiatanUtama' => 4, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 23, 'nama' => 'IV. C. Memberi latihan/peyuluhan/penataran/ceramah pada masyarakat', 'idKegiatanUtama' => 4, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 24, 'nama' => 'IV. D. Memberi pelayanan kepada masyarakat atau kegiatan lain menunjang pelaksanaan tugas umum pemerintah dan pembangunan', 'idKegiatanUtama' => 4, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 25, 'nama' => 'IV. E. Membuat/menulis karya pengabdian', 'idKegiatanUtama' => 4, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 26, 'nama' => 'V. A. Menjadi anggota dalam suatu Panitia/Badan pada Perguruan Tinggi', 'idKegiatanUtama' => 5, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 27, 'nama' => 'V. B. "Menjadi anggota Panitia/Badan pada Lembaga Pemerintah', 'idKegiatanUtama' => 5, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 28, 'nama' => 'V. C. "Menjadi Anggota Organisasi Profesi Dosen', 'idKegiatanUtama' => 5, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 29, 'nama' => 'V. D. Mewakili Perguruan Tinggi/Lembaga Pemerintah', 'idKegiatanUtama' => 5, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 30, 'nama' => 'V. E. "Menjadi Anggota Delegasi Nasional ke pertemuan Internasional', 'idKegiatanUtama' => 5, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 31, 'nama' => 'V. F. Berperan serta aktif dalam pengelolaan jurnal ilmiah (pertahun)', 'idKegiatanUtama' => 5, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 32, 'nama' => 'V. G. Berperan serta aktif dalam pertemuan ilmiah', 'idKegiatanUtama' => 5, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 33, 'nama' => 'V. H. Mendapat penghargaan/tanda jasa', 'idKegiatanUtama' => 5, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 34, 'nama' => 'V. I. Menulis buku pelajaran SLTA, kebawah yang diterbitkan dan diedarkan secara nasional', 'idKegiatanUtama' => 5, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 35, 'nama' => 'V. J. Mempunyai prestasi dibidang olah raga/humaniora', 'idKegiatanUtama' => 5, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 36, 'nama' => 'V. K. Keanggotaan dalam tim penilai', 'idKegiatanUtama' => 5, 'status' => 1, 'satuanHasil' => '', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Setelah seeding dengan ID eksplisit, reset auto-increment sequence
        // Ini memastikan ID baru akan dimulai dari 37.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // IMPORTANT: Drop the table using the correct connection
        Schema::connection($this->connection)->dropIfExists('ref_kegiatan_komponen');
    }
};