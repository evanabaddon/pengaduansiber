<?php

namespace App\Filament\Subbagrenmin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Personil;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\KlasterJabatan;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Wizard\Step;
use Filament\Infolists\Components\TextEntry;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use App\Filament\Subbagrenmin\Resources\PersonilResource\Pages;
use App\Filament\Subbagrenmin\Resources\PersonilResource\RelationManagers;

class PersonilResource extends Resource
{
    protected static ?string $model = Personil::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Personel';

    // protected static ?string $slug = 'personel';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make()
                    ->steps([
                        // 🧩 STEP 1 — KLASTER JABATAN
                        Step::make('Klaster Jabatan')->schema([
                            Section::make('Struktur Klaster Jabatan')->schema([
                        
                                Select::make('level_1')
                                    ->label('Klaster Utama')
                                    ->options(
                                        KlasterJabatan::whereNull('parent_id')->pluck('nama', 'id')
                                    )
                                    ->reactive()
                                    ->afterStateHydrated(function ($set, $get, $record = null) {
                                        if (! $record?->klaster_jabatan_id) return;

                                        $node = KlasterJabatan::find($record->klaster_jabatan_id);
                                        if ($node) {
                                            $ancestors = $node->ancestorsAndSelf()->pluck('id')->toArray();
                                            $set('level_1', $ancestors[0] ?? null);
                                            $set('level_2', $ancestors[1] ?? null);
                                            $set('level_3', $ancestors[2] ?? null);
                                            $set('level_4', $ancestors[3] ?? null);
                                        }
                                    })
                                    ->afterStateUpdated(fn (callable $set) => $set('level_2', null)),

                        
                                Select::make('level_2')
                                    ->label('Sub Unit / Bagian')
                                    ->options(fn (callable $get) =>
                                        KlasterJabatan::where('parent_id', $get('level_1'))->pluck('nama', 'id')
                                    )
                                    ->reactive()
                                    ->visible(fn (callable $get) => $get('level_1'))
                                    ->afterStateUpdated(fn (callable $set) => $set('level_3', null)),
                        
                                Select::make('level_3')
                                    ->label('Jabatan / Subjabatan')
                                    ->options(fn (callable $get) =>
                                        KlasterJabatan::where('parent_id', $get('level_2'))->pluck('nama', 'id')
                                    )
                                    ->reactive()
                                    ->visible(fn (callable $get) => $get('level_2'))
                                    ->afterStateUpdated(fn (callable $set) => $set('level_4', null)),
                        
                                Select::make('level_4')
                                    ->label('Detail Jabatan (jika ada)')
                                    ->options(fn (callable $get) =>
                                        KlasterJabatan::where('parent_id', $get('level_3'))->pluck('nama', 'id')
                                    )
                                    ->reactive()
                                    ->visible(fn (callable $get) => $get('level_3')),
                        
                                // Placeholder::make('jabatan_terpilih')
                                //     ->label('Jabatan yang Dipilih')
                                //     ->content(fn ($get) => collect([
                                //         $get('level_1'),
                                //         $get('level_2'),
                                //         $get('level_3'),
                                //         $get('level_4'),
                                //     ])->filter()->map(fn ($id) => KlasterJabatan::find($id)?->nama)->join(' / '))
                                //     ->visible(fn ($get) => filled($get('level_1')))->columnSpanFull(),
                        
                                Hidden::make('klaster_jabatan_id')
                                    ->reactive()
                                    ->dehydrated(true)
                                    ->dehydrateStateUsing(fn ($get) =>
                                        $get('level_4') ?? $get('level_3') ?? $get('level_2') ?? $get('level_1')
                                    ),
                            ])->columns(4),
                        ]),
                        Step::make('Data Pribadi & Keluarga')->schema([
                            Section::make('Data Personil')->schema([
                                TextInput::make('nama')->required()->label('Nama Personil'),
                                TextInput::make('nrp')->label('NRP'),
                                TextInput::make('tempat_lahir')->label('Tempat Lahir'),
                                Flatpickr::make('tanggal_lahir')
                                    ->label('Tanggal Lahir')
                                    ->required()
                                    ->allowInput()
                                    ->dateFormat('Y-m-d') 
                                    ->altFormat('d F Y')
                                    ->altInput(true),
                                Select::make('golongan_darah')->label('Golongan Darah')->searchable()->options([
                                    'A' => 'A',
                                    'B' => 'B',
                                    'AB' => 'AB',
                                    'O' => 'O',
                                ]),
                                Select::make('agama')
                                    ->label('Agama')
                                    ->searchable()
                                    ->options([
                                        'Islam' => 'Islam',
                                        'Kristen' => 'Kristen',
                                        'Katolik' => 'Katolik',
                                        'Hindu' => 'Hindu',
                                        'Budha' => 'Budha',
                                        'Konghucu' => 'Konghucu',
                                    ]),
                                TextInput::make('suku')->label('Suku'),
                                PhoneInput::make('telp')->inputNumberFormat(PhoneInputNumberType::NATIONAL)->label('No. Telp'),
                                FileUpload::make('photo')->label('Foto Personil'),
                                FileUpload::make('bpjs')->label('Kartu BPJS'),
                                Select::make('status')->label('Status')->options([
                                    'Aktif' => 'Aktif',
                                    'Tidak Aktif' => 'Tidak Aktif',
                                ]),
                            ])->columns(2),
                            Section::make('Data Alamat')->schema([
                                TextInput::make('alamat')->label('Alamat')->columnSpanFull(),
                                Select::make('province_id')
                                    ->label('Provinsi')
                                    ->provinsi()
                                    ->live()
                                    ->searchable()
                                    ->afterStateUpdated(fn (callable $set) => $set('city_id', null)),
                                Select::make('city_id')
                                    ->label('Kota')
                                    ->kabupatenUmum()
                                    ->live()
                                    ->searchable()
                                    ->afterStateUpdated(fn (callable $set) => $set('district_id', null)),
                                Select::make('district_id')
                                    ->label('Kecamatan')
                                    ->kecamatanUmum()
                                    ->live()
                                    ->searchable()
                                    ->afterStateUpdated(fn (callable $set) => $set('subdistrict_id', null)),
                                Select::make('subdistrict_id')
                                    ->label('Kelurahan')
                                    ->kelurahanUmum()
                                    ->live()
                                    ->searchable(),
                            ])->columns(2),
                            Section::make('Pasangan (Istri/Suami)')->schema([
                                TextInput::make('pasangan.nama')->label('Nama Pasangan'),
                                TextInput::make('pasangan.tempat_lahir')->label('Tempat Lahir Pasangan'),
                                Flatpickr::make('pasangan.tanggal_lahir')
                                    ->label('Tanggal Lahir Pasangan')
                                    ->required()
                                    ->allowInput()
                                    ->dateFormat('Y-m-d') 
                                    ->altFormat('d F Y')
                                    ->altInput(true),
                                Select::make('pasangan.golongan_darah')->label('Golongan Darah Pasangan')->options([
                                    'A' => 'A',
                                    'B' => 'B',
                                    'AB' => 'AB',
                                    'O' => 'O',
                                ]),
                                
                                TextInput::make('pasangan.telp')->label('No. Telp Pasangan'),
                                FileUpload::make('pasangan.kartu_ktp')->label('Kartu KTP Pasangan'),
                                FileUpload::make('pasangan.bpjs')->label('Kartu BPJS'),
                            ])->columns(2),
                            Repeater::make('keluarga')->schema([
                                TextInput::make('nama')->label('Nama Anak'),
                                Flatpickr::make('tanggal_lahir')
                                    ->label('Tanggal Lahir Anak')
                                    ->required()
                                    ->allowInput()
                                    ->dateFormat('Y-m-d')
                                    ->altFormat('d F Y')
                                    ->altInput(true),
                            ])->columns(2),                            
                        ]),
                        Step::make('Data Pendidikan & Pangkat')->schema([
                            Section::make('Pendidikan Polri')->schema([
                                Repeater::make('pendidikan_polri')->schema([
                                    TextInput::make('tingkat')->label('Tingkat Pendidikan'),
                                    TextInput::make('tahun')->label('Tahun Pendidikan')->numeric(),
                                    FileUpload::make('dokumen')->label('Dokumen Pendukung'),
                                ])->columns(3),
                            ]),
                            Section::make('Pendidikan Umum')->schema([
                                Repeater::make('pendidikan_umum')->schema([
                                    TextInput::make('tingkat')->label('Tingkat Pendidikan'),
                                    TextInput::make('nama_institusi')->label('Nama Institusi'),
                                    TextInput::make('tahun')->label('Tahun Pendidikan')->numeric(),
                                    FileUpload::make('dokumen')->label('Dokumen Pendukung'),
                                ])->columns(4),
                            ]),
                            Section::make('Riwayat Pangkat')->schema([
                                Repeater::make('riwayat_pangkat')->schema([
                                    Select::make('pangkat')
                                        ->label('Pangkat')
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
                                    Flatpickr::make('tmt')->label('TMT Pangkat')->required()->allowInput()->dateFormat('Y-m-d')->altFormat('d F Y')->altInput(true),
                                    FileUpload::make('dokumen')->label('Dokumen Pendukung'),
                                ])->columns(3),
                            ]),
                            Section::make('Riwayat Jabatan')->schema([
                                Repeater::make('riwayat_jabatan')->schema([
                                    TextInput::make('jabatan')->label('Jabatan'),
                                    Flatpickr::make('tmt')->label('TMT Jabatan')->required()->allowInput()->dateFormat('Y-m-d')->altFormat('d F Y')->altInput(true),
                                    FileUpload::make('dokumen')->label('Dokumen Pendukung'),
                                ])->columns(3),
                            ]),
                            Section::make('Dikbang Pelatihan')->schema([
                                Repeater::make('dikbang_pelatihan')->schema([
                                    TextInput::make('nama_pelatihan')->label('Nama Pelatihan'),
                                    Flatpickr::make('tmt')->label('TMT Pelatihan')->required()->allowInput()->dateFormat('Y-m-d')->altFormat('d F Y')->altInput(true),
                                    FileUpload::make('dokumen')->label('Dokumen Pendukung'),
                                ])->columns(3),
                            ]),
                            Section::make('Tanda Kehormatan')->schema([
                                Repeater::make('tanda_kehormatan')->schema([
                                    TextInput::make('nama_tanda')->label('Nama Tanda'),
                                    Flatpickr::make('tmt')->label('TMT Tanda')->required()->allowInput()->dateFormat('Y-m-d')->altFormat('d F Y')->altInput(true),
                                    FileUpload::make('dokumen')->label('Dokumen Pendukung'),
                                ])->columns(3),
                            ]),
                            Section::make('Kemampuan Bahasa')->schema([
                                Repeater::make('kemampuan_bahasa')->schema([
                                    TextInput::make('bahasa')->label('Bahasa'),
                                    Select::make('status')->label('Status')->options([
                                        'Aktif' => 'Aktif',
                                        'Pasif' => 'Pasif',
                                    ]),
                                    FileUpload::make('dokumen')->label('Dokumen Pendukung'),
                                ])->columns(3),
                            ]),
                            Section::make('Penugasan LN')->schema([
                                Repeater::make('penugasan_ln')->label('Penugasan Luar Negeri')->schema([
                                    TextInput::make('penugasan')->label('Penugasan'),
                                    TextInput::make('lokasi')->label('Lokasi'),
                                    Flatpickr::make('tmt')->label('TMT Penugasan')->required()->allowInput()->dateFormat('Y-m-d')->altFormat('d F Y')->altInput(true),
                                    FileUpload::make('dokumen')->label('Dokumen Pendukung'),
                                ])->columns(3),
                            ]),
                        ]),
                    ])
                    ->skippable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
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

    public static function getTableColumns(): array
    {
        return [
            TextColumn::make('nama')->label('Nama')->searchable()->sortable(),
            TextColumn::make('pangkatTerakhir')
                ->label('Pangkat')
                ->sortable(),
            TextColumn::make('jabatanTerakhir')
                ->label('Jabatan')
                ->sortable(),
        ];
    }

    public static function getTableFilters(): array
    {
        return [
            // contoh filter
            \Filament\Tables\Filters\SelectFilter::make('pangkatTerakhir')
                ->label('Pangkat')
                ->options([
                    'Bripda' => 'Bripda',
                    'Briptu' => 'Briptu',
                    'Aipda' => 'Aipda',
                ]),
        ];
    }

    public static function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('view')
                ->label('Lihat')
                ->icon('heroicon-o-eye')
                ->url(fn ($record) => route('filament.subbagrenmin.resources.personils.view', ['record' => $record->id])),

            Tables\Actions\EditAction::make('edit')
                ->label('Ubah')
                ->url(fn ($record) => route('filament.subbagrenmin.resources.personils.edit', ['record' => $record->id])),
        ];
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
            'index' => Pages\ListPersonils::route('/'),
            'create' => Pages\CreatePersonil::route('/create'),
            'edit' => Pages\EditPersonil::route('/{record}/edit'),
            'view' => Pages\ViewPersonil::route('/{record}/view'),
        ];
    }
}
