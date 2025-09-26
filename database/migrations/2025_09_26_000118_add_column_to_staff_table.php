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
        Schema::table('staff', function (Blueprint $table) {
            $table->string('name'); // Nama penyidik
            $table->unsignedTinyInteger('pangkat_staff'); // Simpan angka 1-13 sesuai pilihan
            $table->string('nrp_staff')->unique(); // NRP unik
            $table->string('kontak'); // Nomor telepon
            $table->string('jabatan')->nullable(); // jabatan opsional
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            //
        });
    }
};
