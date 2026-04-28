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
        Schema::create('levels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_level', 30);
            $table->string('singkatan_level', 12);
            $table->string('urut',5)->nullable();
            $table->foreignUuid('atasan_level')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();

            $table->foreign('atasan_level')->references('id')->on('levels')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
