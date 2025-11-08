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
        Schema::table('surats', function (Blueprint $table) {
            // 🧭 Struktur 3 level klasterisasi
            $table->string('jenis_dokumen');     // Level 1
            $table->string('kategori_surat');    // Level 2
            $table->string('pejabat_penerbit');  // Level 3

            // 🧩 Template dan dokumen
            $table->string('template_path')->nullable();  // Path ke template docx
            $table->string('document_url')->nullable();   // Hasil editing OnlyOffice
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            //
        });
    }
};
