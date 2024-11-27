<?php

namespace App\Filament\Resources\LaporanInfoResource\Pages;

use App\Filament\Resources\LaporanInfoResource;
use Filament\Actions;
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

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
}
