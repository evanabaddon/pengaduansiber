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
        Schema::table('subdits', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu
            $table->dropForeign(['unit_id']);
            
            // Ubah tipe data dan rename kolom
            $table->string('unit_id')->change();
            $table->renameColumn('unit_id', 'nama_pimpinan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subdits', function (Blueprint $table) {
            $table->renameColumn('nama_pimpinan', 'unit_id');
            $table->integer('unit_id')->change();
            
            // Kembalikan foreign key jika diperlukan
            $table->foreign('unit_id')->references('id')->on('units');
        });
    }
};
