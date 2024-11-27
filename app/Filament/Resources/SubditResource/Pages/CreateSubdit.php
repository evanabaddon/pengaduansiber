<?php

namespace App\Filament\Resources\SubditResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\SubditResource;

class CreateSubdit extends CreateRecord
{
    protected static string $resource = SubditResource::class;

    // redirect ke halaman list setelah create
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

