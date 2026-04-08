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
        Schema::connection($this->connection)->create('pengajuan', function (Blueprint $table) {
            $table->id(); // Primary key for the table

            // Data fields from the Model's $fillable array
            $table->uuid('idDosen')->comment('Foreign key to the Dosen table (which uses UUIDs) in the main database');
            $table->date('start')->comment('Start date of the application period');
            $table->date('end')->comment('End date of the application period');

            // Academic year/semester details
            $table->string('TahunAjaranAjuanAwal', 9);
            $table->string('TahunAjaranAjuanAkhir', 9);
            $table->string('semesterAjuan', 10); // e.g., 'Ganjil', 'Genap'

            // Academic Functional Positions (JFA)
            // Assuming these are foreign keys to a reference table
            $table->uuid('jfaAsal')->comment('ID of the starting JFA');
            $table->uuid('jfaTujuan')->comment('ID of the target JFA');

            $table->string('status', 30)->default('Draft')->comment('Current status of the application');


            // Timestamps
            $table->timestamps();

            // karena tidak bisa langsung fk ke db sebelah, maka alternatifnya langsung lewat modelnya nanti.
            // $table->foreign('idDosen')->references('id')->on('dosens'); 
            // $table->foreign('jfaAsal')->references('id')->on('ref_jabatan_fungsional_akademik');
            // $table->foreign('jfaTujuan')->references('id')->on('ref_jabatan_fungsional_akademik');
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

        Schema::connection($this->connection)->dropIfExists('pengajuan');

        // Mengaktifkan kembali pengecekan kunci asing.
        Schema::connection($this->connection)->enableForeignKeyConstraints();
        // === END: Solusi Kunci Asing ===
    }
};
