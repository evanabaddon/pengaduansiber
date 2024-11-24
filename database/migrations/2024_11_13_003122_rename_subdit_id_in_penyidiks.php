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
            $table->renameColumn('subdit_id', 'unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyidiks', function (Blueprint $table) {
            $table->renameColumn('unit_id', 'subdit_id');
        });
    }
};
