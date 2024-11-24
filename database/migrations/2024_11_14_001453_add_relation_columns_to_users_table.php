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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('subdit_id')->nullable()->constrained('subdits');
            $table->foreignId('unit_id')->nullable()->constrained('units');
            $table->foreignId('penyidik_id')->nullable()->constrained('penyidiks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['subdit_id']);
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['penyidik_id']);
            $table->dropColumn(['subdit_id', 'unit_id', 'penyidik_id']);
        });
    }
};
