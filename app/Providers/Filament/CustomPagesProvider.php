<?php

namespace App\Providers\Filament;

use Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\Login as CustomLogin;
use Illuminate\Support\ServiceProvider;

class CustomPagesProvider extends ServiceProvider
{
    public function boot(): void
    {
        Login::$view = 'filament.pages.auth.custom-login';
    }
}