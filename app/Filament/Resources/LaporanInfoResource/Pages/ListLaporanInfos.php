<?php

namespace App\Filament\Resources\LaporanInfoResource\Pages;

use App\Filament\Resources\LaporanInfoResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListLaporanInfos extends ListRecords
{
    protected static string $resource = LaporanInfoResource::class;

    // coming soon
    public function getMaxContentWidth(): string
    {
        return 'full';
    }

    // buat view coming soon
    protected static string $view = 'filament.pages.coming-soon';

    protected function getHeaderWidgets(): array
    {
        if (auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('subdit')) {
            return [
                // LaporanInformasiStatusOverview::class,
            ];
        } else {
            return [];
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Buat Laporan')
                ->url(self::$resource::getUrl('create'))
                ->button(),
        ];
    }
}
