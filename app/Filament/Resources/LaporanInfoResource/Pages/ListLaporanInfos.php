<?php

namespace App\Filament\Resources\LaporanInfoResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\LaporanInfoResource;
use App\Filament\Resources\LaporanInfoResource\Widgets\LaporanInfoStatusOverview;

class ListLaporanInfos extends ListRecords
{
    protected static string $resource = LaporanInfoResource::class;

    // coming soon
    public function getMaxContentWidth(): string
    {
        return 'full';
    }

    // buat view coming soon
    // protected static string $view = 'filament.pages.coming-soon';

    protected function getHeaderWidgets(): array
    {
        if (auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('subdit')) {
            return [
                LaporanInfoStatusOverview::class,
            ];
        } else {
            return [];
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Buat Laporan Informasi')
                ->url(self::$resource::getUrl('create'))
                ->button(),
        ];
    }
}
