<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('target_kinerja', function (Blueprint $table) {
            $table->date('start')->nullable()->after('periode');
            $table->date('end')->nullable()->after('start');
        });
    }

    public function down()
    {
        Schema::table('target_kinerja', function (Blueprint $table) {
            $table->dropColumn(['start', 'end']);
        });
    }
};
