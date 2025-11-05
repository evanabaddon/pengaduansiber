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
        Schema::create('klaster_jabatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Nama jabatan atau klaster
            $table->foreignId('parent_id') // Self relation
                ->nullable()
                ->constrained('klaster_jabatan')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('klaster_jabatan');
    }
};
