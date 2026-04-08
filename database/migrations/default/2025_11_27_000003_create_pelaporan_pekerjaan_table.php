<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pelaporan_pekerjaan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('target_harian_id')->nullable();
            $table->text('realisasi')->nullable();
            $table->unsignedBigInteger('referensi_set_target_id')->nullable();
            $table->integer('realisasi_jumlah')->nullable();
            $table->integer('realisasi_waktu_minutes')->nullable();
            $table->integer('approved_jumlah')->nullable();
            $table->integer('approved_waktu_minutes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();

            $table->foreign('target_harian_id')->references('id')->on('target_kinerja_harian')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pelaporan_pekerjaan');
    }
};
