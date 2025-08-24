<?php

namespace App\Filament\Subbagrenmin\Resources\UrmintuResource\Pages;

use App\Filament\Subbagrenmin\Resources\UrmintuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUrmintus extends ListRecords
{
    protected static string $resource = UrmintuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
