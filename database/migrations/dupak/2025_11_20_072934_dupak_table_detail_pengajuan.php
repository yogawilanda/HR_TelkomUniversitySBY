<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'dupak';

    public function up(): void
    {
        Schema::connection($this->connection)->create('detail_pengajuan', function (Blueprint $table) {
            $table->id();

            // Relasi ke header pengajuan
            $table->unsignedBigInteger('pengajuan_id');

            // Relasi ke referensi kegiatan
            $table->unsignedBigInteger('idKomponen')->nullable()->comment('FK ke ref_kegiatan_komponen');
            $table->unsignedSmallInteger('idJenisInput')->nullable()->comment('FK ke ref_jenis_input (opsional)');

            // Data Inputan Dosen
            $table->text('deskripsi_kegiatan')->nullable()->comment('Deskripsi kegiatan yang dilakukan.');
            $table->decimal('volume', 8, 2)->default(0)->nullable()->comment('Jumlah SKS/Kegiatan/Bulan');
            $table->decimal('angka_kredit_murni', 8, 2)->nullable()->comment('Nilai KUM sebelum dikalikan volume');
            $table->decimal('angka_kredit_total', 8, 2)->nullable()->comment('Hasil akhir (volume * murni)');

            // status
            $table->string('status')->nullable();

            // Bukti Fisik
            $table->string('link_bukti_pendukung')->nullable()->comment('URL ke dokumen/drive sesuai catatan pengerjaan');

            // Periode Pengajuan
            $table->string('periode_pengajuan')->nullable();

            // Status Flagging (Sesuai catatan pengerjaan: flaging hanya dilakukan oleh admin)
            $table->boolean('is_verified')->nullable()->comment('Apakah detil pengajuan sudah diverifikasi oleh admin?');
            $table->text('catatan_pemeriksa')->nullable()->comment('Catatan revisi per poin kegiatan');

            $table->timestamps();

            // Foreign Keys
            $table->foreign('pengajuan_id')->references('id')->on('pengajuan')->onDelete('cascade');
            $table->foreign('idKomponen')->references('id')->on('ref_kegiatan_komponen');
            $table->foreign('idJenisInput')->references('id')->on('ref_jenis_input');
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('detail_pengajuan');
    }
};
