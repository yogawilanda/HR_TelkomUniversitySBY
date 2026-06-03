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
        // 1. Rename existing unit-based table to avoid conflict
        if (Schema::hasTable('akumulasi_kinerja')) {
            Schema::rename('akumulasi_kinerja', 'akumulasi_kinerja_unit');
        }

        // 2. Create the employee-based akumulasi_kinerja table expected by the model and controller
        Schema::create('akumulasi_kinerja', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('employee_id');
            $table->string('fullname');
            $table->integer('year');
            $table->integer('month');
            $table->decimal('jam_kerja', 10, 2)->default(0);
            $table->integer('kehadiran')->default(0);
            $table->integer('tepat_waktu')->default(0);
            $table->integer('tidak_tap_pulang')->default(0);
            $table->uuid('user_id')->nullable();
            $table->timestamps();
            
            // FK to users if needed, but the controller uses with('user') which depends on the model relation
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akumulasi_kinerja');
        if (Schema::hasTable('akumulasi_kinerja_unit')) {
            Schema::rename('akumulasi_kinerja_unit', 'akumulasi_kinerja');
        }
    }
};
