<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class BarangBukti extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'nama_barang',
        'deskripsi',
        'lokasi_penyimpanan',
        'kondisi',
        'jumlah',
        'satuan',
        'media'
    ];

    protected $casts = [
        'media' => 'array'
    ];

    public function buktiable(): MorphTo
    {
        return $this->morphTo();
    }
}