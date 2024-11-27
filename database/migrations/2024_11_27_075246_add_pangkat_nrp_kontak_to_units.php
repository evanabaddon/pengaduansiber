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
        Schema::table('units', function (Blueprint $table) {
            $table->string('pangkat_pimpinan')->nullable();
            $table->string('nrp_pimpinan')->nullable();
            $table->string('kontak_pimpinan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('pangkat_pimpinan');
            $table->dropColumn('nrp_pimpinan');
            $table->dropColumn('kontak_pimpinan');
        });
    }
};
