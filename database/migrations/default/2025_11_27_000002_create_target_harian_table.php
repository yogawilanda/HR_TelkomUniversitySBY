<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('target_kinerja_harian', function (Blueprint $table) {
            $table->id();
            $table->string('pekerjaan');
            $table->string('kontrak_type')->nullable(); // institusi/unit/pribadi
            $table->unsignedBigInteger('target_kinerja_id')->nullable();
            $table->string('result')->nullable();
            $table->integer('jumlah')->nullable();
            $table->integer('waktu_minutes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('bobot')->nullable();
            $table->timestamp('start')->nullable();
            $table->timestamp('end')->nullable();
            $table->timestamps();

            $table->foreign('target_kinerja_id')->references('id')->on('target_kinerja')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('target_kinerja_harian');
    }
};
