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

            // Audit trail: User ID dari admin yang melakukan penunjukan (DB utama sdm_tus)
            // Tidak dibuat foreign key karena tabel users berada di database lain.
            $table->uuid('created_by')->nullable()->comment('User ID dari admin yang menunjuk TPAK (DB utama)');

            // Timestamps
            $table->timestamps();

            // foreign key connection ke pengajuan
            $table->foreign('pengajuan_id')->references('id')->on('pengajuan')->onDelete('cascade');
        });
    }

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
