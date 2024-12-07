<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Repeater;

class DataTambahanRepeater extends Repeater
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->schema([
            \Filament\Forms\Components\TextInput::make('nama_data')
                ->label('NAMA DATA')
                ->required(),
            \Filament\Forms\Components\TextInput::make('keterangan')
                ->label('KETERANGAN')
                ->required(),
        ])
        ->addActionLabel('Tambah Data')
        ->defaultItems(0)
        ->columnSpanFull()
        ->columns(2)
        ->itemLabel(function (array $state): ?string {
            return $state['nama_data'] ?? null;
        })
        ->collapsible()
        ->cloneable();
    }
} 