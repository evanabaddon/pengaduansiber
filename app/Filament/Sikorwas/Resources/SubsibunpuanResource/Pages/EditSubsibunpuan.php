<?php

namespace App\Filament\Sikorwas\Resources\SubsibunpuanResource\Pages;

use App\Filament\Sikorwas\Resources\SubsibunpuanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubsibunpuan extends EditRecord
{
    protected static string $resource = SubsibunpuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
