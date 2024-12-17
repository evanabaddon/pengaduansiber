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
        Schema::table('pelapors', function (Blueprint $table) {
            $table->foreignId('pengaduan_id')->nullable()->constrained('pengaduans')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelapors', function (Blueprint $table) {
            $table->dropForeign(['pengaduan_id']);
            $table->dropColumn('pengaduan_id');
        });
    }
};
