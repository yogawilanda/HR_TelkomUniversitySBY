<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::connection($this->connection)->create('hasil_evaluasi', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('detail_pengajuan_id')->comment('Foreign key to the detail_pengajuan table');
            
            // Pemeriksa (TPAK atau Admin) | tidak perlu relasi ke table dosen karena ada di db yang berbeda, cukup muat/simpan UUID-nya saja.
            $table->uuid('idUserPemeriksa')->comment('UUID of the evaluator (TPAK or Admin)');
            $table->enum('peran_pemeriksa', ['TPAK', 'Admin'])->comment('Role of the evaluator');

            // Hasil Penilaian
            $table->string('status_evaluasi', 20)->default('Verified')->comment('Status: Verified, Rejected, Revision Needed');
            $table->string('bukti_penunjukan')->nullable()->comment('Path to assignment letter/document');
            $table->decimal('nilai_angka_kredit', 8, 2)->nullable()->comment('Credit score given by this TPAK');
            $table->text('catatan')->nullable()->comment('Notes from TPAK for this application');

            // Timestamps
            $table->timestamps();

            // foreign key connection ke pengajuan
            $table->foreign('detail_pengajuan_id')->references('id')->on('detail_pengajuan')->onDelete('cascade');
        });
    }

    // karena tidak bisa langsung fk ke db sebelah, maka alternatifnya langsung lewat modelnya nanti.
            // $table->foreign('idDosen')->references('id')->on('dosens'); 
            // $table->foreign('jfaAsal')->references('id')->on('ref_jabatan_fungsional_akademik');
            // $table->foreign('jfaTujuan')->references('id')->on('ref_jabatan_fungsional_akademik');

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // === START: Solusi Kunci Asing untuk migrate:fresh ===
        // Menonaktifkan pengecekan kunci asing sementara untuk koneksi 'dupak' 
        // agar penghapusan tabel bisa dilakukan tanpa terhalang relasi.
        Schema::connection($this->connection)->disableForeignKeyConstraints();

        Schema::connection($this->connection)->dropIfExists('hasil_evaluasi');

        // Mengaktifkan kembali pengecekan kunci asing.
        Schema::connection($this->connection)->enableForeignKeyConstraints();
        // === END: Solusi Kunci Asing ===
    }
};
