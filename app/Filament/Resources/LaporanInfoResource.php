<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanInfoResource\Pages;
use App\Filament\Resources\LaporanInfoResource\RelationManagers;
use App\Models\LaporanInfo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LaporanInfoResource extends Resource
{
    protected static ?string $model = LaporanInfo::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    // label
    public static function getLabel(): string
    {
        return 'Laporan Informasi (LI)';
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
            'index' => Pages\ListLaporanInfos::route('/'),
            'create' => Pages\CreateLaporanInfo::route('/create'),
            'edit' => Pages\EditLaporanInfo::route('/{record}/edit'),
        ];
    }
}
