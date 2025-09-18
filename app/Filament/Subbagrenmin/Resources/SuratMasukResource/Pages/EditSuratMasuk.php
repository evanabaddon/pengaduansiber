<?php

namespace App\Filament\Subbagrenmin\Resources\SuratMasukResource\Pages;

use App\Filament\Subbagrenmin\Resources\SuratMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuratMasuk extends EditRecord
{
    protected static string $resource = SuratMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
