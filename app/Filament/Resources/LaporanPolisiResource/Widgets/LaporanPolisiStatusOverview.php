<?php

namespace App\Filament\Resources\LaporanPolisiResource\Widgets;

use App\Models\LaporanPolisi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LaporanPolisiStatusOverview extends BaseWidget
{

    protected function getStats(): array
    {
        return [ 
            Stat::make('Jumlah', LaporanPolisi::query()
                    ->when(auth()->user()->subdit_id, function($query) {
                        $query->where('subdit_id', auth()->user()->subdit_id);
                        
                        if (auth()->user()->unit_id) {
                            $query->where('unit_id', auth()->user()->unit_id);
                        }
                    })
                    ->count())
                ->description('Laporan Polisi (LP)')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),

            Stat::make('Proses', LaporanPolisi::query()
                    ->when(auth()->user()->subdit_id, function($query) {
                        $query->where('subdit_id', auth()->user()->subdit_id);
                        
                        if (auth()->user()->unit_id) {
                            $query->where('unit_id', auth()->user()->unit_id);
                        }
                    })
                    ->where('status', 'Proses')
                    ->count())
                ->description('Laporan Polisi (LP)')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),

            Stat::make('Terkendala', LaporanPolisi::query()
                    ->when(auth()->user()->subdit_id, function($query) {
                        $query->where('subdit_id', auth()->user()->subdit_id);
                        
                        if (auth()->user()->unit_id) {
                            $query->where('unit_id', auth()->user()->unit_id);
                        }
                    })
                    ->where('status', 'Terkendala')
                    ->count())
                ->description('Laporan Polisi (LP)')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Selesai', LaporanPolisi::query()
                    ->when(auth()->user()->subdit_id, function($query) {
                        $query->where('subdit_id', auth()->user()->subdit_id);
                        
                        if (auth()->user()->unit_id) {
                            $query->where('unit_id', auth()->user()->unit_id);
                        }
                    })
                    ->where('status', 'Selesai')
                    ->count())
                ->description('Laporan Polisi (LP)')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
