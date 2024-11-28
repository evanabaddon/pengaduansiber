<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Unit;
use App\Models\User;
use Filament\Tables;
use App\Models\Subdit;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Illuminate\View\View;
use Filament\Tables\Table;
use App\Models\LaporanInformasi;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use App\Filament\Resources\LaporanInformasiResource\Pages;
use App\Notifications\LaporanInformasiAssignedNotification;
use Filament\Tables\Actions\ViewAction as ActionsViewAction;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;
use Parfaitementweb\FilamentCountryField\Infolists\Components\CountryEntry;

class LaporanInformasiResource extends Resource
{
    protected static ?string $model = LaporanInformasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // global search
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return LaporanInformasi::query()->with(['pelapors', 'korbans', 'terlapors']);
    }

    // sort navigation
    protected static ?int $navigationSort = -11;

    // navigation label
    public static function getNavigationLabel(): string
    {
        return 'Informasi / Surat Masyarakat (Dumas)';
    }

    // navigation group
    public static function getNavigationGroup(): ?string
    {
        return 'Laporan';
    }

    // navigation icon
    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-document-text';
    }

    // judul form
    public static function getLabel(): string
    {
        return 'Informasi / Surat Masyarakat (Dumas)';
    }

    // widget
    public static function getWidgets(): array
    {
        return [
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make()
                    ->steps([
                        Wizard\Step::make('Pelapor')
                            ->description('Identitas Pelapor')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        DatePicker::make('tanggal_lapor')->default('now')->label('TGL. LAPOR')->readOnly()->native(false)->displayFormat('d F Y'),
                                        DatePicker::make('tanggal_kejadian')->default('now')->label('TGL. KEJADIAN')->native(false)->displayFormat('d F Y'),
                                    ]),
                                // identitas pelapor
                                Grid::make(5)
                                ->schema([
                                    TextInput::make('pelapors.nama')->label('NAMA')->required(),
                                    TextInput::make('pelapors.identity_no')->label('NO IDENTITAS')->required(),
                                    PhoneInput::make('pelapors.kontak')->inputNumberFormat(PhoneInputNumberType::NATIONAL)->required()->label('KONTAK'),
                                    Country::make('pelapors.kewarganegaraan')->label('KEWARGANEGARAAN')->default('Indonesia')->searchable(),
                                    Select::make('pelapors.jenis_kelamin')
                                            ->label('JENIS KELAMIN')
                                            ->options([
                                                'Laki - Laki' => 'Laki - Laki',
                                                'Perempuan' => 'Perempuan'
                                            ])
                                            ->required(),
                                ]),
                                Grid::make(5)
                                    ->schema([
                                        TextInput::make('pelapors.tempat_lahir')->label('TEMPAT LAHIR')->required(),
                                        DatePicker::make('pelapors.tanggal_lahir')
                                            ->label('TGL. LAHIR')
                                            ->reactive()
                                            ->displayFormat('d F Y')
                                            ->native(false)
                                            ->required()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $birthDate = \Carbon\Carbon::parse($state);
                                                    $age = $birthDate->age;
                                                    $set('pelapors.usia', $age);
                                                } else {
                                                    $set('pelapors.usia', null);
                                                }
                                            }),
                                        TextInput::make('pelapors.usia')->readonly()->label('USIA'),
                                        TextInput::make('pelapors.pekerjaan')->label('PEKERJAAN')->required(),
                                        // select agama tidak ada di database
                                        Select::make('pelapors.agama')->label('AGAMA')->options([
                                            'Islam' => 'Islam',
                                            'Kristen' => 'Kristen',
                                            'Katolik' => 'Katolik',
                                            'Hindu' => 'Hindu',
                                            'Budha' => 'Budha',
                                            'Konghucu' => 'Konghucu',
                                            'Lainnya' => 'Lainnya',
                                        ]),
                                    ]),
                                // checkbox domestic atau tidak
                                // Checkbox::make('pelapors.domestic')
                                //     ->label('LUAR NEGERI')
                                //     ->reactive()
                                //     ->afterStateUpdated(function ($state, callable $set) {
                                //         if ($state) {
                                //             $set('pelapors.province_id', null);
                                //             $set('pelapors.city_id', null); 
                                //             $set('pelapors.district_id', null);
                                //             $set('pelapors.subdistrict_id', null);
                                //         }
                                //     }),
                                Textarea::make('pelapors.alamat')->label('ALAMAT')->required(),
                                Grid::make(2)
                                    ->schema([
                                        Select::make('pelapors.province_id')
                                            ->label('PROVINSI')
                                            ->hidden(fn (Get $get) => $get('pelapors.domestic'))
                                            ->provinsi()
                                            ->afterStateUpdated(function (callable $set) {
                                                $set('pelapors.city_id', null);
                                                $set('pelapors.district_id', null);
                                                $set('pelapors.subdistrict_id', null);
                                            }),
                                        Select::make('pelapors.city_id')
                                            ->label('KABUPATEN / KOTA')
                                            ->hidden(fn (Get $get) => $get('pelapors.domestic'))
                                            ->kabupaten()
                                            ->afterStateUpdated(function (callable $set) {
                                                $set('pelapors.district_id', null);
                                                $set('pelapors.subdistrict_id', null);
                                            }),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        Select::make('pelapors.district_id')
                                            ->label('KECAMATAN')
                                            ->hidden(fn (Get $get) => $get('pelapors.domestic'))
                                            ->kecamatan()
                                            ->afterStateUpdated(fn (callable $set) => $set('pelapors.subdistrict_id', null)),
                                        Select::make('pelapors.subdistrict_id')
                                            ->label('KELURAHAN / DESA')
                                            ->hidden(fn (Get $get) => $get('pelapors.domestic'))
                                            ->kelurahan(),
                                    ]),
                                ]),
                        Wizard\Step::make('Korban')
                            ->description('Identitas Korban')
                            ->schema([
                                Checkbox::make('sama_dengan_pelapor')
                                ->label('SAMA DENGAN PELAPOR')
                                ->reactive()  // Agar form bisa diperbarui saat checkbox dicentang
                                ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                    // jika sama dengan pelapor maka data korban akan sama dengan pelapor
                                    if ($state) {
                                        $set('korbans.identity_no', $get('pelapors.identity_no'));
                                        $set('korbans.nama', $get('pelapors.nama'));
                                        $set('korbans.kontak', $get('pelapors.kontak'));
                                        $set('korbans.kewarganegaraan', $get('pelapors.kewarganegaraan'));
                                        $set('korbans.tempat_lahir', $get('pelapors.tempat_lahir'));
                                        $set('korbans.tanggal_lahir', $get('pelapors.tanggal_lahir'));
                                        $set('korbans.jenis_kelamin', $get('pelapors.jenis_kelamin'));
                                        $set('korbans.pekerjaan', $get('pelapors.pekerjaan'));
                                        $set('korbans.usia', $get('pelapors.usia'));
                                        $set('korbans.alamat', $get('pelapors.alamat'));
                                        $set('korbans.province_id', $get('pelapors.province_id'));
                                        $set('korbans.city_id', $get('pelapors.city_id'));
                                        $set('korbans.district_id', $get('pelapors.district_id'));
                                        $set('korbans.subdistrict_id', $get('pelapors.subdistrict_id'));
                                        $set('korbans.domestic', $get('pelapors.domestic'));
                                        $set('korbans.agama', $get('pelapors.agama'));

                                    } else {
                                        $set('korbans.identity_no', null);
                                        $set('korbans.nama', null);
                                        $set('korbans.kontak', null);
                                        $set('korbans.kewarganegaraan', null);
                                        $set('korbans.tempat_lahir', null);
                                        $set('korbans.tanggal_lahir', null);
                                        $set('korbans.jenis_kelamin', null);
                                        $set('korbans.pekerjaan', null);
                                        $set('korbans.usia', null);
                                        $set('korbans.alamat', null);
                                        $set('korbans.province_id', null);
                                        $set('korbans.city_id', null);
                                        $set('korbans.district_id', null);
                                        $set('korbans.subdistrict_id', null);
                                        $set('korbans.domestic', null);
                                        $set('korbans.agama', null);
                                    }
                                }),
                                Grid::make(5)
                                    ->schema([
                                        TextInput::make('korbans.nama')->label('NAMA'),
                                        TextInput::make('korbans.identity_no')->label('NO IDENTITAS'),
                                        PhoneInput::make('korbans.kontak')->inputNumberFormat(PhoneInputNumberType::NATIONAL)->label('KONTAK'),
                                        Country::make('korbans.kewarganegaraan')->label('KEWARGANEGARAAN')->default('Indonesia')->searchable(),
                                        Select::make('korbans.jenis_kelamin')
                                            ->label('JENIS KELAMIN')
                                            ->options([
                                                'Laki - Laki' => 'Laki - Laki',
                                                'Perempuan' => 'Perempuan'
                                            ]),
                                    ]),
                                Grid::make(5)
                                    ->schema([
                                        TextInput::make('korbans.tempat_lahir')->label('TEMPAT LAHIR')->required(),
                                        DatePicker::make('korbans.tanggal_lahir')
                                            ->label('TGL. LAHIR')
                                            ->reactive()
                                            ->displayFormat('d F Y')
                                            ->native(false)
                                            ->closeOnDateSelection()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $birthDate = \Carbon\Carbon::parse($state);
                                                    $age = $birthDate->age;
                                                    $set('korbans.usia', $age);
                                                } else {
                                                    $set('korbans.usia', null);
                                                }
                                            }),
                                        TextInput::make('korbans.usia')->readonly()->label('USIA'),
                                        TextInput::make('korbans.pekerjaan')->label('PEKERJAAN'),
                                        Select::make('korbans.agama')->label('AGAMA')->options([
                                            'Islam' => 'Islam',
                                            'Kristen' => 'Kristen',
                                            'Katolik' => 'Katolik',
                                            'Hindu' => 'Hindu',
                                            'Budha' => 'Budha',
                                            'Konghucu' => 'Konghucu',
                                            'Lainnya' => 'Lainnya',
                                        ]),
                                    ]),
                                // checkbox domestic atau tidak
                                // Checkbox::make('korbans.domestic')
                                // ->label('LUAR NEGERI')
                                // ->reactive()
                                // ->afterStateUpdated(function ($state, callable $set) {
                                //     if ($state) {
                                //         $set('korbans.province_id', null);
                                //         $set('korbans.city_id', null); 
                                //         $set('korbans.district_id', null);
                                //         $set('korbans.subdistrict_id', null);
                                //     }
                                // }),
                                Textarea::make('korbans.alamat')->label('ALAMAT'),
                                Grid::make(2)
                                    ->schema([
                                        Select::make('korbans.province_id')
                                            ->label('PROVINSI')
                                            ->hidden(fn (Get $get) => $get('korbans.domestic'))
                                            ->provinsi()
                                            ->afterStateUpdated(function (callable $set) {
                                                $set('korbans.city_id', null);
                                                $set('korbans.district_id', null);
                                                $set('korbans.subdistrict_id', null);
                                            }),
                                        Select::make('korbans.city_id')
                                            ->label('KABUPATEN / KOTA')
                                            ->hidden(fn (Get $get) => $get('korbans.domestic'))
                                            ->kabupaten()
                                            ->afterStateUpdated(function (callable $set) {
                                                $set('korbans.district_id', null);
                                                $set('korbans.subdistrict_id', null);
                                            }),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        Select::make('korbans.district_id')
                                            ->label('KECAMATAN')
                                            ->hidden(fn (Get $get) => $get('korbans.domestic'))
                                            ->kecamatan()
                                            ->afterStateUpdated(fn (callable $set) => $set('korbans.subdistrict_id', null)),
                                        Select::make('korbans.subdistrict_id')
                                            ->label('KELURAHAN / DESA')
                                            ->hidden(fn (Get $get) => $get('korbans.domestic'))
                                            ->kelurahan(),
                                    ]),
                            ]),
                        Wizard\Step::make('Terlapor')
                            ->description('Identitas Terlapor')
                            ->schema([
                                Grid::make(5)
                                    ->schema([
                                        TextInput::make('terlapors.nama')->label('NAMA'),
                                        TextInput::make('terlapors.identity_no')->label('NO IDENTITAS'),
                                        PhoneInput::make('terlapors.kontak')->inputNumberFormat(PhoneInputNumberType::NATIONAL)->label('KONTAK'),
                                        Country::make('terlapors.kewarganegaraan')->label('KEWARGANEGARAAN')->default('Indonesia')->searchable(),
                                        Select::make('terlapors.jenis_kelamin')
                                            ->label('JENIS KELAMIN')
                                            ->options([
                                                'Laki - Laki' => 'Laki - Laki',
                                                'Perempuan' => 'Perempuan'
                                            ]),
                                    ]),
                                Grid::make(5)
                                    ->schema([
                                        TextInput::make('terlapors.tempat_lahir')->label('TEMPAT LAHIR'),
                                        DatePicker::make('terlapors.tanggal_lahir')
                                            ->label('TGL. LAHIR')
                                            ->reactive()
                                            ->displayFormat('d F Y')
                                            ->native(false)
                                            ->closeOnDateSelection()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $birthDate = \Carbon\Carbon::parse($state);
                                                    $age = $birthDate->age;
                                                    $set('terlapors.usia', $age);
                                                } else {
                                                    $set('terlapors.usia', null);
                                                }
                                            }),
                                        TextInput::make('terlapors.usia')->label('USIA')->readonly(),
                                        TextInput::make('terlapors.pekerjaan')->label('PEKERJAAN'),
                                        // agama
                                        Select::make('terlapors.agama')->label('AGAMA')->options([
                                            'Islam' => 'Islam',
                                            'Kristen' => 'Kristen',
                                            'Katolik' => 'Katolik',
                                            'Hindu' => 'Hindu',
                                            'Budha' => 'Budha',
                                            'Konghucu' => 'Konghucu',
                                            'Lainnya' => 'Lainnya',
                                        ]),
                                    ]),
                                // checkbox domestic atau tidak
                                // Checkbox::make('terlapors.domestic')
                                //     ->label('LUAR NEGERI')
                                //     ->reactive()
                                //     ->afterStateUpdated(function ($state, callable $set) {
                                //         if ($state) {
                                //             $set('terlapors.province_id', null);
                                //             $set('terlapors.city_id', null);
                                //             $set('terlapors.district_id', null);
                                //             $set('terlapors.subdistrict_id', null);
                                //         }
                                //     }),
                                Textarea::make('terlapors.alamat')->label('ALAMAT'),
                                Grid::make(2)
                                    ->schema([
                                        Select::make('terlapors.province_id')
                                            ->label('PROVINSI')
                                            ->hidden(fn (Get $get) => $get('terlapors.domestic'))
                                            ->provinsi()
                                            ->afterStateUpdated(function (callable $set) {
                                                $set('terlapors.city_id', null);
                                                $set('terlapors.district_id', null);
                                                $set('terlapors.subdistrict_id', null);
                                            }),
                                        Select::make('terlapors.city_id')
                                            ->label('KABUPATEN / KOTA')
                                            ->hidden(fn (Get $get) => $get('terlapors.domestic'))
                                            ->kabupaten()
                                            ->afterStateUpdated(function (callable $set) {
                                                $set('terlapors.district_id', null);
                                                $set('terlapors.subdistrict_id', null);
                                            }),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        Select::make('terlapors.district_id')
                                            ->label('KECAMATAN')
                                            ->hidden(fn (Get $get) => $get('terlapors.domestic'))
                                            ->kecamatan()
                                            ->afterStateUpdated(fn (callable $set) => $set('terlapors.subdistrict_id', null)),
                                        Select::make('terlapors.subdistrict_id')
                                            ->label('KELURAHAN / DESA')
                                            ->hidden(fn (Get $get) => $get('terlapors.domestic'))
                                            ->kelurahan(),
                                    ]),
                            ]),
                        Wizard\Step::make('TKP')
                            ->description('Tempat Kejadian Perkara')
                            ->schema([
                                Textarea::make('tkp')->label('TEMPAT KEJADIAN PERKARA'),
                                Select::make('province_id')
                                    ->label('PROVINSI')
                                    ->provinsi()
                                    ->afterStateUpdated(fn (callable $set) => $set('city_id', null)),
                                Select::make('city_id')
                                    ->label('KABUPATEN / KOTA')
                                    ->kabupaten()
                                    ->afterStateUpdated(fn (callable $set) => $set('district_id', null)),
                                Select::make('district_id')
                                    ->label('KECAMATAN')
                                    ->kecamatan()
                                    ->afterStateUpdated(fn (callable $set) => $set('subdistrict_id', null)),
                                Select::make('subdistrict_id')
                                    ->label('KELURAHAN / DESA')
                                    ->kelurahan(),
                            ]),
                        Wizard\Step::make('Perkara')
                            ->description('Informasi Perkara')
                            ->schema([
                                TextInput::make('perkara')
                                    ->label('PERKARA')
                                    ->required(),
                                Textarea::make('uraian_peristiwa')
                                    ->label('URAIAN PERISTIWA')
                                    ->required(),
                                TextInput::make('kerugian')
                                    ->label('KERUGIAN')
                                    ->required(),
                            ]),
                        // barang bukti
                        Wizard\Step::make('Barang Bukti')
                            ->label('BARANG BUKTI')
                            ->description('Barang Bukti')
                            ->schema([
                                Repeater::make('barangBuktis')
                                    ->label('BARANG BUKTI')
                                    ->relationship('barangBuktis')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('jumlah')->label('JUMLAH'),
                                                TextInput::make('nama_barang')->label('NAMA BARANG'),
                                            ]),
                                        // Textarea::make('deskripsi')->label('DESKRIPSI'),
                                        // Grid::make(3)
                                        //     ->schema([
                                        //         TextInput::make('jumlah')->label('JUMLAH'),
                                        //         Select::make('kondisi')
                                        //         ->options([
                                        //             'Baik' => 'Baik',
                                        //             'Rusak Ringan' => 'Rusak Ringan',
                                        //             'Rusak Berat' => 'Rusak Berat'
                                        //         ]),
                                        //         TextInput::make('lokasi_penyimpanan')
                                        //     ->label('LOKASI PENYIMPANAN'),
                                        //     ]),
                                        
                                        // FileUpload::make('media')
                                        //     ->label('FOTO BARANG BUKTI')
                                        //     ->multiple()
                                        //     ->directory('barang-bukti')
                                        //     ->preserveFilenames()
                                        //     ->image()
                                        //     ->maxSize(2048)
                                    ])
                                    ->columnSpanFull()
                                    ->defaultItems(0)
                                    ->addActionLabel('Tambah Barang Bukti'),
                            ]),
                        // wizard media
                        Wizard\Step::make('Media')
                            ->description('Media')
                            ->schema([
                                FileUpload::make('media')
                            ->label('MEDIA / DOKUMEN PENDUKUNG')
                            ->multiple()
                            ->directory('laporan-informasi')
                            ->preserveFilenames()
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file, $livewire): string => (string) str($file->getClientOriginalName())
                                    ->prepend('laporan-'.(optional($livewire->record)->id ?? 'new').'-'),
                            )
                            ->acceptedFileTypes(['image/*', 'application/pdf', 'application/msword'])
                            ->maxSize(5120)
                            ->downloadable()
                            ->reorderable()
                            ->columnSpanFull()
                            ->required(false)
                                    ->storeFileNamesIn('media'), // Pastikan nama file tersimpan di kolom media
                            ]),
                            ])
                            ->columnSpanFull()
                            ->submitAction(new HtmlString(Blade::render(<<<BLADE
                                <x-filament::button
                                    type="submit"
                                    size="sm"
                                >
                                    Simpan
                                </x-filament::button>
                            BLADE))),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal_lapor')
                ->wrapHeader()
                    ->sortable()
                    ->wrapHeader()
                    ->alignment(Alignment::Center)
                    ->dateTime('d M Y')
                    ->label('TGL. LAPOR'),
                TextColumn::make('pelapors.nama')
                    ->searchable()
                    ->description(fn (LaporanInformasi $record): string => $record->korbans()->where('laporan_informasi_id', $record->id)->pluck('nama')->join(', '))
                    ->label('PELAPOR / KORBAN'),
                // terlapor
                TextColumn::make('terlapors.nama')->label('TERLAPOR')->alignment(Alignment::Center)->toggleable()->searchable(),
                TextColumn::make('tkp')->label('TKP')->alignment(Alignment::Center)->toggleable()->searchable(),
                TextColumn::make('perkara')->label('PERKARA')->alignment(Alignment::Center)->toggleable()->searchable(),
                TextColumn::make('uraian_peristiwa')->label('URAIAN PERISTIWA')->limit(15)->toggleable(isToggledHiddenByDefault: true)->searchable()->wrap(),
                TextColumn::make('kerugian')
                    ->sortable()
                    ->toggleable()
                    ->searchable()
                    ->alignment(Alignment::Center)
                    ->money('IDR', locale: 'id')
                    ->label('KERUGIAN'),
                ColumnGroup::make('YANG MENANGANI')
                    ->wrapHeader()
                    ->alignment(Alignment::Center)
                    ->columns([
                        SelectColumn::make('subdit_id')
                            ->label('SUBDIT')
                            ->alignment(Alignment::Center)
                            ->selectablePlaceholder('Pilih Subdit')
                            ->options(Subdit::all()->pluck('name', 'id'))
                            ->searchable()
                            ->disabled(auth()->user()->hasRole('subdit') || auth()->user()->hasRole('unit') || auth()->user()->hasRole('penyidik'))
                            ->afterStateUpdated(function (LaporanInformasi $record, $state) {
                                $record->update([
                                    'unit_id' => null,
                                    'penyidik_id' => null
                                ]);

                                // Kirim notifikasi ke user subdit
                                if ($state) {
                                    $users = User::where('subdit_id', $state)->get();
                                    foreach ($users as $user) {
                                        $user->notify(new LaporanInformasiAssignedNotification($record, 'subdit'));
                                    }
                                }
                            }),
                        SelectColumn::make('unit_id')
                            ->label('UNIT')
                            ->alignment(Alignment::Center)
                            ->disabled(auth()->user()->hasRole('unit') || auth()->user()->hasRole('penyidik'))
                            ->selectablePlaceholder('Pilih Unit')
                            ->options(function (LaporanInformasi $record) {
                                if (!$record->subdit_id) return [];
                                return Unit::where('subdit_id', $record->subdit_id)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->afterStateUpdated(function (LaporanInformasi $record, $state) {
                                $record->update([
                                    'penyidik_id' => null
                                ]);

                                // Kirim notifikasi ke user unit
                                if ($state) {
                                    $users = User::where('unit_id', $state)->get();
                                    foreach ($users as $user) {
                                        $user->notify(new LaporanInformasiAssignedNotification($record, 'unit'));
                                    }
                                }
                            }),
                        SelectColumn::make('penyidik_id')
                            ->label('PENYIDIK')
                            ->alignment(Alignment::Center)
                            ->selectablePlaceholder('Pilih Penyidik')
                            ->disabled(auth()->user()->hasRole('penyidik'))
                            ->options(function (LaporanInformasi $record) {
                                if (!$record->unit_id) return [];
                                return User::where('unit_id', $record->unit_id)
                                    ->whereDoesntHave('roles', function($query) {
                                        $query->whereIn('name', [
                                            'super_admin',
                                            'Admin Subdit',
                                            'Kasubdit',
                                            'Admin Unit',
                                            'Kanit',
                                            'Admin Bagbinopsnal',
                                            'Kabagbinopsnal',
                                            'Direktur/Wakil Direktur',
                                            'Kasubbagrenmin',
                                            'Admin Subbagrenmin',
                                            'Kabagwassidik',
                                            'Admin Bagwassidik',
                                        ]);
                                    })
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->afterStateUpdated(function (LaporanInformasi $record, $state) {
                                // Kirim notifikasi ke user penyidik
                                if ($state) {
                                    $user = User::find($state);
                                    $user->notify(new LaporanInformasiAssignedNotification($record, 'penyidik'));
                                }
                            }),
                    ]),
                // barang bukti
                TextColumn::make('barangBuktis.nama_barang')->label('BARANG BUKTI')->limit(15)->toggleable(isToggledHiddenByDefault: true),
                SelectColumn::make('status')
                    ->searchable()
                    ->selectablePlaceholder('Pilih Status')
                    ->options([
                        'Proses' => 'Proses',
                        'Terkendala' => 'Terkendala',
                        'Selesai' => 'Selesai',
                    ])
                    ->rules(['required']),
            ])
            ->filters([
                // filter by status
                SelectFilter::make('status')
                    ->label('STATUS')
                    ->options([
                        'Terlapor' => 'Terlapor',
                        'Proses' => 'Proses',
                        'Selesai' => 'Selesai',
                        'Terkendala' => 'Terkendala',
                    ]),
                // filter by subdit
                SelectFilter::make('subdit_id')
                    ->label('SUBDIT')
                    ->options(Subdit::all()->pluck('name', 'id'))
                    ->searchable()
                    ->visible(!auth()->user()->hasRole('subdit') && !auth()->user()->hasRole('unit') && !auth()->user()->hasRole('penyidik')),
                // filter by unit  
                SelectFilter::make('unit_id')
                    ->label('UNIT')
                    ->options(Unit::all()->pluck('name', 'id'))
                    ->searchable()
                    ->visible(!auth()->user()->hasRole('unit') && !auth()->user()->hasRole('penyidik')),
                // filter by penyidik
                SelectFilter::make('penyidik_id')
                    ->label('PENYIDIK')
                    ->options(User::whereDoesntHave('roles', function($query) {
                        $query->whereIn('name', ['super_admin', 'subdit', 'unit']);
                    })->pluck('name', 'id'))
                    ->searchable()
                    ->visible(!auth()->user()->hasRole('penyidik')),
            ])
            ->actions([
                ActionsViewAction::make()
                    ->label(false)
                    ->modalContent(fn (LaporanInformasi $record): View => view(
                        'filament.resources.laporan.view',
                        ['record' => $record]
                    ))
                    ->modalWidth('7xl'), 
                EditAction::make()->label(false),
                DeleteAction::make()->label(false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        if (auth()->user()->hasRole('super_admin')) {
            return $query; // Super Admin bisa lihat semua
        }
        
        if (auth()->user()->hasRole('subdit')) {
            return $query->where('subdit_id', auth()->user()->subdit_id); // Subdit bisa lihat laporan miliknya
        }
        
        if (auth()->user()->hasRole('unit')) {
            return $query->where('unit_id', auth()->user()->unit_id); // Unit bisa lihat laporan miliknya
        }
        
        if (auth()->user()->hasRole('penyidik')) {
            return $query->where('penyidik_id', auth()->user()->id); // Penyidik bisa lihat laporan miliknya
        }
        
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanInformasis::route('/'),
            'create' => Pages\CreateLaporanInformasi::route('/create'),
            'view' => Pages\ViewLaporanInformasi::route('/{record}'),
            'edit' => Pages\EditLaporanInformasi::route('/{record}/edit'),
        ];
    }

    // Untuk form edit
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->record;
        dd($record);
        
        if ($record) {
            // Load relationships jika belum dimuat
            if (!$record->relationLoaded('pelapors')) {
                $record->load('pelapors');
            }
            if (!$record->relationLoaded('korbans')) {
                $record->load('korbans');
            }
            if (!$record->relationLoaded('terlapors')) {
                $record->load('terlapors');
            }

            // Pastikan data relationship dimuat ke form
            if ($record->pelapors) {
                $data['pelapors'] = $record->pelapors->toArray();
            }
            if ($record->korbans) {
                $data['korbans'] = $record->korbans->toArray();
            }
            if ($record->terlapors) {
                $data['terlapors'] = $record->terlapors->toArray();
            }
        }

        return $data;
    }

}
