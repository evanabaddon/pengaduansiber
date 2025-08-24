<?php

namespace App\Filament\Subbagrenmin\Resources\UrrenResource\Pages;

use App\Filament\Subbagrenmin\Resources\UrrenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUrrens extends ListRecords
{
    protected static string $resource = UrrenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
