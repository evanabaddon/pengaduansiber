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
            $table->string('kontak_2')->nullable()->after('kontak');
            $table->string('alamat_2')->nullable()->after('alamat');
            $table->string('province_id_2')->nullable()->after('province_id');
            $table->string('city_id_2')->nullable()->after('city_id');
            $table->string('district_id_2')->nullable()->after('district_id');
            $table->string('subdistrict_id_2')->nullable()->after('subdistrict_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('korbans', function (Blueprint $table) {
            $table->dropColumn('kontak_2');
            $table->dropColumn('alamat_2');
            $table->dropColumn('province_id_2');
            $table->dropColumn('city_id_2');
            $table->dropColumn('district_id_2');
            $table->dropColumn('subdistrict_id_2');
        });
    }
};
