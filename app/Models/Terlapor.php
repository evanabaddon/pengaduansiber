<?php

namespace App\Models;

use App\Models\DataTambahan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Terlapor extends Model
{
    protected $fillable = [
        'nama',
        'laporan_id',
        'laporan_informasi_id',
        'pengaduan_id',
        'nama',
        'jenis_kelamin',
        'kewarganegaraan',
        'agama',
        'pekerjaan',
        'alamat',
        'kontak',
        'usia',
        'tempat_lahir',
        'tanggal_lahir',
        'domestic',
        'province_id',
        'city_id',
        'district_id',
        'subdistrict_id',
        'identity_no',
        'alamat_2',
        'kontak_2',
        'province_id_2',
        'city_id_2',
        'district_id_2',
        'subdistrict_id_2',
        'data_tambahan',
    ];

    protected $casts = [
        'data_tambahan' => 'array'
    ];

    // Relasi ke Laporan
    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'laporan_id');
    }

    public function laporanInformasi()
    {
        return $this->belongsTo(LaporanInformasi::class, 'laporan_informasi_id');
    }

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'pengaduan_id');
    }

    public function dataTambahan(): MorphMany
    {
        return $this->morphMany(DataTambahan::class, 'recordable');
    }
}
