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
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
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

    public static function shouldRegisterNavigation(): bool
    {
        $panelId = Filament::getCurrentPanel()->getId();

        return in_array($panelId, [
            'subbagrenmin',
            'sikorwas',
            'bagwassidik',
            'bagbinopsnal'
        ]);
    }

    public static function getSlug(): string
    {
        return 'personil';
    }

    // sort navigation
    protected static ?int $navigationSort = -1;

    // navigation label
    public static function getNavigationLabel(): string
    {
        return 'Personil';
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
                TextInput::make('name')->label('PERSONIL'),
                // select pangkat
                Select::make('pangkat_penyidik')
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
                TextInput::make('nrp_penyidik')->label('NRP'),
                PhoneInput::make('kontak')->inputNumberFormat(PhoneInputNumberType::NATIONAL)->required()->label('KONTAK'),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('unit.name')->label('UNIT')->sortable()->searchable(),
                TextColumn::make('subdit.name')->label('SUBDIT')->sortable()->searchable(),
                TextColumn::make('name')->label('NAMA STAFF')->sortable()->searchable(),
                // pangkat
                TextColumn::make('pangkat_penyidik')
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
                        $query->where('pangkat_penyidik', 'like', "%{$search}%")
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
                TextColumn::make('nrp_penyidik')->label('NRP')->sortable()->searchable(),
                TextColumn::make('kontak')->label('KONTAK')->sortable()->searchable(),
            ])
            ->defaultSort('pangkat_penyidik')
            ->filters([
                // filter by pangkat
                SelectFilter::make('pangkat_penyidik')
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
    // public static function getEloquentQuery(): Builder
    // {
    //     $query = parent::getEloquentQuery();

    //     if (!auth()->user()->hasRole('super_admin')) {
    //         $query->where('unit_id', auth()->user()->unit_id)
    //             ->orWhere('subdit_id', auth()->user()->subdit_id);
    //     }

    //     return $query;
    // }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();

        if (!$user->hasRole('super_admin')) {
            // Hanya tambahkan filter jika ada unit_id atau subdit_id
            if ($user->unit_id || $user->subdit_id) {
                $query->where(function($q) use ($user) {
                    if ($user->unit_id) {
                        $q->where('unit_id', $user->unit_id);
                    }

                    if ($user->subdit_id) {
                        // Gunakan orWhere di dalam closure
                        $q->orWhere('subdit_id', $user->subdit_id);
                    }
                });
            }
            // jika user tidak punya unit_id & subdit_id, query tidak difilter -> tampil semua
        }

        return $query;
    }
}
