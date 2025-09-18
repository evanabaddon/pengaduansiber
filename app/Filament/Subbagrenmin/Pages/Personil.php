<?php

namespace App\Filament\Subbagrenmin\Pages;

use App\Filament\Subbagrenmin\Resources\PenyidikResource;
use App\Filament\Subbagrenmin\Resources\PimpinanResource;
use App\Filament\Subbagrenmin\Resources\StaffResource;
use App\Filament\Subbagrenmin\Resources\SubditResource;
use App\Filament\Subbagrenmin\Resources\UnitResource;
use Filament\Pages\Page;
use AymanAlhattami\FilamentPageWithSidebar\PageNavigationItem;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Navigation\NavigationItem;

class Personil extends Page
{
    use HasPageSidebar; 
    
    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static string $view = 'filament.subbagrenmin.pages.personil';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function sidebar(): FilamentPageSidebar
    {
        return FilamentPageSidebar::make()
            ->setNavigationItems([
                PageNavigationItem::make('Data Pimpinan')
                    ->translateLabel()
                    ->url(PimpinanResource::getUrl())
                    ->icon('heroicon-o-user')
                    ->visible(true),
                PageNavigationItem::make('Data Staff')
                    ->translateLabel()
                    ->url(StaffResource::getUrl())
                    ->icon('heroicon-o-users')
                    ->visible(true),
                PageNavigationItem::make('Data Penyidik')
                    ->translateLabel()
                    ->url(PenyidikResource::getUrl())
                    ->icon('heroicon-o-user-circle')
                    ->visible(true),
                PageNavigationItem::make('Data Unit')
                    ->translateLabel()
                    ->url(UnitResource::getUrl())
                    ->icon('heroicon-o-building-office-2')
                    ->visible(true),
                PageNavigationItem::make('Data Subdit')
                    ->translateLabel()
                    ->url(SubditResource::getUrl())
                    ->icon('heroicon-o-building-office')
                    ->visible(true),
                // ...
            ]);
    }
}
