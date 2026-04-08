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
        Schema::connection($this->connection)->create('penunjukan_tpak', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel pengajuan
            $table->unsignedBigInteger('pengajuan_id')->comment('Foreign key to the pengajuan table');
            
            // Dosen yang ditunjuk sebagai TPAK
            $table->uuid('idDosenTpak')->comment('Foreign key to the Dosen table in main DB who acts as TPAK');
            
            $table->string('bukti_penunjukan')->nullable()->comment('Path to assignment letter/document');
            $table->decimal('nilai_angka_kredit', 8, 2)->nullable()->comment('Credit score given by this TPAK');
            $table->text('catatan')->nullable()->comment('Notes from TPAK for this application');

            // Timestamps
            $table->timestamps();

            // foreign key connection ke pengajuan
            $table->foreign('pengajuan_id')->references('id')->on('pengajuan')->onDelete('cascade');
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

        Schema::connection($this->connection)->dropIfExists('penunjukan_tpak');

        // Mengaktifkan kembali pengecekan kunci asing.
        Schema::connection($this->connection)->enableForeignKeyConstraints();
        // === END: Solusi Kunci Asing ===
    }
};
