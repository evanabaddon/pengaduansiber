<?php

namespace App\Filament\Resources\LaporanInfoResource\Pages;

use App\Filament\Resources\LaporanInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanInfo extends EditRecord
{
    protected static string $resource = LaporanInfoResource::class;

    // dd semua data termasuk data tambahan
    protected function mutateFormDataBeforeFill(array $data): array
    {
        dd($data);
        return $data;
    }

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
