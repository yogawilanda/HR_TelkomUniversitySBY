<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Kontrak Unit berelasi one to many dengan Kontrak Manajemen
     * Kontrak Unit berelasi one to one dengan Kinerja Unit
     */
    public function up(): void
    {
        Schema::create('kontrak_unit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kontrak_manajemen_id');
            $table->string('nama_unit');
            $table->string('pekerjaan');
            $table->string('kontrak_type')->nullable(); // institusi/unit/pribadi
            $table->string('result')->nullable();
            $table->integer('jumlah')->nullable();
            $table->integer('waktu_minutes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('bobot')->nullable();
            $table->timestamp('start')->nullable();
            $table->timestamp('end')->nullable();
            $table->timestamps();

            $table->foreign('kontrak_manajemen_id')
                ->references('id')
                ->on('kontrak_manajemen')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontrak_unit');
    }
};
