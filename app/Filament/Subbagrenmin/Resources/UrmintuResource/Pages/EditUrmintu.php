<?php

namespace App\Filament\Subbagrenmin\Resources\UrmintuResource\Pages;

use App\Filament\Subbagrenmin\Resources\UrmintuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUrmintu extends EditRecord
{
    protected static string $resource = UrmintuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
