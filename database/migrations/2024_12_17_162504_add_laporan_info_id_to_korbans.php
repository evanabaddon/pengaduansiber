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
        Schema::table('korbans', function (Blueprint $table) {
            $table->foreignId('laporan_info_id')->nullable()->constrained('laporan_infos')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('korbans', function (Blueprint $table) {
            $table->dropForeign(['laporan_info_id']);
            $table->dropColumn('laporan_info_id');
        });
    }
};
