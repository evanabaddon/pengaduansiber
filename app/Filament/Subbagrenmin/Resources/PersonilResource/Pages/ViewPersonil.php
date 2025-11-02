<?php

namespace App\Filament\Subbagrenmin\Resources\PersonilResource\Pages;

use App\Filament\Subbagrenmin\Resources\PersonilResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPersonil extends ViewRecord
{
    protected static string $resource = PersonilResource::class;

    protected static string $view = 'filament.subbagrenmin.pages.view-personil';


    public function getTitle(): string
    {
        return 'Detail Personil: ' . $this->record->nama;
    }
}
