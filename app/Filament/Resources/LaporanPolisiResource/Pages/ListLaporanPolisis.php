<?php

namespace App\Filament\Resources\LaporanPolisiResource\Pages;

use App\Filament\Resources\LaporanPolisiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

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

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
