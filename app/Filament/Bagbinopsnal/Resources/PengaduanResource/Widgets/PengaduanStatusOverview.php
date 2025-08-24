<?php

namespace App\Filament\Bagbinopsnal\Resources\PengaduanResource\Widgets;

use App\Models\Pengaduan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PengaduanStatusOverview extends BaseWidget
{

    protected function getStats(): array
    {
        return [ 
            Stat::make('Jumlah', Pengaduan::query()
                    ->when(auth()->user()->subdit_id, function($query) {
                        $query->where('subdit_id', auth()->user()->subdit_id);
                        
                        if (auth()->user()->unit_id) {
                            $query->where('unit_id', auth()->user()->unit_id);
                        }
                    })
                    ->count())
                ->description('Laporan / Pengaduan Masyarakat (LPM)')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),

            Stat::make('Proses', Pengaduan::query()
                    ->when(auth()->user()->subdit_id, function($query) {
                        $query->where('subdit_id', auth()->user()->subdit_id);
                        
                        if (auth()->user()->unit_id) {
                            $query->where('unit_id', auth()->user()->unit_id);
                        }
                    })
                    ->where('status', 'Proses')
                    ->count())
                ->description('Laporan / Pengaduan Masyarakat (LPM)')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),

            Stat::make('Terkendala', Pengaduan::query()
                    ->when(auth()->user()->subdit_id, function($query) {
                        $query->where('subdit_id', auth()->user()->subdit_id);
                        
                        if (auth()->user()->unit_id) {
                            $query->where('unit_id', auth()->user()->unit_id);
                        }
                    })
                    ->where('status', 'Terkendala')
                    ->count())
                ->description('Laporan / Pengaduan Masyarakat (LPM)')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Selesai', Pengaduan::query()
                    ->when(auth()->user()->subdit_id, function($query) {
                        $query->where('subdit_id', auth()->user()->subdit_id);
                        
                        if (auth()->user()->unit_id) {
                            $query->where('unit_id', auth()->user()->unit_id);
                        }
                    })
                    ->where('status', 'Selesai')
                    ->count())
                ->description('Laporan / Pengaduan Masyarakat (LPM)')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
