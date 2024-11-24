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
            $table->string('agama')->nullable();
            $table->string('kewarganegaraan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelapors', function (Blueprint $table) {
            $table->dropColumn('agama');
            $table->dropColumn('kewarganegaraan');
        });
    }
};
