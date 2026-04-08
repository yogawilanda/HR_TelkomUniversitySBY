<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Ensure previous partial table is removed (helps rerunning after earlier failed migration)
        Schema::dropIfExists('target_kinerja_harian_pegawai');

        Schema::create('target_kinerja_harian_pegawai', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('target_kinerja_harian_id');
            // users.id is a UUID primary key in this project, use uuid type
            $table->uuid('user_id');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('status')->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('target_kinerja_harian_id')->references('id')->on('target_kinerja_harian')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['target_kinerja_harian_id', 'user_id'], 'tkh_user_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('target_kinerja_harian_pegawai');
    }
};
