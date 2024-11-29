<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Terlapor extends Model
{
    protected $fillable = [
        'nama',
        'laporan_id',
        'laporan_informasi_id',
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

    // Relasi ke Laporan
    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'laporan_id');
    }

    public function laporanInformasi()
    {
        return $this->belongsTo(LaporanInformasi::class, 'laporan_informasi_id');
    }
}
