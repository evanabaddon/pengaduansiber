<?php

namespace App\Filament\Resources\LaporanResource\Widgets;

use App\Models\Laporan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LaporanStatusOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [ 
            Stat::make('Terlapor', Laporan::query()
                    ->when(auth()->user()->hasRole('subdit'), fn($q) => $q->where('subdit_id', auth()->user()->subdit_id))
                    ->when(auth()->user()->hasRole('unit'), fn($q) => $q->where('unit_id', auth()->user()->unit_id))
                    ->when(auth()->user()->hasRole('penyidik'), fn($q) => $q->where('penyidik_id', auth()->user()->penyidik_id))
                    ->where('status', 'Terlapor')
                    ->count())
                ->description('Total laporan masyarakat status terlapor')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),

            Stat::make('Proses', Laporan::query()
                    ->when(auth()->user()->hasRole('subdit'), fn($q) => $q->where('subdit_id', auth()->user()->subdit_id))
                    ->when(auth()->user()->hasRole('unit'), fn($q) => $q->where('unit_id', auth()->user()->unit_id))
                    ->when(auth()->user()->hasRole('penyidik'), fn($q) => $q->where('penyidik_id', auth()->user()->penyidik_id))
                    ->where('status', 'Proses')
                    ->count())
                ->description('Total laporan masyarakat dalam proses')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),

            Stat::make('Selesai', Laporan::query()
                    ->when(auth()->user()->hasRole('subdit'), fn($q) => $q->where('subdit_id', auth()->user()->subdit_id))
                    ->when(auth()->user()->hasRole('unit'), fn($q) => $q->where('unit_id', auth()->user()->unit_id))
                    ->when(auth()->user()->hasRole('penyidik'), fn($q) => $q->where('penyidik_id', auth()->user()->penyidik_id))
                    ->where('status', 'Selesai')
                    ->count())
                ->description('Total laporan masyarakat selesai')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Terkendala', Laporan::query()
                    ->when(auth()->user()->hasRole('subdit'), fn($q) => $q->where('subdit_id', auth()->user()->subdit_id))
                    ->when(auth()->user()->hasRole('unit'), fn($q) => $q->where('unit_id', auth()->user()->unit_id))
                    ->when(auth()->user()->hasRole('penyidik'), fn($q) => $q->where('penyidik_id', auth()->user()->penyidik_id))
                    ->where('status', 'Terkendala')
                    ->count())
                ->description('Total laporan masyarakat terkendala')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
