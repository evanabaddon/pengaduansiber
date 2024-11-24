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
        Schema::create('laporan_informasis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_lapor');
            $table->date('tanggal_kejadian');
            $table->string('perkara');
            $table->string('tkp');
            $table->text('uraian_peristiwa');
            $table->decimal('kerugian');
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('subdistrict_id')->nullable();
            $table->boolean('domestic')->default(true)->change();
            $table->string('status')->nullable();
            $table->string('media')->nullable();
            $table->foreignId('subdit_id')->nullable();
            $table->foreignId('unit_id')->nullable();
            $table->foreignId('penyidik_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_informasis');
    }
};
