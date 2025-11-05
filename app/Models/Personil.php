<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Personil extends Model
{
    protected $casts = [
        'pasangan' => 'array',
        'keluarga' => 'array',
        'pendidikan_polri' => 'array',
        'pendidikan_umum' => 'array',
        'riwayat_pangkat' => 'array',
        'riwayat_jabatan' => 'array',
        'dikbang_pelatihan' => 'array',
        'tanda_kehormatan' => 'array',
        'kemampuan_bahasa' => 'array',
        'penugasan_ln' => 'array',
    ];

    protected $fillable = [
        'nama', 'pangkat', 'nrp', 'jabatan', 'tmt', 'tempat_lahir', 'tanggal_lahir',
        'golongan_darah', 'agama', 'suku', 'alamat', 'province_id', 'city_id',
        'district_id', 'subdistrict_id', 'telp', 'bpjs', 'nomor_bpjs',
        'pasangan', 'keluarga', 'pendidikan_polri', 'pendidikan_umum',
        'riwayat_pangkat', 'riwayat_jabatan', 'dikbang_pelatihan',
        'tanda_kehormatan', 'kemampuan_bahasa', 'penugasan_ln',
        'photo', 'status', 'klaster_jabatan_id'
    ];

    public function getPangkatLabel($key)
    {
        $options = [
            1 => 'KOMBESPOL',
            2 => 'AKBP',
            3 => 'KOMPOL',
            4 => 'AKP',
            5 => 'IPTU',
            6 => 'IPDA',
            7 => 'AIPTU',
            8 => 'AIPDA',
            9 => 'BRIPKA',
            10 => 'BRIGPOL',
            11 => 'BRIPTU',
            12 => 'BRIPDA',
            13 => 'STAFF',
        ];
        return $options[$key] ?? '-';
    }


    // 🧠 Ambil pangkat terakhir berdasarkan TMT
    protected function pangkatTerakhir(): Attribute
    {
        return Attribute::get(function () {
            $list = collect($this->riwayat_pangkat ?? []);

            // Ambil record dengan TMT terbaru
            $latest = $list->sortByDesc(fn ($item) => $item['tmt'] ?? null)->first();

            if (! $latest) {
                return '-';
            }

            // Mapping ID ke nama pangkat
            $pangkatList = [
                1 => 'KOMBESPOL',
                2 => 'AKBP',
                3 => 'KOMPOL',
                4 => 'AKP',
                5 => 'IPTU',
                6 => 'IPDA',
                7 => 'AIPTU',
                8 => 'AIPDA',
                9 => 'BRIPKA',
                10 => 'BRIGPOL',
                11 => 'BRIPTU',
                12 => 'BRIPDA',
                13 => 'STAFF',
            ];

            // Ambil nama pangkat dari ID
            return $pangkatList[$latest['pangkat']] ?? '-';
        });
    }

    // 🧠 Ambil jabatan terakhir berdasarkan TMT
    protected function jabatanTerakhir(): Attribute
    {
        return Attribute::get(function () {
            $list = collect($this->riwayat_jabatan ?? []);
            return $list->sortByDesc(fn ($item) => $item['tmt'] ?? null)->first()['jabatan'] ?? '-';
        });
    }

    // 🧠 Ambil TMT Jabatan Terakhir berdasarkan riwayat
    protected function tmtJabatanTerakhir(): Attribute
    {
        return Attribute::get(function () {
            $list = collect($this->riwayat_jabatan ?? []);
            return $list->sortByDesc(fn($item) => $item['tmt'] ?? null)
                        ->first()['tmt'] ?? null;
        });
    }

    // 🧭 Lama Jabatan Terakhir (format: 0 TAHUN 9 BULAN 28 HARI)
    protected function lamaJabatanTerakhir(): Attribute
    {
        return Attribute::get(function () {
            $list = collect($this->riwayat_jabatan ?? []);

            $jabatanTerakhir = $list
                ->sortByDesc(fn($item) => $item['tmt'] ?? null)
                ->first();

            if (empty($jabatanTerakhir['tmt'])) {
                return '-';
            }

            $tmt = \Carbon\Carbon::parse($jabatanTerakhir['tmt']);
            $now = \Carbon\Carbon::now();

            // Gunakan diff untuk dapatkan y, m, d (tahun, bulan, hari)
            $diff = $tmt->diff($now);

            $tahun = $diff->y ?? 0;
            $bulan = $diff->m ?? 0;
            $hari = $diff->d ?? 0;

            // Format dalam huruf besar dan rapi
            return sprintf("%d TAHUN %d BULAN %d HARI", $tahun, $bulan, $hari);
        });
    }


    // Usia personil
    protected function usia(): Attribute
    {
        return Attribute::get(function () {
            return Carbon::parse($this->tanggal_lahir)->age;
        });
    }

    // Usia Pasangan
    protected function usiaPasangan(): Attribute
    {
        return Attribute::get(function () {
            return Carbon::parse($this->pasangan['tanggal_lahir'] ?? null)->age;
        });
    }

    // Usia Anak
    protected function usiaAnak(): Attribute
    {
        return Attribute::get(function () {
            return Carbon::parse($this->keluarga['tanggal_lahir'] ?? null)->age;
        });
    }

    public function klasterJabatan() : BelongsTo
    {
        return $this->belongsTo(KlasterJabatan::class, 'klaster_jabatan_id');
    }

}
