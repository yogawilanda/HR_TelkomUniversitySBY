<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Kinerja Unit berelasi one to one dengan Kontrak Unit
     * Kinerja Unit berelasi one to many dengan Pelaporan Pekerjaan (many to one dari sisi pelaporan)
     */
    public function up(): void
    {
        Schema::create('kinerja_unit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kontrak_unit_id')->unique(); // one to one relationship
            $table->string('status')->default('pending'); // pending, in_progress, completed
            $table->decimal('realisasi_percent', 5, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->integer('total_realisasi_jumlah')->default(0);
            $table->integer('total_realisasi_waktu_minutes')->default(0);
            $table->timestamps();

            $table->foreign('kontrak_unit_id')
                ->references('id')
                ->on('kontrak_unit')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kinerja_unit');
    }
};
