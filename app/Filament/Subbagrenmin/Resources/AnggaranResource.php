<?php

namespace App\Filament\Subbagrenmin\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Anggaran;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Wizard\Step;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Subbagrenmin\Resources\AnggaranResource\Pages;
use App\Filament\Subbagrenmin\Resources\AnggaranResource\RelationManagers;

class AnggaranResource extends Resource
{
    protected static ?string $model = Anggaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Urkeu';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('tahun_anggaran')
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(2100)
                    ->required()
                    ->label('Tahun Anggaran')
                    ->columnSpanFull(),

                Wizard::make([
                    Step::make('I. PAGU')
                        ->schema([
                            TextInput::make('pagu')
                                ->numeric()
                                ->prefix('Rp.')
                                ->default(0)
                                ->formatStateUsing(fn ($state) => number_format((int) $state, 0, ',', '.'))
                                ->disabled()
                                ->dehydrated(false),

                            Section::make('Rincian')
                                ->schema([
                                    Grid::make(1)
                                        ->schema([
                                            TextInput::make('belanja_pegawai_pagu')
                                                ->label('1. Belanja Pegawai')
                                                ->prefix('Rp.')
                                                ->numeric()
                                                ->mask(RawJs::make('$money($input)'))
                                                ->stripCharacters('.')
                                                ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                ->default(0)
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungPagu($get, $set)),

                                            // Belanja Barang hanya label
                                            Placeholder::make('')
                                                ->content('2. Belanja Barang')
                                                ->columnSpanFull()
                                                ->extraAttributes(['class' => 'font-medium mt-2']),
                                                Section::make('')
                                                    ->schema([
                                                        TextInput::make('lidik_sidik_pagu')
                                                            ->label('a. Lidik / Sidik')
                                                            ->prefix('Rp.')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungPagu($get, $set)),
    
                                                        TextInput::make('dukops_giat_pagu')
                                                            ->label('b. Dukops Giat')
                                                            ->prefix('Rp.')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungPagu($get, $set)),
    
                                                        TextInput::make('harwat_r4_6_10_pagu')
                                                            ->label('c. Harwat R4/6/10')
                                                            ->prefix('Rp.')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungPagu($get, $set)),

                                                        // Harwat Fungsional hanya label
                                                        Placeholder::make('')
                                                        ->content('d. Harwat Fungsional')
                                                        ->extraAttributes(['class' => 'font-medium mt-2'])
                                                        ->columnSpanFull(),

                                                        Section::make('')->schema([
                                                            TextInput::make('har_alsus_pagu')
                                                            ->label('1) Har Alsus')
                                                            ->prefix('Rp.')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungPagu($get, $set)),

                                                            TextInput::make('lisensi_latfung_pagu')
                                                                ->label('2) Lisensi Latfung')
                                                                ->prefix('Rp.')
                                                                ->numeric()
                                                                ->mask(RawJs::make('$money($input)'))
                                                                ->stripCharacters('.')
                                                                ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                                ->default(0)
                                                                ->live(onBlur: true)
                                                                ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungPagu($get, $set)),
                                                        ])
                                                    ]),
                                        ]),
                                ]),
                        ]),



                    Step::make('II. REALISASI')
                        ->schema([
                            TextInput::make('realisasi')->numeric()
                                ->prefix('Rp.')
                                ->default(0)
                                ->formatStateUsing(fn ($state) => number_format((int) $state, 0, ',', '.'))
                                ->disabled()
                                ->dehydrated(false),
                            Section::make('Rincian')
                                ->schema([
                                    Grid::make(1)
                                        ->schema([
                                            TextInput::make('realisasi_belanja_pegawai')
                                                ->label('1. Belanja Pegawai')
                                                ->prefix('Rp.')
                                                ->numeric()
                                                ->mask(RawJs::make('$money($input)'))
                                                ->stripCharacters('.')
                                                ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                ->default(0)
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasi($get, $set)),
                                            Placeholder::make('')
                                                ->content('2. Belanja Barang')
                                                ->columnSpanFull()
                                                ->extraAttributes(['class' => 'font-medium mt-2']),
                                                Section::make('')
                                                    ->schema([
                                                        TextInput::make('realisasi_lidik_sidik')
                                                            ->label('a. Lidik / Sidik')
                                                            ->prefix('Rp.')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasi($get, $set)),
    
                                                        TextInput::make('realisasi_dukops_giat')
                                                            ->label('b. Dukops Giat')
                                                            ->prefix('Rp.')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasi($get, $set)),
    
                                                        TextInput::make('realisasi_harwat_r4_6_10')
                                                            ->label('c. Harwat R4/6/10')
                                                            ->prefix('Rp.')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasi($get, $set)),

                                                        // Harwat Fungsional hanya label
                                                        Placeholder::make('')
                                                        ->content('d. Harwat Fungsional')
                                                        ->extraAttributes(['class' => 'font-medium mt-2'])
                                                        ->columnSpanFull(),

                                                        Section::make('')->schema([
                                                            TextInput::make('realisasi_har_alsus')
                                                            ->label('1) Har Alsus')
                                                            ->prefix('Rp.')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasi($get, $set)),

                                                            TextInput::make('realisasi_lisensi_latfung')
                                                                ->label('2) Lisensi Latfung')
                                                                ->prefix('Rp.')
                                                                ->numeric()
                                                                ->mask(RawJs::make('$money($input)'))
                                                                ->stripCharacters('.')
                                                                ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                                ->default(0)
                                                                ->live(onBlur: true)
                                                                ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasi($get, $set)),
                                                        ])
                                                    ]),

                                        ]),
                                ]),
                        ]),

                    Step::make('III. SILPA')
                        ->schema([
                            TextInput::make('silpa')->numeric()
                                ->prefix('Rp.')
                                ->default(0)
                                ->suffix('20%')
                                ->formatStateUsing(fn ($state) => number_format((int) $state, 0, ',', '.'))
                                ->disabled()
                                ->dehydrated(false),
                            Section::make('Rincian')
                                ->schema([
                                    Grid::make(1)
                                        ->schema([
                                            TextInput::make('silpa_belanja_pegawai')
                                                ->label('1. Belanja Pegawai')
                                                ->suffix('20%')
                                                ->prefix('Rp.')
                                                ->numeric()
                                                ->mask(RawJs::make('$money($input)'))
                                                ->stripCharacters('.')
                                                ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                ->default(0)
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasi($get, $set)),
                                            Placeholder::make('')
                                                ->content('2. Belanja Barang')
                                                ->columnSpanFull()
                                                ->extraAttributes(['class' => 'font-medium mt-2']),
                                                Section::make('')
                                                    ->schema([
                                                        TextInput::make('silpa_lidik_sidik')
                                                            ->label('a. Lidik / Sidik')
                                                            ->prefix('Rp.')
                                                            ->suffix('20%')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasi($get, $set)),
    
                                                        TextInput::make('silpa_dukops_giat')
                                                            ->label('b. Dukops Giat')
                                                            ->prefix('Rp.')
                                                            ->suffix('20%')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasi($get, $set)),
    
                                                        TextInput::make('silpa_harwat_r4_6_10')
                                                            ->label('c. Harwat R4/6/10')
                                                            ->prefix('Rp.')
                                                            ->suffix('20%')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasi($get, $set)),

                                                        // Harwat Fungsional hanya label
                                                        Placeholder::make('')
                                                        ->content('d. Harwat Fungsional')
                                                        ->extraAttributes(['class' => 'font-medium mt-2'])
                                                        ->columnSpanFull(),

                                                        Section::make('')->schema([
                                                            TextInput::make('silpa_har_alsus')
                                                                ->label('1) Har Alsus')
                                                                ->prefix('Rp.')
                                                                ->suffix('20%')
                                                                ->numeric()
                                                                ->mask(RawJs::make('$money($input)'))
                                                                ->stripCharacters('.')
                                                                ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                                ->default(0)
                                                                ->live(onBlur: true)
                                                                ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasi($get, $set)),

                                                            TextInput::make('silpa_lisensi_latfung')
                                                                ->label('2) Lisensi Latfung')
                                                                ->prefix('Rp.')
                                                                ->suffix('20%')
                                                                ->numeric()
                                                                ->mask(RawJs::make('$money($input)'))
                                                                ->stripCharacters('.')
                                                                ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                                ->default(0)
                                                                ->live(onBlur: true)
                                                                ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasi($get, $set)),
                                                        ])
                                                    ]),

                                        ]),
                                ]),
                        ]),

                    
                        Step::make('IV. LIDIK/SIDIK SUBDIT')
                            ->schema([
                                Grid::make(2) // grid utama: kiri-kanan
                                    ->schema([
                        
                                        // Kolom kiri
                                        Section::make('Subdit I')
                                            ->schema([
                                                TextInput::make('subdit1_lidik_sidik_pagu')
                                                    ->label('Pagu Lidik/Sidik')
                                                    ->prefix('Rp.')
                                                    ->default(0)
                                                    ->formatStateUsing(fn ($state) => number_format((int) $state, 0, ',', '.'))
                                                    ->disabled()
                                                    ->dehydrated(false)
                                                    ->numeric(),
                        
                                                Fieldset::make('Rincian') // ini tetap full width dalam Section
                                                    ->schema([
                                                        TextInput::make('subdit1_unit1_lidik_sidik_realisasi')
                                                            ->label('Unit I')
                                                            ->prefix('Rp.')
                                                            ->suffix('20%')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasiPagu($get, $set)),
                                                        TextInput::make('subdit1_unit2_lidik_sidik_realisasi')
                                                            ->label('Unit II')
                                                            ->prefix('Rp.')
                                                            ->suffix('20%')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasiPagu($get, $set)),
                                                        TextInput::make('subdit1_unit3_lidik_sidik_realisasi')
                                                            ->label('Unit III')
                                                            ->prefix('Rp.')
                                                            ->suffix('20%')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasiPagu($get, $set)),
                                                        TextInput::make('subdit1_unit4_lidik_sidik_realisasi')
                                                            ->label('Unit IV')
                                                            ->prefix('Rp.')
                                                            ->suffix('20%')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasiPagu($get, $set)),
                                                        TextInput::make('subdit1_unit5_lidik_sidik_realisasi')
                                                            ->label('Unit V')
                                                            ->prefix('Rp.')
                                                            ->suffix('20%')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasiPagu($get, $set)),
                                                    ])
                                                    ->columns(1), // penting, biar tidak ikut terbagi 2 kolom
                        
                                                TextInput::make('subdit1_lidik_sidik_realisasi')
                                                    ->label('Realisasi')
                                                    ->prefix('Rp.')
                                                    ->suffix('20%')
                                                    ->default(0)
                                                    ->formatStateUsing(fn ($state) => number_format((int) $state, 0, ',', '.'))
                                                    ->disabled()
                                                    ->dehydrated(false)
                                                    ->numeric(),
                                            ])
                                            ->columns(1)
                                            ->columnSpan(1), // pastikan isi Section tetap 1 kolom
                        
                                        // Kolom kanan
                                        Section::make('Subdit II')
                                            ->schema([
                                                TextInput::make('subdit2_lidik_sidik_pagu')
                                                    ->label('Pagu Lidik/Sidik')
                                                    ->prefix('Rp.')
                                                    ->default(0)
                                                    ->formatStateUsing(fn ($state) => number_format((int) $state, 0, ',', '.'))
                                                    ->disabled()
                                                    ->dehydrated(false)
                                                    ->numeric(),
                        
                                                Fieldset::make('Rincian')
                                                    ->schema([
                                                        TextInput::make('subdit2_unit1_lidik_sidik_realisasi')
                                                            ->label('Unit V')
                                                            ->prefix('Rp.')
                                                            ->suffix('20%')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasiPagu($get, $set)),
                                                        TextInput::make('subdit2_unit2_lidik_sidik_realisasi')
                                                            ->label('Unit V')
                                                            ->prefix('Rp.')
                                                            ->suffix('20%')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasiPagu($get, $set)),
                                                        TextInput::make('subdit2_unit3_lidik_sidik_realisasi')
                                                            ->label('Unit V')
                                                            ->prefix('Rp.')
                                                            ->suffix('20%')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasiPagu($get, $set)),
                                                        TextInput::make('subdit2_unit4_lidik_sidik_realisasi')
                                                            ->label('Unit V')
                                                            ->prefix('Rp.')
                                                            ->suffix('20%')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasiPagu($get, $set)),
                                                        TextInput::make('subdit2_unit5_lidik_sidik_realisasi')
                                                            ->label('Unit V')
                                                            ->prefix('Rp.')
                                                            ->suffix('20%')
                                                            ->numeric()
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->stripCharacters('.')
                                                            ->dehydrateStateUsing(fn ($state) => (int) preg_replace('/\D/', '', (string) $state))
                                                            ->default(0)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungRealisasiPagu($get, $set)),
                                                    ])
                                                    ->columns(1),
                        
                                                TextInput::make('subdit2_lidik_sidik_realisasi')
                                                    ->label('Realisasi')
                                                    ->prefix('Rp.')
                                                    ->suffix('20%')
                                                    ->default(0)
                                                    ->formatStateUsing(fn ($state) => number_format((int) $state, 0, ',', '.'))
                                                    ->disabled()
                                                    ->dehydrated(false)
                                                    ->numeric(),
                                            ])
                                            ->columns(1)
                                            ->columnSpan(1),
                                    ]),
                            ]),
                    

                    Step::make('V. HARWAT FUNGSIONAL')
                        ->schema([
                            TextInput::make('subdit3_har_alsus_pagu')->numeric(),
                            TextInput::make('subdit3_lisensi_latfung_pagu')->numeric(),
                            TextInput::make('subdit3_har_alsus_realisasi')->numeric(),
                            TextInput::make('subdit3_lisensi_latfung_realisasi')->numeric(),

                            TextInput::make('subdit3_unit1_har_alsus_pagu')->numeric(),
                            TextInput::make('subdit3_unit1_har_alsus_realisasi')->numeric(),
                            TextInput::make('subdit3_unit2_har_alsus_pagu')->numeric(),
                            TextInput::make('subdit3_unit2_har_alsus_realisasi')->numeric(),
                            TextInput::make('subdit3_unit3_har_alsus_pagu')->numeric(),
                            TextInput::make('subdit3_unit3_har_alsus_realisasi')->numeric(),
                            TextInput::make('subdit3_unit4_har_alsus_pagu')->numeric(),
                            TextInput::make('subdit3_unit4_har_alsus_realisasi')->numeric(),
                            TextInput::make('subdit3_unit5_har_alsus_pagu')->numeric(),
                            TextInput::make('subdit3_unit5_har_alsus_realisasi')->numeric(),

                            TextInput::make('subdit3_unit1_lisensi_latfung_pagu')->numeric(),
                            TextInput::make('subdit3_unit1_lisensi_latfung_realisasi')->numeric(),
                            TextInput::make('subdit3_unit2_lisensi_latfung_pagu')->numeric(),
                            TextInput::make('subdit3_unit2_lisensi_latfung_realisasi')->numeric(),
                            TextInput::make('subdit3_unit3_lisensi_latfung_pagu')->numeric(),
                            TextInput::make('subdit3_unit3_lisensi_latfung_realisasi')->numeric(),
                            TextInput::make('subdit3_unit4_lisensi_latfung_pagu')->numeric(),
                            TextInput::make('subdit3_unit4_lisensi_latfung_realisasi')->numeric(),
                            TextInput::make('subdit3_unit5_lisensi_latfung_pagu')->numeric(),
                            TextInput::make('subdit3_unit5_lisensi_latfung_realisasi')->numeric(),
                        ]),
                ])
                    ->columnSpanFull()
                    ->skippable()
                    ->startOnStep(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama'),
                TextColumn::make('tahun_anggaran'),
                TextColumn::make('file_path')->label('Dokumen'),
            ])
            ->filters([
                
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ActionGroup::make([
                    Tables\Actions\Action::make('editDoc')
                        ->label('Edit Dokumen')
                        ->icon('heroicon-o-pencil-square')
                        ->url(fn ($record) => url("/subbagrenmin/anggaran/editor/{$record->id}"))
                        ->openUrlInNewTab(),

                    Tables\Actions\Action::make('viewDoc')
                        ->label('View Dokumen')
                        ->icon('heroicon-o-eye')
                        ->url(fn ($record) => url("/subbagrenmin/anggaran/view/{$record->id}"))
                        ->openUrlInNewTab(),
                
                    Tables\Actions\Action::make('downloadDoc')
                        ->label('Cetak / Download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->url(fn ($record) => url("/subbagrenmin/anggaran/download/{$record->id}"))
                        ->openUrlInNewTab(),
                ]),
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
            'index' => Pages\ListAnggarans::route('/'),
            'create' => Pages\CreateAnggaran::route('/create'),
            'edit' => Pages\EditAnggaran::route('/{record}/edit'),
        ];
    }

    /**
     * Helper function untuk menghitung total Pagu
     */
    public static function hitungPagu(Get $get, Set $set): void
    {
        $toInt = fn ($value) => (int) preg_replace('/\D/', '', (string) $value);

        $total =
            $toInt($get('belanja_pegawai_pagu')) +
            $toInt($get('lidik_sidik_pagu')) +
            $toInt($get('dukops_giat_pagu')) +
            $toInt($get('harwat_r4_6_10_pagu')) +
            $toInt($get('har_alsus_pagu')) +
            $toInt($get('lisensi_latfung_pagu'));

        $set('pagu', $total);
    }
    /**
     * Helper function untuk menghitung total Realisasi
     */
    public static function hitungRealisasi(Get $get, Set $set): void
    {
        $toInt = fn ($value) => (int) preg_replace('/\D/', '', (string) $value);

        $total =
            $toInt($get('realisasi_belanja_pegawai')) +
            $toInt($get('realisasi_lidik_sidik')) +
            $toInt($get('realisasi_dukops_giat')) +
            $toInt($get('realisasi_harwat_r4_6_10')) +
            $toInt($get('realisasi_har_alsus')) +
            $toInt($get('realisasi_lisensi_latfung'));

        $set('realisasi', $total);
    }
}
