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
        Schema::table('laporan_informasis', function (Blueprint $table) {
            $table->string('kerugian', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_informasis', function (Blueprint $table) {
            $table->decimal('kerugian', 10, 0)->change();
        });
    }
};
