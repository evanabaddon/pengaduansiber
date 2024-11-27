<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanPolisiResource\Pages;
use App\Filament\Resources\LaporanPolisiResource\RelationManagers;
use App\Models\LaporanPolisi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LaporanPolisiResource extends Resource
{
    protected static ?string $model = LaporanPolisi::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    // label
    public static function getLabel(): string
    {
        return 'Laporan Polisi (LP)';
    }

    // navigation group
    public static function getNavigationGroup(): ?string
    {
        return 'Laporan';
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
            'index' => Pages\ListLaporanPolisis::route('/'),
            'create' => Pages\CreateLaporanPolisi::route('/create'),
            'edit' => Pages\EditLaporanPolisi::route('/{record}/edit'),
        ];
    }
}
