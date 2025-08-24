<?php

namespace App\Filament\Sikorwas\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Subsibansidik;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Sikorwas\Resources\SubsibansidikResource\Pages;
use App\Filament\Sikorwas\Resources\SubsibansidikResource\RelationManagers;

class SubsibansidikResource extends Resource
{
    protected static ?string $model = Subsibansidik::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function shouldRegisterNavigation(): bool
    {
        return Filament::getCurrentPanel()->getId() === 'sikorwas';
    }

    // navigation group
    public static function getNavigationGroup(): ?string
    {
        return 'Data Sikorwas';
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
            'index' => Pages\ListSubsibansidiks::route('/'),
            'create' => Pages\CreateSubsibansidik::route('/create'),
            'edit' => Pages\EditSubsibansidik::route('/{record}/edit'),
        ];
    }
}
