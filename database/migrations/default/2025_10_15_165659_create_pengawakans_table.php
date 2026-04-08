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
        Schema::create('pengawakans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('users_id')->nullable();
            $table->foreignUuid('formasi_id')->nullable();
            $table->string('is_main_position')->nullable();
            $table->date('tmt_mulai');
            $table->date('tmt_selesai')->nullable();            
            $table->foreignUuid('sk_ypt_id')->nullable();            
            $table->timestamps();

            
            $table->foreign('sk_ypt_id')->references('id')->on('sks')->onDelete('set null');
            $table->foreign('users_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('formasi_id')->references('id')->on('formations')->onDelete('set null');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengawakans');
    }
};
