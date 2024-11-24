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
            // rename postal code to domestic and set default to true
            $table->renameColumn('postal_code', 'domestic');
            $table->boolean('domestic')->default(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('korban', function (Blueprint $table) {
            //
        });
    }
};
