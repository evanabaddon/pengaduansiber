<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Subdit;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SubditResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SubditResource\RelationManagers;

class SubditResource extends Resource
{
    protected static ?string $model = Subdit::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    // sort navigation
    protected static ?int $navigationSort = -3;

    // navigation label
    public static function getNavigationLabel(): string
    {
        return 'Data Subdit';
    }

    // navigation group
    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label('NAMA SUBDIT'),
                TextInput::make('nama_pimpinan')->label('NAMA PIMPINAN'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('NAMA SUBDIT'),
                TextColumn::make('nama_pimpinan')->label('NAMA PIMPINAN'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSubdits::route('/'),
            'create' => Pages\CreateSubdit::route('/create'),
            'edit' => Pages\EditSubdit::route('/{record}/edit'),
        ];
    }
}
