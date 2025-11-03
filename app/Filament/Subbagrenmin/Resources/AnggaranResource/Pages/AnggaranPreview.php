<?php

namespace App\Filament\Subbagrenmin\Resources\AnggaranResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Subbagrenmin\Resources\AnggaranResource;

class AnggaranPreview extends ViewRecord
{
    protected static string $resource = AnggaranResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $title = 'Lihat Anggaran';

    protected static string $view = 'filament.subbagrenmin.pages.anggaran-preview';

    public function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->url(route('filament.subbagrenmin.resources.anggarans.index')),  // arahkan ke index resource
        ];
    }
}
