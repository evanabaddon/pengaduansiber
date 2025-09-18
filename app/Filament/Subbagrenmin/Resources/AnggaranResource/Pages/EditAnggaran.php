<?php

namespace App\Filament\Subbagrenmin\Resources\AnggaranResource\Pages;

use App\Filament\Subbagrenmin\Resources\AnggaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnggaran extends EditRecord
{
    protected static string $resource = AnggaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
