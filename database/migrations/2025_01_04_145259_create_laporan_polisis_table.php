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
        Schema::create('laporan_polisis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_lapor');
            $table->date('tanggal_kejadian');
            $table->string('perkara');
            $table->string('tkp');
            $table->text('uraian_peristiwa');
            $table->string('kerugian');
            $table->bigInteger('province_id')->nullable();
            $table->bigInteger('city_id')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('subdistrict_id')->nullable();
            $table->string('status')->default('Proses');
            $table->longText('media')->nullable();
            $table->bigInteger('subdit_id')->nullable();
            $table->bigInteger('unit_id')->nullable();
            $table->bigInteger('penyidik_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_polisis');
    }
};
