<?php

namespace App\Filament\Subbagrenmin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Subdit;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use App\Services\PangkatService;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use App\Filament\Resources\SubditResource\RelationManagers;
use App\Filament\Subbagrenmin\Resources\SubditResource\Pages;

class SubditResource extends Resource
{
    protected static ?string $model = Subdit::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function shouldRegisterNavigation(): bool
    {
        // return Filament::getCurrentPanel()->getId() === 'subbagrenmin';
        return false;
    }

    // sort navigation
    protected static ?int $navigationSort = -1;

    // navigation label
    public static function getNavigationLabel(): string
    {
        return 'Data Subdit';
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
                TextInput::make('name')->label('SUBDIT')->columnSpanFull(),
                TextInput::make('nama_pimpinan')->label('KASUBDIT'),
                // select pangkat pimpinan
                Select::make('pangkat_pimpinan')
                    ->label('PANGKAT')
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
                TextColumn::make('name')->label('SUBDIT')->sortable()->searchable(),
                TextColumn::make('nama_pimpinan')->label('KASUBDIT')->sortable()->searchable(),
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
                    ->sortable()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function ($query) use ($search) {
                            // Pencarian berdasarkan nilai asli
                            $query->where('pangkat_pimpinan', 'like', "%{$search}%")
                                // Pencarian berdasarkan nilai yang diformat
                                ->orWhere(function ($query) use ($search) {
                                    $pangkatMap = [
                                        'KOMBESPOL' => '1',
                                        'AKBP' => '2',
                                        'KOMPOL' => '3',
                                        'AKP' => '4',
                                        'IPTU' => '5',
                                        'IPDA' => '6',
                                        'AIPTU' => '7',
                                        'AIPDA' => '8',
                                        'BRIPKA' => '9',
                                        'BRIGPOL' => '10',
                                        'BRIPTU' => '11',
                                        'BRIPDA' => '12',
                                    ];
                                    
                                    foreach ($pangkatMap as $pangkat => $value) {
                                        if (stripos($pangkat, $search) !== false) {
                                            $query->orWhere('pangkat_pimpinan', $value);
                                        }
                                    }
                                });
                        });
                    }),
                TextColumn::make('nrp_pimpinan')->label('NRP')->sortable()->searchable(),
                TextColumn::make('kontak_pimpinan')->label('KONTAK')->sortable()->searchable(),
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
