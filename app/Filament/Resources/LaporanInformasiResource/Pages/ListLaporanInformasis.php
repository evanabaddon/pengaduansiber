<?php

namespace App\Filament\Resources\LaporanInformasiResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Blade;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\View\TablesRenderHook;
use App\Filament\Resources\LaporanInformasiResource;
use App\Filament\Resources\LaporanInformasiResource\Widgets\LaporanInformasiStatusOverview;

class ListLaporanInformasis extends ListRecords
{
    protected static string $resource = LaporanInformasiResource::class;

    protected function getHeaderWidgets(): array
    {
        if (auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('subdit')) {
            return [
                LaporanInformasiStatusOverview::class,
            ];
        } else {
            return [];
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Buat Dumas')
                ->url(self::$resource::getUrl('create'))
                ->button(),
        ];
    }

}
