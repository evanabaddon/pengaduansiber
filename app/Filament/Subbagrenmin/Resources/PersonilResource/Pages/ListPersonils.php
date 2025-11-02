<?php

namespace App\Filament\Subbagrenmin\Resources\PersonilResource\Pages;

use App\Filament\Subbagrenmin\Resources\PersonilResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPersonils extends ListRecords
{
    protected static string $resource = PersonilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
