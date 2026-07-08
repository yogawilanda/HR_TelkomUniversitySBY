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
        Schema::connection($this->connection)->create('ref_target_jabatan_pengajuan', function (Blueprint $table) {
            $table->id();

            // Kolom sesuai Model & SQL
            // Menggunakan unsignedTinyInteger agar cocok dengan id di ref_jabatan_fungsional_akademik
            $table->uuid('jfaAsal')->comment('ID Jabatan Asal dari pengaju melalui riwayat_jabatan_fungsional_akademik yang ada di database sdm_tus');
            $table->uuid('jfaTujuan')->comment('ID Jabatan Tujuan dari pengaju melalui riwayat_jabatan_fungsional_akademik yang ada di database sdm_tus');

            // Di SQL namanya Saya pakai kumTarget sesuai data SQL.
            $table->integer('kumTarget')->comment('Target KUM yang harus dicapai (selisih/total)');

            // update permintaan 6/17/2026: penambahan lampiran guna membuat standard penilaian untuk pengaju.
            $table->decimal('limit_lampiran_1')->default('100')->nullable();
            $table->decimal('limit_lampiran_2')->default('100')->nullable();
            $table->decimal('limit_lampiran_3')->default('100')->nullable();
            $table->decimal('limit_lampiran_4')->default('100')->nullable();
            $table->decimal('limit_lampiran_5')->default('100')->nullable();

            $table->string('klausa_lampiran_1')->default('>=')->nullable();
            $table->string('klausa_lampiran_2')->default('>=')->nullable();
            $table->string('klausa_lampiran_3')->default('>=')->nullable();
            $table->string('klausa_lampiran_4')->default('>=')->nullable();
            $table->string('klausa_lampiran_5')->default('>=')->nullable();

            $table->boolean('isActive')->default(true)->comment('Status aktif aturan ini');

            $table->timestamps();

            // Foreign Keys dimatikan karena nanti akan di-handle lewat model.
            // karena tidak bisa langsung fk ke db sebelah, maka alternatifnya langsung lewat modelnya
            // $table->foreign('jfaAsal')->references('id')->on('ref_jabatan_fungsional_akademik')->onDelete('cascade');
            // $table->foreign('jfaTujuan')->references('id')->on('ref_jabatan_fungsional_akademik')->onDelete('cascade');
        });

        // -- kurang jabatan NJAD => 'Non JAD' -> 0 poin,
        // 'b467678d-8e9f-4453-bb76-f0cba91468dc' => 'Asisten Ahli' -> 150 poin,
        // 'f6890047-b0ea-4b45-a9f9-b0584c65bdd6' => 'Lektor' -> 200 poin,
        // '21ac00aa-1f19-4347-84c1-9e70413209ab' => 'Lektor Kepala' -> 450 poin,
        // 'd6418a5e-b76f-4d67-9990-056e1acabe66' => 'Guru Besar (Profesor)' -> 850 poin,
        // --- Seeding Data Aturan Target ---
        DB::connection($this->connection)->table('ref_target_jabatan_pengajuan')->insert([
            [
                'id' => 1,
                'jfaAsal' => '8a7c0b44-2c2e-4a16-a4df-111111111111', // NJAD
                'jfaTujuan' => 'b467678d-8e9f-4453-bb76-f0cba91468dc', // Asisten Ahli
                'kumTarget' => 150,
                'limit_lampiran_1' => 100,
                'limit_lampiran_2' => 100,
                'limit_lampiran_3' => 100,
                'limit_lampiran_4' => 40,
                'limit_lampiran_5' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'jfaAsal' => 'b467678d-8e9f-4453-bb76-f0cba91468dc', // Asisten Ahli
                'jfaTujuan' => 'f6890047-b0ea-4b45-a9f9-b0584c65bdd6', // Lektor
                'kumTarget' => 50,
                'limit_lampiran_1' => 100,
                'limit_lampiran_2' => 142,
                'limit_lampiran_3' => 100,
                'limit_lampiran_4' => 40,
                'limit_lampiran_5' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'jfaAsal' => 'f6890047-b0ea-4b45-a9f9-b0584c65bdd6', // Lektor
                'jfaTujuan' => '21ac00aa-1f19-4347-84c1-9e70413209ab', // Lektor Kepala
                'kumTarget' => 250,
                'limit_lampiran_1' => 100,
                'limit_lampiran_2' => 100,
                'limit_lampiran_3' => 100,
                'limit_lampiran_4' => 40,
                'limit_lampiran_5' => 100,
                'created_at' => now(),
                'updated_at()',
            ],
            [
                'id' => 4,
                'jfaAsal' => '21ac00aa-1f19-4347-84c1-9e70413209ab', // Lektor Kepala
                'jfaTujuan' => 'd6418a5e-b76f-4d67-9990-056e1acabe66', // Guru Besar
                'kumTarget' => 400,
                'limit_lampiran_1' => 100,
                'limit_lampiran_2' => 100,
                'limit_lampiran_3' => 100,
                'limit_lampiran_4' => 100,
                'limit_lampiran_5' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'jfaAsal' => 'd6418a5e-b76f-4d67-9990-056e1acabe66', // Guru Besar
                'jfaTujuan' => 'd6418a5e-b76f-4d67-9990-056e1acabe66', // Guru Besar (Tetap)
                'kumTarget' => 0,
                'limit_lampiran_1' => 100,
                'limit_lampiran_2' => 100,
                'limit_lampiran_3' => 100,
                'limit_lampiran_4' => 100,
                'limit_lampiran_5' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('ref_target_jabatan_pengajuan');
    }
};