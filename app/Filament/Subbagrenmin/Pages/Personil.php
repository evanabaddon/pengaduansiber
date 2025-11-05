<?php

namespace App\Filament\Subbagrenmin\Pages;

use Filament\Tables;
use Filament\Actions;
use Filament\Pages\Page;
use Tables\Actions\EditAction;
use Filament\Navigation\NavigationItem;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use App\Models\Personil as ModelsPersonil;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Subbagrenmin\Resources\UnitResource;
use App\Filament\Subbagrenmin\Resources\StaffResource;
use App\Filament\Subbagrenmin\Resources\SubditResource;
use App\Filament\Subbagrenmin\Resources\PenyidikResource;
use App\Filament\Subbagrenmin\Resources\PersonilResource;
use App\Filament\Subbagrenmin\Resources\PimpinanResource;
use AymanAlhattami\FilamentPageWithSidebar\PageNavigationItem;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;

class Personil extends Page implements HasTable
{
    use HasPageSidebar; 
    use InteractsWithTable; // 🔥 ini yang penting

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static string $view = 'filament.subbagrenmin.pages.personil';

    protected static ?string $slug = 'personel';

    public function getTitle(): string
    {
        return 'Personel';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Tambah Personel')
                // ->icon('heroicon-o-plus')
                ->url(\App\Filament\Subbagrenmin\Resources\PersonilResource::getUrl('create')),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    // 🔹 Query data personil
    protected function getTableQuery()
    {
        return ModelsPersonil::query();
    }

    // 🔹 Kolom tabel
    protected function getTableColumns(): array
    {
        return \App\Filament\Subbagrenmin\Resources\PersonilResource::getTableColumns();
    }

    protected function getTableFilters(): array
    {
        return \App\Filament\Subbagrenmin\Resources\PersonilResource::getTableFilters();
    }

    // 🔹 Aksi tiap baris
    protected function getTableActions(): array
    {
        return \App\Filament\Subbagrenmin\Resources\PersonilResource::getTableActions();
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
