<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use App\Models\Unit;
use App\Models\User;
use Filament\Tables;
use App\Models\Subdit;
use App\Models\Laporan;
use Filament\Forms\Get;
use App\Models\Penyidik;
use Filament\Forms\Form;
use Illuminate\View\View;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Wizard\Step;
use Filament\Infolists\Components\Section;
use Filament\Resources\Pages\CreateRecord;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ContentTabPosition;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use App\Filament\Resources\LaporanResource\Pages;
use App\Notifications\LaporanAssignedNotification;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use App\Filament\Resources\LaporanResource\RelationManagers;
use Filament\Tables\Actions\ViewAction as ActionsViewAction;
use Teguh02\IndonesiaTerritoryForms\IndonesiaTerritoryForms;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Filament\Resources\LaporanResource\Widgets\LaporanStatusOverview;
use App\Filament\Resources\LaporanResource\RelationManagers\KorbansRelationManager;
use App\Filament\Resources\LaporanResource\RelationManagers\PelaporsRelationManager;
use App\Filament\Resources\LaporanResource\RelationManagers\TerlaporsRelationManager;

class LaporanResource extends Resource
{
    protected static ?string $model = Laporan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // global search
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return Laporan::query()->with(['pelapors', 'korbans', 'terlapors']);
    }

    // sort navigation
    protected static ?int $navigationSort = -10;

    // navigation label
    public static function getNavigationLabel(): string
    {
        return 'Laporan Masyarakat';
    }

    // navigation group
    public static function getNavigationGroup(): ?string
    {
        return 'Laporan';
    }

    // navigation icon
    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-document-chart-bar';
    }

    // judul form
    public static function getLabel(): string
    {
        return 'Laporan Masyarakat';
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
                                    // kewarganegaraan 
                                    Select::make('pelapors.kewarganegaraan')
                                        ->label('KEWARGANEGARAAN')
                                        ->options([
                                            'WNI' => 'WNI',
                                            'WNA' => 'WNA'
                                        ]),
                                    Select::make('pelapors.jenis_kelamin')
                                        ->label('JENIS KELAMIN')
                                        ->options([
                                            'Laki - Laki' => 'Laki - Laki',
                                            'Perempuan' => 'Perempuan'
                                        ]),

                                ]),
                                Grid::make(5)
                                    ->schema([
                                        TextInput::make('pelapors.tempat_lahir')->label('TEMPAT LAHIR'),
                                        DatePicker::make('pelapors.tanggal_lahir')
                                            ->label('TGL. LAHIR')
                                            ->reactive()
                                            ->displayFormat('d F Y')
                                            ->native(false)
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
                                        TextInput::make('pelapors.pekerjaan')->label('PEKERJAAN'),
                                        // agama
                                        Select::make('pelapors.agama')
                                            ->label('AGAMA')
                                            ->options([
                                                'Islam' => 'Islam',
                                                'Kristen' => 'Kristen',
                                                'Katolik' => 'Katolik',
                                                'Hindu' => 'Hindu',
                                                'Budha' => 'Budha',
                                                'Konghucu' => 'Konghucu',
                                            ])
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
                                Textarea::make('pelapors.alamat')->label('ALAMAT'),
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
                                        $set('korbans.tempat_lahir', $get('pelapors.tempat_lahir'));
                                        $set('korbans.tanggal_lahir', $get('pelapors.tanggal_lahir'));
                                        $set('korbans.jenis_kelamin', $get('pelapors.jenis_kelamin'));
                                        $set('korbans.kewarganegaraan', $get('pelapors.kewarganegaraan'));
                                        $set('korbans.agama', $get('pelapors.agama'));
                                        $set('korbans.pekerjaan', $get('pelapors.pekerjaan'));
                                        $set('korbans.usia', $get('pelapors.usia'));
                                        $set('korbans.alamat', $get('pelapors.alamat'));
                                        $set('korbans.province_id', $get('pelapors.province_id'));
                                        $set('korbans.city_id', $get('pelapors.city_id'));
                                        $set('korbans.district_id', $get('pelapors.district_id'));
                                        $set('korbans.subdistrict_id', $get('pelapors.subdistrict_id'));
                                        $set('korbans.domestic', $get('pelapors.domestic'));
                                    } else {
                                        $set('korbans.identity_no', null);
                                        $set('korbans.nama', null);
                                        $set('korbans.kontak', null);
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
                                    }
                                }),
                                Grid::make(5)
                                    ->schema([
                                        TextInput::make('korbans.nama')->label('NAMA'),
                                        TextInput::make('korbans.identity_no')->label('NO IDENTITAS'),
                                        PhoneInput::make('korbans.kontak')->inputNumberFormat(PhoneInputNumberType::NATIONAL)->label('KONTAK'),
                                        // kewarganegaraan
                                        Select::make('korbans.kewarganegaraan')
                                            ->label('KEWARGANEGARAAN')
                                            ->options([
                                                'WNI' => 'WNI',
                                                'WNA' => 'WNA'
                                            ]),
                                        // jenis kelamin
                                        Select::make('korbans.jenis_kelamin')
                                            ->label('JENIS KELAMIN')
                                            ->options([
                                                'Laki - Laki' => 'Laki - Laki',
                                                'Perempuan' => 'Perempuan'
                                            ]),
                                    ]),
                                Grid::make(5)
                                    ->schema([
                                        TextInput::make('korbans.tempat_lahir')->label('TEMPAT LAHIR'),
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
                                        // agama
                                        Select::make('korbans.agama')
                                            ->label('AGAMA')
                                            ->options([
                                                'Islam' => 'Islam',
                                                'Kristen' => 'Kristen',
                                                'Katolik' => 'Katolik',
                                                'Hindu' => 'Hindu',
                                                'Budha' => 'Budha',
                                                'Konghucu' => 'Konghucu',
                                                'Lainnya' => 'Lainnya',
                                            ])
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
                                        // kewarganegaraan
                                        Select::make('terlapors.kewarganegaraan')
                                            ->label('KEWARGANEGARAAN')
                                            ->options([
                                                'WNI' => 'WNI',
                                                'WNA' => 'WNA'
                                            ]),
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
                                        TextInput::make('terlapors.usia')->label('USIA')->numeric(),
                                        // pekerjaan
                                        TextInput::make('terlapors.pekerjaan')->label('PEKERJAAN'),
                                        // agama
                                        Select::make('terlapors.agama')
                                            ->label('AGAMA')
                                            ->options([
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
                                Textarea::make('tkp')->label('Tempat Kejadian Perkara'),
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
                        // wizard media
                        Wizard\Step::make('Media')
                            ->description('Media')
                            ->schema([
                                FileUpload::make('media')
                            ->label('MEDIA')
                            ->multiple()
                            ->directory('laporan-masyarakat')
                            ->preserveFilenames()
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file, $livewire): string => (string) str($file->getClientOriginalName())
                                    ->prepend('laporan-masyarakat-'.$livewire->record->id.'-'),
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
                            ->columnSpanFull(),
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
                    ->description(fn (Laporan $record): string => $record->korbans()->where('laporan_id', $record->id)->pluck('nama')->join(', '))
                    ->label('PELAPOR / KORBAN'),
                TextColumn::make('tkp')->label('TKP')->alignment(Alignment::Center)->toggleable(),
                TextColumn::make('perkara')->label('PERKARA')->alignment(Alignment::Center)->toggleable(),
                TextColumn::make('uraian_peristiwa')->label('URAIAN PERISTIWA')->limit(15)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('kerugian')
                    ->sortable()
                    ->toggleable()
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
                            ->afterStateUpdated(function (Laporan $record, $state) {
                                $record->update([
                                    'unit_id' => null,
                                    'penyidik_id' => null
                                ]);

                                // Kirim notifikasi ke user subdit
                                if ($state) {
                                    $users = User::where('subdit_id', $state)->get();
                                    foreach ($users as $user) {
                                        $user->notify(new LaporanAssignedNotification($record, 'subdit'));
                                    }
                                }
                            }),
                        SelectColumn::make('unit_id')
                            ->label('UNIT')
                            ->alignment(Alignment::Center)
                            ->disabled(auth()->user()->hasRole('unit') || auth()->user()->hasRole('penyidik'))
                            ->selectablePlaceholder('Pilih Unit')
                            ->options(function (Laporan $record) {
                                if (!$record->subdit_id) return [];
                                return Unit::where('subdit_id', $record->subdit_id)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->afterStateUpdated(function (Laporan $record, $state) {
                                $record->update([
                                    'penyidik_id' => null
                                ]);

                                // Kirim notifikasi ke user unit
                                if ($state) {
                                    $users = User::where('unit_id', $state)->get();
                                    foreach ($users as $user) {
                                        $user->notify(new LaporanAssignedNotification($record, 'unit'));
                                    }
                                }
                            }),
                        SelectColumn::make('penyidik_id')
                            ->label('PENYIDIK')
                            ->alignment(Alignment::Center)
                            ->selectablePlaceholder('Pilih Penyidik')
                            ->disabled(auth()->user()->hasRole('penyidik'))
                            ->options(function (Laporan $record) {
                                if (!$record->unit_id) return [];
                                return User::where('unit_id', $record->unit_id)
                                    ->whereDoesntHave('roles', function($query) {
                                        $query->whereIn('name', ['super_admin', 'subdit', 'unit']);
                                    })
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->afterStateUpdated(function (Laporan $record, $state) {
                                // Kirim notifikasi ke user penyidik
                                if ($state) {
                                    $user = User::find($state);
                                    $user->notify(new LaporanAssignedNotification($record, 'penyidik'));
                                }
                            }),
                    ]),
                SelectColumn::make('status')
                    ->selectablePlaceholder('Pilih Status')
                    ->options([
                        'Terlapor' => 'Terlapor',
                        'Proses' => 'Proses',
                        'Selesai' => 'Selesai',
                        'Terkendala' => 'Terkendala',
                    ])
                    ->rules(['required']),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionsViewAction::make()
                    ->label(false)
                    ->modalContent(fn (Laporan $record): View => view(
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
            'index' => Pages\ListLaporans::route('/'),
            'create' => Pages\CreateLaporan::route('/create'),
            'view' => Pages\ViewLaporan::route('/{record}'),
            'edit' => Pages\EditLaporan::route('/{record}/edit'),
        ];
    }

    // public static function infolist(Infolist $infolist): Infolist
    // {
    //     return $infolist
    //         ->schema([
    //             Section::make('Pelapor')
    //                 ->description('Identitas Pelapor')
    //                 ->columnSpan(1)
    //                 ->schema([
    //                     TextEntry::make('pelapors.identity_no')->label('NIK / PASSPORT'),
    //                     TextEntry::make('pelapors.nama')->label('NAMA'),
    //                     TextEntry::make('pelapors.tempat_lahir')->label('TEMPAT LAHIR'),
    //                     TextEntry::make('pelapors.tanggal_lahir')->dateTime('d F Y')->label('TGL LAHIR'),
    //                     TextEntry::make('pelapors.jenis_kelamin')->label('JENIS KELAMIN'),
    //                     TextEntry::make('pelapors.alamat')->label('ALAMAT'),
    //             ]),
    //             Section::make('Korban')
    //                 ->description('Identitas Korban')
    //                 ->columnSpan(1)
    //                 ->schema([
    //                     TextEntry::make('korbans.identity_no')->label('NIK / PASSPORT'),
    //                     TextEntry::make('korbans.nama')->label('NAMA'),
    //                     TextEntry::make('korbans.tempat_lahir')->label('TEMPAT LAHIR'),
    //                     TextEntry::make('korbans.tanggal_lahir')->dateTime('d F Y')->label('TGL LAHIR'),
    //                     TextEntry::make('korbans.jenis_kelamin')->label('JENIS KELAMIN'),
    //                     TextEntry::make('korbans.alamat')->label('ALAMAT'),
    //             ]),
    //             Section::make('Terlapor')
    //                 ->description('Identitas Terlapor')
    //                 ->columnSpan(1)
    //                 ->schema([
    //                     TextEntry::make('terlapors.identity_no')->label('NIK / PASSPORT'),
    //                     TextEntry::make('terlapors.nama')->label('NAMA'),
    //                     TextEntry::make('terlapors.jenis_kelamin')->label('JENIS KELAMIN'),
    //                     TextEntry::make('terlapors.alamat')->label('ALAMAT'),
    //                     TextEntry::make('terlapors.usia')->label('USIA'),
    //                 ]),
    //             Section::make('Perkara')
    //                 ->description('Informasi Perkara')
    //                 ->columnSpan(1)
    //                 ->schema([
    //                     TextEntry::make('tkp')->label('TKP'),
    //                     TextEntry::make('tanggal_lapor')->dateTime('d F Y')->label('TGL. LAPOR'),
    //                     TextEntry::make('tanggal_kejadian')->dateTime('d F Y')->label('TGL. KEJADIAN'),
    //                     TextEntry::make('perkara')->label('PERKARA'),
    //                     TextEntry::make('uraian_peristiwa')->label('URAIAN PERISTIWA'),
    //                     TextEntry::make('kerugian')->label('KERUGIAN')->money('IDR'),
    //             ]),
    //             Section::make('Status')
    //                 ->description('Status Laporan')
    //                 ->columnSpan(1)
    //                 ->schema([
    //                     // media
    //                     TextEntry::make('subdit_id')->label('SUBDIT'),
    //                     TextEntry::make('unit_id')->label('UNIT'),
    //                     TextEntry::make('penyidik_id')->label('PENYIDIK'),
    //                     TextEntry::make('status')->label('STATUS')
    //                     ->badge()
    //                     ->color(fn (string $state): string => match ($state) {
    //                         'Terlapor' => 'gray',
    //                         'Proses' => 'warning',
    //                         'Selesai' => 'success',
    //                         'Terkendala' => 'danger',
    //                 }),
    //             ]),
    //         ]);
    // }

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
