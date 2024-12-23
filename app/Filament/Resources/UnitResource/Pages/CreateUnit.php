<?php

namespace App\Filament\Resources\UnitResource\Pages;

use App\Models\Unit;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UnitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUnit extends CreateRecord
{
    protected static string $resource = UnitResource::class;

    protected static bool $canCreateAnother = false;

    // fungsi handle record creation
    // public function handleRecordCreation(array $data): Unit
    // {
    //     $data['subdit_id'] = auth()->user()->subdit_id;

    //     // dd($data);

    //     return parent::handleRecordCreation($data);
    // }

    // redirect ke halaman list setelah create
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
