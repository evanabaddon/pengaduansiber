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
        Schema::create('laporan_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('laporan_informasis')->onDelete('cascade');
            $table->longText('kronologi_analysis');
            $table->longText('possible_laws');
            $table->longText('investigation_steps');
            $table->longText('priority_level');
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_analyses');
    }
};
