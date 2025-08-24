<?php

namespace App\Filament\Bagbinopsnal\Resources\PengaduanResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Bagbinopsnal\Resources\PengaduanResource;
use App\Filament\Bagbinopsnal\Resources\PengaduanResource\Widgets\PengaduanStatusOverview;

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

    protected function getHeaderWidgets(): array
    {
        return [
            PengaduanStatusOverview::class,
        ];
    }
}
