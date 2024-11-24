<?php

namespace App\Filament\Resources\SubditResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\SubditResource;

class ListSubdits extends ListRecords
{
    protected static string $resource = SubditResource::class;

    public function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Subdit'),
        ];
    }
}

