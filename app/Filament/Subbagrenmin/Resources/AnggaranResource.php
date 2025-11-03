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
use Filament\Actions\DeleteAction;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Wizard;
use Illuminate\Support\Facades\Blade;
use App\Forms\Components\MoneyDisplay;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Wizard\Step;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Subbagrenmin\Resources\AnggaranResource\Pages;
use Filament\Tables\Actions\DeleteAction as ActionsDeleteAction;
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
                                ->label('Total Pagu')
                                ->readOnly()
                                ->hidden()
                                ->prefix('Rp.')
                                ->dehydrated(true)
                                ->extraAttributes([
                                    'x-data' => '{}',
                                    'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                ]),
                            MoneyDisplay::make('pagu')
                                ->label('Total Pagu')
                                ->prefix('Rp.'),
                            
                            TextInput::make('belanja_pegawai_pagu')
                                ->label('Belanja Pegawai')
                                ->default(0)
                                ->prefix('Rp.')
                                ->mask(RawJs::make('$money($input)'))
                                ->dehydrateStateUsing(fn ($state) => (int) str_replace(['Rp.', '.', ','], '', $state))
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                    // hitung total pagu
                                    $pegawai = self::toInt($get('belanja_pegawai_pagu'));
                                    $barang  = self::toInt($get('belanja_barang_pagu'));
                                    $set('pagu', $pegawai + $barang);
                                    self::hitungSemuaSilpa($get, $set);
                                })
                                ->extraAttributes([
                                    'x-data' => '{}',
                                    'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                ]),
                            
                            Section::make('Belanja Barang')
                                ->schema([             
                                    TextInput::make('belanja_barang_pagu')
                                        ->label('Belanja Barang')
                                        ->default(0)
                                        ->disabled()
                                        ->hidden()
                                        ->dehydrated(true)
                                        ->prefix('Rp.')
                                        ->mask(RawJs::make('$money($input)'))
                                        ->columnSpanFull()
                                        ->extraAttributes([
                                            'x-data' => '{}',
                                            'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                        ]),
                                    MoneyDisplay::make('belanja_barang_pagu')
                                        ->label('Belanja Barang')
                                        ->prefix('Rp.'),
                                    Fieldset::make('Lidik / Sidik')
                                        ->schema([
                                            TextInput::make('lidik_sidik_pagu')
                                                ->disabled()
                                                ->dehydrated(true) 
                                                ->prefix('Rp.')
                                                ->hidden()
                                                ->mask(RawJs::make('$money($input)'))
                                                ->label('Total Lidik/Sidik')
                                                ->afterStateHydrated(function (Get $get, Set $set) {
                                                    $total = self::toInt($get('subdit1_lidik_sidik_pagu'))
                                                            + self::toInt($get('subdit2_lidik_sidik_pagu'));
                                                    $set('pagu_lidik_sidik', $total);
                                                })
                                                ->afterStateUpdated(function (Get $get, Set $set) {
                                                    $total = self::toInt($get('subdit1_lidik_sidik_pagu'))
                                                            + self::toInt($get('subdit2_lidik_sidik_pagu'));
                                                    $set('pagu_lidik_sidik', $total);
                                                })
                                                ->extraAttributes([
                                                    'x-data' => '{}',
                                                    'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                ])
                                                ->columnSpanFull(),
                                            MoneyDisplay::make('lidik_sidik_pagu')
                                                ->label('Total Lidik/Sidik')
                                                ->columnSpanFull()
                                                ->prefix('Rp.'),
                                            Grid::make(2)->schema([
                                                // Section Subdit I
                                                Section::make('Subdit I')
                                                    ->columns(1)
                                                    ->columnSpan(1)
                                                    ->schema([
                                                        // Total Subdit I
                                                        TextInput::make('subdit1_lidik_sidik_pagu')
                                                            ->label('Total Subdit I')
                                                            ->disabled()
                                                            ->hidden()
                                                            ->dehydrated(true)
                                                            ->prefix('Rp.')
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->afterStateHydrated(function (Get $get, Set $set) {
                                                                $total = 0;
                                                                foreach (range(1, 5) as $i) {
                                                                    $total += (int) $get("subdit1_unit{$i}_lidik_sidik_pagu") ?: 0;
                                                                }
                                                                $set('subdit1_lidik_sidik_pagu', $total);
                                                            })
                                                            ->extraAttributes([
                                                                'x-data' => '{}',
                                                                'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                            ]),
                                                        MoneyDisplay::make('subdit1_lidik_sidik_pagu')
                                                            ->label('Total Subdit I')
                                                            ->prefix('Rp.'),
                                                        // Unit Subdit I
                                                        ...collect(range(1, 5))->map(fn ($unit) =>
                                                            TextInput::make("subdit1_unit{$unit}_lidik_sidik_pagu")
                                                                ->label("Subdit I - Unit {$unit}")
                                                                ->default(0)
                                                                ->prefix('Rp.')
                                                                ->mask(RawJs::make('$money($input)'))
                                                                ->live(onBlur: true) 
                                                                ->extraAttributes([
                                                                    'x-data' => '{}',
                                                                    'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                                ])
                                                                ->dehydrateStateUsing(fn ($state) => (int) str_replace([',', '.'], '', $state))
                                                                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                                                    $total = 0;
                                                                    foreach (range(1, 5) as $i) {
                                                                        $total += self::toInt($get("subdit1_unit{$i}_lidik_sidik_pagu"));
                                                                    }
                                                                    $set('subdit1_lidik_sidik_pagu', $total);

                                                                    // Hitung total Lidik/Sidik
                                                                    $set('lidik_sidik_pagu', 
                                                                        self::toInt($get('subdit1_lidik_sidik_pagu')) + self::toInt($get('subdit2_lidik_sidik_pagu'))
                                                                    );

                                                                    // Hitung belanja barang pagu
                                                                    $set('belanja_barang_pagu',
                                                                        self::toInt($get('lidik_sidik_pagu')) 
                                                                        + self::toInt($get('dukops_giat_pagu')) 
                                                                        + self::toInt($get('harwat_r4_6_10_pagu')) 
                                                                        + self::toInt($get('harwat_fungsional_pagu'))
                                                                    );

                                                                    // Hitung total pagu
                                                                    $pegawai = self::toInt($get('belanja_pegawai_pagu'));
                                                                    $barang  = self::toInt($get('belanja_barang_pagu'));
                                                                    $set('pagu', $pegawai + $barang);
                                                                })
                                                        )->toArray(),
                                                    ]),


                                            
                                                // Section Subdit II
                                                Section::make('Subdit II')
                                                    ->columns(1)
                                                    ->columnSpan(1)
                                                    ->schema([
                                                        TextInput::make('subdit2_lidik_sidik_pagu')
                                                            ->label('Total Subdit II')
                                                            ->hidden()
                                                            ->disabled()
                                                            ->dehydrated(true)
                                                            ->prefix('Rp.')
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->extraAttributes([
                                                                'x-data' => '{}',
                                                                'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                            ])
                                                            ->afterStateHydrated(function (Get $get, Set $set) {
                                                                $total = 0;
                                                                foreach (range(1, 5) as $i) {
                                                                    $total += self::toInt($get("subdit2_unit{$i}_lidik_sidik_pagu"))  ?: 0;
                                                                }
                                                                $set('subdit2_lidik_sidik_pagu', $total);
                                                            }),
                                                        MoneyDisplay::make('subdit2_lidik_sidik_pagu')
                                                            ->label('Total Subdit II')
                                                            ->prefix('Rp.'),
                                                        ...collect(range(1, 5))->map(fn ($unit) =>
                                                            TextInput::make("subdit2_unit{$unit}_lidik_sidik_pagu")
                                                                ->label("Subdit II - Unit {$unit}")
                                                                ->default(0)
                                                                ->prefix('Rp.')
                                                                ->mask(RawJs::make('$money($input)'))
                                                                ->live(onBlur: true)
                                                                ->extraAttributes([
                                                                    'x-data' => '{}',
                                                                    'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                                ])
                                                                ->dehydrateStateUsing(fn ($state) => (int) str_replace([',', '.'], '', $state))
                                                                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                                                    $total = 0;
                                                                    foreach (range(1, 5) as $i) {
                                                                        $total += self::toInt($get("subdit2_unit{$i}_lidik_sidik_pagu")) ?: 0;
                                                                    }
                                                                    $set('subdit2_lidik_sidik_pagu', $total);

                                                                    // Hitung total Lidik/Sidik
                                                                    $set('lidik_sidik_pagu', 
                                                                        self::toInt($get('subdit1_lidik_sidik_pagu')) + self::toInt($get('subdit2_lidik_sidik_pagu'))
                                                                    );

                                                                    // Hitung belanja barang pagu
                                                                    $set('belanja_barang_pagu',
                                                                        self::toInt($get('lidik_sidik_pagu')) 
                                                                        + self::toInt($get('dukops_giat_pagu')) 
                                                                        + self::toInt($get('harwat_r4_6_10_pagu')) 
                                                                        + self::toInt($get('harwat_fungsional_pagu'))
                                                                    );

                                                                    // Hitung total pagu
                                                                    $pegawai = self::toInt($get('belanja_pegawai_pagu'));
                                                                    $barang  = self::toInt($get('belanja_barang_pagu'));
                                                                    $set('pagu', $pegawai + $barang);
                                                                })
                                                        )->toArray(),
                                                    ]),
                                            ])

                                        ]),
                                    Fieldset::make('Dukops Giat')
                                        ->schema([
                                            TextInput::make('dukops_giat_pagu')->label('')->columnSpanFull()
                                                ->live(onBlur: true)
                                                ->prefix('Rp.')
                                                ->default(0)
                                                ->mask(RawJs::make('$money($input)'))
                                                ->extraAttributes([
                                                    'x-data' => '{}',
                                                    'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                ])
                                                ->dehydrateStateUsing(fn ($state) => (int) str_replace([',', '.'], '', $state))
                                                ->afterStateUpdated(function (Get $get, Set $set){
                                                    // Hitung belanja barang pagu
                                                    $set('belanja_barang_pagu',
                                                        self::toInt($get('lidik_sidik_pagu')) 
                                                        + self::toInt($get('dukops_giat_pagu')) 
                                                        + self::toInt($get('harwat_r4_6_10_pagu')) 
                                                        + self::toInt($get('harwat_fungsional_pagu'))
                                                    );

                                                    // Hitung total pagu
                                                    $pegawai = self::toInt($get('belanja_pegawai_pagu'));
                                                    $barang  = self::toInt($get('belanja_barang_pagu'));
                                                    $set('pagu', $pegawai + $barang);
                                                }),
                                        ])->columnSpanFull(),
                                    Fieldset::make('Harwat R4/6/10')
                                        ->schema([
                                            TextInput::make('harwat_r4_6_10_pagu')->label('')
                                                ->columnSpanFull()
                                                ->live(onBlur: true)
                                                ->default(0)
                                                ->prefix('Rp.')
                                                ->mask(RawJs::make('$money($input)'))
                                                ->extraAttributes([
                                                    'x-data' => '{}',
                                                    'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                ])
                                                ->dehydrateStateUsing(fn ($state) => (int) str_replace([',', '.'], '', $state))
                                                ->afterStateUpdated(function (Get $get, Set $set){
                                                    // Hitung belanja barang pagu
                                                    $set('belanja_barang_pagu',
                                                        self::toInt($get('lidik_sidik_pagu')) 
                                                        + self::toInt($get('dukops_giat_pagu')) 
                                                        + self::toInt($get('harwat_r4_6_10_pagu')) 
                                                        + self::toInt($get('harwat_fungsional_pagu'))
                                                    );

                                                    // Hitung total pagu
                                                    $pegawai = self::toInt($get('belanja_pegawai_pagu'));
                                                    $barang  = self::toInt($get('belanja_barang_pagu'));
                                                    $set('pagu', $pegawai + $barang);
                                            }),
                                        ])->columnSpanFull(),
                                    Fieldset::make('Harwat Fungsional (Subdit III)')
                                        ->schema([
                                            TextInput::make('harwat_fungsional_pagu')
                                                ->label('Total Harwat Fungsional (Subdit III)')
                                                ->hidden()
                                                ->default(0)
                                                ->disabled()
                                                ->prefix('Rp.')
                                                ->mask(RawJs::make('$money($input)'))
                                                ->dehydrated(true)
                                                ->columnSpanFull()
                                                ->extraAttributes([
                                                    'x-data' => '{}',
                                                    'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                ])
                                                ->afterStateUpdated(function (Get $get, Set $set){
                                                    // Hitung belanja barang pagu
                                                    $set('belanja_barang_pagu',
                                                        self::toInt($get('lidik_sidik_pagu')) 
                                                        + self::toInt($get('dukops_giat_pagu')) 
                                                        + self::toInt($get('harwat_r4_6_10_pagu')) 
                                                        + self::toInt($get('harwat_fungsional_pagu'))
                                                    );

                                                    // Hitung total pagu
                                                    $pegawai = self::toInt($get('belanja_pegawai_pagu'));
                                                    $barang  = self::toInt($get('belanja_barang_pagu'));
                                                    $set('pagu', $pegawai + $barang);
                                            }),
                                            MoneyDisplay::make('harwat_fungsional_pagu')
                                                ->label('Total Harwat Fungsional (Subdit III)')
                                                ->prefix('Rp.')
                                                ->columnSpanFull(),
                                            Grid::make(2)->schema([
                                                // Section Har Alsus
                                                Section::make('Har Alsus')
                                                    ->columns(1)
                                                    ->columnSpan(1)
                                                    ->schema([
                                                        TextInput::make('subdit3_har_alsus_pagu')
                                                            ->disabled()
                                                            ->hidden()
                                                            ->dehydrated(true)
                                                            ->prefix('Rp.')
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->label('Total Subdit III Har Alsus')
                                                            ->extraAttributes([
                                                                'x-data' => '{}',
                                                                'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                            ])
                                                            ->afterStateHydrated(function (Get $get, Set $set) {
                                                                $total = 0;
                                                                foreach (range(1, 5) as $unit) {
                                                                    $total += self::toInt($get("subdit3_unit{$unit}_har_alsus_pagu")) ?: 0;
                                                                }
                                                                $set('subdit3_har_alsus_pagu', $total);
                                                            })
                                                            ->afterStateUpdated(function (Get $get, Set $set){
                                                                $set('harwat_fungsional_pagu', 
                                                                self::toInt($get('subdit3_har_alsus_pagu')) + self::toInt($get('subdit3_lisensi_latfung_pagu')));
                                                            }),
                                                        MoneyDisplay::make('subdit3_har_alsus_pagu')
                                                            ->label('Total Subdit III Har Alsus')
                                                            ->prefix('Rp.'),
                                                            
                                                        ...collect(range(1, 5))->map(fn ($unit) =>
                                                            TextInput::make("subdit3_unit{$unit}_har_alsus_pagu")
                                                                ->label("Unit {$unit}")
                                                                ->default(0)
                                                                ->prefix('Rp.')
                                                                ->mask(RawJs::make('$money($input)'))
                                                                ->live(onBlur: true)
                                                                ->extraAttributes([
                                                                    'x-data' => '{}',
                                                                    'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                                ])
                                                                ->dehydrateStateUsing(fn ($state) => (int) str_replace([',', '.'], '', $state))
                                                                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                                                    $total = 0;
                                                                    foreach (range(1, 5) as $unit) {
                                                                        $total += self::toInt($get("subdit3_unit{$unit}_har_alsus_pagu")) ?: 0;
                                                                    }
                                                                    $set('subdit3_har_alsus_pagu', $total);

                                                                    // Hitung Harwat Fungsional
                                                                    $set('harwat_fungsional_pagu',
                                                                        self::toInt($get('subdit3_har_alsus_pagu')) +  self::toInt($get('subdit3_lisensi_latfung_pagu'))
                                                                    );
                                                                    // Hitung belanja barang pagu
                                                                    $set('belanja_barang_pagu',
                                                                        self::toInt($get('lidik_sidik_pagu')) 
                                                                        + self::toInt($get('dukops_giat_pagu')) 
                                                                        + self::toInt($get('harwat_r4_6_10_pagu')) 
                                                                        + self::toInt($get('harwat_fungsional_pagu'))
                                                                    );

                                                                    // Hitung total pagu
                                                                    $pegawai = self::toInt($get('belanja_pegawai_pagu'));
                                                                    $barang  = self::toInt($get('belanja_barang_pagu'));
                                                                    $set('pagu', $pegawai + $barang);
                                                                })
                                                        )->toArray(),
                                                    ]),

                                                // Section Lisensi Latfung
                                                Section::make('Lisensi Latfung')
                                                    ->columns(1)
                                                    ->columnSpan(1)
                                                    ->schema([
                                                        TextInput::make('subdit3_lisensi_latfung_pagu')
                                                            ->label('Total Subdit III Lisensi Latfung')
                                                            ->hidden()
                                                            ->disabled()
                                                            ->dehydrated(true)
                                                            ->prefix('Rp.')
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->extraAttributes([
                                                                'x-data' => '{}',
                                                                'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                            ])
                                                            ->afterStateHydrated(function (Get $get, Set $set) {
                                                                $total = 0;
                                                                foreach (range(1, 5) as $unit) {
                                                                    $total += self::toInt($get("subdit3_unit{$unit}_lisensi_latfung_pagu")) ?: 0;
                                                                }
                                                                $set('subdit3_lisensi_latfung_pagu', $total);
                                                            }),
                                                        MoneyDisplay::make('subdit3_lisensi_latfung_pagu')
                                                            ->label('Total Subdit III Lisensi Latfung')
                                                            ->prefix('Rp.'),
                                                        ...collect(range(1, 5))->map(fn ($unit) =>
                                                            TextInput::make("subdit3_unit{$unit}_lisensi_latfung_pagu")
                                                                ->label("Unit {$unit}")
                                                                ->default(0)
                                                                ->prefix('Rp.')
                                                                ->mask(RawJs::make('$money($input)'))
                                                                ->live(onBlur: true)
                                                                ->extraAttributes([
                                                                    'x-data' => '{}',
                                                                    'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                                ])
                                                                ->dehydrateStateUsing(fn ($state) => (int) str_replace([',', '.'], '', $state))
                                                                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                                                    $total = 0;
                                                                    foreach (range(1, 5) as $unit) {
                                                                        $total += self::toInt($get("subdit3_unit{$unit}_lisensi_latfung_pagu")) ?: 0;
                                                                    }
                                                                    $set('subdit3_lisensi_latfung_pagu', $total);

                                                                    // Hitung Harwat Fungsional
                                                                    $set('harwat_fungsional_pagu',
                                                                        self::toInt($get('subdit3_har_alsus_pagu')) + self::toInt($get('subdit3_lisensi_latfung_pagu'))
                                                                    );

                                                                     // Hitung belanja barang pagu
                                                                    $set('belanja_barang_pagu',
                                                                        self::toInt($get('lidik_sidik_pagu')) 
                                                                        + self::toInt($get('dukops_giat_pagu')) 
                                                                        + self::toInt($get('harwat_r4_6_10_pagu')) 
                                                                        + self::toInt($get('harwat_fungsional_pagu'))
                                                                    );

                                                                    // Hitung total pagu
                                                                    $pegawai = self::toInt($get('belanja_pegawai_pagu'));
                                                                    $barang  = self::toInt($get('belanja_barang_pagu'));
                                                                    $set('pagu', $pegawai + $barang);
                                                                })
                                                        )->toArray(),
                                                    ]),
                                            ]),
                                        ])->columnSpanFull()
                                ]),
                            
                            
                        ]),
                    
                    Step::make('II. REALISASI')
                        ->schema([
                            TextInput::make('realisasi')
                                ->label('Total Realisasi')
                                ->hidden()
                                ->disabled()
                                ->prefix('Rp.')
                                ->dehydrated(true)
                                ->extraAttributes([
                                    'x-data' => '{}',
                                    'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                ])
                                ->afterStateUpdated(function(Get $get,Set $set){

                                    self::hitungSemuaSilpa($get, $set);
                                })
                                ->suffix(fn (Get $get) => self::toInt($get('pagu')) > 0
                                    ? (self::toInt($get('realisasi')) <= self::toInt($get('pagu'))
                                        // normal ≤ 100%
                                        ? round(self::toInt($get('realisasi')) / self::toInt($get('pagu')) * 100, 2) . ' %'
                                        // overbudget → hitung minus selisih
                                        : '-' . round((self::toInt($get('realisasi')) - self::toInt($get('pagu'))) / self::toInt($get('pagu')) * 100, 2) . ' %')
                                    : '0 %'),
                            MoneyDisplay::make('realisasi')
                                ->label('Total Realisasi')
                                ->prefix('Rp.')
                                ->suffix(fn (Get $get) => self::toInt($get('pagu')) > 0
                                    ? (self::toInt($get('realisasi')) <= self::toInt($get('pagu'))
                                        ? round(self::toInt($get('realisasi')) / self::toInt($get('pagu')) * 100, 2) . ' %'
                                        : '-' . round((self::toInt($get('realisasi')) - self::toInt($get('pagu'))) / self::toInt($get('pagu')) * 100, 2) . ' %')
                                    : '0 %'),
                                
                            TextInput::make('realisasi_belanja_pegawai')
                                ->label('Belanja Pegawai')
                                ->default(0)
                                ->prefix('Rp.')
                                ->mask(RawJs::make('$money($input)'))
                                ->live(onBlur: true)
                                ->extraAttributes([
                                    'x-data' => '{}',
                                    'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                ])
                                ->dehydrateStateUsing(fn ($state) => (int) str_replace([',', '.'], '', $state))
                                ->afterStateUpdated(function(Get $get,Set $set){
                                    // hitung total realisasi
                                    $realisasi_pegawai = self::toInt($get('realisasi_belanja_pegawai'));
                                    $realisasi_barang  = self::toInt($get('realisasi_belanja_barang'));
                                    $set('realisasi', $realisasi_pegawai + $realisasi_barang);

                                    self::hitungSemuaSilpa($get, $set);
                                })
                                ->afterStateHydrated(function (Get $get, Set $set) {
                                    self::hitungSemuaSilpa($get, $set);
                                })                                
                                ->suffix(fn (Get $get) => self::toInt($get('belanja_pegawai_pagu')) > 0
                                    ? (self::toInt($get('realisasi_belanja_pegawai')) <= self::toInt($get('belanja_pegawai_pagu'))
                                        // progress normal
                                        ? round(self::toInt($get('realisasi_belanja_pegawai')) / self::toInt($get('belanja_pegawai_pagu')) * 100, 2) . ' %'
                                        // overbudget → tampil minus selisih
                                        : '-' . round((self::toInt($get('realisasi_belanja_pegawai')) - self::toInt($get('belanja_pegawai_pagu'))) / self::toInt($get('belanja_pegawai_pagu')) * 100, 2) . ' %')
                                    : '0 %'),
                            
                            Section::make('Belanja Barang')
                                ->schema([             
                                    TextInput::make('realisasi_belanja_barang')
                                        ->label('Belanja Barang')
                                        ->default(0)
                                        ->disabled()
                                        ->hidden()
                                        ->prefix('Rp.')
                                        ->mask(RawJs::make('$money($input)'))
                                        ->dehydrated(false)
                                        ->columnSpanFull()
                                        ->extraAttributes([
                                            'x-data' => '{}',
                                            'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                        ])
                                        ->suffix(function(){

                                        })
                                        ->afterStateUpdated(function(Get $get,Set $set){
                                                // Update belanja barang
                                                $set('realisasi_belanja_barang',
                                                self::toInt($get('realisasi_lidik_sidik')) +
                                                self::toInt($get('realisasi_dukops_giat')) +
                                                self::toInt($get('realisasi_harwat_r4_6_10')) +
                                                self::toInt($get('realisasi_harwat_fungsional'))
                                                );

                                                self::hitungSemuaSilpa($get, $set);
                                            }
                                        ),
                                    MoneyDisplay::make('realisasi_belanja_barang')
                                        ->label('Belanja Barang')
                                        ->prefix('Rp.')
                                        ->suffix(fn (Get $get) => self::toInt($get('belanja_pegawai_pagu')) > 0
                                        ? (self::toInt($get('realisasi_belanja_barang')) <= self::toInt($get('belanja_pegawai_pagu'))
                                            // progress normal
                                            ? round(self::toInt($get('realisasi_belanja_barang')) / self::toInt($get('belanja_pegawai_pagu')) * 100, 2) . ' %'
                                            // overbudget → tampil minus selisih
                                            : '-' . round((self::toInt($get('realisasi_belanja_barang')) - self::toInt($get('belanja_pegawai_pagu'))) / self::toInt($get('belanja_pegawai_pagu')) * 100, 2) . ' %')
                                        : '0 %'),
                                    Fieldset::make('Lidik / Sidik')
                                        ->schema([
                                            TextInput::make('realisasi_lidik_sidik')
                                                ->label('Total Lidik/Sidik')
                                                ->disabled()
                                                ->dehydrated(true)
                                                ->hidden()
                                                ->prefix('Rp.')
                                                ->mask(RawJs::make('$money($input)'))
                                                ->extraAttributes([
                                                    'x-data' => '{}',
                                                    'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                ])
                                                ->afterStateHydrated(function (Get $get, Set $set) {
                                                    $total = self::toInt($get('subdit1_lidik_sidik_realisasi'))
                                                            + self::toInt($get('subdit2_lidik_sidik_realisasi'));
                                                    $set('realisasi_lidik_sidik', $total);

                                                    // Update belanja barang
                                                    $set('realisasi_belanja_barang',
                                                    self::toInt($get('realisasi_lidik_sidik')) +
                                                    self::toInt($get('realisasi_dukops_giat')) +
                                                    self::toInt($get('realisasi_harwat_r4_6_10')) +
                                                    self::toInt($get('realisasi_harwat_fungsional'))
                                                    );
                                                })
                                                ->afterStateUpdated(function (Get $get, Set $set) {
                                                    $total = self::toInt($get('subdit1_lidik_sidik_realisasi'))
                                                            + self::toInt($get('subdit2_lidik_sidik_realisasi'));
                                                    $set('realisasi_lidik_sidik', $total);

                                                    // Update belanja barang
                                                    $set('realisasi_belanja_barang',
                                                    self::toInt($get('realisasi_lidik_sidik')) +
                                                    self::toInt($get('realisasi_dukops_giat')) +
                                                    self::toInt($get('realisasi_harwat_r4_6_10')) +
                                                    self::toInt($get('realisasi_harwat_fungsional'))
                                                    );

                                                    self::hitungSemuaSilpa($get, $set);
                                                })
                                                ->suffix(fn (Get $get) => self::toInt($get('lidik_sidik_pagu')) > 0
                                                    ? (self::toInt($get('realisasi_lidik_sidik')) <= self::toInt($get('lidik_sidik_pagu'))
                                                        // progress normal
                                                        ? round(self::toInt($get('realisasi_lidik_sidik')) / self::toInt($get('lidik_sidik_pagu')) * 100, 2) . ' %'
                                                        // overbudget → minus selisih
                                                        : '-' . round(
                                                            (self::toInt($get('realisasi_lidik_sidik')) - self::toInt($get('lidik_sidik_pagu')))
                                                            / self::toInt($get('lidik_sidik_pagu')) * 100,
                                                            2
                                                        ) . ' %')
                                                    : '0 %')
                                                ->columnSpanFull(), 
                                            MoneyDisplay::make('realisasi_lidik_sidik')
                                                ->label('Total Lidik/Sidik')
                                                ->prefix('Rp.')
                                                ->columnSpanFull()
                                                ->suffix(fn (Get $get) => self::toInt($get('lidik_sidik_pagu')) > 0
                                                    ? (self::toInt($get('realisasi_lidik_sidik')) <= self::toInt($get('lidik_sidik_pagu'))
                                                        // progress normal
                                                        ? round(self::toInt($get('realisasi_lidik_sidik')) / self::toInt($get('lidik_sidik_pagu')) * 100, 2) . ' %'
                                                        // overbudget → minus selisih
                                                        : '-' . round(
                                                            (self::toInt($get('realisasi_lidik_sidik')) - self::toInt($get('lidik_sidik_pagu')))
                                                            / self::toInt($get('lidik_sidik_pagu')) * 100,
                                                            2
                                                        ) . ' %')
                                                    : '0 %'),
                                            Grid::make(2)->schema([
                                                Section::make('Subdit I')
                                                    ->columns(1)
                                                    ->columnSpan(1)
                                                    ->schema([
                                                        TextInput::make('subdit1_lidik_sidik_realisasi')
                                                            ->label('Total Subdit I')
                                                            ->disabled()
                                                            ->hidden()
                                                            ->dehydrated(true)
                                                            ->prefix('Rp.')
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->afterStateUpdated(function(Get $get,Set $set){
                                                                // Update belanja barang
                                                                $set('realisasi_belanja_barang',
                                                                self::toInt($get('realisasi_lidik_sidik')) +
                                                                self::toInt($get('realisasi_dukops_giat')) +
                                                                self::toInt($get('realisasi_harwat_r4_6_10')) +
                                                                self::toInt($get('realisasi_harwat_fungsional'))
                                                                );

                                                                self::hitungSemuaSilpa($get, $set);
                                                            })
                                                            ->extraAttributes([
                                                                'x-data' => '{}',
                                                                'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                            ])
                                                            ->afterStateHydrated(function (Get $get, Set $set) {
                                                                $total = 0;
                                                                foreach (range(1, 5) as $i) {
                                                                    $total += self::toInt($get("subdit1_unit{$i}_lidik_sidik_realisasi")) ?: 0;
                                                                }
                                                                $set('subdit1_lidik_sidik_realisasi', $total);
                                                            })
                                                            ->suffix(fn (Get $get) => self::toInt($get('subdit1_lidik_sidik_pagu')) > 0
                                                                ? (self::toInt($get('subdit1_lidik_sidik_realisasi')) <= self::toInt($get('subdit1_lidik_sidik_pagu'))
                                                                    // progress normal
                                                                    ? round(self::toInt($get('subdit1_lidik_sidik_realisasi')) / self::toInt($get('subdit1_lidik_sidik_pagu')) * 100, 2) . ' %'
                                                                    // overbudget → minus selisih
                                                                    : '-' . round(
                                                                        (self::toInt($get('subdit1_lidik_sidik_realisasi')) - self::toInt($get('subdit1_lidik_sidik_pagu')))
                                                                        / self::toInt($get('subdit1_lidik_sidik_pagu')) * 100, 
                                                                        2
                                                                    ) . ' %')
                                                                : '0 %'),
                                                        MoneyDisplay::make('subdit1_lidik_sidik_realisasi')
                                                            ->label('Total Subdit I')
                                                            ->prefix('Rp.')
                                                            ->columnSpanFull()
                                                            ->suffix(fn (Get $get) => self::toInt($get('subdit1_lidik_sidik_pagu')) > 0
                                                                ? (self::toInt($get('subdit1_lidik_sidik_realisasi')) <= self::toInt($get('subdit1_lidik_sidik_pagu'))
                                                                    // progress normal
                                                                    ? round(self::toInt($get('subdit1_lidik_sidik_realisasi')) / self::toInt($get('subdit1_lidik_sidik_pagu')) * 100, 2) . ' %'
                                                                    // overbudget → minus selisih
                                                                    : '-' . round(
                                                                        (self::toInt($get('subdit1_lidik_sidik_realisasi')) - self::toInt($get('subdit1_lidik_sidik_pagu')))
                                                                        / self::toInt($get('subdit1_lidik_sidik_pagu')) * 100, 
                                                                        2
                                                                    ) . ' %')
                                                                : '0 %'),
                    
                                                        // unit-unit
                                                        ...collect(range(1, 5))->map(fn ($i) =>
                                                            TextInput::make("subdit1_unit{$i}_lidik_sidik_realisasi")
                                                                ->label("Subdit I - Unit {$i}")
                                                                ->prefix('Rp.')
                                                                ->mask(RawJs::make('$money($input)'))
                                                                ->default(0)
                                                                ->live(onBlur: true)
                                                                ->extraAttributes([
                                                                    'x-data' => '{}',
                                                                    'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                                ])
                                                                ->dehydrateStateUsing(fn ($state) => (int) str_replace([',', '.'], '', $state))
                                                                ->afterStateUpdated(function(Get $get,Set $set){
                                                                    $total = 0;
                                                                    foreach (range(1, 5) as $i) {
                                                                        $total += self::toInt($get("subdit1_unit{$i}_lidik_sidik_realisasi"));
                                                                    }
                                                                    $set('subdit1_lidik_sidik_realisasi', $total);

                                                                    // Hitung total Lidik/Sidik
                                                                    $set('realisasi_lidik_sidik', 
                                                                        self::toInt($get('subdit1_lidik_sidik_realisasi')) + self::toInt($get('subdit2_lidik_sidik_realisasi'))
                                                                    );

                                                                    // Update belanja barang
                                                                        $set('realisasi_belanja_barang',
                                                                        self::toInt($get('realisasi_lidik_sidik')) +
                                                                        self::toInt($get('realisasi_dukops_giat')) +
                                                                        self::toInt($get('realisasi_harwat_r4_6_10')) +
                                                                        self::toInt($get('realisasi_harwat_fungsional'))
                                                                    );

                                                                    // Hitung total realisasi
                                                                    $pegawai = self::toInt($get('realisasi_belanja_pegawai'));
                                                                    $barang  = self::toInt($get('realisasi_belanja_barang'));
                                                                    $set('realisasi', $pegawai + $barang);

                                                                    self::hitungSemuaSilpa($get, $set);
                                                                })
                                                                ->suffix(fn (Get $get) => self::toInt($get("subdit1_unit{$i}_lidik_sidik_pagu")) > 0
                                                                    ? (self::toInt($get("subdit1_unit{$i}_lidik_sidik_realisasi")) <= self::toInt($get("subdit1_unit{$i}_lidik_sidik_pagu"))
                                                                        // progress normal
                                                                        ? round(self::toInt($get("subdit1_unit{$i}_lidik_sidik_realisasi")) / self::toInt($get("subdit1_unit{$i}_lidik_sidik_pagu")) * 100, 2) . ' %'
                                                                        // overbudget → minus selisih
                                                                        : '-' . round((self::toInt($get("subdit1_unit{$i}_lidik_sidik_realisasi")) - self::toInt($get("subdit1_unit{$i}_lidik_sidik_pagu"))) / self::toInt($get("subdit1_unit{$i}_lidik_sidik_pagu")) * 100, 2) . ' %')
                                                                    : '0 %')
                                                        )->toArray(),
                                                    ]),
                                                Section::make('Subdit II')    
                                                    ->columns(1)
                                                    ->columnSpan(1)
                                                    ->schema([
                                                        TextInput::make('subdit2_lidik_sidik_realisasi')
                                                            ->label('Total Subdit II')
                                                            ->disabled()
                                                            ->hidden()
                                                            ->dehydrated(true)
                                                            ->extraAttributes([
                                                                'x-data' => '{}',
                                                                'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                            ])
                                                            ->afterStateUpdated(function(Get $get,Set $set){
                                                                // Update belanja barang
                                                                $set('realisasi_belanja_barang',
                                                                self::toInt($get('realisasi_lidik_sidik')) +
                                                                self::toInt($get('realisasi_dukops_giat')) +
                                                                self::toInt($get('realisasi_harwat_r4_6_10')) +
                                                                self::toInt($get('realisasi_harwat_fungsional'))
                                                                );

                                                                self::hitungSemuaSilpa($get, $set);
                                                            })
                                                            ->afterStateHydrated(function (Get $get, Set $set) {
                                                                $total = 0;
                                                                foreach (range(1, 5) as $i) {
                                                                    $total += self::toInt($get("subdit2_unit{$i}_lidik_sidik_realisasi")) ?: 0;
                                                                }
                                                                $set('subdit2_lidik_sidik_realisasi', $total);
                                                            })
                                                            ->suffix(fn (Get $get) => self::toInt($get('subdit2_lidik_sidik_pagu')) > 0
                                                                ? (self::toInt($get('subdit2_lidik_sidik_realisasi')) <= self::toInt($get('subdit2_lidik_sidik_pagu'))
                                                                    // progress normal
                                                                    ? round(self::toInt($get('subdit2_lidik_sidik_realisasi')) / self::toInt($get('subdit2_lidik_sidik_pagu')) * 100, 2) . ' %'
                                                                    // overbudget → minus selisih
                                                                    : '-' . round(
                                                                        (self::toInt($get('subdit2_lidik_sidik_realisasi')) - self::toInt($get('subdit2_lidik_sidik_pagu')))
                                                                        / self::toInt($get('subdit2_lidik_sidik_pagu')) * 100, 
                                                                        2
                                                                    ) . ' %')
                                                                : '0 %'),
                                                        MoneyDisplay::make('subdit2_lidik_sidik_realisasi')
                                                            ->label('Total Subdit I')
                                                            ->prefix('Rp.')
                                                            ->columnSpanFull()
                                                            ->suffix(fn (Get $get) => self::toInt($get('subdit2_lidik_sidik_pagu')) > 0
                                                                ? (self::toInt($get('subdit2_lidik_sidik_realisasi')) <= self::toInt($get('subdit2_lidik_sidik_pagu'))
                                                                    // progress normal
                                                                    ? round(self::toInt($get('subdit2_lidik_sidik_realisasi')) / self::toInt($get('subdit2_lidik_sidik_pagu')) * 100, 2) . ' %'
                                                                    // overbudget → minus selisih
                                                                    : '-' . round(
                                                                        (self::toInt($get('subdit2_lidik_sidik_realisasi')) - self::toInt($get('subdit2_lidik_sidik_pagu')))
                                                                        / self::toInt($get('subdit2_lidik_sidik_pagu')) * 100, 
                                                                        2
                                                                    ) . ' %')
                                                                : '0 %'),
                                                        ...collect(range(1, 5))->map(fn ($i) =>
                                                            TextInput::make("subdit2_unit{$i}_lidik_sidik_realisasi")
                                                                ->label("Subdit II - Unit {$i}")
                                                                ->default(0)
                                                                ->live(onBlur: true)
                                                                ->mask(RawJs::make('$money($input)'))
                                                                ->extraAttributes([
                                                                    'x-data' => '{}',
                                                                    'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                                ])
                                                                ->dehydrateStateUsing(fn ($state) => (int) str_replace([',', '.'], '', $state))
                                                                ->afterStateUpdated(function(Get $get,Set $set){
                                                                    $total = 0;
                                                                    foreach (range(1, 5) as $i) {
                                                                        $total += self::toInt($get("subdit2_unit{$i}_lidik_sidik_realisasi"));
                                                                    }
                                                                    $set('subdit2_lidik_sidik_realisasi', $total);

                                                                    // Hitung total Lidik/Sidik
                                                                    $set('realisasi_lidik_sidik', 
                                                                        self::toInt($get('subdit1_lidik_sidik_realisasi')) + self::toInt($get('subdit2_lidik_sidik_realisasi'))
                                                                    );

                                                                    // Hitung belanja barang pagu
                                                                    $set('realisasi_belanja_barang',
                                                                        self::toInt($get('realisasi_lidik_sidik')) 
                                                                        + self::toInt($get('realisasi_dukops_giat')) 
                                                                        + self::toInt($get('realisasi_harwat_r4_6_10')) 
                                                                        + self::toInt($get('realisasi_harwat_fungsional'))
                                                                    );

                                                                    // Hitung total realisasi
                                                                    $pegawai = self::toInt($get('realisasi_belanja_pegawai'));
                                                                    $barang  = self::toInt($get('realisasi_belanja_barang'));
                                                                    $set('realisasi', $pegawai + $barang);

                                                                    self::hitungSemuaSilpa($get, $set);
                                                                })
                                                                ->suffix(fn (Get $get) => self::toInt($get("subdit2_unit{$i}_lidik_sidik_pagu")) > 0
                                                                    ? (self::toInt($get("subdit2_unit{$i}_lidik_sidik_realisasi")) <= self::toInt($get("subdit2_unit{$i}_lidik_sidik_pagu"))
                                                                        // progress normal
                                                                        ? round(self::toInt($get("subdit2_unit{$i}_lidik_sidik_realisasi")) / self::toInt($get("subdit2_unit{$i}_lidik_sidik_pagu")) * 100, 2) . ' %'
                                                                        // overbudget → minus selisih
                                                                        : '-' . round((self::toInt($get("subdit2_unit{$i}_lidik_sidik_realisasi")) - self::toInt($get("subdit2_unit{$i}_lidik_sidik_pagu"))) / self::toInt($get("subdit1_unit{$i}_lidik_sidik_pagu")) * 100, 2) . ' %')
                                                                    : '0 %')
                                                        )->toArray(),
                                                    ]),
                                            ]),
                                        ]),
                                    Fieldset::make('Dukops Giat')
                                        ->schema([
                                            TextInput::make('realisasi_dukops_giat')
                                                ->label('')
                                                ->default(0)
                                                ->columnSpanFull()
                                                ->live(onBlur: true)
                                                ->mask(RawJs::make('$money($input)'))
                                                ->prefix('Rp.')
                                                ->dehydrateStateUsing(fn ($state) => (int) str_replace([',', '.'], '', $state))
                                                ->afterStateUpdated(function(Get $get,Set $set){
                                                       // Update belanja barang
                                                        $set('realisasi_belanja_barang',
                                                        self::toInt($get('realisasi_lidik_sidik')) +
                                                        self::toInt($get('realisasi_dukops_giat')) +
                                                        self::toInt($get('realisasi_harwat_r4_6_10')) +
                                                        self::toInt($get('realisasi_harwat_fungsional'))
                                                    );

                                                    // Hitung total realisasi
                                                    $pegawai = self::toInt($get('realisasi_belanja_pegawai'));
                                                    $barang  = self::toInt($get('realisasi_belanja_barang'));
                                                    $set('realisasi', $pegawai + $barang);

                                                    self::hitungSemuaSilpa($get, $set);
                                                })
                                                ->suffix(fn (Get $get) => self::toInt($get('dukops_giat_pagu')) > 0
                                                    ? (self::toInt($get('realisasi_dukops_giat')) <= self::toInt($get('dukops_giat_pagu'))
                                                        // normal progress
                                                        ? round(self::toInt($get('realisasi_dukops_giat')) / self::toInt($get('dukops_giat_pagu')) * 100, 2) . ' %'
                                                        // overbudget → minus selisih
                                                        : '-' . round(
                                                            (self::toInt($get('realisasi_dukops_giat')) - self::toInt($get('dukops_giat_pagu')))
                                                            / self::toInt($get('dukops_giat_pagu')) * 100, 2
                                                        ) . ' %')
                                                    : '0 %'),
                                        ])->columnSpanFull(),
                                    Fieldset::make('Harwat R4/6/10')
                                        ->schema([
                                            TextInput::make('realisasi_harwat_r4_6_10')->label('')
                                                ->columnSpanFull()
                                                ->default(0)
                                                ->live(onBlur: true)
                                                ->mask(RawJs::make('$money($input)'))
                                                ->prefix('Rp.')
                                                ->dehydrateStateUsing(fn ($state) => (int) str_replace([',', '.'], '', $state))
                                                ->afterStateUpdated(function(Get $get,Set $set){
                                                    // Update belanja barang
                                                    $set('realisasi_belanja_barang',
                                                        self::toInt($get('realisasi_lidik_sidik')) +
                                                        self::toInt($get('realisasi_dukops_giat')) +
                                                        self::toInt($get('realisasi_harwat_r4_6_10')) +
                                                        self::toInt($get('realisasi_harwat_fungsional'))
                                                    );

                                                    // Hitung total realisasi
                                                    $pegawai = self::toInt($get('realisasi_belanja_pegawai'));
                                                    $barang  = self::toInt($get('realisasi_belanja_barang'));
                                                    $set('realisasi', $pegawai + $barang);

                                                    self::hitungSemuaSilpa($get, $set);
                                                })
                                                ->suffix(fn (Get $get) => self::toInt($get('dukops_giat_pagu')) > 0
                                                    ? (self::toInt($get('realisasi_dukops_giat')) <= self::toInt($get('dukops_giat_pagu'))
                                                        // normal progress
                                                        ? round(self::toInt($get('realisasi_dukops_giat')) / self::toInt($get('dukops_giat_pagu')) * 100, 2) . ' %'
                                                        // overbudget → minus selisih
                                                        : '-' . round(
                                                            (self::toInt($get('realisasi_dukops_giat')) - self::toInt($get('dukops_giat_pagu')))
                                                            / self::toInt($get('dukops_giat_pagu')) * 100, 2
                                                        ) . ' %')
                                                    : '0 %'),
                                        ])->columnSpanFull(),
                                    Fieldset::make('Harwat Fungsional (Subdit III)')
                                        ->schema([
                                            TextInput::make('realisasi_harwat_fungsional')
                                                ->label('Total Harwat Fungsional (Subdit III)')
                                                ->hidden()
                                                ->default(0)
                                                ->disabled()
                                                ->mask(RawJs::make('$money($input)'))
                                                ->prefix('Rp.')
                                                ->dehydrated(true)
                                                ->extraAttributes([
                                                    'x-data' => '{}',
                                                    'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                ])
                                                ->afterStateUpdated(function (Get $get, Set $set) {
                                                    // Update belanja barang
                                                    $set('realisasi_belanja_barang',
                                                        self::toInt($get('realisasi_lidik_sidik')) +
                                                        self::toInt($get('realisasi_dukops_giat')) +
                                                        self::toInt($get('realisasi_harwat_r4_6_10')) +
                                                        self::toInt($get('realisasi_harwat_fungsional'))
                                                    );
                                    
                                                    // Hitung total realisasi
                                                    $pegawai = self::toInt($get('realisasi_belanja_pegawai'));
                                                    $barang  = self::toInt($get('realisasi_belanja_barang'));
                                                    $set('realisasi', $pegawai + $barang);

                                                    self::hitungSemuaSilpa($get, $set);
                                                })
                                                ->suffix(fn (Get $get) => self::toInt($get('harwat_fungsional_pagu')) > 0
                                                    ? (self::toInt($get('realisasi_harwat_fungsional')) <= self::toInt($get('harwat_fungsional_pagu'))
                                                        ? round(self::toInt($get('realisasi_harwat_fungsional')) / self::toInt($get('harwat_fungsional_pagu')) * 100, 2) . ' %'
                                                        : '-' . round(
                                                            (self::toInt($get('realisasi_harwat_fungsional')) - self::toInt($get('harwat_fungsional_pagu')))
                                                            / self::toInt($get('harwat_fungsional_pagu')) * 100, 2
                                                        ) . ' %')
                                                    : '0 %')
                                                ->columnSpanFull(),
                                            MoneyDisplay::make('realisasi_harwat_fungsional')
                                                ->label('Total Harwat Fungsional (Subdit III)')
                                                ->prefix('Rp.')
                                                ->columnSpanFull()
                                                ->suffix(fn (Get $get) => self::toInt($get('harwat_fungsional_pagu')) > 0
                                                    ? (self::toInt($get('realisasi_harwat_fungsional')) <= self::toInt($get('harwat_fungsional_pagu'))
                                                        ? round(self::toInt($get('realisasi_harwat_fungsional')) / self::toInt($get('harwat_fungsional_pagu')) * 100, 2) . ' %'
                                                        : '-' . round(
                                                            (self::toInt($get('realisasi_harwat_fungsional')) - self::toInt($get('harwat_fungsional_pagu')))
                                                            / self::toInt($get('harwat_fungsional_pagu')) * 100, 2
                                                        ) . ' %')
                                                    : '0 %')
                                                ->columnSpanFull(),
                                            Grid::make(2)->schema([
                                                Section::make('Har Alsus')
                                                    ->schema([
                                                        TextInput::make('subdit3_har_alsus_realisasi')
                                                            ->label('Total Subdit III Har Alsus')
                                                            ->disabled()
                                                            ->hidden()
                                                            ->dehydrated(true)
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->prefix('Rp.')
                                                            ->extraAttributes([
                                                                'x-data' => '{}',
                                                                'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                            ])
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungSemuaSilpa($get, $set))
                                                            ->suffix(fn (Get $get) => self::toInt($get('subdit3_har_alsus_pagu')) > 0
                                                                ? (self::toInt($get('subdit3_har_alsus_realisasi')) <= self::toInt($get('subdit3_har_alsus_pagu'))
                                                                    ? round(self::toInt($get('subdit3_har_alsus_realisasi')) / self::toInt($get('subdit3_har_alsus_pagu')) * 100, 2) . ' %'
                                                                    : '-' . round(
                                                                        (self::toInt($get('subdit3_har_alsus_realisasi')) - self::toInt($get('subdit3_har_alsus_pagu')))
                                                                        / self::toInt($get('subdit3_har_alsus_pagu')) * 100, 2
                                                                    ) . ' %')
                                                                : '0 %'),
                                                        MoneyDisplay::make('subdit3_har_alsus_realisasi')
                                                            ->label('Total Subdit III Har Alsus')
                                                            ->prefix('Rp.')
                                                            ->columnSpanFull()
                                                            ->suffix(fn (Get $get) => self::toInt($get('subdit3_har_alsus_pagu')) > 0
                                                                ? (self::toInt($get('subdit3_har_alsus_realisasi')) <= self::toInt($get('subdit3_har_alsus_pagu'))
                                                                    ? round(self::toInt($get('subdit3_har_alsus_realisasi')) / self::toInt($get('subdit3_har_alsus_pagu')) * 100, 2) . ' %'
                                                                    : '-' . round(
                                                                        (self::toInt($get('subdit3_har_alsus_realisasi')) - self::toInt($get('subdit3_har_alsus_pagu')))
                                                                        / self::toInt($get('subdit3_har_alsus_pagu')) * 100, 2
                                                                    ) . ' %')
                                                                : '0 %'),
                                    
                                                        ...collect(range(1, 5))->map(fn ($i) =>
                                                            TextInput::make("subdit3_unit{$i}_har_alsus_realisasi")
                                                                ->label("Unit {$i}")
                                                                ->default(0)
                                                                ->mask(RawJs::make('$money($input)'))
                                                                ->prefix('Rp.')
                                                                ->live(onBlur: true)
                                                                ->extraAttributes([
                                                                    'x-data' => '{}',
                                                                    'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                                ])
                                                                ->dehydrateStateUsing(fn ($state) => (int) str_replace([',', '.'], '', $state))
                                                                ->afterStateUpdated(function (Get $get, Set $set) {
                                                                    // Sum unit1-5 ke total Har Alsus
                                                                    $total = collect(range(1, 5))->reduce(
                                                                        fn ($carry, $j) => $carry + self::toInt($get("subdit3_unit{$j}_har_alsus_realisasi")),
                                                                        0
                                                                    );
                                                                    $set('subdit3_har_alsus_realisasi', $total);
                                    
                                                                    // Update Harwat Fungsional
                                                                    $set('realisasi_harwat_fungsional',
                                                                        self::toInt($get('subdit3_har_alsus_realisasi')) +
                                                                        self::toInt($get('subdit3_lisensi_latfung_realisasi'))
                                                                    );

                                                                    // Update belanja barang
                                                                    $set('realisasi_belanja_barang',
                                                                        self::toInt($get('realisasi_lidik_sidik')) +
                                                                        self::toInt($get('realisasi_dukops_giat')) +
                                                                        self::toInt($get('realisasi_harwat_r4_6_10')) +
                                                                        self::toInt($get('realisasi_harwat_fungsional'))
                                                                    );

                                                                    // Hitung total realisasi
                                                                    $pegawai = self::toInt($get('realisasi_belanja_pegawai'));
                                                                    $barang  = self::toInt($get('realisasi_belanja_barang'));
                                                                    $set('realisasi', $pegawai + $barang);

                                                                    self::hitungSemuaSilpa($get, $set);
                                                                })
                                                                ->suffix(function (Get $get) use ($i) {
                                                                    $paguKey = "subdit3_unit{$i}_har_alsus_pagu";
                                                                    $realisasi = self::toInt($get("subdit3_unit{$i}_har_alsus_realisasi"));
                                                                    $pagu      = self::toInt($get($paguKey));
                                                                
                                                                    if ($pagu > 0) {
                                                                        if ($realisasi <= $pagu) {
                                                                            return round($realisasi / $pagu * 100, 2) . ' %';
                                                                        } else {
                                                                            return '-' . round(($realisasi - $pagu) / $pagu * 100, 2) . ' %';
                                                                        }
                                                                    }
                                                                
                                                                    return '0 %';
                                                                })
                                                                
                                                        )->toArray(),
                                                    ])
                                                    ->columns(1)
                                                    ->columnSpan(1),
                                    
                                                Section::make('Lisensi Latfung')
                                                    ->schema([
                                                        TextInput::make('subdit3_lisensi_latfung_realisasi')
                                                            ->label('Total Subdit III Lisensi Latfung')
                                                            ->disabled()
                                                            ->hidden()
                                                            ->dehydrated(true)
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->prefix('Rp.')
                                                            ->extraAttributes([
                                                                'x-data' => '{}',
                                                                'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                            ])
                                                            ->afterStateUpdated(fn ($state, Get $get, Set $set) => self::hitungSemuaSilpa($get, $set))
                                                            ->suffix(fn (Get $get) => self::toInt($get('subdit3_lisensi_latfung_pagu')) > 0
                                                                ? (self::toInt($get('subdit3_lisensi_latfung_realisasi')) <= self::toInt($get('subdit3_lisensi_latfung_pagu'))
                                                                    ? round(self::toInt($get('subdit3_lisensi_latfung_realisasi')) / self::toInt($get('subdit3_lisensi_latfung_pagu')) * 100, 2) . ' %'
                                                                    : '-' . round(
                                                                        (self::toInt($get('subdit3_lisensi_latfung_realisasi')) - self::toInt($get('subdit3_lisensi_latfung_pagu')))
                                                                        / self::toInt($get('subdit3_lisensi_latfung_pagu')) * 100, 2
                                                                    ) . ' %')
                                                                : '0 %'),
                                                        MoneyDisplay::make('subdit3_lisensi_latfung_realisasi')
                                                            ->label('Total Subdit III Lisensi Latfung')
                                                            ->prefix('Rp.')
                                                            ->columnSpanFull()
                                                            ->suffix(fn (Get $get) => self::toInt($get('subdit3_lisensi_latfung_pagu')) > 0
                                                                ? (self::toInt($get('subdit3_lisensi_latfung_realisasi')) <= self::toInt($get('subdit3_lisensi_latfung_pagu'))
                                                                    ? round(self::toInt($get('subdit3_lisensi_latfung_realisasi')) / self::toInt($get('subdit3_lisensi_latfung_pagu')) * 100, 2) . ' %'
                                                                    : '-' . round(
                                                                        (self::toInt($get('subdit3_lisensi_latfung_realisasi')) - self::toInt($get('subdit3_lisensi_latfung_pagu')))
                                                                        / self::toInt($get('subdit3_lisensi_latfung_pagu')) * 100, 2
                                                                    ) . ' %')
                                                                : '0 %'),
                                                        ...collect(range(1, 5))->map(fn ($i) =>
                                                            TextInput::make("subdit3_unit{$i}_lisensi_latfung_realisasi")
                                                                ->label("Unit {$i}")
                                                                ->default(0)
                                                                ->mask(RawJs::make('$money($input)'))
                                                                ->prefix('Rp.')
                                                                ->live(onBlur: true)
                                                                ->extraAttributes([
                                                                    'x-data' => '{}',
                                                                    'x-init' => '$el.value = new Intl.NumberFormat("id-ID").format($el.value)',
                                                                ])
                                                                ->dehydrateStateUsing(fn ($state) => (int) str_replace([',', '.'], '', $state))
                                                                ->afterStateUpdated(function (Get $get, Set $set) {
                                                                    // Sum unit1-5 ke total Lisensi Latfung
                                                                    $total = collect(range(1, 5))->reduce(
                                                                        fn ($carry, $j) => $carry + self::toInt($get("subdit3_unit{$j}_lisensi_latfung_realisasi")),
                                                                        0
                                                                    );
                                                                    $set('subdit3_lisensi_latfung_realisasi', $total);
                                    
                                                                    // Update Harwat Fungsional
                                                                    $set('realisasi_harwat_fungsional',
                                                                        self::toInt($get('subdit3_har_alsus_realisasi')) +
                                                                        self::toInt($get('subdit3_lisensi_latfung_realisasi'))
                                                                    );

                                                                    // Update belanja barang
                                                                    $set('realisasi_belanja_barang',
                                                                        self::toInt($get('realisasi_lidik_sidik')) +
                                                                        self::toInt($get('realisasi_dukops_giat')) +
                                                                        self::toInt($get('realisasi_harwat_r4_6_10')) +
                                                                        self::toInt($get('realisasi_harwat_fungsional'))
                                                                    );

                                                                    // Hitung total realisasi
                                                                    $pegawai = self::toInt($get('realisasi_belanja_pegawai'));
                                                                    $barang  = self::toInt($get('realisasi_belanja_barang'));
                                                                    $set('realisasi', $pegawai + $barang);

                                                                    self::hitungSemuaSilpa($get, $set);
                                                                })
                                                                ->suffix(function (Get $get) use ($i) {
                                                                    $paguKey = "subdit3_unit{$i}_lisensi_latfung_pagu";
                                                                    $realisasi = self::toInt($get("subdit3_unit{$i}_lisensi_latfung_realisasi"));
                                                                    $pagu      = self::toInt($get($paguKey));
                                                                
                                                                    if ($pagu > 0) {
                                                                        if ($realisasi <= $pagu) {
                                                                            return round($realisasi / $pagu * 100, 2) . ' %';
                                                                        } else {
                                                                            return '-' . round(($realisasi - $pagu) / $pagu * 100, 2) . ' %';
                                                                        }
                                                                    }
                                                                
                                                                    return '0 %';
                                                                })
                                                                
                                                        )->toArray(),
                                                    ])
                                                    ->columns(1)
                                                    ->columnSpan(1),
                                            ]),
                                        ])->columnSpanFull()
                                    
                                ]),
                            
                        ]),
                    Step::make('III. SILPA')
                        ->schema([
                            TextInput::make('silpa')
                                ->label('Total SILPA')
                                ->disabled()
                                ->dehydrated(true)
                                ->prefix('Rp.')
                                ->mask(RawJs::make('$money($input)'))
                                ->afterStateHydrated(function (Get $get, Set $set) {
                                    self::hitungSemuaSilpa($get, $set);
                                })
                                ->suffix(fn (Get $get) => self::persenSilpa($get('pagu'), $get('realisasi'))),
                    
                            TextInput::make('silpa_belanja_pegawai')
                                ->label('Belanja Pegawai')
                                ->disabled()
                                ->dehydrated(true)
                                ->prefix('Rp.')
                                ->afterStateHydrated(function (Get $get, Set $set) {
                                    self::hitungSemuaSilpa($get, $set);
                                })
                                ->default(fn (Get $get) => self::toInt($get('belanja_pegawai_pagu')) - self::toInt($get('realisasi_belanja_pegawai')))
                                ->mask(RawJs::make('$money($input)'))
                                ->suffix(fn (Get $get) => self::persenSilpa($get('belanja_pegawai_pagu'), $get('realisasi_belanja_pegawai'))),
                    
                            Section::make('Belanja Barang')
                                ->schema([
                                    TextInput::make('silpa_belanja_barang')
                                        ->label('Belanja Barang')
                                        ->live()
                                        ->disabled()
                                        ->mask(RawJs::make('$money($input)'))
                                        ->afterStateHydrated(function (Get $get, Set $set) {
                                            self::hitungSemuaSilpa($get, $set);
                                        })
                                        ->dehydrated(true)
                                        ->prefix('Rp.')
                                        ->suffix(fn (Get $get) => self::persenSilpa($get('belanja_barang_pagu'), $get('realisasi_belanja_barang'))),
                    
                                    Fieldset::make('Lidik / Sidik')
                                        ->schema([
                                            TextInput::make('silpa_lidik_sidik')
                                                ->label('Total Lidik/Sidik')
                                                ->disabled()
                                                ->dehydrated(true)
                                                ->prefix('Rp.')
                                                ->mask(RawJs::make('$money($input)'))
                                                ->columnSpanFull()
                                                ->suffix(fn (Get $get) => self::persenSilpa($get('lidik_sidik_pagu'), $get('realisasi_lidik_sidik'))),
                    
                                            Grid::make(2)->schema([
                                                Section::make('Subdit I')
                                                    ->columns(1)
                                                    ->columnSpan(1)
                                                    ->schema([
                                                        TextInput::make('subdit1_lidik_sidik_silpa')
                                                            ->label('Total Subdit I')
                                                            ->disabled()
                                                            ->dehydrated(true)
                                                            ->prefix('Rp.')
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->afterStateHydrated(function (Get $get, Set $set) {
                                                                self::hitungSemuaSilpa($get, $set);
                                                            })
                                                            ->suffix(fn (Get $get) => self::persenSilpa($get('subdit1_lidik_sidik_pagu'), $get('subdit1_lidik_sidik_realisasi'))),
                    
                                                        ...collect(range(1, 5))->map(fn ($i) =>
                                                            TextInput::make("subdit1_unit{$i}_lidik_sidik_silpa")
                                                                ->label("Subdit I - Unit {$i}")
                                                                ->disabled()
                                                                ->dehydrated(true)
                                                                ->prefix('Rp.')
                                                                ->mask(RawJs::make('$money($input)'))
                                                                ->afterStateHydrated(function (Get $get, Set $set) {
                                                                    self::hitungSemuaSilpa($get, $set);
                                                                })
                                                                ->suffix(fn (Get $get) => self::persenSilpa(
                                                                    $get("subdit1_unit{$i}_lidik_sidik_pagu"), 
                                                                    $get("subdit1_unit{$i}_lidik_sidik_realisasi")
                                                                ))
                                                        )->toArray(),
                                                    ]),
                    
                                                Section::make('Subdit II')
                                                    ->columns(1)
                                                    ->columnSpan(1)
                                                    ->schema([
                                                        TextInput::make('subdit2_lidik_sidik_silpa')
                                                            ->label('Total Subdit II')
                                                            ->disabled()
                                                            ->dehydrated(true)
                                                            ->prefix('Rp.')
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->afterStateHydrated(function (Get $get, Set $set) {
                                                                self::hitungSemuaSilpa($get, $set);
                                                            })
                                                            ->suffix(fn (Get $get) => self::persenSilpa($get('subdit2_lidik_sidik_pagu'), $get('subdit2_lidik_sidik_realisasi'))),
                    
                                                        ...collect(range(1, 5))->map(fn ($i) =>
                                                            TextInput::make("subdit2_unit{$i}_lidik_sidik_silpa")
                                                                ->label("Subdit II - Unit {$i}")
                                                                ->disabled()
                                                                ->dehydrated(true)
                                                                ->prefix('Rp.')
                                                                ->mask(RawJs::make('$money($input)'))
                                                                ->afterStateHydrated(function (Get $get, Set $set) {
                                                                    self::hitungSemuaSilpa($get, $set);
                                                                })
                                                                ->suffix(fn (Get $get) => self::persenSilpa(
                                                                    $get("subdit2_unit{$i}_lidik_sidik_pagu"), 
                                                                    $get("subdit2_unit{$i}_lidik_sidik_realisasi")
                                                                ))
                                                        )->toArray(),
                                                    ]),
                                            ]),
                                        ]),
                    
                                    Fieldset::make('Dukops Giat')
                                        ->schema([
                                            TextInput::make('silpa_dukops_giat')
                                                ->disabled()
                                                ->dehydrated(true)
                                                ->prefix('Rp.')
                                                ->mask(RawJs::make('$money($input)'))
                                                ->columnSpanFull()
                                                ->afterStateHydrated(function (Get $get, Set $set) {
                                                    self::hitungSemuaSilpa($get, $set);
                                                })
                                                ->suffix(fn (Get $get) => self::persenSilpa($get('dukops_giat_pagu'), $get('realisasi_dukops_giat'))),
                                        ]),
                    
                                    Fieldset::make('Harwat R4/6/10')
                                        ->schema([
                                            TextInput::make('silpa_harwat_r4_6_10')
                                                ->disabled()
                                                ->dehydrated(true)
                                                ->prefix('Rp.')
                                                ->mask(RawJs::make('$money($input)'))
                                                ->columnSpanFull()
                                                ->afterStateHydrated(function (Get $get, Set $set) {
                                                    self::hitungSemuaSilpa($get, $set);
                                                })
                                                ->suffix(fn (Get $get) => self::persenSilpa($get('harwat_r4_6_10_pagu'), $get('realisasi_harwat_r4_6_10'))),
                                        ]),
                    
                                    Fieldset::make('Harwat Fungsional (Subdit III)')
                                        ->schema([
                                            TextInput::make('silpa_harwat_fungsional')
                                                ->label('Total Harwat Fungsional (Subdit III)')
                                                ->disabled()
                                                ->dehydrated(true)
                                                ->prefix('Rp.')
                                                ->mask(RawJs::make('$money($input)'))
                                                ->columnSpanFull()
                                                ->afterStateHydrated(function (Get $get, Set $set) {
                                                    self::hitungSemuaSilpa($get, $set);
                                                })
                                                ->suffix(fn (Get $get) => self::persenSilpa($get('harwat_fungsional_pagu'), $get('realisasi_harwat_fungsional'))),
                    
                                            Grid::make(2)->schema([
                                                Section::make('Har Alsus')
                                                    ->columns(1)
                                                    ->columnSpan(1)
                                                    ->schema([
                                                        TextInput::make('subdit3_har_alsus_silpa')
                                                            ->label('Total Subdit III Har Alsus')
                                                            ->disabled()
                                                            ->dehydrated(true)
                                                            ->prefix('Rp.')
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->afterStateHydrated(function (Get $get, Set $set) {
                                                                self::hitungSemuaSilpa($get, $set);
                                                            })
                                                            ->suffix(fn (Get $get) => self::persenSilpa($get('subdit3_har_alsus_pagu'), $get('subdit3_har_alsus_realisasi'))),
                    
                                                        ...collect(range(1, 5))->map(fn ($i) =>
                                                            TextInput::make("subdit3_unit{$i}_har_alsus_silpa")
                                                                ->label("Unit {$i}")
                                                                ->disabled()
                                                                ->dehydrated(true)
                                                                ->prefix('Rp.')
                                                                ->mask(RawJs::make('$money($input)'))
                                                                ->afterStateHydrated(function (Get $get, Set $set) {
                                                                    self::hitungSemuaSilpa($get, $set);
                                                                })
                                                                ->suffix(fn (Get $get) => self::persenSilpa(
                                                                    $get("subdit3_unit{$i}_har_alsus_pagu"), 
                                                                    $get("subdit3_unit{$i}_har_alsus_realisasi")
                                                                ))
                                                        )->toArray(),
                                                    ]),
                    
                                                Section::make('Lisensi Latfung')
                                                    ->columns(1)
                                                    ->columnSpan(1)
                                                    ->schema([
                                                        TextInput::make('subdit3_lisensi_latfung_silpa')
                                                            ->label('Total Subdit III Lisensi Latfung')
                                                            ->disabled()
                                                            ->dehydrated(true)
                                                            ->prefix('Rp.')
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->afterStateHydrated(function (Get $get, Set $set) {
                                                                self::hitungSemuaSilpa($get, $set);
                                                            })
                                                            ->suffix(fn (Get $get) => self::persenSilpa($get('subdit3_lisensi_latfung_pagu'), $get('subdit3_lisensi_latfung_realisasi'))),
                    
                                                        ...collect(range(1, 5))->map(fn ($i) =>
                                                            TextInput::make("subdit3_unit{$i}_lisensi_latfung_silpa")
                                                                ->label("Unit {$i}")
                                                                ->disabled()
                                                                ->dehydrated(true)
                                                                ->prefix('Rp.')
                                                                ->mask(RawJs::make('$money($input)'))
                                                                ->afterStateHydrated(function (Get $get, Set $set) {
                                                                    self::hitungSemuaSilpa($get, $set);
                                                                })
                                                                ->suffix(fn (Get $get) => self::persenSilpa(
                                                                    $get("subdit3_unit{$i}_lisensi_latfung_pagu"), 
                                                                    $get("subdit3_unit{$i}_lisensi_latfung_realisasi")
                                                                ))
                                                        )->toArray(),
                                                    ]),
                                            ]),
                                        ]),
                                ]),
                        ]),
                    

                ])->submitAction(new HtmlString(Blade::render(<<<BLADE
                <x-filament::button
                    type="submit"
                    size="sm"
                >
                    Submit
                </x-filament::button>
            BLADE)))
                
                ->live()
                ->columnSpanFull()
                ->skippable()
                ->startOnStep(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tahun_anggaran'),
                TextColumn::make('file_path')->label('Dokumen'),
            ])
            ->filters([
                
            ])
            ->actions([
                // ActionGroup::make([
                //     Tables\Actions\Action::make('preview')
                //         ->label('Preview')
                //         ->icon('heroicon-o-eye')
                //         ->url(fn ($record) => route(
                //             'filament.subbagrenmin.resources.anggarans.preview', 
                //             $record
                //         ))
                //         ->openUrlInNewTab(),


                //     Tables\Actions\Action::make('editDoc')
                //         ->label('Edit Dokumen')
                //         ->icon('heroicon-o-pencil-square')
                //         ->url(fn ($record) => url("/subbagrenmin/anggaran/editor/{$record->id}"))
                //             ->openUrlInNewTab(),
                        
                //     Tables\Actions\Action::make('viewDoc')
                //         ->label('View Dokumen')
                //         ->icon('heroicon-o-eye')
                //         ->url(fn ($record) => url("/subbagrenmin/anggaran/view/{$record->id}"))
                //         ->openUrlInNewTab(),

                //     Tables\Actions\Action::make('viewPdf')
                //         ->label('View PDF')
                //         ->icon('heroicon-o-document-text')
                //         ->url(fn ($record) => route('anggaran.convertPdf', $record))
                //         ->openUrlInNewTab(),

                //     Tables\Actions\Action::make('viewPdf')
                //         ->label('View PDF')
                //         ->icon('heroicon-o-document-text')
                //         ->url(fn ($record) => route('anggaran.convertPdf', $record->id))
                //         ->openUrlInNewTab(),

                        
                //     Tables\Actions\Action::make('downloadDoc')
                //         ->label('Cetak / Download')
                //         ->icon('heroicon-o-arrow-down-tray')
                //         ->url(fn ($record) => url("/subbagrenmin/anggaran/download/{$record->id}"))
                //         ->openUrlInNewTab(),
                //         ])->button()->label('Dokumen'),
                Tables\Actions\Action::make('preview')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route(
                        'filament.subbagrenmin.resources.anggarans.preview', 
                        $record
                    )),
                Tables\Actions\Action::make('viewPdf')
                        ->label('Unduh')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->url(fn ($record) => route('anggaran.convertPdf', $record))
                        ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                ActionsDeleteAction::make(),
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
            'preview' => Pages\AnggaranPreview::route('/{record}/preview'),
        ];
    }
    private static function toInt($value): int
    {
        return (int) str_replace(['.', ','], '', $value ?? 0);
    }

    public static function persenSilpa($pagu, $realisasi): string
    {
        $pagu = self::toInt($pagu);
        $realisasi = self::toInt($realisasi);

        if ($pagu <= 0) {
            return '0 %';
        }

        $silpa = $pagu - $realisasi;

        if ($silpa >= 0) {
            return round(($silpa / $pagu) * 100, 2) . ' %';
        }

        // overbudget → minus
        return '-' . round((abs($silpa) / $pagu) * 100, 2) . ' %';
    }

    private static function hitungSemuaSilpa(Get $get, Set $set): void
    {
        // Hitung SILPA Total
        $pagu = self::toInt($get('pagu'));
        $realisasi = self::toInt($get('realisasi'));
        $silpa = $pagu - $realisasi;
        $set('silpa', $silpa);

        // Hitung SILPA Belanja Pegawai
        $paguPegawai = self::toInt($get('belanja_pegawai_pagu'));
        $realisasiPegawai = self::toInt($get('realisasi_belanja_pegawai'));
        $set('silpa_belanja_pegawai', $paguPegawai - $realisasiPegawai);

        // Hitung SILPA Belanja Barang
        $paguBarang = self::toInt($get('belanja_barang_pagu'));
        $realisasiBarang = self::toInt($get('realisasi_belanja_barang'));
        $set('silpa_belanja_barang', $paguBarang - $realisasiBarang);

        // Hitung SILPA Lidik/Sidik
        $paguLidikSidik = self::toInt($get('lidik_sidik_pagu'));
        $realisasiLidikSidik = self::toInt($get('realisasi_lidik_sidik'));
        $set('silpa_lidik_sidik', $paguLidikSidik - $realisasiLidikSidik);

        // Hitung SILPA Subdit I
        $paguSubdit1 = self::toInt($get('subdit1_lidik_sidik_pagu'));
        $realisasiSubdit1 = self::toInt($get('subdit1_lidik_sidik_realisasi'));
        $set('subdit1_lidik_sidik_silpa', $paguSubdit1 - $realisasiSubdit1);

        // Hitung SILPA Unit Subdit I
        foreach (range(1, 5) as $i) {
            $paguUnit = self::toInt($get("subdit1_unit{$i}_lidik_sidik_pagu"));
            $realisasiUnit = self::toInt($get("subdit1_unit{$i}_lidik_sidik_realisasi"));
            $set("subdit1_unit{$i}_lidik_sidik_silpa", $paguUnit - $realisasiUnit);
        }

        // Hitung SILPA Subdit II
        $paguSubdit2 = self::toInt($get('subdit2_lidik_sidik_pagu'));
        $realisasiSubdit2 = self::toInt($get('subdit2_lidik_sidik_realisasi'));
        $set('subdit2_lidik_sidik_silpa', $paguSubdit2 - $realisasiSubdit2);

        // Hitung SILPA Unit Subdit II
        foreach (range(1, 5) as $i) {
            $paguUnit = self::toInt($get("subdit2_unit{$i}_lidik_sidik_pagu"));
            $realisasiUnit = self::toInt($get("subdit2_unit{$i}_lidik_sidik_realisasi"));
            $set("subdit2_unit{$i}_lidik_sidik_silpa", $paguUnit - $realisasiUnit);
        }

        // Hitung SILPA Dukops Giat
        $paguDukops = self::toInt($get('dukops_giat_pagu'));
        $realisasiDukops = self::toInt($get('realisasi_dukops_giat'));
        $set('silpa_dukops_giat', $paguDukops - $realisasiDukops);

        // Hitung SILPA Harwat R4/6/10
        $paguHarwat = self::toInt($get('harwat_r4_6_10_pagu'));
        $realisasiHarwat = self::toInt($get('realisasi_harwat_r4_6_10'));
        $set('silpa_harwat_r4_6_10', $paguHarwat - $realisasiHarwat);

        // Hitung SILPA Harwat Fungsional
        $paguFungsional = self::toInt($get('harwat_fungsional_pagu'));
        $realisasiFungsional = self::toInt($get('realisasi_harwat_fungsional'));
        $set('silpa_harwat_fungsional', $paguFungsional - $realisasiFungsional);

        // Hitung SILPA Subdit III Har Alsus
        $paguHarAlsus = self::toInt($get('subdit3_har_alsus_pagu'));
        $realisasiHarAlsus = self::toInt($get('subdit3_har_alsus_realisasi'));
        $set('subdit3_har_alsus_silpa', $paguHarAlsus - $realisasiHarAlsus);

        // Hitung SILPA Unit Har Alsus
        foreach (range(1, 5) as $i) {
            $paguUnit = self::toInt($get("subdit3_unit{$i}_har_alsus_pagu"));
            $realisasiUnit = self::toInt($get("subdit3_unit{$i}_har_alsus_realisasi"));
            $set("subdit3_unit{$i}_har_alsus_silpa", $paguUnit - $realisasiUnit);
        }

        // Hitung SILPA Subdit III Lisensi Latfung
        $paguLisensi = self::toInt($get('subdit3_lisensi_latfung_pagu'));
        $realisasiLisensi = self::toInt($get('subdit3_lisensi_latfung_realisasi'));
        $set('subdit3_lisensi_latfung_silpa', $paguLisensi - $realisasiLisensi);

        // Hitung SILPA Unit Lisensi Latfung
        foreach (range(1, 5) as $i) {
            $paguUnit = self::toInt($get("subdit3_unit{$i}_lisensi_latfung_pagu"));
            $realisasiUnit = self::toInt($get("subdit3_unit{$i}_lisensi_latfung_realisasi"));
            $set("subdit3_unit{$i}_lisensi_latfung_silpa", $paguUnit - $realisasiUnit);
        }
    }
}
