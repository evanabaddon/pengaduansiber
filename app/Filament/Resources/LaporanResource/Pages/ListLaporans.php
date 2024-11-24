<?php

namespace App\Filament\Resources\LaporanResource\Pages;

use Illuminate\Support\Facades\Blade;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\View\TablesRenderHook;
use App\Filament\Resources\LaporanResource;
use App\Filament\Resources\LaporanResource\Widgets\LaporanStatusOverview;

class ListLaporans extends ListRecords
{
    protected static string $resource = LaporanResource::class;

    protected function getHeaderWidgets(): array
    {
        if (auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('subdit')) {
            return [
                LaporanStatusOverview::class,
            ];
        } else {
            return [];
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Buat Laporan')
                ->url(self::$resource::getUrl('create'))
                ->button(),
        ];
    }

}
