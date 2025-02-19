<?php

namespace App\Filament\Widgets;


use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Pengaduan;
use App\Models\LaporanInfo;
use App\Models\LaporanPolisi;
use App\Models\LaporanInformasi;

class AllLaporanWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Jumlah Total', LaporanInformasi::query()->count())
                ->description('Informasi / Surat Masyarakat (Dumas) ')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),

            Stat::make('Jumlah Total', Pengaduan::query()->count())
                ->description('Laporan / Pengaduan Masyarakat (LPM)')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),

            Stat::make('Jumlah Total', LaporanInfo::query()->count())
                ->description('Laporan Informasi (LI)')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('Jumlah Total', LaporanPolisi::query()->count())
                ->description('Laporan Polisi (LP)')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('danger'),
        ];
    }
}
