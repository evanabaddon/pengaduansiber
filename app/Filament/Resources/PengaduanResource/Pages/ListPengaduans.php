<?php

namespace App\Filament\Resources\PengaduanResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PengaduanResource;

class ListPengaduans extends ListRecords
{
    protected static string $resource = PengaduanResource::class;

    // buat view coming soon
    // protected static string $view = 'filament.pages.coming-soon';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Buat Pengaduan')
                ->url(self::$resource::getUrl('create'))
                ->button(),
        ];
    }
}
