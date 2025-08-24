<?php

namespace App\Filament\Bagbinopsnal\Resources\LaporanInfoResource\Widgets;

use App\Models\LaporanInfo;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LaporanInfoStatusOverview extends BaseWidget
{

    protected function getStats(): array
    {
        return [ 
            Stat::make('Jumlah', LaporanInfo::query()
                    ->when(auth()->user()->subdit_id, function($query) {
                        $query->where('subdit_id', auth()->user()->subdit_id);
                        
                        if (auth()->user()->unit_id) {
                            $query->where('unit_id', auth()->user()->unit_id);
                        }
                    })
                    ->count())
                ->description('Laporan Informasi (LI)')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),

            Stat::make('Proses', LaporanInfo::query()
                    ->when(auth()->user()->subdit_id, function($query) {
                        $query->where('subdit_id', auth()->user()->subdit_id);
                        
                        if (auth()->user()->unit_id) {
                            $query->where('unit_id', auth()->user()->unit_id);
                        }
                    })
                    ->where('status', 'Proses')
                    ->count())
                ->description('Laporan Informasi (LI)')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),

            Stat::make('Terkendala', LaporanInfo::query()
                    ->when(auth()->user()->subdit_id, function($query) {
                        $query->where('subdit_id', auth()->user()->subdit_id);
                        
                        if (auth()->user()->unit_id) {
                            $query->where('unit_id', auth()->user()->unit_id);
                        }
                    })
                    ->where('status', 'Terkendala')
                    ->count())
                ->description('Laporan Informasi (LI)')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Selesai', LaporanInfo::query()
                    ->when(auth()->user()->subdit_id, function($query) {
                        $query->where('subdit_id', auth()->user()->subdit_id);
                        
                        if (auth()->user()->unit_id) {
                            $query->where('unit_id', auth()->user()->unit_id);
                        }
                    })
                    ->where('status', 'Selesai')
                    ->count())
                ->description('Laporan Informasi (LI')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
