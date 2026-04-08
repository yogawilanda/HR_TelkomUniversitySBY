<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('target_kinerja', function (Blueprint $table) {
            $table->string('responsibility')->nullable()->after('nama');
            $table->string('satuan')->nullable()->after('responsibility');
            $table->integer('target_percent')->nullable()->after('bobot');
            $table->integer('pencapaian_percent')->nullable()->after('target_percent');
            $table->string('status')->nullable()->after('pencapaian_percent'); // institusi/unit/pribadi
            $table->string('unit_penanggung_jawab')->nullable()->after('status');
            $table->string('evidence')->nullable()->after('unit_penanggung_jawab');
            $table->string('periode')->nullable()->after('evidence');
        });
    }

    public function down()
    {
        Schema::table('target_kinerja', function (Blueprint $table) {
            $table->dropColumn([
                'responsibility',
                'satuan',
                'target_percent',
                'pencapaian_percent',
                'status',
                'unit_penanggung_jawab',
                'evidence',
                'periode',
            ]);
        });
    }
};
