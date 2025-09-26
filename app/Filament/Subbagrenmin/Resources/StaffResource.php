<?php

namespace App\Filament\Subbagrenmin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Staff;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use App\Filament\Subbagrenmin\Resources\StaffResource\Pages;
use App\Filament\Subbagrenmin\Resources\StaffResource\RelationManagers;

class StaffResource extends Resource
{
    protected static ?string $model = Staff::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function shouldRegisterNavigation(): bool
    {
        $panelId = Filament::getCurrentPanel()->getId();

        return in_array($panelId, [
            // 'subbagrenmin',
            'sikorwas',
            'bagwassidik',
            'bagbinopsnal'
        ]);
    }

    // sort navigation
    protected static ?int $navigationSort = -4;

    // navigation label
    public static function getNavigationLabel(): string
    {
        return 'Data Staff';
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
                TextInput::make('name')->label('NAMA'),
                // select pangkat
                Select::make('pangkat_staff')
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
                        13 => 'STAFF'
                    ])
                    ->searchable(),
                TextInput::make('nrp_staff')->label('NRP'),
                PhoneInput::make('kontak')->inputNumberFormat(PhoneInputNumberType::NATIONAL)->required()->label('KONTAK'),
                Select::make('jabatan')
                    ->options([
                        'kaurkeu' => 'Kaurkeu'
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('NAMA STAFF')->sortable()->searchable(),
                // pangkat
                TextColumn::make('pangkat_staff')
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
                        '13' => 'STAFF',
                        default => $state,
                    })
                    ->sortable()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function ($query) use ($search) {
                        // Pencarian berdasarkan nilai asli
                        $query->where('pangkat_staff', 'like', "%{$search}%")
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
                TextColumn::make('nrp_staff')->label('NRP')->sortable()->searchable(),
                TextColumn::make('kontak')->label('KONTAK')->sortable()->searchable(),
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
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
        ];
    }
}
