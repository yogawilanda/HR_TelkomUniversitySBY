<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pelaporan_pekerjaan', function (Blueprint $table) {
            $table->integer('pencapaian_percent')->nullable()->after('status');
            $table->string('evidence')->nullable()->after('pencapaian_percent');
        });
    }

    public function down()
    {
        Schema::table('pelaporan_pekerjaan', function (Blueprint $table) {
            $table->dropColumn(['pencapaian_percent', 'evidence']);
        });
    }
};
