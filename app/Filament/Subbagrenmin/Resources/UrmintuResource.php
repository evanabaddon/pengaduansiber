<?php

namespace App\Filament\Subbagrenmin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Urmintu;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Subbagrenmin\Resources\UrmintuResource\Pages;
use App\Filament\Subbagrenmin\Resources\UrmintuResource\RelationManagers;

class UrmintuResource extends Resource
{
    protected static ?string $model = Urmintu::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function shouldRegisterNavigation(): bool
    {
        // return Filament::getCurrentPanel()->getId() === 'subbagrenmin';
        return false;
    }

    // navigation group
    public static function getNavigationGroup(): ?string
    {
        return 'Data Subbagrenmin';
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
            'index' => Pages\ListUrmintus::route('/'),
            'create' => Pages\CreateUrmintu::route('/create'),
            'edit' => Pages\EditUrmintu::route('/{record}/edit'),
        ];
    }
}
