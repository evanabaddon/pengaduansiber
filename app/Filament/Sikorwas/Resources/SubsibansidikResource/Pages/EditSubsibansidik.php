<?php

namespace App\Filament\Sikorwas\Resources\SubsibansidikResource\Pages;

use App\Filament\Sikorwas\Resources\SubsibansidikResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubsibansidik extends EditRecord
{
    protected static string $resource = SubsibansidikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
