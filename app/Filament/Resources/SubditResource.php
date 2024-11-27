<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Subdit;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Services\PangkatService;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SubditResource\Pages;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
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
                TextInput::make('name')->label('NAMA SUBDIT')->columnSpanFull(),
                TextInput::make('nama_pimpinan')->label('KASUBDIT'),
                // select pangkat pimpinan
                Select::make('pangkat_pimpinan')
                    ->label('PANGKAT PIMPINAN')
                    ->options([
                        1 => 'KOMBESPOL',
                        2 => 'AKBP',
                        3 => 'KOMPOL',
                        4 => 'AKP',
                        5 => 'IPTU',
                        6 => 'IPDA',
                        7 => 'AIPTU',
                        8 => 'AIPDA',
                        9 => 'BRIPKA',
                        10 => 'BRIGPOL',
                        11 => 'BRIPTU',
                        12 => 'BRIPDA',
                    ])
                    ->searchable(),
                TextInput::make('nrp_pimpinan')->label('NRP'),
                // kontak pimpinan
                PhoneInput::make('kontak_pimpinan')->label('KONTAK')->inputNumberFormat(PhoneInputNumberType::NATIONAL),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('SUBDIT')->sortable(),
                TextColumn::make('nama_pimpinan')->label('KASUBDIT')->sortable(),
                TextColumn::make('pangkat_pimpinan')
                    ->label('PANGKAT')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '1' => 'KOMBESPOL',
                        '2' => 'AKBP', 
                        '3' => 'KOMPOL',
                        '4' => 'AKP',
                        '5' => 'IPTU',
                        '6' => 'IPDA',
                        '7' => 'AIPTU',
                        '8' => 'AIPDA',
                        '9' => 'BRIPKA',
                        '10' => 'BRIGPOL',
                        '11' => 'BRIPTU',
                        '12' => 'BRIPDA',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('nrp_pimpinan')->label('NRP')->sortable(),
                TextColumn::make('kontak_pimpinan')->label('KONTAK')->sortable(),
            ])
            ->defaultSort('name')
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
