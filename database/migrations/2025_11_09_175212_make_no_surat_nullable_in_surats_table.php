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
            $table->string('nomor_surat')->nullable()->change();
            $table->renameColumn('perihal', 'nama_dokumen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->string('nomor_surat')->nullable(false)->change();
            $table->renameColumn('nama_dokumen', 'perihal');
        });
    }
};
