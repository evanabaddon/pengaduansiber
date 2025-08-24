<?php

namespace App\Filament\Subbagrenmin\Resources\UrkeuResource\Pages;

use App\Filament\Subbagrenmin\Resources\UrkeuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUrkeu extends EditRecord
{
    protected static string $resource = UrkeuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
