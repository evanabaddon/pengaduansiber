<?php

namespace App\Filament\Subbagrenmin\Resources\SubditResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Subbagrenmin\Resources\SubditResource;

class CreateSubdit extends CreateRecord
{
    protected static string $resource = SubditResource::class;

    protected static bool $canCreateAnother = false;

    // redirect ke halaman list setelah create
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

