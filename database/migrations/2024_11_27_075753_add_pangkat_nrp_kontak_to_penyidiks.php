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
            $table->string('pangkat_penyidik')->nullable();
            $table->string('nrp_penyidik')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyidiks', function (Blueprint $table) {
            $table->dropColumn('pangkat_penyidik');
            $table->dropColumn('nrp_penyidik');
        });
    }
};
