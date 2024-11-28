<?php

namespace App\Filament\Resources\PenyidikResource\Pages;

use App\Filament\Resources\PenyidikResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPenyidiks extends ListRecords
{
    protected static string $resource = PenyidikResource::class;

    // title
    public function getTitle(): string
    {
        return 'Penyidik/Penyidik Pembantu';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Penyidik'),
        ];
    }

    protected function modifyQueryUsing(Builder $query): Builder
    {
        return $query->when(!auth()->user()->hasRole('super_admin'), function ($query) {
            $query->where('unit_id', auth()->user()->unit_id);
        });
    }
}
