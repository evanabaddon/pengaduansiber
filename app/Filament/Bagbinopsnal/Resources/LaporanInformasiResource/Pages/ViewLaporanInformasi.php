<?php

namespace App\Filament\Bagbinopsnal\Resources\LaporanInformasiResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Bagbinopsnal\Resources\LaporanInformasiResource;

class ViewLaporanInformasi extends ViewRecord
{
    protected static string $resource = LaporanInformasiResource::class;

    protected static string $view = 'filament.resources.laporan.view';
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // ambil data pelapor beserta relasi dan data tambahan
        $pelapor = $this->record->pelapors;
        $data['pelapors'] = $pelapor->toArray();
        
        // Tambahkan data wilayah pelapor
        if ($pelapor) {
            $data['pelapors']['wilayah'] = [
                'utama' => [
                    'provinsi' => app('wilayah')->getProvinsi()[$pelapor->province_id] ?? '-',
                    'kabupaten' => app('wilayah')->getKabupaten($pelapor->province_id)[$pelapor->city_id] ?? '-',
                    'kecamatan' => app('wilayah')->getKecamatan($pelapor->city_id)[$pelapor->district_id] ?? '-',
                    'kelurahan' => app('wilayah')->getKelurahan($pelapor->district_id)[$pelapor->subdistrict_id] ?? '-',
                ],
                'kedua' => [
                    'provinsi' => app('wilayah')->getProvinsi()[$pelapor->province_id_2] ?? '-',
                    'kabupaten' => app('wilayah')->getKabupaten($pelapor->province_id_2)[$pelapor->city_id_2] ?? '-',
                    'kecamatan' => app('wilayah')->getKecamatan($pelapor->city_id_2)[$pelapor->district_id_2] ?? '-',
                    'kelurahan' => app('wilayah')->getKelurahan($pelapor->district_id_2)[$pelapor->subdistrict_id_2] ?? '-',
                ]
            ];
        }

        $dataTambahanPelapor = \App\Models\DataTambahan::where('recordable_type', 'App\\Models\\Pelapor')
            ->where('recordable_id', $pelapor->getKey())
            ->get();
        $data['pelapors']['data_tambahan'] = $dataTambahanPelapor->toArray();
        
        // ambil data korban yang bisa jadi lebih dari satu
        $korbans = $this->record->korbans;
        $data['korbans'] = [];
        
        foreach ($korbans as $korban) {
            $korbanData = $korban->toArray();
            
            // Tambahkan data wilayah ke array korban
            if ($korban) {
                $korbanData['wilayah'] = [
                    'utama' => [
                        'provinsi' => app('wilayah')->getProvinsi()[$korban->province_id] ?? '-',
                        'kabupaten' => app('wilayah')->getKabupaten($korban->province_id)[$korban->city_id] ?? '-',
                        'kecamatan' => app('wilayah')->getKecamatan($korban->city_id)[$korban->district_id] ?? '-',
                        'kelurahan' => app('wilayah')->getKelurahan($korban->district_id)[$korban->subdistrict_id] ?? '-',
                    ],
                    'kedua' => [
                        'provinsi' => app('wilayah')->getProvinsi()[$korban->province_id_2] ?? '-',
                        'kabupaten' => app('wilayah')->getKabupaten($korban->province_id_2)[$korban->city_id_2] ?? '-',
                        'kecamatan' => app('wilayah')->getKecamatan($korban->city_id_2)[$korban->district_id_2] ?? '-',
                        'kelurahan' => app('wilayah')->getKelurahan($korban->district_id_2)[$korban->subdistrict_id_2] ?? '-',
                    ]
                ];
            }

            // Tambahkan data tambahan
            $dataTambahanKorban = \App\Models\DataTambahan::where('recordable_type', 'App\\Models\\Korban')
                ->where('recordable_id', $korban->getKey())
                ->get();
            $korbanData['data_tambahan'] = $dataTambahanKorban->toArray();
            
            $data['korbans'][] = $korbanData;
        }

        // ambil data terlapor beserta relasi dan data tambahan
        $terlapor = $this->record->terlapors;
        $data['terlapors'] = $terlapor->toArray();

        // Tambahkan data wilayah ke array terlapor
        if ($terlapor) {
            $data['terlapors']['wilayah'] = [
                'utama' => [
                    'provinsi' => app('wilayah')->getProvinsi()[$terlapor->province_id] ?? '-',
                    'kabupaten' => app('wilayah')->getKabupaten($terlapor->province_id)[$terlapor->city_id] ?? '-',
                    'kecamatan' => app('wilayah')->getKecamatan($terlapor->city_id)[$terlapor->district_id] ?? '-',
                    'kelurahan' => app('wilayah')->getKelurahan($terlapor->district_id)[$terlapor->subdistrict_id] ?? '-',
                ],
                'kedua' => [
                    'provinsi' => app('wilayah')->getProvinsi()[$terlapor->province_id_2] ?? '-',
                    'kabupaten' => app('wilayah')->getKabupaten($terlapor->province_id_2)[$terlapor->city_id_2] ?? '-',
                    'kecamatan' => app('wilayah')->getKecamatan($terlapor->city_id_2)[$terlapor->district_id_2] ?? '-',
                    'kelurahan' => app('wilayah')->getKelurahan($terlapor->district_id_2)[$terlapor->subdistrict_id_2] ?? '-',
                ]
            ];
        }

        $dataTambahanTerlapor = \App\Models\DataTambahan::where('recordable_type', 'App\\Models\\Terlapor')
            ->where('recordable_id', $terlapor->getKey())
            ->get();
        $data['terlapors']['data_tambahan'] = $dataTambahanTerlapor->toArray();

        // wilayah tkp
        $data['wilayah_tkp'] = [
            'provinsi' => app('wilayah')->getProvinsi()[$this->record->province_id] ?? '-',
            'kabupaten' => app('wilayah')->getKabupaten($this->record->province_id)[$this->record->city_id] ?? '-',
            'kecamatan' => app('wilayah')->getKecamatan($this->record->city_id)[$this->record->district_id] ?? '-',
            'kelurahan' => app('wilayah')->getKelurahan($this->record->district_id)[$this->record->subdistrict_id] ?? '-',
        ];

        // ambil data laporaninformasis
        // dd($data);

        return $data;
    }
}
