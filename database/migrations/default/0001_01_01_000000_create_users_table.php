<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_lengkap');
            // $table->string('telepon');
            $table->string('telepon')->unique();
            $table->string('alamat');
            $table->string('nik');
            $table->string('email_institusi')->unique();
            $table->enum('jenis_kelamin', ['Perempuan', 'Laki-laki']);
            $table->string('tempat_lahir');
            $table->date('tgl_lahir');
            $table->enum('tipe_pegawai', ['TPA','Dosen']);
            $table->date('tgl_bergabung');
            $table->string('email_pribadi')->unique();
            $table->string('verified_code')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('username');
            $table->string('password_hash')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_new')->default(true);
            $table->rememberToken();
            $table->timestamps();

        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
