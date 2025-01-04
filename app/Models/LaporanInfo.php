<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Unit;
use App\Models\Korban;
use App\Models\Subdit;
use App\Models\Pelapor;
use App\Models\Penyidik;
use App\Models\Terlapor;
use App\Models\BarangBukti;
use Illuminate\Database\Eloquent\Model;

class LaporanInfo extends Model
{
    protected $with = ['pelapors', 'korbans', 'terlapors']; // Eager load relationships

    protected $appends = ['tanggal_lapor', 'tanggal_kejadian'];

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
        return $this->hasMany(Korban::class);
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

    public function barangBuktis()
    {
        return $this->morphMany(BarangBukti::class, 'buktiable');
    }

    protected static function booted()
    {
        static::deleting(function ($laporanInfo) {
            $laporanInfo->korbans()->delete(); // Hapus semua korban terkait
            $laporanInfo->pelapors()->delete(); // Hapus semua pelapor terkait
            $laporanInfo->terlapors()->delete(); // Hapus semua terlapor terkait
        });
    }

    // Mutator untuk tanggal_lapor
    public function setTanggalLaporAttribute($value)
    {
        if ($value) {
            $this->attributes['tanggal_lapor'] = Carbon::createFromFormat('d F Y', $value)->format('Y-m-d');
        }
    }

    // Mutator untuk tanggal_kejadian
    public function setTanggalKejadianAttribute($value)
    {
        if ($value) {
            $this->attributes['tanggal_kejadian'] = Carbon::createFromFormat('d F Y', $value)->format('Y-m-d');
        }
    }

    // Tambahkan accessor untuk tanggal_lapor
    public function getTanggalLaporAttribute()
    {
        if ($this->attributes['tanggal_lapor']) {
            return Carbon::parse($this->attributes['tanggal_lapor'])->format('d F Y');
        }
        return null;
    }

    // Tambahkan accessor untuk tanggal_kejadian
    public function getTanggalKejadianAttribute()
    {
        if ($this->attributes['tanggal_kejadian']) {
            return Carbon::parse($this->attributes['tanggal_kejadian'])->format('d F Y');
        }
        return null;
    }
}
