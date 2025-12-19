<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Kontrak Manajemen adalah kontrak utama yang menggantikan target_kinerja
     */
    public function up(): void
    {
        Schema::create('kontrak_manajemen', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('keterangan')->nullable();
            $table->integer('bobot')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('responsibility')->nullable();
            $table->string('satuan')->nullable();
            $table->decimal('target_percent', 5, 2)->nullable();
            $table->string('status')->default('draft'); // draft, active, completed
            $table->string('unit_penanggung_jawab')->nullable();
            $table->string('periode')->nullable();
            $table->timestamp('start')->nullable();
            $table->timestamp('end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontrak_manajemen');
    }
};
