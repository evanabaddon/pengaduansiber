<?php

namespace App\Filament\Widgets;


use App\Models\Pengaduan;
use App\Models\LaporanInfo;
use App\Models\LaporanPolisi;
use App\Models\LaporanInformasi;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class AllLaporanWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Ambil data dari LaporanInformasi setiap bulan
        $laporanInformasi = DB::table('laporan_informasis')
        ->select(
            DB::raw('MONTH(tanggal_lapor) as month'), 
            DB::raw('YEAR(tanggal_lapor) as year'),
            DB::raw('count(*) as total')
        )
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        // Buat array dengan 12 bulan yang diisi 0
        $monthlyLaporanInformasi = array_fill(0, 12, 0);

        // Generate data untuk 12 bulan terakhir
        for ($i = 0; $i < 12; $i++) {
        $date = now()->subMonths(11 - $i);
        $monthKey = $date->format('n');
        $yearKey = $date->format('Y');

        // Cari data untuk bulan ini
        $monthData = $laporanInformasi->first(function($item) use ($monthKey, $yearKey) {
            return $item->month == $monthKey && $item->year == $yearKey;
        });

        $monthlyLaporanInformasi[$i] = $monthData ? $monthData->total : 0;
        }

        // Ambil data dari Pengaduan setiap bulan
        $laporanPengaduan = DB::table('pengaduans')
        ->select(
            DB::raw('MONTH(tanggal_lapor) as month'),
            DB::raw('YEAR(tanggal_lapor) as year'),
            DB::raw('count(*) as total')
        )
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        // Buat array dengan 12 bulan yang diisi 0
        $monthlyPengaduan = array_fill(0, 12, 0);

        // Generate data untuk 12 bulan terakhir
        for ($i = 0; $i < 12; $i++) {
        $date = now()->subMonths(11 - $i);
        $monthKey = $date->format('n');
        $yearKey = $date->format('Y');

        // Cari data untuk bulan ini
        $monthData = $laporanPengaduan->first(function($item) use ($monthKey, $yearKey) {
            return $item->month == $monthKey && $item->year == $yearKey;
        });

        $monthlyPengaduan[$i] = $monthData ? $monthData->total : 0;
        }

        // Ambil data dari LaporanInfo setiap bulan
        $laporanInfo = DB::table('laporan_infos')
        ->select(
            DB::raw('MONTH(tanggal_lapor) as month'),
            DB::raw('YEAR(tanggal_lapor) as year'),
            DB::raw('count(*) as total')
        )
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();
        
        // Buat array dengan 12 bulan yang diisi 0
        $monthlyInfo = array_fill(0, 12, 0);

        // Generate data untuk 12 bulan terakhir
        for ($i = 0; $i < 12; $i++) {
        $date = now()->subMonths(11 - $i);
        $monthKey = $date->format('n');
        $yearKey = $date->format('Y');

        // Cari data untuk bulan ini
        $monthData = $laporanInfo->first(function($item) use ($monthKey, $yearKey) {
            return $item->month == $monthKey && $item->year == $yearKey;
        });

        $monthlyInfo[$i] = $monthData ? $monthData->total : 0;
        }

        
        // Ambil data dari LaporanPolisi setiap bulan
        $laporanPolisi = DB::table('laporan_polisis')
        ->select(
            DB::raw('MONTH(tanggal_lapor) as month'),
            DB::raw('YEAR(tanggal_lapor) as year'),
            DB::raw('count(*) as total')
        )
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        // Buat array dengan 12 bulan yang diisi 0
        $monthlyPolisi = array_fill(0, 12, 0);

        // Generate data untuk 12 bulan terakhir
        for ($i = 0; $i < 12; $i++) {
        $date = now()->subMonths(11 - $i);
        $monthKey = $date->format('n');
        $yearKey = $date->format('Y');  

        // Cari data untuk bulan ini
        $monthData = $laporanPolisi->first(function($item) use ($monthKey, $yearKey) {
            return $item->month == $monthKey && $item->year == $yearKey;
        });

        $monthlyPolisi[$i] = $monthData ? $monthData->total : 0;
        }

        

        return [
            Stat::make('Jumlah Total', LaporanInformasi::query()->count())
                ->description('Informasi / Surat Masyarakat (Dumas) ')
                ->descriptionIcon('heroicon-m-document-text')
                ->chart(
                    $monthlyLaporanInformasi
                )
                ->color('success'),

            Stat::make('Jumlah Total', Pengaduan::query()->count())
                ->description('Laporan / Pengaduan Masyarakat (LPM)')
                ->descriptionIcon('heroicon-m-document-text')
                ->chart(
                    $monthlyPengaduan
                )
                ->color('warning'),

            Stat::make('Jumlah Total', LaporanInfo::query()->count())
                ->description('Laporan Informasi (LI)')
                ->descriptionIcon('heroicon-m-document-text')
                ->chart(
                    $monthlyInfo
                )
                ->color('info'),

            Stat::make('Jumlah Total', LaporanPolisi::query()->count())
                ->description('Laporan Polisi (LP)')
                ->descriptionIcon('heroicon-m-document-text')
                ->chart(
                    $monthlyPolisi
                )
                ->color('danger'),
        ];
    }
}
