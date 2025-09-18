<?php

namespace App\Filament\Subbagrenmin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Pimpinan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Subbagrenmin\Resources\PimpinanResource\Pages;
use App\Filament\Subbagrenmin\Resources\PimpinanResource\RelationManagers;

class PimpinanResource extends Resource
{
    protected static ?string $model = Pimpinan::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

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

    // sort navigation
    protected static ?int $navigationSort = -5;

    // navigation label
    public static function getNavigationLabel(): string
    {
        return 'Data Pimpinan';
    }

    // navigation group
    // public static function getNavigationGroup(): ?string
    // {
    //     return 'Menu Personel';
    // }

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
            'index' => Pages\ListPimpinans::route('/'),
            'create' => Pages\CreatePimpinan::route('/create'),
            'edit' => Pages\EditPimpinan::route('/{record}/edit'),
        ];
    }
}
