<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Pivot table untuk many-to-many relationship antara kontrak_unit dan users (pegawai)
     */
    public function up(): void
    {
        Schema::create('kontrak_unit_pegawai', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kontrak_unit_id');
            $table->uuid('user_id'); // Changed to uuid to match users table
            $table->timestamp('tanggal_mulai')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();
            $table->string('status')->default('assigned'); // assigned, in_progress, completed
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('kontrak_unit_id')
                ->references('id')
                ->on('kontrak_unit')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Prevent duplicate assignments
            $table->unique(['kontrak_unit_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontrak_unit_pegawai');
    }
};
