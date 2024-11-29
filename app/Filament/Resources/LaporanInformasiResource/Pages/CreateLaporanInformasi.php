<?php

namespace App\Filament\Resources\LaporanInformasiResource\Pages;

use Filament\Actions;
use App\Models\Korban;
use App\Models\Pelapor;
use App\Models\Terlapor;
use App\Models\LaporanInformasi;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\LaporanInformasiResource;

class CreateLaporanInformasi extends CreateRecord
{
    protected static string $resource = LaporanInformasiResource::class;

    protected static bool $canCreateAnother = false;

    // redirect ke halaman list setelah create
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function handleRecordCreation(array $data): LaporanInformasi
    {
        // dd($data);

        // default value status adalah 'Terlapor'
        $data['status'] = 'Proses';


        // jika yang input usernya memliki subdit_id dan atau memiliki unit_id maka inject subdit_id dan unit_id ke data
        if (auth()->user()->subdit_id) {
            $data['subdit_id'] = auth()->user()->subdit_id;
        }
        if (auth()->user()->unit_id) {
            $data['unit_id'] = auth()->user()->unit_id;
        }

        // dd($data);

        // Step 1: Simpan data utama LaporanInformasi
        $laporanInformasi = LaporanInformasi::create($data);

        // dd($laporanInformasi);

        
        // Step 2: Simpan data Pelapor
        Pelapor::create([
            'laporan_informasi_id' => $laporanInformasi->id,
            'identity_no' => $data['pelapors']['identity_no'],
            'usia' => $data['pelapors']['usia'],
            'nama' => $data['pelapors']['nama'],
            'tempat_lahir' => $data['pelapors']['tempat_lahir'],
            'tanggal_lahir' => $data['pelapors']['tanggal_lahir'],
            'jenis_kelamin' => $data['pelapors']['jenis_kelamin'],
            'pekerjaan' => $data['pelapors']['pekerjaan'],
            'kontak' => $data['pelapors']['kontak'],
            // 'domestic' => $data['pelapors']['domestic'],
            'province_id' => $data['pelapors']['province_id'] ?? null,
            'city_id' => $data['pelapors']['city_id'] ?? null,
            'district_id' => $data['pelapors']['district_id'] ?? null,
            'subdistrict_id' => $data['pelapors']['subdistrict_id'] ?? null,
            'alamat' => $data['pelapors']['alamat'],
        ]);

        // Step 3: Simpan data Korban 
        Korban::create([
            'laporan_informasi_id' => $laporanInformasi->id,
            'identity_no' => $data['korbans']['identity_no'],
            'nama' => $data['korbans']['nama'],
            'kontak' => $data['korbans']['kontak'],
            'usia' => $data['korbans']['usia'],
            'tempat_lahir' => $data['korbans']['tempat_lahir'],
            'tanggal_lahir' => $data['korbans']['tanggal_lahir'],
            'jenis_kelamin' => $data['korbans']['jenis_kelamin'],
            'pekerjaan' => $data['korbans']['pekerjaan'],
            // 'domestic' => $data['korbans']['domestic'],
            'province_id' => $data['korbans']['province_id'] ?? null,
            'city_id' => $data['korbans']['city_id'] ?? null,
            'district_id' => $data['korbans']['district_id'] ?? null,
            'subdistrict_id' => $data['korbans']['subdistrict_id'] ?? null,
            'alamat' => $data['korbans']['alamat'],

        ]);

        // jika nama terlapor tidak diisi maka isi dengan null
        if ($data['terlapors']['nama'] == '') {
            $data['terlapors']['nama'] = 'null';
        }

        // Step 4: Simpan data Terlapor
        Terlapor::create([
            'laporan_informasi_id' => $laporanInformasi->id,
            'identity_no' => $data['terlapors']['identity_no'],
            'nama' => $data['terlapors']['nama'],
            'kontak' => $data['terlapors']['kontak'],
            'usia' => $data['terlapors']['usia'],
            'jenis_kelamin' => $data['terlapors']['jenis_kelamin'],
            // 'domestic' => $data['terlapors']['domestic'],
            'province_id' => $data['terlapors']['province_id'] ?? null,
            'city_id' => $data['terlapors']['city_id'] ?? null,
            'district_id' => $data['terlapors']['district_id'] ?? null,
            'subdistrict_id' => $data['terlapors']['subdistrict_id'] ?? null,
            'alamat' => $data['terlapors']['alamat'],
        ]);

        // Step 5: Update data laporan dengan pelapor, korban, dan terlapor jika diperlukan
        return $laporanInformasi;
    }

    // hidden save button
    protected function getFormActions(): array
    {
        return [
            //$this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }
}
