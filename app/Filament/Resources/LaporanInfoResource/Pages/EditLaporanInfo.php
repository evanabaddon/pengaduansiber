<?php

namespace App\Filament\Resources\LaporanInfoResource\Pages;

use App\Filament\Resources\LaporanInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanInfo extends EditRecord
{
    protected static string $resource = LaporanInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
