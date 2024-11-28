<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\UserResource;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected static bool $canCreateAnother = false;

    // redirect after create
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

