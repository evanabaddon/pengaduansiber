<?php

namespace App\Filament\Resources\PenyidikResource\Pages;

use Filament\Actions;
use App\Models\Penyidik;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PenyidikResource;

class CreatePenyidik extends CreateRecord
{
    protected static string $resource = PenyidikResource::class;

    protected static bool $canCreateAnother = false;

    // title
    public function getTitle(): string
    {
        return 'Tambah Penyidik / Penyidik Pembantu';
    }

    // fungsi handle record creation
    // public function handleRecordCreation(array $data): Penyidik
    // {
    //     $data['unit_id'] = auth()->user()->unit_id;

    //     $data['subdit_id'] = auth()->user()->subdit_id;

    //     return parent::handleRecordCreation($data);
    // }

    // redirect ke halaman list setelah create
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
