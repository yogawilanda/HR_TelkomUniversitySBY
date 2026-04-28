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
        Schema::create('sks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // $table->foreignUuid('users_id')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('no_sk')->nullable();
            $table->date('tmt_mulai')->nullable();
            $table->string('file_sk')->nullable();
            $table->enum('tipe_sk', ['LLDIKTI', 'Pengakuan YPT'])->nullable();
            $table->timestamps();

            // $table->foreign('users_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sks');
    }
};
