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
        Schema::create('work_positions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode', 100)->unique();
            $table->string('position_name', 100);
            $table->string('type_work_position', 50);
            $table->uuid('parent_id')->nullable();
            $table->timestamps();
            $table->string('type_pekerja', 50);

            $table->foreign('type_work_position')->references('position_name')->on('ref_work_positions')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('work_positions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_positions');
    }
};