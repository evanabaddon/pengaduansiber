<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use App\Models\LaporanInfo;
use App\Models\LaporanInformasi; 
use App\Models\LaporanPolisi;
use App\Models\Pengaduan;

class MapsWidget extends Widget
{
    protected static string $view = 'filament.widgets.maps';

    public function mount(): void
    {
        $this->dispatch('updateMap');
    }

    protected function getViewData(): array
    {
        return [
            'data' => $this->getData(),
        ];
    }

    protected function getProvinsiData()
    {
        // Baca data provinsi dari JSON
        $provinsiPath = public_path('data-indonesia/provinsi.json');
        $provinsiList = collect(json_decode(file_get_contents($provinsiPath), true))
            ->pluck('nama', 'id')
            ->toArray();

        // Mengambil data sebaran per provinsi dari semua jenis laporan
        $laporanInfo = LaporanInfo::select('province_id', DB::raw('count(*) as total'))
            ->groupBy('province_id')
            ->get();

        $laporanInformasi = LaporanInformasi::select('province_id', DB::raw('count(*) as total'))
            ->groupBy('province_id')
            ->get();

        $laporanPolisi = LaporanPolisi::select('province_id', DB::raw('count(*) as total'))
            ->groupBy('province_id')
            ->get();

        $pengaduan = Pengaduan::select('province_id', DB::raw('count(*) as total'))
            ->groupBy('province_id')
            ->get();

        // Menggabungkan data
        $provinsiData = [];
        foreach ($laporanInfo as $laporan) {
            $namaProvinsi = $provinsiList[$laporan->province_id] ?? 'Unknown';
            $provinsiData[$namaProvinsi]['LaporanInfo'] = $laporan->total;
        }
        foreach ($laporanInformasi as $laporan) {
            $namaProvinsi = $provinsiList[$laporan->province_id] ?? 'Unknown';
            $provinsiData[$namaProvinsi]['LaporanInformasi'] = $laporan->total;
        }
        foreach ($laporanPolisi as $laporan) {
            $namaProvinsi = $provinsiList[$laporan->province_id] ?? 'Unknown';
            $provinsiData[$namaProvinsi]['LaporanPolisi'] = $laporan->total;
        }
        foreach ($pengaduan as $laporan) {
            $namaProvinsi = $provinsiList[$laporan->province_id] ?? 'Unknown';
            $provinsiData[$namaProvinsi]['Pengaduan'] = $laporan->total;
        }

        // Inisialisasi nilai 0 untuk provinsi yang tidak memiliki laporan
        foreach ($provinsiList as $id => $nama) {
            if (!isset($provinsiData[$nama])) {
                $provinsiData[$nama] = [
                    'LaporanInfo' => 0,
                    'LaporanInformasi' => 0,
                    'LaporanPolisi' => 0,
                    'Pengaduan' => 0
                ];
            }
        }
        return $provinsiData;
    }

    public function getData()
    {
        return $this->getProvinsiData();
    }
}
