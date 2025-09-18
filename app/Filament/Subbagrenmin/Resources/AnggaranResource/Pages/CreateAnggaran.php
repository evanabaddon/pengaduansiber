<?php

namespace App\Filament\Subbagrenmin\Resources\AnggaranResource\Pages;

use App\Filament\Subbagrenmin\Resources\AnggaranResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAnggaran extends CreateRecord
{
    protected static string $resource = AnggaranResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
