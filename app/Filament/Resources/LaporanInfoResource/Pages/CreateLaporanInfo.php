<?php

namespace App\Filament\Resources\LaporanInfoResource\Pages;

use App\Filament\Resources\LaporanInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLaporanInfo extends CreateRecord
{
    protected static string $resource = LaporanInfoResource::class;

    protected static bool $canCreateAnother = false;
}
