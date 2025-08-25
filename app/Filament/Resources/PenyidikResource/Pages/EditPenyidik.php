<?php

namespace App\Filament\Resources\PenyidikResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\PenyidikResource;

class EditPenyidik extends EditRecord
{
    protected static string $resource = PenyidikResource::class;

    // title
    public function getTitle(): string
    {
        return 'Ubah Penyidik / Penyidik Pembantu';
    }

    // redirect ke halaman list setelah edit
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
