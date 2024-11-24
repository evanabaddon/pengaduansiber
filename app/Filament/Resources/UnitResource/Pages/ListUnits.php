<?php

namespace App\Filament\Resources\UnitResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\UnitResource;

class ListUnits extends ListRecords
{
    protected static string $resource = UnitResource::class;

    public function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Unit'),
        ];
    }
}


