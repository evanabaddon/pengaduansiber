<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Anggaran extends Model
{
    protected $fillable = [
        'tahun_anggaran',
        'file_path',

        'pagu',
        'belanja_pegawai_pagu',
        'belanja_barang_pagu',
        'lidik_sidik_pagu',
        'dukops_giat_pagu',
        'harwat_r4_6_10_pagu',
        'harwat_fungsional_pagu',
        'har_alsus_pagu',
        'lisensi_latfung_pagu',

        'realisasi',
        'realisasi_belanja_pegawai',
        'realisasi_belanja_barang',
        'realisasi_lidik_sidik',
        'realisasi_dukops_giat',
        'realisasi_harwat_r4_6_10',
        'realisasi_harwat_fungsional',
        'realisasi_har_alsus',
        'realisasi_lisensi_latfung',

        'silpa',
        'silpa_belanja_pegawai',
        'silpa_belanja_barang',
        'silpa_lidik_sidik',
        'silpa_dukops_giat',
        'silpa_harwat_r4_6_10',
        'silpa_harwat_fungsional',
        'silpa_har_alsus',
        'silpa_lisensi_latfung',

        'subdit1_lidik_sidik_pagu',
        'subdit2_lidik_sidik_pagu',
        'subdit1_lidik_sidik_realisasi',
        'subdit2_lidik_sidik_realisasi',

        'subdit3_har_alsus_pagu',
        'subdit3_lisensi_latfung_pagu',
        'subdit3_har_alsus_realisasi',
        'subdit3_lisensi_latfung_realisasi',

        'subdit1_unit1_lidik_sidik_pagu',
        'subdit1_unit1_lidik_sidik_realisasi',
        'subdit1_unit2_lidik_sidik_pagu',
        'subdit1_unit2_lidik_sidik_realisasi',
        'subdit1_unit3_lidik_sidik_pagu',
        'subdit1_unit3_lidik_sidik_realisasi',
        'subdit1_unit4_lidik_sidik_pagu',
        'subdit1_unit4_lidik_sidik_realisasi',
        'subdit1_unit5_lidik_sidik_pagu',
        'subdit1_unit5_lidik_sidik_realisasi',

        'subdit2_unit1_lidik_sidik_pagu',
        'subdit2_unit1_lidik_sidik_realisasi',
        'subdit2_unit2_lidik_sidik_pagu',
        'subdit2_unit2_lidik_sidik_realisasi',
        'subdit2_unit3_lidik_sidik_pagu',
        'subdit2_unit3_lidik_sidik_realisasi',
        'subdit2_unit4_lidik_sidik_pagu',
        'subdit2_unit4_lidik_sidik_realisasi',
        'subdit2_unit5_lidik_sidik_pagu',
        'subdit2_unit5_lidik_sidik_realisasi',

        'subdit3_unit1_har_alsus_pagu',
        'subdit3_unit1_har_alsus_realisasi',
        'subdit3_unit2_har_alsus_pagu',
        'subdit3_unit2_har_alsus_realisasi',
        'subdit3_unit3_har_alsus_pagu',
        'subdit3_unit3_har_alsus_realisasi',
        'subdit3_unit4_har_alsus_pagu',
        'subdit3_unit4_har_alsus_realisasi',
        'subdit3_unit5_har_alsus_pagu',
        'subdit3_unit5_har_alsus_realisasi',

        'subdit3_unit1_lisensi_latfung_pagu',
        'subdit3_unit1_lisensi_latfung_realisasi',
        'subdit3_unit2_lisensi_latfung_pagu',
        'subdit3_unit2_lisensi_latfung_realisasi',
        'subdit3_unit3_lisensi_latfung_pagu',
        'subdit3_unit3_lisensi_latfung_realisasi',
        'subdit3_unit4_lisensi_latfung_pagu',
        'subdit3_unit4_lisensi_latfung_realisasi',
        'subdit3_unit5_lisensi_latfung_pagu',
        'subdit3_unit5_lisensi_latfung_realisasi',
    ];

    protected static function booted()
    {
        static::created(function ($anggaran) {
            $source = storage_path('app/templates/anggaran.xlsx');
    
            // Bikin nama file sesuai input
            $safeNama = str_replace(' ', '_', strtolower($anggaran->nama));
            $filename = "{$safeNama}-{$anggaran->tahun_anggaran}.xlsx";
            $dest     = "docs/{$filename}";
    
            // Copy template ke storage/app/public/docs
            \Illuminate\Support\Facades\Storage::disk('public')->put($dest, file_get_contents($source));
    
            // Update path di database
            $anggaran->update([
                'file_path' => $dest,
            ]);
        });
    }
}
