<?php

namespace App\Models;

use App\Models\Unit;
use App\Models\Subdit;
use App\Models\Penyidik;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $with = ['pelapors', 'korbans', 'terlapors']; // Eager load relationships

    protected $fillable = [
        'tanggal_lapor',
        'tanggal_kejadian',
        'perkara',
        'tkp',
        'uraian_peristiwa',
        'kerugian',
        'status',
        'subdit_id',
        'unit_id',
        'penyidik_id',
        'province_id',
        'city_id',
        'district_id',
        'subdistrict_id',
        'postal_code',
        'media',
    ];

    protected $casts = [
        'media' => 'array',
    ];

    public function pelapors()
    {
        return $this->hasOne(Pelapor::class);
    }

    public function korbans()
    {
        return $this->hasOne(Korban::class);
    }

    public function terlapors()
    {
        return $this->hasOne(Terlapor::class);
    }

    public function subdit()
    {
        return $this->belongsTo(Subdit::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function penyidik()
    {
        return $this->belongsTo(Penyidik::class);
    }

    protected static function booted()
    {
        static::deleting(function ($laporan) {
            $laporan->korbans()->delete(); // Hapus semua korban terkait
            $laporan->pelapors()->delete(); // Hapus semua pelapor terkait
            $laporan->terlapors()->delete(); // Hapus semua terlapor terkait
        });
    }
}
