<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix pelaporan_pekerjaan user foreign keys to use uuid instead of unsignedBigInteger
     */
    public function up(): void
    {
        Schema::table('pelaporan_pekerjaan', function (Blueprint $table) {
            // Change created_by and approved_by to uuid type to match users table
            $table->uuid('created_by')->nullable()->change();
            $table->uuid('approved_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelaporan_pekerjaan', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->change();
            $table->unsignedBigInteger('approved_by')->nullable()->change();
        });
    }
};
