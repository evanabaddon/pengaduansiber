<?php

namespace App\Filament\Sikorwas\Resources\SubsibansidikResource\Pages;

use App\Filament\Sikorwas\Resources\SubsibansidikResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubsibansidiks extends ListRecords
{
    protected static string $resource = SubsibansidikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
