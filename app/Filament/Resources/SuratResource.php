<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Surat;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Filament\Navigation\NavigationItem;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SuratResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SuratResource\RelationManagers;

class SuratResource extends Resource
{
    protected static ?string $model = Surat::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label ="Persuratan";

    public static function shouldRegisterNavigation(): bool
    {
        $panelId = Filament::getCurrentPanel()->getId();

        return in_array($panelId, [
            // 'subbagrenmin',
            'sikorwas',
            'bagwassidik',
            'bagbinopsnal'
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSurats::route('/'),
            'create' => Pages\CreateSurat::route('/create'),
            'edit' => Pages\EditSurat::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
            // ->when(request('panel'), fn($q, $panel) => $q->where('panel', $panel))
            // ->when(request('menu'), fn($q, $menu) => $q->where('menu', $menu))
            // ->when(request('submenu'), fn($q, $submenu) => $q->where('submenu', $submenu))
            // ->when(request('type'), fn($q, $type) => $q->where('type', $type));
    }
    
}
