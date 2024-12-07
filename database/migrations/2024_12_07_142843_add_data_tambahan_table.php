<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_tambahan', function (Blueprint $table) {
            $table->id();
            $table->morphs('recordable'); // Untuk pelapor, terlapor, atau korban
            $table->string('nama_data');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_tambahan');
    }
};