<?php

namespace App\Filament\Resources\LaporanInformasiResource\Pages;

use App\Filament\Resources\LaporanInformasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLaporanInformasi extends ViewRecord
{
    protected static string $resource = LaporanInformasiResource::class;

    protected static string $view = 'filament.resources.laporan.view';
}
