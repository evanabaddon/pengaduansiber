<?php

namespace App\Filament\Subbagrenmin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Str;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static bool $shouldRegisterNavigation = false;

    public  function getTitle(): string
    {
        // Ambil segmen pertama setelah domain
        $segment = request()->segment(1); // contoh: 'subbagrenmin'

        if ($segment) {
            // Ubah jadi format judul rapi
            return Str::title(str_replace('-', ' ', $segment));
        }

        // Default ke judul bawaan Filament
        return __('filament-panels::pages/dashboard.title');
    }
}
