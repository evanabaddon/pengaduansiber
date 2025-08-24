<?php

namespace App\Filament\Bagbinopsnal\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use App\Models\Subbagminopsnal;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Bagbinopsnal\Resources\SubbagminopsnalResource\Pages;
use App\Filament\Bagbinopsnal\Resources\SubbagminopsnalResource\RelationManagers;

class SubbagminopsnalResource extends Resource
{
    protected static ?string $model = Subbagminopsnal::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function shouldRegisterNavigation(): bool
    {
        return Filament::getCurrentPanel()->getId() === 'bagbinopsnal';
    }

    // navigation group
    public static function getNavigationGroup(): ?string
    {
        return 'Data Bagbinopsnal';
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
            'index' => Pages\ListSubbagminopsnals::route('/'),
            'create' => Pages\CreateSubbagminopsnal::route('/create'),
            'edit' => Pages\EditSubbagminopsnal::route('/{record}/edit'),
        ];
    }
}
