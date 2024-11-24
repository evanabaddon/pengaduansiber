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
        Schema::create('terlapors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('laporans');
            $table->string('nama');
            $table->string('jenis_kelamin')->nullable();
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('subdistrict_id')->nullable();
            $table->unsignedBigInteger('postal_code')->nullable();
            $table->string('kontak')->nullable();
            $table->integer('usia')->nullable();
            $table->text('uraian_terlapor')->nullable(); // Uraian terkait terlapor
            $table->string('status_terlapor')->default('belum diproses'); // Status apakah sudah diproses, ditangkap, dll.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terlapors');
    }
};
