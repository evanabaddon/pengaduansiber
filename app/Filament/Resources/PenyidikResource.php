<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Unit;
use Filament\Tables;
use App\Models\Subdit;
use Filament\Forms\Get;
use App\Models\Penyidik;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use App\Filament\Resources\PenyidikResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use App\Filament\Resources\PenyidikResource\RelationManagers;

class PenyidikResource extends Resource
{
    protected static ?string $model = Penyidik::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    // sort navigation
    protected static ?int $navigationSort = -1;

    // navigation label
    public static function getNavigationLabel(): string
    {
        return 'Data Penyidik';
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
                TextInput::make('name')->label('NAMA PENYIDIK'),
                PhoneInput::make('kontak')->inputNumberFormat(PhoneInputNumberType::NATIONAL)->required()->label('KONTAK'),
                // select subdit dan unit
                Select::make('subdit_id')
                    ->label('SUBDIT')
                    ->options(Subdit::all()->pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->hidden(auth()->user()->hasRole('subdit') || auth()->user()->hasRole('unit')),
                // select unit berdasarkan subdit_id
                Select::make('unit_id')
                    ->label('UNIT')
                    ->hidden(auth()->user()->hasRole('subdit') || auth()->user()->hasRole('unit'))
                    ->options(fn (Get $get): Collection => Unit::query()
                        ->where('subdit_id', $get('subdit_id'))
                        ->pluck('name', 'id'))
                        ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('NAMA PENYIDIK'),
                // subdit
                TextColumn::make('subdit.name')->label('SUBDIT'),
                TextColumn::make('unit.name')->label('UNIT'),
                TextColumn::make('kontak')->label('KONTAK'),
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
            'index' => Pages\ListPenyidiks::route('/'),
            'create' => Pages\CreatePenyidik::route('/create'),
            'edit' => Pages\EditPenyidik::route('/{record}/edit'),
        ];
    }

    // tampilkan penyidik berdasarkan user dengan unit_id dan atau subdit_id
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('unit_id', auth()->user()->unit_id)
                ->orWhere('subdit_id', auth()->user()->subdit_id);
        }

        return $query;
    }
}
