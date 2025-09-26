<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Anggaran extends Model
{
    protected $fillable = [
        // meta
        'nama',
        'tahun_anggaran',
        'file_path',

        /*
         I. PAGU
         */
        'pagu',
        'belanja_pegawai_pagu',
        'belanja_barang_pagu',
        'lidik_sidik_pagu',
        'dukops_giat_pagu',
        'harwat_r4_6_10_pagu',
        'harwat_fungsional_pagu',

        // Subdit / unit - PAGU
        'subdit1_lidik_sidik_pagu',
        'subdit1_unit1_lidik_sidik_pagu',
        'subdit1_unit2_lidik_sidik_pagu',
        'subdit1_unit3_lidik_sidik_pagu',
        'subdit1_unit4_lidik_sidik_pagu',
        'subdit1_unit5_lidik_sidik_pagu',

        'subdit2_lidik_sidik_pagu',
        'subdit2_unit1_lidik_sidik_pagu',
        'subdit2_unit2_lidik_sidik_pagu',
        'subdit2_unit3_lidik_sidik_pagu',
        'subdit2_unit4_lidik_sidik_pagu',
        'subdit2_unit5_lidik_sidik_pagu',

        'subdit3_har_alsus_pagu',
        'subdit3_unit1_har_alsus_pagu',
        'subdit3_unit2_har_alsus_pagu',
        'subdit3_unit3_har_alsus_pagu',
        'subdit3_unit4_har_alsus_pagu',
        'subdit3_unit5_har_alsus_pagu',

        'subdit3_lisensi_latfung_pagu',
        'subdit3_unit1_lisensi_latfung_pagu',
        'subdit3_unit2_lisensi_latfung_pagu',
        'subdit3_unit3_lisensi_latfung_pagu',
        'subdit3_unit4_lisensi_latfung_pagu',
        'subdit3_unit5_lisensi_latfung_pagu',

        /*
          II. REALISASI
         */
        'realisasi',
        'realisasi_belanja_pegawai',
        'realisasi_belanja_barang',
        'realisasi_lidik_sidik',
        'realisasi_dukops_giat',
        'realisasi_harwat_r4_6_10',
        'realisasi_harwat_fungsional',

        // Subdit / unit - REALISASI
        'subdit1_lidik_sidik_realisasi',
        'subdit1_unit1_lidik_sidik_realisasi',
        'subdit1_unit2_lidik_sidik_realisasi',
        'subdit1_unit3_lidik_sidik_realisasi',
        'subdit1_unit4_lidik_sidik_realisasi',
        'subdit1_unit5_lidik_sidik_realisasi',

        'subdit2_lidik_sidik_realisasi',
        'subdit2_unit1_lidik_sidik_realisasi',
        'subdit2_unit2_lidik_sidik_realisasi',
        'subdit2_unit3_lidik_sidik_realisasi',
        'subdit2_unit4_lidik_sidik_realisasi',
        'subdit2_unit5_lidik_sidik_realisasi',

        'subdit3_har_alsus_realisasi',
        'subdit3_unit1_har_alsus_realisasi',
        'subdit3_unit2_har_alsus_realisasi',
        'subdit3_unit3_har_alsus_realisasi',
        'subdit3_unit4_har_alsus_realisasi',
        'subdit3_unit5_har_alsus_realisasi',

        'subdit3_lisensi_latfung_realisasi',
        'subdit3_unit1_lisensi_latfung_realisasi',
        'subdit3_unit2_lisensi_latfung_realisasi',
        'subdit3_unit3_lisensi_latfung_realisasi',
        'subdit3_unit4_lisensi_latfung_realisasi',
        'subdit3_unit5_lisensi_latfung_realisasi',

        /*
          III. SILPA (lengkap / mirroring PAGU & REALISASI)
         */
        'silpa',
        'silpa_belanja_pegawai',
        'silpa_belanja_barang',
        'silpa_lidik_sidik',
        'silpa_dukops_giat',
        'silpa_harwat_r4_6_10',
        'silpa_harwat_fungsional',
        'silpa_har_alsus',
        'silpa_lisensi_latfung',

        // Subdit / unit - SILPA (Subdit I)
        'subdit1_lidik_sidik_silpa',
        'subdit1_unit1_lidik_sidik_silpa',
        'subdit1_unit2_lidik_sidik_silpa',
        'subdit1_unit3_lidik_sidik_silpa',
        'subdit1_unit4_lidik_sidik_silpa',
        'subdit1_unit5_lidik_sidik_silpa',

        // Subdit / unit - SILPA (Subdit II)
        'subdit2_lidik_sidik_silpa',
        'subdit2_unit1_lidik_sidik_silpa',
        'subdit2_unit2_lidik_sidik_silpa',
        'subdit2_unit3_lidik_sidik_silpa',
        'subdit2_unit4_lidik_sidik_silpa',
        'subdit2_unit5_lidik_sidik_silpa',

        // Sub-rincian silpa harwat fungsional (Subdit III)
        'subdit3_har_alsus_silpa',
        'subdit3_unit1_har_alsus_silpa',
        'subdit3_unit2_har_alsus_silpa',
        'subdit3_unit3_har_alsus_silpa',
        'subdit3_unit4_har_alsus_silpa',
        'subdit3_unit5_har_alsus_silpa',

        'subdit3_lisensi_latfung_silpa',
        'subdit3_unit1_lisensi_latfung_silpa',
        'subdit3_unit2_lisensi_latfung_silpa',
        'subdit3_unit3_lisensi_latfung_silpa',
        'subdit3_unit4_lisensi_latfung_silpa',
        'subdit3_unit5_lisensi_latfung_silpa',
    ];

    protected static function booted()
    {
        static::created(function ($anggaran) {
            $source = storage_path('app/templates/anggaran.xlsx');
    
            // Bikin nama file sesuai input
            $safeNama = 'Anggaran_Ditressiber_Polda_Jatim_Tahun';
            $filename = "{$safeNama}-{$anggaran->tahun_anggaran}.xlsx";
            $dest     = "docs/{$filename}";
    
            // Copy template ke storage/app/public/docs
            \Illuminate\Support\Facades\Storage::disk('public')->put($dest, file_get_contents($source));
    
            // Update path di database
            $anggaran->update([
                'file_path' => $dest,
            ]);
        });

        static::saved(function ($anggaran) {
            if ($anggaran->file_path) {
                \Log::info("Overwriting Excel for {$anggaran->id} -> {$anggaran->file_path}");
                app(\App\Http\Controllers\AnggaranEditorController::class)
                    ->generateFileFromTemplate($anggaran);
            }
        });
    }
    // protected static function booted()
    // {
    //     static::created(function ($anggaran) {
    //         app(\App\Http\Controllers\AnggaranEditorController::class)
    //             ->generateFileFromTemplate($anggaran);
    //     });

    //     static::updated(function ($anggaran) {
    //         // Generate Excel baru
    //         app(\App\Http\Controllers\AnggaranEditorController::class)
    //             ->generateFileFromTemplate($anggaran);

    //         // langsung generate pdf juga
    //         app(\App\Http\Controllers\AnggaranEditorController::class)
    //             ->convertExcelToPdf($anggaran->id);
    //     });
    // }

}
