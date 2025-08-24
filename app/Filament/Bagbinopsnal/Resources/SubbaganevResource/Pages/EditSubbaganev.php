<?php

namespace App\Filament\Bagbinopsnal\Resources\SubbaganevResource\Pages;

use App\Filament\Bagbinopsnal\Resources\SubbaganevResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubbaganev extends EditRecord
{
    protected static string $resource = SubbaganevResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
