<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Update pelaporan_pekerjaan untuk berelasi dengan kinerja_unit
     * Pelaporan Pekerjaan berelasi many to one dengan Kinerja Unit
     * Pelaporan Pekerjaan berasal dari TPA dan beberapa pegawai lain
     */
    public function up(): void
    {
        Schema::table('pelaporan_pekerjaan', function (Blueprint $table) {
            // Tambahkan kolom kinerja_unit_id
            $table->unsignedBigInteger('kinerja_unit_id')->nullable()->after('target_harian_id');
            
            // Tambahkan kolom tpa_id untuk pelaporan yang berasal dari TPA
            $table->string('tpa_id')->nullable()->after('kinerja_unit_id');
            
            // Tambahkan foreign key
            $table->foreign('kinerja_unit_id')
                ->references('id')
                ->on('kinerja_unit')
                ->onDelete('set null');
            
            $table->foreign('tpa_id')
                ->references('id')
                ->on('tpas')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelaporan_pekerjaan', function (Blueprint $table) {
            $table->dropForeign(['kinerja_unit_id']);
            $table->dropForeign(['tpa_id']);
            $table->dropColumn(['kinerja_unit_id', 'tpa_id']);
        });
    }
};
