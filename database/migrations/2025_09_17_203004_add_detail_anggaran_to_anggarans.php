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
        Schema::table('anggarans', function (Blueprint $table) {
            // I. PAGU
            $table->double('pagu')->default(0);

            // Rincian PAGU
            $table->double('belanja_pegawai_pagu')->default(0);
            $table->double('lidik_sidik_pagu')->default(0);
            $table->double('dukops_giat_pagu')->default(0);
            $table->double('harwat_r4_6_10_pagu')->default(0);

            // Sub-rincian harwat fungsional
            $table->double('har_alsus_pagu')->default(0);
            $table->double('lisensi_latfung_pagu')->default(0);

            // II. REALISASI
            $table->double('realisasi')->default(0);
            $table->double('realisasi_belanja_pegawai')->default(0);
            $table->double('realisasi_lidik_sidik')->default(0);
            $table->double('realisasi_dukops_giat')->default(0);
            $table->double('realisasi_harwat_r4_6_10')->default(0);

            // Sub-rincian realisasi harwat fungsional
            $table->double('realisasi_har_alsus')->default(0);
            $table->double('realisasi_lisensi_latfung')->default(0);

            // III. SILPA
            $table->double('silpa')->default(0);
            $table->double('silpa_belanja_pegawai')->default(0);
            $table->double('silpa_lidik_sidik')->default(0);
            $table->double('silpa_dukops_giat')->default(0);
            $table->double('silpa_harwat_r4_6_10')->default(0);

            // Sub-rincian silpa harwat fungsional
            $table->double('silpa_har_alsus')->default(0);
            $table->double('silpa_lisensi_latfung')->default(0);

            // IV. Lidik/Sidik per Subdit
            $table->double('subdit1_lidik_sidik_pagu')->default(0);
            // Units Subdit I Lidik/Sidik
            $table->double('subdit1_unit1_lidik_sidik_realisasi')->default(0);
            $table->double('subdit1_unit2_lidik_sidik_realisasi')->default(0);
            $table->double('subdit1_unit3_lidik_sidik_realisasi')->default(0);
            $table->double('subdit1_unit4_lidik_sidik_realisasi')->default(0);
            $table->double('subdit1_unit5_lidik_sidik_realisasi')->default(0);
            
            $table->double('subdit1_lidik_sidik_realisasi')->default(0);
            
            $table->double('subdit2_lidik_sidik_pagu')->default(0);
            // Units Subdit II Lidik/Sidik
            $table->double('subdit2_unit1_lidik_sidik_realisasi')->default(0);
            $table->double('subdit2_unit2_lidik_sidik_realisasi')->default(0);
            $table->double('subdit2_unit3_lidik_sidik_realisasi')->default(0);
            $table->double('subdit2_unit4_lidik_sidik_realisasi')->default(0);
            $table->double('subdit2_unit5_lidik_sidik_realisasi')->default(0);
            
            $table->double('subdit2_lidik_sidik_realisasi')->default(0);

            // V. Harwat Fungsional per Subdit
            $table->double('subdit3_har_alsus_pagu')->default(0);
            // Units Subdit III Harwat Fungsional Har Alsus
            $table->double('subdit3_unit1_har_alsus_realisasi')->default(0);
            $table->double('subdit3_unit2_har_alsus_realisasi')->default(0);
            $table->double('subdit3_unit3_har_alsus_realisasi')->default(0);
            $table->double('subdit3_unit4_har_alsus_realisasi')->default(0);
            $table->double('subdit3_unit5_har_alsus_realisasi')->default(0);

            $table->double('subdit3_har_alsus_realisasi')->default(0);

            $table->double('subdit3_lisensi_latfung_pagu')->default(0);
            // Units Subdit III Harwat Fungsional Lisensi Latfung
            $table->double('subdit3_unit1_lisensi_latfung_realisasi')->default(0);
            $table->double('subdit3_unit2_lisensi_latfung_realisasi')->default(0);
            $table->double('subdit3_unit3_lisensi_latfung_realisasi')->default(0);
            $table->double('subdit3_unit4_lisensi_latfung_realisasi')->default(0);
            $table->double('subdit3_unit5_lisensi_latfung_realisasi')->default(0);

            $table->double('subdit3_lisensi_latfung_realisasi')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggarans', function (Blueprint $table) {
            //
        });
    }
};
