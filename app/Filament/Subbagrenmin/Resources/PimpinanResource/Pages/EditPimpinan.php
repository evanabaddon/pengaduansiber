<?php

namespace App\Filament\Subbagrenmin\Resources\PimpinanResource\Pages;

use App\Filament\Subbagrenmin\Resources\PimpinanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPimpinan extends EditRecord
{
    protected static string $resource = PimpinanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
