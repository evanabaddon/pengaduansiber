<?php

namespace App\Filament\Resources\UnitResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\UnitResource;

class EditUnit extends EditRecord
{
    protected static string $resource = UnitResource::class;

    // redirect ke halaman list setelah edit
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}


