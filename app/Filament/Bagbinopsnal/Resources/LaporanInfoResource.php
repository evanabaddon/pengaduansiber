<?php

namespace App\Filament\Bagbinopsnal\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\Unit;
use App\Models\User;
use Filament\Tables;
use App\Models\Korban;
use App\Models\Subdit;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Penyidik;
use Filament\Forms\Form;
use App\Models\FormDraft;
use Illuminate\View\View;
use Filament\Tables\Table;
use App\Models\LaporanInfo;
use Filament\Facades\Filament;
use App\Services\OllamaService;
use Filament\Resources\Resource;
use App\Services\DeepSeekService;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Wizard\Step;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use App\Filament\Forms\Components\DataTambahanRepeater;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use App\Notifications\LaporanInformasiAssignedNotification;
use Filament\Tables\Actions\ViewAction as ActionsViewAction;
use App\Filament\Bagbinopsnal\Resources\LaporanInfoResource\Pages;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;
use App\Filament\Bagbinopsnal\Resources\LaporanInfoResource\RelationManagers;
use App\Filament\Bagbinopsnal\Resources\LaporanInfoResource\Pages\EditLaporanInfo;
use App\Filament\Bagbinopsnal\Resources\LaporanInfoResource\Pages\ListLaporanInfos;
use App\Filament\Bagbinopsnal\Resources\LaporanInfoResource\Pages\CreateLaporanInfo;

class LaporanInfoResource extends Resource
{
    protected static ?string $model = LaporanInfo::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    public static function shouldRegisterNavigation(): bool
    {
        $panelId = Filament::getCurrentPanel()->getId();

        return in_array($panelId, [
            'bagwassidik',
            'bagbinopsnal'
        ]);
    }

    // label
    public static function getLabel(): string
    {
        return 'Laporan Informasi (LI)';
    }

    // navigation group
    public static function getNavigationGroup(): ?string
    {
        return 'Laporan';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make()
                    ->steps([
                        Wizard\Step::make('Pelapor')
                            ->description('Identitas Pelapor')
                            // auto save ketika next
                            ->afterValidation(function ($state, $component) {
                                // Extract only pelapors data and convert to JSON string
                                $pelaporData = json_encode($state['pelapors'] ?? []);

                                // Get existing draft
                                $draft = FormDraft::firstWhere([
                                    'user_id' => auth()->id(),
                                    'form_type' => 'laporan_info'
                                ]);

                                // Decode existing main_data or initialize empty array
                                $mainData = json_decode($draft?->main_data ?? '{}', true) ?: [];

                                // Add or update tanggal_lapor and tanggal_kejadian
                                $mainData['tanggal_lapor'] = $state['tanggal_lapor'];
                                $mainData['tanggal_kejadian'] = $state['tanggal_kejadian'];

                                FormDraft::updateOrCreate(
                                    [
                                        'user_id' => auth()->id(),
                                        'form_type' => 'laporan_info'
                                    ],
                                    [
                                        'current_step' => 1,
                                        'pelapor_data' => $pelaporData,
                                        'main_data' => json_encode($mainData)
                                    ]
                                );
                            })
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Flatpickr::make('tanggal_lapor')
                                            ->label('TGL. LAPOR')
                                            ->default(today()->format('d F Y'))
                                            ->required()
                                            ->allowInput()
                                            ->dateFormat('d F Y')
                                            ->altFormat('d F Y')
                                            ->readonly()
                                            ->altInput(true),
                                        Flatpickr::make('tanggal_kejadian')
                                            ->label('TGL. KEJADIAN')
                                            ->default(today()->format('d F Y'))
                                            ->required()
                                            ->allowInput()
                                            ->dateFormat('d F Y')
                                            ->altFormat('d F Y')
                                            ->altInput(true),
                                    ]),
                                // identitas pelapor
                                Grid::make(6)
                                ->schema([
                                    TextInput::make('no_laporan')->label('NO LAPORAN')->required(),
                                    TextInput::make('pelapors.nama')->label('NAMA')->required(),
                                    TextInput::make('pelapors.identity_no')->label('NO IDENTITAS')->required(),
                                    PhoneInput::make('pelapors.kontak')->inputNumberFormat(PhoneInputNumberType::NATIONAL)->required()->label('KONTAK'),
                                    PhoneInput::make('pelapors.kontak_2')->inputNumberFormat(PhoneInputNumberType::NATIONAL)->label('KONTAK LAIN'),
                                    Country::make('pelapors.kewarganegaraan')->label('KEWARGANEGARAAN')->default('Indonesia')->searchable(),
                                    
                                ]),
                                Grid::make(6)
                                    ->schema([
                                        Select::make('pelapors.jenis_kelamin')
                                            ->label('JENIS KELAMIN')
                                            ->options([
                                                'Laki - Laki' => 'Laki - Laki',
                                                'Perempuan' => 'Perempuan'
                                            ])
                                            ->required(),
                                        TextInput::make('pelapors.tempat_lahir')->label('TEMPAT LAHIR')->required(),
                                        Flatpickr::make('pelapors.tanggal_lahir')
                                            ->label('TGL. LAHIR')
                                            ->required()
                                            ->allowInput()
                                            ->dateFormat('d F Y')
                                            ->altFormat('d F Y')
                                            ->altInput(true)
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $birthDate = Carbon::parse($state);
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
                                    // section data tambahan
                                    Section::make('Data Tambahan')
                                        ->schema([
                                            DataTambahanRepeater::make('pelapors.data_tambahan'),
                                        ]),
                                    // Alamat section
                                    Section::make('Alamat')
                                        ->schema([
                                            // Alamat 1 (Utama)
                                            Section::make('Alamat Utama')
                                                ->schema([
                                                    Textarea::make('pelapors.alamat')
                                                        ->label('ALAMAT')
                                                        ->required(),
                                                    Select::make('pelapors.province_id')
                                                        ->label('PROVINSI')
                                                        ->provinsi()
                                                        ->live()
                                                        ->searchable()
                                                        ->afterStateUpdated(function (callable $set) {
                                                            $set('pelapors.city_id', null);
                                                            $set('pelapors.district_id', null);
                                                            $set('pelapors.subdistrict_id', null);
                                                        }),
                                                    Select::make('pelapors.city_id')
                                                        ->label('KABUPATEN / KOTA')
                                                        ->kabupaten()
                                                        ->live()
                                                        ->searchable()
                                                        ->afterStateUpdated(function (callable $set) {
                                                            $set('pelapors.district_id', null);
                                                            $set('pelapors.subdistrict_id', null);
                                                        }),
                                                    Select::make('pelapors.district_id')
                                                        ->label('KECAMATAN')
                                                        ->kecamatan()
                                                        ->live()
                                                        ->searchable()
                                                        ->afterStateUpdated(fn (callable $set) => $set('pelapors.subdistrict_id', null)),
                                                    Select::make('pelapors.subdistrict_id')
                                                        ->label('KELURAHAN / DESA')
                                                        ->kelurahan()
                                                        ->live()
                                                        ->searchable(),
                                                ]),

                                            // Toggle untuk menampilkan Alamat 2
                                            Toggle::make('pelapors_has_second_address')
                                                ->label('Tambah Alamat Lain?')
                                                ->live()
                                                ->dehydrated(false)
                                                ->afterStateHydrated(function (Get $get, Set $set, $state) {
                                                    if ($get('pelapors.alamat_2') || 
                                                        $get('pelapors.province_id_2') || 
                                                        $get('pelapors.city_id_2') || 
                                                        $get('pelapors.district_id_2') || 
                                                        $get('pelapors.subdistrict_id_2')) {
                                                        $set('pelapors_has_second_address', true);
                                                    }
                                                }),

                                            // Alamat 2 (Opsional)
                                            Section::make('Alamat Lain')
                                                ->schema([
                                                    Textarea::make('pelapors.alamat_2')
                                                        ->label('ALAMAT LAIN'),
                                                    Select::make('pelapors.province_id_2')
                                                        ->label('PROVINSI')
                                                        ->provinsi()
                                                        ->live()
                                                        ->searchable()
                                                        ->afterStateUpdated(function (callable $set) {
                                                            $set('pelapors.city_id_2', null);
                                                            $set('pelapors.district_id_2', null);
                                                            $set('pelapors.subdistrict_id_2', null);
                                                        }),
                                                    Select::make('pelapors.city_id_2')
                                                        ->label('KABUPATEN / KOTA')
                                                        ->kabupaten(fn (Get $get): ?string => $get('pelapors.province_id_2'))
                                                        ->live()
                                                        ->searchable()
                                                        ->afterStateUpdated(function (callable $set) {
                                                            $set('pelapors.district_id_2', null);
                                                            $set('pelapors.subdistrict_id_2', null);
                                                        }),
                                                    Select::make('pelapors.district_id_2')
                                                        ->label('KECAMATAN')
                                                        ->kecamatan(fn (Get $get): ?string => $get('pelapors.city_id_2'))
                                                        ->live()
                                                        ->searchable()
                                                        ->afterStateUpdated(fn (callable $set) => $set('pelapors.subdistrict_id_2', null)),
                                                    Select::make('pelapors.subdistrict_id_2')
                                                        ->label('KELURAHAN / DESA')
                                                        ->kelurahan(fn (Get $get): ?string => $get('pelapors.district_id_2'))
                                                        ->live()
                                                        ->searchable(),
                                                ])
                                                ->visible(function (Get $get): bool {
                                                    // Cek toggle state
                                                    if ($get('pelapors_has_second_address')) {
                                                        return true;
                                                    }
                                                    // Cek apakah ada data alamat 2 yang sudah tersimpan
                                                    return $get('pelapors.alamat_2') || 
                                                           $get('pelapors.province_id_2') || 
                                                           $get('pelapors.city_id_2') || 
                                                           $get('pelapors.district_id_2') || 
                                                           $get('pelapors.subdistrict_id_2');
                                                }),
                                        ])
                                    ]),
                        Wizard\Step::make('Korban')
                            ->description('Identitas Korban')
                            ->afterValidation(function ($state, $component) {
                                // Extract only korbans data and convert to JSON string
                                $korbansData = json_encode($state['korbans'] ?? []);

                                FormDraft::updateOrCreate(
                                    [
                                        'user_id' => auth()->id(),
                                        'form_type' => 'laporan_info'
                                    ],
                                    [
                                        'current_step' => 2,
                                        'korban_data' => $korbansData
                                    ]
                                );
                            })
                            ->schema([
                                Repeater::make('korbans')
                                    ->columns(1)
                                    ->defaultItems(1)
                                    ->addActionLabel('Tambah Korban')
                                    ->columnSpanFull()
                                    ->label('KORBAN')
                                    ->schema([
                                        Checkbox::make('sama_dengan_pelapor')
                                            ->label('SAMA DENGAN PELAPOR')
                                            ->hidden(function (Component $component) {
                                                $firstItemKey = array_key_first($component->getContainer()->getParentComponent()->getState());
                                                // Tampilkan hanya jika ini adalah item pertama
                                                return !str_contains($component->getStatePath(), $firstItemKey);
                                            }) // Sembunyikan jika bukan item pertama
                                            ->reactive()  // Agar form bisa diperbarui saat checkbox dicentang
                                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                                // jika sama dengan pelapor maka data korban akan sama dengan pelapor
                                                
                                                if ($state) {
                                                    // Mengakses data pelapor dari form utama, bukan dari dalam repeater
                                                    $set('korbans.nama', $get('../../pelapors.nama'));
                                                    $set('korbans.identity_no', $get('../../pelapors.identity_no'));
                                                    $set('korbans.kontak', $get('../../pelapors.kontak'));
                                                    $set('korbans.kontak_2', $get('../../pelapors.kontak_2'));
                                                    $set('korbans.kewarganegaraan', $get('../../pelapors.kewarganegaraan'));
                                                    $set('korbans.tempat_lahir', $get('../../pelapors.tempat_lahir'));
                                                    
                                                    // Set tanggal lahir dengan format yang benar
                                                    $tanggalLahir = $get('../../pelapors.tanggal_lahir');
                                                    if ($tanggalLahir) {
                                                        $formattedDate = Carbon::parse($tanggalLahir)->format('Y-m-d');
                                                        $set('korbans.tanggal_lahir', $formattedDate);
                                                        
                                                        // Hitung dan set usia
                                                        $birthDate = Carbon::parse($tanggalLahir);
                                                        $age = $birthDate->age;
                                                        $set('korbans.usia', $age);
                                                    }
                                                    
                                                    $set('korbans.jenis_kelamin', $get('../../pelapors.jenis_kelamin'));
                                                    $set('korbans.pekerjaan', $get('../../pelapors.pekerjaan'));
                                                    $set('korbans.agama', $get('../../pelapors.agama'));
                                                    $set('korbans.alamat', $get('../../pelapors.alamat'));
                                                    $set('korbans.province_id', $get('../../pelapors.province_id'));
                                                    $set('korbans.city_id', $get('../../pelapors.city_id'));
                                                    $set('korbans.district_id', $get('../../pelapors.district_id'));
                                                    $set('korbans.subdistrict_id', $get('../../pelapors.subdistrict_id'));
                                                    $set('korbans.alamat_2', $get('../../pelapors.alamat_2'));
                                                    $set('korbans.province_id_2', $get('../../pelapors.province_id_2'));
                                                    $set('korbans.city_id_2', $get('../../pelapors.city_id_2'));
                                                    $set('korbans.district_id_2', $get('../../pelapors.district_id_2'));
                                                    $set('korbans.subdistrict_id_2', $get('../../pelapors.subdistrict_id_2'));
                                                    $set('data_tambahan', collect($get('../../pelapors.data_tambahan'))
                                                                        ->map(function ($item) {
                                                                            return [
                                                                                'nama_data' => $item['nama_data'],
                                                                                'keterangan' => $item['keterangan'],
                                                                            ];
                                                                        })
                                                                        ->toArray()
                                                                    );

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
                                                    $set('korbans.alamat_2', null);
                                                    $set('korbans.province_id_2', null);
                                                    $set('korbans.city_id_2', null);
                                                    $set('korbans.district_id_2', null);
                                                    $set('korbans.subdistrict_id_2', null);
                                                    $set('korbans.kontak_2', null);
                                                    $set('korbans.data_tambahan', null);
                                                    }
                                                }),
                                        Grid::make(5)
                                            ->schema([
                                                TextInput::make('korbans.nama')->label('NAMA'),
                                                TextInput::make('korbans.identity_no')->label('NO IDENTITAS'),
                                                PhoneInput::make('korbans.kontak')->inputNumberFormat(PhoneInputNumberType::NATIONAL)->label('KONTAK')->required(),
                                                PhoneInput::make('korbans.kontak_2')->inputNumberFormat(PhoneInputNumberType::NATIONAL)->label('KONTAK LAIN'),
                                                Country::make('korbans.kewarganegaraan')->label('KEWARGANEGARAAN')->default('Indonesia')->searchable(),
                                            ]),
                                        Grid::make(6)
                                            ->schema([
                                                Select::make('korbans.jenis_kelamin')
                                                ->label('JENIS KELAMIN')
                                                ->options([
                                                    'Laki - Laki' => 'Laki - Laki',
                                                    'Perempuan' => 'Perempuan'
                                                ])->required(),
                                                TextInput::make('korbans.tempat_lahir')->label('TEMPAT LAHIR')->required(),
                                                Flatpickr::make('korbans.tanggal_lahir')
                                                    ->label('TGL. LAHIR')
                                                    ->required()
                                                    ->allowInput()
                                                    ->dateFormat('d F Y')
                                                    ->altFormat('d F Y')
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, callable $set) {
                                                        if ($state) {
                                                            $birthDate = Carbon::parse($state);
                                                            $age = $birthDate->age;
                                                            $set('korbans.usia', $age);
                                                        } else {
                                                            $set('korbans.usia', null);
                                                        }
                                                    }),
                                                TextInput::make('korbans.usia')->readonly()->label('USIA'),
                                                TextInput::make('korbans.pekerjaan')->label('PEKERJAAN')->required(),
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
                                        Section::make('Data Tambahan')
                                            ->schema([
                                                DataTambahanRepeater::make('data_tambahan')  // Hapus 'korbans.' dari path
                                                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data) {
                                                        $data['datatable_type'] = Korban::class;
                                                        return $data;
                                                    }),
                                            ]),
                                        // Alamat section
                                        Section::make('Alamat')
                                        ->schema([
                                            // Alamat 1 (Utama)
                                            Section::make('Alamat Utama')
                                                ->schema([
                                                    Textarea::make('korbans.alamat')
                                                        ->label('ALAMAT')
                                                        ->required(),
                                                    Select::make('korbans.province_id')
                                                        ->label('PROVINSI')
                                                        ->provinsi()
                                                        ->live()
                                                        ->searchable()
                                                        ->options(fn () => app('wilayah')->getProvinsi() ?? [])  // Langsung panggil helper
                                                        ->afterStateUpdated(function (callable $set) {
                                                            $set('korbans.city_id', null);
                                                            $set('korbans.district_id', null);
                                                            $set('korbans.subdistrict_id', null);
                                                        }),
                                                    Select::make('korbans.city_id')
                                                        ->label('KABUPATEN / KOTA')
                                                        ->kabupaten()
                                                        ->live()
                                                        ->searchable()
                                                        ->options(function (callable $get) {
                                                            $provinceId = $get('korbans.province_id');
                                                            return $provinceId ? (app('wilayah')->getKabupaten($provinceId) ?? []) : [];
                                                        })
                                                        ->afterStateUpdated(function (callable $set) {
                                                            $set('korbans.district_id', null);
                                                            $set('korbans.subdistrict_id', null);
                                                        }),
                                                    Select::make('korbans.district_id')
                                                        ->label('KECAMATAN')
                                                        ->kecamatan()
                                                        ->live()
                                                        ->searchable()
                                                        ->options(function (callable $get) {
                                                            $cityId = $get('korbans.city_id');
                                                            return $cityId ? (app('wilayah')->getKecamatan($cityId) ?? []) : [];
                                                        })
                                                        ->afterStateUpdated(fn (callable $set) => $set('korbans.subdistrict_id', null)),
                                                    Select::make('korbans.subdistrict_id')
                                                        ->label('KELURAHAN / DESA')
                                                        ->kelurahan()
                                                        ->live()
                                                        ->searchable()
                                                        ->options(function (callable $get) {
                                                            $districtId = $get('korbans.district_id');
                                                            return $districtId ? (app('wilayah')->getKelurahan($districtId) ?? []) : [];
                                                        }),
                                                ]),
                                            // Toggle untuk menampilkan Alamat 2
                                            Toggle::make('korbans_has_second_address')
                                                ->label('Tambah Alamat Lain?')
                                                ->live()
                                                ->dehydrated(false)
                                                ->afterStateHydrated(function (Get $get, Set $set, $state) {
                                                    // Cek jika ada data alamat kedua dari pelapor ketika checkbox sama_dengan_pelapor aktif
                                                    if ($get('sama_dengan_pelapor') && 
                                                        ($get('pelapors.alamat_2') || 
                                                        $get('pelapors.province_id_2') || 
                                                        $get('pelapors.city_id_2') || 
                                                        $get('pelapors.district_id_2') || 
                                                        $get('pelapors.subdistrict_id_2'))) {
                                                        $set('korbans_has_second_address', true);
                                                        return;
                                                    }
                                                    // Cek data alamat kedua korban
                                                    if ($get('korbans.alamat_2') || 
                                                        $get('korbans.province_id_2') || 
                                                        $get('korbans.city_id_2') || 
                                                        $get('korbans.district_id_2') || 
                                                        $get('korbans.subdistrict_id_2')) {
                                                        $set('korbans_has_second_address', true);
                                                        return;
                                                    }
                                                })
                                                ->default(function (Get $get) {
                                                    // Tambahkan default state sebagai backup
                                                    return $get('korbans.alamat_2') || 
                                                        $get('korbans.province_id_2') || 
                                                        $get('korbans.city_id_2') || 
                                                        $get('korbans.district_id_2') || 
                                                        $get('korbans.subdistrict_id_2') ||
                                                        ($get('sama_dengan_pelapor') && 
                                                            ($get('pelapors.alamat_2') || 
                                                            $get('pelapors.province_id_2') || 
                                                            $get('pelapors.city_id_2') || 
                                                            $get('pelapors.district_id_2') || 
                                                            $get('pelapors.subdistrict_id_2')));
                                                }),
                                            // Alamat 2 (Opsional)
                                            Section::make('Alamat Lain')
                                            ->visible(fn (Get $get): bool => (bool) $get('korbans_has_second_address'))
                                            ->schema([
                                                Textarea::make('korbans.alamat_2')
                                                    ->label('ALAMAT LAIN'),
                                                Select::make('korbans.province_id_2')
                                                    ->label('PROVINSI')
                                                    ->options(fn () => app('wilayah')->getProvinsi() ?? []) // Tambahkan options
                                                    ->live()
                                                    ->searchable()
                                                    ->afterStateUpdated(function (callable $set) {
                                                        $set('korbans.city_id_2', null);
                                                        $set('korbans.district_id_2', null);
                                                        $set('korbans.subdistrict_id_2', null);
                                                    }),
                                                Select::make('korbans.city_id_2')
                                                    ->label('KABUPATEN / KOTA')
                                                    ->options(function (callable $get) {
                                                        $provinceId = $get('korbans.province_id_2');
                                                        return $provinceId ? (app('wilayah')->getKabupaten($provinceId) ?? []) : [];
                                                    })
                                                    ->live()
                                                    ->searchable()
                                                    ->afterStateUpdated(function (callable $set) {
                                                        $set('korbans.district_id_2', null);
                                                        $set('korbans.subdistrict_id_2', null);
                                                    }),
                                                Select::make('korbans.district_id_2')
                                                    ->label('KECAMATAN') 
                                                    ->options(function (callable $get) {
                                                        $cityId = $get('korbans.city_id_2');
                                                        return $cityId ? (app('wilayah')->getKecamatan($cityId) ?? []) : [];
                                                    })
                                                    ->live()
                                                    ->searchable()
                                                    ->afterStateUpdated(fn (callable $set) => $set('korbans.subdistrict_id_2', null)),
                                                Select::make('korbans.subdistrict_id_2')
                                                    ->label('KELURAHAN / DESA')
                                                    ->options(function (callable $get) {
                                                        $districtId = $get('korbans.district_id_2');
                                                        return $districtId ? (app('wilayah')->getKelurahan($districtId) ?? []) : [];
                                                    })
                                                    ->live()
                                                    ->searchable(),
                                            ])
                                        ])
                                    ])
                            ]),
                        Wizard\Step::make('Terlapor')
                            ->description('Identitas Terlapor')
                            ->afterValidation(function ($state, $component) {
                                // Extract only terlapors data and convert to JSON string
                                $terlaporData = json_encode($state['terlapors'] ?? []);

                                // jika nama terlapor tidak ada maka set nama terlapor menjadi 'Belum ada nama'
                                if (!isset($state['terlapors']['nama'])) {
                                    $state['terlapors']['nama'] = 'Belum ada nama';
                                }

                                FormDraft::updateOrCreate(
                                    [
                                        'user_id' => auth()->id(),
                                        'form_type' => 'laporan_info'
                                    ],
                                    [
                                        'current_step' => 2,
                                        'terlapor_data' => $terlaporData
                                    ]
                                );
                            })
                            ->schema([
                                Grid::make(5)
                                    ->schema([
                                        TextInput::make('terlapors.nama')->label('NAMA')->default('Belum ada nama terlapor'),
                                        TextInput::make('terlapors.identity_no')->label('NO IDENTITAS'),
                                        // TextInput::make('terlapors.data_tambahan')->label('DATA TAMBAHAN'),
                                        PhoneInput::make('terlapors.kontak')->inputNumberFormat(PhoneInputNumberType::NATIONAL)->label('KONTAK'),
                                        PhoneInput::make('terlapors.kontak_2')->inputNumberFormat(PhoneInputNumberType::NATIONAL)->label('KONTAK LAIN'),
                                        Country::make('terlapors.kewarganegaraan')->label('KEWARGANEGARAAN')->default('Indonesia')->searchable(),
                                    ]),
                                Grid::make(6)
                                    ->schema([
                                        Select::make('terlapors.jenis_kelamin')
                                            ->label('JENIS KELAMIN')
                                            ->options([
                                                'Laki - Laki' => 'Laki - Laki',
                                                'Perempuan' => 'Perempuan'
                                            ]),
                                        TextInput::make('terlapors.tempat_lahir')->label('TEMPAT LAHIR'),
                                        Flatpickr::make('terlapors.tanggal_lahir')
                                            ->label('TGL. LAHIR')
                                            ->allowInput()
                                            // ->dateFormat('d F Y')
                                            ->altFormat('d F Y')
                                            ->altInput(true)
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $birthDate = Carbon::parse($state);
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
                                // section data tambahan
                                Section::make('Data Tambahan')
                                    ->schema([
                                        DataTambahanRepeater::make('terlapors.data_tambahan'),
                                    ]),
                                // Alamat section
                                Section::make('Alamat')
                                ->schema([
                                    // Alamat 1 (Utama)
                                    Section::make('Alamat Utama')
                                        ->schema([
                                            Textarea::make('terlapors.alamat')
                                                ->label('ALAMAT'),
                                            Select::make('terlapors.province_id')
                                                ->label('PROVINSI')
                                                ->provinsi()
                                                ->live()
                                                ->searchable()
                                                ->afterStateUpdated(function (callable $set) {
                                                    $set('terlapors.city_id', null);
                                                    $set('terlapors.district_id', null);
                                                    $set('terlapors.subdistrict_id', null);
                                                }),
                                            Select::make('terlapors.city_id')
                                                ->label('KABUPATEN / KOTA')
                                                ->kabupaten()
                                                ->live()
                                                ->searchable()
                                                ->afterStateUpdated(function (callable $set) {
                                                    $set('terlapors.district_id', null);
                                                    $set('terlapors.subdistrict_id', null);
                                                }),
                                            Select::make('terlapors.district_id')
                                                ->label('KECAMATAN')
                                                ->kecamatan()
                                                ->live()
                                                ->searchable()
                                                ->afterStateUpdated(fn (callable $set) => $set('terlapors.subdistrict_id', null)),
                                            Select::make('terlapors.subdistrict_id')
                                                ->label('KELURAHAN / DESA')
                                                ->kelurahan()
                                                ->live()
                                                ->searchable(),
                                        ]),
                                    // Toggle untuk menampilkan Alamat 2
                                    Toggle::make('terlapors_has_second_address')
                                        ->label('Tambah Alamat Lain?')
                                        ->live(),
                                    // Alamat 2 (Opsional)
                                    Section::make('Alamat Lain')
                                        ->schema([
                                            Textarea::make('terlapors.alamat_2')
                                                ->label('ALAMAT LAIN'),
                                            Select::make('terlapors.province_id_2')
                                                ->label('PROVINSI')
                                                ->provinsi()
                                                ->live()
                                                ->searchable()
                                                ->afterStateUpdated(function (callable $set) {
                                                    $set('terlapors.city_id_2', null);
                                                    $set('terlapors.district_id_2', null);
                                                    $set('terlapors.subdistrict_id_2', null);
                                                }),
                                            Select::make('terlapors.city_id_2')
                                                ->label('KABUPATEN / KOTA')
                                                ->kabupaten(fn (Get $get): ?string => $get('terlapors.province_id_2'))
                                                ->live()
                                                ->searchable()
                                                ->afterStateUpdated(function (callable $set) {
                                                    $set('terlapors.district_id_2', null);
                                                    $set('terlapors.subdistrict_id_2', null);
                                                }),
                                            Select::make('terlapors.district_id_2')
                                                ->label('KECAMATAN')
                                                ->kecamatan(fn (Get $get): ?string => $get('terlapors.city_id_2'))
                                                ->live()
                                                ->searchable()
                                                ->afterStateUpdated(fn (callable $set) => $set('terlapors.subdistrict_id_2', null)),
                                            Select::make('terlapors.subdistrict_id_2')
                                                ->label('KELURAHAN / DESA')
                                                ->kelurahan(fn (Get $get): ?string => $get('terlapors.district_id_2'))
                                                ->live()
                                                ->searchable(),
                                        ])
                                        ->visible(fn (Get $get): bool => $get('terlapors_has_second_address')),
                                ])
                            ]),
                        Wizard\Step::make('TKP')
                            ->description('Tempat Kejadian Perkara')
                            ->afterValidation(function ($state, $component) {
                                // Extract relevant data
                                $draftData = [
                                    'tkp' => $state['tkp'] ?? null,
                                    'city_id' => $state['city_id'] ?? null,
                                    'perkara' => $state['perkara'] ?? null,
                                    'kerugian' => $state['kerugian'] ?? null,
                                    'district_id' => $state['district_id'] ?? null,
                                    'province_id' => $state['province_id'] ?? null,
                                    'barangBuktis' => $state['barangBuktis'] ?? [],
                                    'tanggal_lapor' => $state['tanggal_lapor'] ?? null,
                                    'subdistrict_id' => $state['subdistrict_id'] ?? null,
                                    'tanggal_kejadian' => $state['tanggal_kejadian'] ?? null,
                                    'uraian_peristiwa' => $state['uraian_peristiwa'] ?? null,
                                    'media' => $state['media'] ?? null,
                                ];

                                FormDraft::updateOrCreate(
                                    [
                                        'user_id' => auth()->id(),
                                        'form_type' => 'laporan_info'
                                    ],
                                    [
                                        'current_step' => 3, // Adjust this number based on your step sequence
                                        'main_data' => json_encode($draftData)
                                    ]
                                );
                            })
                            ->schema([
                                Textarea::make('tkp')
                                    ->label('TEMPAT KEJADIAN PERKARA')
                                    ->required(),
                                Select::make('province_id')
                                    ->label('PROVINSI')
                                    ->searchable()
                                    ->provinsi()
                                    ->live()
                                    ->afterStateUpdated(function (callable $set) {
                                        $set('city_id', null);
                                        $set('district_id', null);
                                        $set('subdistrict_id', null);
                                    }),
                                Select::make('city_id')
                                    ->label('KABUPATEN / KOTA')
                                    ->searchable()
                                    ->kabupatenTkp()
                                    ->live()
                                    ->afterStateUpdated(function (callable $set) {
                                        $set('district_id', null);
                                        $set('subdistrict_id', null);
                                    }),
                                Select::make('district_id')
                                    ->label('KECAMATAN')
                                    ->kecamatanTkp()
                                    ->live()
                                    ->searchable()
                                    ->afterStateUpdated(function (callable $set) {
                                        $set('subdistrict_id', null);
                                    }),
                                Select::make('subdistrict_id')
                                    ->label('KELURAHAN / DESA')
                                    ->kelurahanTkp()
                                    ->live()
                                    ->searchable(),
                            ]),
                        Wizard\Step::make('Perkara')
                            ->description('Informasi Perkara')
                            ->afterValidation(function ($state, $component) {
                                // Ambil draft yang ada
                                $draft = FormDraft::firstWhere([
                                    'user_id' => auth()->id(),
                                    'form_type' => 'laporan_info'
                                ]);

                                // Decode main_data yang ada atau gunakan array kosong jika belum ada
                                $existingMainData = json_decode($draft?->main_data ?? '{}', true) ?: [];

                                // Merge data yang ada dengan data baru
                                $updatedMainData = array_merge($existingMainData, [
                                    'perkara' => $state['perkara'] ?? null,
                                    'uraian_peristiwa' => $state['uraian_peristiwa'] ?? null,
                                    'kerugian' => $state['kerugian'] ?? null,
                                ]);

                                // Update draft dengan data yang sudah di-merge
                                FormDraft::updateOrCreate(
                                    [
                                        'user_id' => auth()->id(),
                                        'form_type' => 'laporan_info'
                                    ],
                                    [
                                        'current_step' => 4,
                                        'main_data' => json_encode($updatedMainData)
                                    ]
                                );
                            })
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
                            ->afterStateHydrated(function ($state, $component) {
                                $draft = FormDraft::firstWhere([
                                    'user_id' => auth()->id(),
                                    'form_type' => 'laporan_info'
                                ]);
                                if ($draft && $draft->main_data) {
                                    $mainData = json_decode($draft->main_data, true);
                                    $state['barangBuktis'] = $mainData['barangBuktis'] ?? [];
                                }
                            })
                            ->afterValidation(function ($state, $component) {
                                $draft = FormDraft::firstWhere([
                                    'user_id' => auth()->id(),
                                    'form_type' => 'laporan_info'
                                ]);

                                $existingMainData = json_decode($draft?->main_data ?? '{}', true) ?: [];

                                $updatedMainData = array_merge($existingMainData, [
                                    'barangBuktis' => $state['barangBuktis'] ?? [],
                                ]);

                                FormDraft::updateOrCreate(
                                    [
                                        'user_id' => auth()->id(),
                                        'form_type' => 'laporan_info'
                                    ],
                                    [
                                        'current_step' => 5,
                                        'main_data' => json_encode($updatedMainData)
                                    ]
                                );
                            })
                            ->schema([
                                Repeater::make('barangBuktis')
                                    ->label('BARANG BUKTI')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('jumlah')
                                                    ->label('JUMLAH')
                                                    ->required(),
                                                TextInput::make('satuan')
                                                    ->label('SATUAN / UNIT')
                                                    ->required(),
                                                TextInput::make('nama_barang')
                                                    ->label('NAMA BARANG')
                                                    ->required(),
                                            ]),
                                    ])
                                    ->columnSpanFull()
                                    ->defaultItems(0)
                                    ->addActionLabel('Tambah Barang Bukti')
                                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                        $data['buktiable_type'] = LaporanInformasi::class;
                                        return $data;
                                    }),
                            ]),
                        // wizard media
                        Wizard\Step::make('Media')
                            ->description('Media')
                            ->schema([
                                FileUpload::make('media')
                            ->label('MEDIA / DOKUMEN PENDUKUNG')
                            ->multiple()
                            ->disk('public')
                            ->visibility('public')
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
                            BLADE)))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_laporan')->label('NO LAPORAN')->toggleable()->searchable()->sortable(),
                TextColumn::make('tanggal_lapor')
                ->wrapHeader()
                    ->sortable()
                    ->wrapHeader()
                    ->dateTime('d M Y')
                    ->label('TGL. LAPOR'),
                TextColumn::make('pelapors.nama')
                    ->searchable()
                    ->description(fn (LaporanInfo $record): string => $record->korbans()->where('laporan_info_id', $record->id)->pluck('nama')->join(', '))
                    ->label('PELAPOR / KORBAN'),
                // terlapor
                TextColumn::make('terlapors.nama')->label('TERLAPOR')->toggleable()->searchable(),
                TextColumn::make('tkp')->label('TKP')->toggleable()->searchable(),
                TextColumn::make('perkara')->label('PERKARA')->toggleable()->searchable()->sortable(),
                TextColumn::make('uraian_peristiwa')->label('URAIAN PERISTIWA')->limit(15)->toggleable(isToggledHiddenByDefault: true)->searchable()->wrap(),
                TextColumn::make('kerugian')
                    ->sortable()
                    ->toggleable()
                    ->searchable()
                    ->alignment(Alignment::Right)
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
                            ->disabled(auth()->user()->subdit_id || auth()->user()->unit_id || auth()->user()->penyidik_id)
                            ->afterStateUpdated(function (LaporanInformasi $record, $state) {
                                $record->update([
                                    'unit_id' => null,
                                ]);
                                // Kirim notifikasi ke user subdit saja (yang tidak memiliki unit_id)
                                if ($state) {
                                    $users = User::where('subdit_id', $state)
                                        ->whereNull('unit_id')  // Tambahkan filter ini
                                        ->get();
                                    foreach ($users as $user) {
                                        // $user->notify(new LaporanInformasiAssignedNotification($record, 'subdit'));
                                    }
                                }
                            }),
                        SelectColumn::make('unit_id')
                            ->label('UNIT')
                            ->alignment(Alignment::Center)
                            ->disabled(auth()->user()->unit_id || auth()->user()->penyidik_id)
                            ->selectablePlaceholder('Pilih Unit')
                            ->options(function (LaporanInfo $record) {
                                if (!$record->subdit_id) return [];
                                return Unit::where('subdit_id', $record->subdit_id)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->afterStateUpdated(function (LaporanInfo $record, $state) {
                                $record->update([
                                    // 'penyidik_id' => null
                                ]);

                                // Kirim notifikasi ke user unit saja (yang memiliki unit_id tapi bukan penyidik)
                                if ($state) {
                                    $users = User::where('unit_id', $state)
                                        // ->whereNull('penyidik_id')  // Tambahkan filter ini
                                        ->get();
                                    foreach ($users as $user) {
                                        // $user->notify(new LaporanInformasiAssignedNotification($record, 'unit'));
                                    }
                                }
                            }),
                        SelectColumn::make('penyidik_id')
                            ->label('PENYIDIK')
                            ->alignment(Alignment::Center)
                            ->selectablePlaceholder('Pilih Penyidik')
                            ->disabled(fn(): bool => (bool) auth()->user()->penyidik_id)
                            ->options(function (LaporanInfo $record) {
                                if (!$record->subdit_id || !$record->unit_id) return [];
                                return Penyidik::where('subdit_id', $record->subdit_id)
                                    ->where('unit_id', $record->unit_id)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->afterStateUpdated(function (LaporanInfo $record, $state) {
                                // Kirim notifikasi ke user penyidik
                                // TODO
                                // if ($state) {
                                //     $user = User::find($state);
                                //     $user->notify(new LaporanInformasiAssignedNotification($record, 'penyidik'));
                                // }
                            }),
                    ]),
                // barang bukti
                TextColumn::make('barangBuktis.nama_barang')->label('BARANG BUKTI')->limit(15)->toggleable(isToggledHiddenByDefault: true),
                SelectColumn::make('status')
                    ->searchable()
                    ->alignment(Alignment::Center)
                    ->selectablePlaceholder('Pilih Status')
                    ->options([
                        'Proses' => 'Proses',
                        'Terkendala' => 'Terkendala',
                        'Selesai' => 'Selesai',
                    ])
                    ->rules(['required']),
            ])
            ->filters([
                // filter by perkara
                SelectFilter::make('perkara')
                    ->label('PERKARA')
                    ->options(function() {
                        return LaporanInfo::distinct()
                            ->pluck('perkara', 'perkara')
                            ->filter()
                            ->toArray();
                    })
                    ->searchable()
                    ->query(function (Builder $query, $state) {
                        if (!empty($state['value'])) {
                            $query->where('perkara', $state['value']);
                        }
                    }),
                // filter by status
                SelectFilter::make('status')
                    ->label('STATUS')
                    ->options([
                        'Terlapor' => 'Terlapor',
                        'Proses' => 'Proses',
                        'Selesai' => 'Selesai',
                        'Terkendala' => 'Terkendala',
                    ]),
                Filter::make('filter')
                    ->form([
                        Select::make('subdit_id')
                            ->label('SUBDIT')
                            ->placeholder('Pilih Subdit')
                            ->options(Subdit::all()->pluck('name', 'id'))
                            ->searchable()
                            ->afterStateUpdated(function (callable $set) {
                                $set('unit_id', null);
                            })
                            ->reactive()
                            ->default(auth()->user()->subdit_id)
                            ->disabled(auth()->user()->subdit_id || auth()->user()->unit_id || auth()->user()->penyidik_id),
                        Select::make('unit_id')
                            ->label('UNIT')
                            ->placeholder('Pilih Unit')
                            ->options(function (Get $get) {
                                $subditId = $get('subdit_id');
                                if (!$subditId) return [];
                                return Unit::where('subdit_id', $subditId)->pluck('name', 'id');
                            })
                            ->reactive()
                            ->default(auth()->user()->unit_id)
                            ->disabled(auth()->user()->unit_id || auth()->user()->penyidik_id),
                        Select::make('penyidik_id')
                            ->label('PENYIDIK')
                            ->placeholder('Pilih Penyidik')
                            ->options(function (Get $get) {
                                $subditId = $get('subdit_id');
                                $unitId = $get('unit_id');
                                if (!$subditId && !$unitId) return [];
                                return Penyidik::where('subdit_id', $subditId)
                                    ->where('unit_id', $unitId)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                    ])->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['subdit_id'], function (Builder $query, $subditId) {
                            return $query->where('subdit_id', $subditId);
                        })->when($data['unit_id'], function (Builder $query, $unitId) {
                            return $query->where('unit_id', $unitId);
                        })->when($data['penyidik_id'], function (Builder $query, $penyidikId) {
                            return $query->where('penyidik_id', $penyidikId);
                        });
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('Analyze')
                        ->visible(fn (LaporanInfo $record): bool => env('AI_ANALYZER', false))
                        ->action(function (LaporanInfo $record, OllamaService $ollamaService, DeepSeekService $deepSeekService) {
                            try {
                                // Persiapkan data untuk analisis
                                $data = [
                                    'pelapor' => $record->pelapors->nama ?? 'Tidak ada',
                                    'korban' => $record->korbans->pluck('nama')->join(', ') ?? 'Tidak ada',
                                    'terlapor' => $record->terlapors->nama ?? 'Tidak ada',
                                    'uraian_peristiwa' => $record->uraian_peristiwa,
                                    'tkp' => $record->tkp,
                                    'kerugian' => $record->kerugian,
                                    'perkara' => $record->perkara,
                                ];

                                if (env('AI_SERVICE') == 'ollama') {
                                    // Dapatkan analisis dari Ollama
                                    $analysis = $ollamaService->analyze($data);
                                } else {
                                    // Dapatkan analisis dari DeepSeek
                                    $analysis = $deepSeekService->analyze($data);
                                }

                                if (isset($analysis['data'])) {
                                    $data = $analysis['data'];
                                    
                                    // Make sure we have proper data structure before proceeding
                                    if (is_array($data) && 
                                        isset($data['ringkasan_kronologi']) && 
                                        isset($data['analisis_hukum']) && 
                                        isset($data['langkah_penyidikan']) && 
                                        isset($data['tingkat_urgensi'])) {
                                        
                                        try {
                                            // Format data for kronologi_analysis
                                            $kronologiAnalysis = [
                                                'ringkasan' => trim($data['ringkasan_kronologi'])
                                            ];
                                            
                                            // Format data for possible_laws
                                            $possibleLaws = [
                                                'pidana_umum' => trim($data['analisis_hukum']['pidana_umum'] ?? ''),
                                                'teknologi_informasi' => trim($data['analisis_hukum']['teknologi_informasi'] ?? ''),
                                                'perundangan_lain' => trim($data['analisis_hukum']['perundangan_lain'] ?? '')
                                            ];
                                            
                                            // Format data for investigation_steps
                                            $investigationSteps = [
                                                'barang_bukti_digital' => $data['langkah_penyidikan']['barang_bukti_digital'] ?? [],
                                                'analisis_forensik' => $data['langkah_penyidikan']['analisis_forensik'] ?? [],
                                                'penelusuran_pelaku' => $data['langkah_penyidikan']['penelusuran_pelaku'] ?? [],
                                                'tindakan_penyidikan' => $data['langkah_penyidikan']['tindakan_penyidikan'] ?? []
                                            ];
                                            
                                            // Tentukan priority level berdasarkan tingkat urgensi
                                            $priorityLevels = [
                                                $data['tingkat_urgensi']['dampak_kejadian']['level'] ?? 'Sedang',
                                                $data['tingkat_urgensi']['nilai_kerugian']['level'] ?? 'Sedang',
                                                $data['tingkat_urgensi']['tingkat_kompleksitas']['level'] ?? 'Sedang',
                                                $data['tingkat_urgensi']['potensi_dampak']['level'] ?? 'Sedang'
                                            ];
                                            
                                            // Hitung level prioritas berdasarkan mayoritas
                                            $levelCounts = array_count_values($priorityLevels);
                                            arsort($levelCounts);
                                            $majorityLevel = key($levelCounts);
                                            
                                            // Format priority level dan tingkat urgensi
                                            $priorityData = [
                                                'calculated_level' => $majorityLevel,
                                                'level_counts' => $levelCounts,
                                                'urgensi' => $data['tingkat_urgensi']
                                            ];
                                            
                                            // Persiapkan data yang akan disimpan
                                            $dataToSave = [
                                                'laporan_id' => $record->id,
                                                'kronologi_analysis' => json_encode($kronologiAnalysis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                                                'possible_laws' => json_encode($possibleLaws, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                                                'investigation_steps' => json_encode($investigationSteps, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                                                'priority_level' => json_encode($priorityData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                                                'raw_response' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                                            ];
                                            
                                            // Tambahkan created_at dan updated_at
                                            $dataToSave['created_at'] = now();
                                            $dataToSave['updated_at'] = now();
                                            
                                            // Log data yang akan disimpan
                                            \Log::info('Data yang akan disimpan:', $dataToSave);
                                            
                                            // Simpan data
                                            $saved = $record->analysis()->updateOrCreate(
                                                ['laporan_id' => $record->id],
                                                $dataToSave
                                            );
                                            
                                            if (!$saved) {
                                                throw new \Exception('Gagal menyimpan data analisis');
                                            }
                                            
                                            Notification::make()
                                                ->title('Analisis Berhasil')
                                                ->success()
                                                ->body('Laporan berhasil dianalisis')
                                                ->send();
                                            
                                            // Redirect ke view-analysis
                                            return redirect()->to(LaporanInfoResource::getUrl('view-analysis', ['record' => $record]));
                                            
                                        } catch (\Exception $e) {
                                            \Log::error('Error saat menyimpan analisis:', [
                                                'error' => $e->getMessage(),
                                                'trace' => $e->getTraceAsString(),
                                                'data' => $data ?? null
                                            ]);
                                            
                                            throw new \Exception('Gagal menyimpan hasil analisis: ' . $e->getMessage());
                                        }
                                    } else {
                                        throw new \Exception('Format analisis tidak sesuai: Data tidak lengkap');
                                    }
                                } else {
                                    throw new \Exception('Format analisis tidak sesuai: Tidak ada data dalam respons');
                                }
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Gagal Menganalisis')
                                    ->danger()
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        })
                        ->color('success')
                        ->icon('heroicon-o-cpu-chip')
                        ->requiresConfirmation()
                        ->modalHeading('Analisis Laporan')
                        ->modalDescription('Apakah Anda yakin ingin menganalisis laporan ini menggunakan AI SiberBot?')
                        ->modalSubmitActionLabel('Ya, Analisis'),
                    Action::make('viewAnalysis')
                        ->label('Lihat Analisis')
                        ->icon('heroicon-o-document-magnifying-glass')
                        ->url(fn (LaporanInfo $record): string => static::getUrl('view-analysis', ['record' => $record]))
                        ->visible(fn (LaporanInfo $record): bool => $record->analysis()->exists()),
                ]),
                ActionsViewAction::make()
                    ->label(false)
                    ->modalContent(fn (LaporanInfo $record): View => view(
                        'filament.resources.laporan-info.view',
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
            ->recordUrl(null)
            ->emptyStateActions([]);
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
            'index' => Pages\ListLaporanInfos::route('/'),
            'create' => Pages\CreateLaporanInfo::route('/create'),
            'view' => Pages\ViewLaporanInfo::route('/{record}'),
            'edit' => Pages\EditLaporanInfo::route('/{record}/edit'),
            'view-analysis' => Pages\ViewAnalysis::route('/{record}/view-analysis'),
        ];
    }
}
