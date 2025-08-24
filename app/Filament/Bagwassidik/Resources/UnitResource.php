<?php

namespace App\Filament\Bagwassidik\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use App\Models\BagwassidikUnit;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Bagwassidik\Resources\UnitResource\Pages;
use App\Filament\Bagwassidik\Resources\UnitResource\RelationManagers;

class UnitResource extends Resource
{
    protected static ?string $model = BagwassidikUnit::class;

    public static function getSlug(): string
    {
        return 'unit-bagwassidik';
    }

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $label = "Data Unit";

    public static function shouldRegisterNavigation(): bool
    {
        return Filament::getCurrentPanel()->getId() === 'bagwassidik';
    }

    // navigation group
    public static function getNavigationGroup(): ?string
    {
        return 'Master Data Bagwassidik';
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
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}
