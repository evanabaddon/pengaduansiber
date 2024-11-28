<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.login';

    public function getViewData(): array
    {
        return [
            ...parent::getViewData(),
            'logo' => asset('images/logo-siber-polri.png'), // Sesuaikan path logo
        ];
    }
}