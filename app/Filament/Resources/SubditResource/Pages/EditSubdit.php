<?php

namespace App\Filament\Resources\SubditResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SubditResource;

class EditSubdit extends EditRecord
{
    protected static string $resource = SubditResource::class;

    // redirect ke halaman list setelah edit
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

