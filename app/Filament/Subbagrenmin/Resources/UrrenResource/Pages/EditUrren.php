<?php

namespace App\Filament\Subbagrenmin\Resources\UrrenResource\Pages;

use App\Filament\Subbagrenmin\Resources\UrrenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUrren extends EditRecord
{
    protected static string $resource = UrrenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
