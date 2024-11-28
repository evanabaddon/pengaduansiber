<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Unit;
use Filament\Tables;
use App\Models\Subdit;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UnitResource\Pages;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use App\Filament\Resources\UnitResource\RelationManagers;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    // sort navigation
    protected static ?int $navigationSort = -2;

    // navigation label
    public static function getNavigationLabel(): string
    {
        return 'Data Unit';
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
                TextInput::make('name')->label('UNIT'),
                //jika user adalah subdit dan unit serta penyidik jangan tampilkan subdit_id
                Select::make('subdit_id')
                    ->label('SUBDIT')
                    ->options(Subdit::all()->pluck('name', 'id'))
                    ->hidden(auth()->user()->hasRole('subdit'))
                    ->required()
                    ->searchable(),
                TextInput::make('nama_pimpinan')->label('KANIT'),
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
                TextColumn::make('name')->label('UNIT')->sortable()->searchable(),
                // tampilkan nama subdit
                TextColumn::make('subdit.name')->label('SUBDIT')->sortable()->searchable(),
                TextColumn::make('nama_pimpinan')->label('KANIT')->sortable()->searchable(),
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
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Jika bukan admin, filter berdasarkan subdit_id user
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('subdit_id', auth()->user()->subdit_id);
        }

        return $query;
    }
}
