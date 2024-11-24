<?php

namespace App\Filament\Resources\PenyidikResource\Pages;

use Filament\Actions;
use App\Models\Penyidik;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PenyidikResource;

class CreatePenyidik extends CreateRecord
{
    protected static string $resource = PenyidikResource::class;

    // fungsi handle record creation
    public function handleRecordCreation(array $data): Penyidik
    {
        $data['unit_id'] = auth()->user()->unit_id;

        $data['subdit_id'] = auth()->user()->subdit_id;

        return parent::handleRecordCreation($data);
    }
}
