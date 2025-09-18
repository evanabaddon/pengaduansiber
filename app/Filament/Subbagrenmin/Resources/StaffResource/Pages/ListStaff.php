<?php

namespace App\Filament\Subbagrenmin\Resources\StaffResource\Pages;

use App\Filament\Subbagrenmin\Resources\StaffResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStaff extends ListRecords
{
    protected static string $resource = StaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
