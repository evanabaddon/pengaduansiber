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
        Schema::table('personils', function (Blueprint $table) {
            $table->string('nomor_bpjs')->nullable()->after('bpjs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personils', function (Blueprint $table) {
            $table->dropColumn('nomor_bpjs');
        });
    }
};
