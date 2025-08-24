<?php

namespace App\Filament\Bagbinopsnal\Resources\LaporanPolisiResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Bagbinopsnal\Resources\LaporanPolisiResource;
use App\Filament\Bagbinopsnal\Resources\LaporanPolisiResource\Widgets\LaporanPolisiStatusOverview;

class ListLaporanPolisis extends ListRecords
{
    protected static string $resource = LaporanPolisiResource::class;

    // coming soon
    public function getMaxContentWidth(): string
    {
        return 'full';
    }

    // buat view coming soon
    // protected static string $view = 'filament.pages.coming-soon';

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Buat Laporan Polisi')
                ->url(self::$resource::getUrl('create'))
                ->button(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LaporanPolisiStatusOverview::class,
        ];
    }
}
