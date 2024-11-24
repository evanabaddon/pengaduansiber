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
        Schema::table('penyidiks', function (Blueprint $table) {
            // Hapus foreign key dan kolom jika sudah ada
            if (Schema::hasColumn('penyidiks', 'subdit_id')) {
                $table->dropForeign('subdit_id');
                $table->dropColumn('subdit_id');
            }
            
            // Tambah kolom dan foreign key baru
            $table->foreignId('subdit_id')->nullable()->constrained('subdits')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyidiks', function (Blueprint $table) {
            $table->dropForeign(['subdit_id']);
            $table->dropColumn('subdit_id');
        });
    }
};
