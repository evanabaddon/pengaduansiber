<?php

namespace App\Filament\Bagbinopsnal\Resources\SubbagminopsnalResource\Pages;

use App\Filament\Bagbinopsnal\Resources\SubbagminopsnalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubbagminopsnals extends ListRecords
{
    protected static string $resource = SubbagminopsnalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
