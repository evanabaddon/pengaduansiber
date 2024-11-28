<?php

namespace App\Filament\Resources\LaporanPolisiResource\Pages;

use App\Filament\Resources\LaporanPolisiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanPolisi extends EditRecord
{
    protected static string $resource = LaporanPolisiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // redirect after edit
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }   
}
