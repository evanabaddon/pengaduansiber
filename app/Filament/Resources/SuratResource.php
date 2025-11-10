<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Surat;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Hidden;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\SuratResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SuratResource\RelationManagers;
use HayderHatem\FilamentSubNavigation\Concerns\HasBadgeSubNavigation;

class SuratResource extends Resource
{    
    protected static ?string $model = Surat::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label ="Persuratan";


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

    public static function form(Form $form): Form
    {
        // 💾 Load JSON klasterisasi (disimpan di storage/app/klasterisasi.json misalnya)
        $klasterPath = storage_path('app/klasterisasi.json');
        $klaster = file_exists($klasterPath)
            ? json_decode(file_get_contents($klasterPath), true)['jenis_dokumen']
            : [];

        return $form
            ->schema([
                Hidden::make('panel')
                    ->required()
                    ->dehydrated(),

                Hidden::make('satker')
                    ->required()
                    ->dehydrated(),


                Forms\Components\Section::make('Klasifikasi Surat')
                    ->schema([
                        // 🧭 LEVEL 1
                        // Forms\Components\Select::make('jenis_dokumen')
                        //     ->label('Jenis Dokumen')
                        //     ->options(collect($klaster)->pluck('nama', 'nama'))
                        //     ->reactive()
                        //     ->default(function () use ($klaster) {
                        //         $fromUrl = request('jenis_dokumen');
                                
                        //         // Debug: lihat apa yang datang dari URL
                        //         \Log::info('Jenis Dokumen Parameter', [
                        //             'from_url' => $fromUrl,
                        //             'available_options' => collect($klaster)->pluck('nama')
                        //         ]);
                                
                        //         // Validasi: pastikan value dari URL ada di options
                        //         $validOptions = collect($klaster)->pluck('nama');
                        //         if ($fromUrl && $validOptions->contains($fromUrl)) {
                        //             return $fromUrl;
                        //         }
                                
                        //         return null;
                        //     })
                        //     ->afterStateUpdated(fn($set) => $set('kategori_surat', null)),
                        Hidden::make('jenis_dokumen')
                            ->required()
                            ->default(function () use ($klaster) {
                                $fromUrl = request('jenis_dokumen');
                                
                                // Debug: lihat apa yang datang dari URL
                                \Log::info('Jenis Dokumen Parameter', [
                                    'from_url' => $fromUrl,
                                    'available_options' => collect($klaster)->pluck('nama')
                                ]);
                                
                                // Validasi: pastikan value dari URL ada di options
                                $validOptions = collect($klaster)->pluck('nama');
                                if ($fromUrl && $validOptions->contains($fromUrl)) {
                                    return $fromUrl;
                                }
                                
                                return null;
                            })
                            ->afterStateUpdated(fn($set) => $set('kategori_surat', null))
                            ->dehydrated(),

                        // 🗂️ LEVEL 2
                        Forms\Components\Select::make('kategori_surat')
                            ->label('Kategori Surat')
                            ->options(function (callable $get) use ($klaster) {
                                $jenis = $get('jenis_dokumen');
                                $kategori = collect($klaster)
                                    ->firstWhere('nama', $jenis)['kategori'] ?? [];
                                return collect($kategori)->pluck('nama', 'nama');
                            })
                            ->reactive()
                            ->afterStateUpdated(fn($set) => $set('pejabat_penerbit', null)),

                        // 🧑‍💼 LEVEL 3
                        Forms\Components\Select::make('pejabat_penerbit')
                            ->label('Pejabat Penerbit')
                            ->options(function (callable $get) use ($klaster) {
                                $jenis = $get('jenis_dokumen');
                                $kategori = $get('kategori_surat');
                                $pejabat = collect($klaster)
                                    ->firstWhere('nama', $jenis)['kategori'] ?? [];
                                $target = collect($pejabat)->firstWhere('nama', $kategori)['pejabat'] ?? [];
                                return collect($target)->mapWithKeys(fn($p) => [$p => $p]);
                            })
                            ->reactive(),
                        
                        // Khusus untuk NASKAH DINAS > 1. SURAT PERINTAH > ADA PILIHAN
                        // 📝 LEVEL 4: Pilihan Versi Template
                        Forms\Components\Select::make('template_version')
                            ->label('Versi Template')
                            ->visible(fn(callable $get) => $get('kategori_surat') === 'Surat Perintah')
                            ->options(function (callable $get) {
                                $kategori = $get('kategori_surat');
                                $pejabat = $get('pejabat_penerbit');

                                if ($kategori !== 'Surat Perintah' || !$pejabat) return [];

                                $folder = storage_path("app/templates/NASKAH DINAS/1. SURAT PERINTAH");
                                $files = scandir($folder);

                                $isAn = str_starts_with(strtolower($pejabat), 'a.n.');
                                $pejabatName = strtolower(trim(str_ireplace('a.n.', '', $pejabat)));

                                $filtered = array_filter($files, function ($f) use ($pejabatName, $isAn) {
                                    $fname = strtolower($f);
                                    $fileIsAn = str_contains($fname, 'a.n.');
                                    if ($fileIsAn !== $isAn) return false;
                                    return str_contains($fname, $pejabatName) && str_ends_with($f, '.docx');
                                });

                                return collect($filtered)->mapWithKeys(fn($f) => [$f => $f])->toArray();
                            })
                            ->reactive()
                            ->afterStateUpdated(function ($set, $get) {
                                $file = $get('template_version');
                                if ($file) {
                                    // bangun path lengkap
                                    $fullPath = "NASKAH DINAS/1. SURAT PERINTAH/{$file}";
                                    $set('template_path', $fullPath);
                                }
                            }),




                        Forms\Components\TextInput::make('nama_dokumen')
                            ->label('Nama Dokumen')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Template')
                    ->schema([
                        Forms\Components\Placeholder::make('template_path')
                            ->label('Template Otomatis')
                            ->content(function ($get) {
                                $kategori = $get('kategori_surat');
                                $pejabat = $get('pejabat_penerbit');
                                $versi   = $get('template_version');
                
                                // Kasus 1: Surat Perintah dengan versi template dipilih
                                if ($kategori === 'Surat Perintah' && $versi) {
                                    return "storage/templates/NASKAH DINAS/1. SURAT PERINTAH/{$versi}";
                                }
                
                                // Kasus 2: Surat lain atau Surat Perintah tanpa versi, fallback default
                                if ($kategori && $pejabat) {
                                    // bisa panggil TemplateResolver untuk cari file default
                                    $file = \App\Helpers\TemplateResolver::resolve('NASKAH DINAS', $kategori, $pejabat);
                                    return $file ? "storage/templates/{$file}" : '— Template tidak ditemukan —';
                                }
                
                                // Kasus 3: belum pilih kategori/pejabat
                                return '— Pilih kategori, pejabat & versi terlebih dahulu —';
                            }),
                        ]),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_dokumen')->searchable()->label('Nama Dokumen'),
                Tables\Columns\TextColumn::make('kategori_surat')->label('Kategori'),
                Tables\Columns\TextColumn::make('pejabat_penerbit')->label('Pejabat'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Ubah Data'),
                Tables\Actions\Action::make('open_onlyoffice')
                    ->label('Buka Dokumen')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn(Surat $record) => route('onlyoffice.edit', $record))
                    ->openUrlInNewTab(),
                
                    // Download — placeholder (TODO: implement actual file download)
                Tables\Actions\Action::make('download')
                ->label('Unduh')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (Surat $record, array $data): void {
                    // TODO: implement download logic
                    // Example options:
                    // - return response()->download(storage_path("app/{$record->nama_file}"));
                    // - or stream file, or generate file on the fly, etc.
                    //
                    // For now we log & flash so you see the action fires:
                    \Log::info('TODO: download action called for Surat id=' . $record->id);
                    session()->flash('success', 'Download action not implemented yet (TODO).');
                })
                ->requiresConfirmation(false), // set true if you want confirmation

                Tables\Actions\Action::make('delete')
                    ->label('Hapus')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation() // require confirmation for delete
                    ->modalHeading('Konfirmasi Hapus')
                    ->action(function (Surat $record, array $data): void {
                        // TODO: implement delete logic
                        // Example options:
                        $record->delete();
                        Storage::delete($record->nama_dokumen);
                        //
                        // For safety the actual delete is disabled until you implement it:
                        \Log::warning('TODO: delete action called for Surat id=' . $record->id);
                        session()->flash('warning', 'Delete action not implemented yet (TODO).');
                    })->requiresConfirmation(true),
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
            'index' => Pages\ListSurats::route('/'),
            'create' => Pages\CreateSurat::route('/create'),
            'edit' => Pages\EditSurat::route('/{record}/edit'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery();
    //         // ->when(request('panel'), fn($q, $panel) => $q->where('panel', $panel))
    //         // ->when(request('menu'), fn($q, $menu) => $q->where('menu', $menu))
    //         // ->when(request('submenu'), fn($q, $submenu) => $q->where('submenu', $submenu))
    //         // ->when(request('type'), fn($q, $type) => $q->where('type', $type));
    // }

    // public static function getEloquentQuery(): Builder
    // {
    //     $query = parent::getEloquentQuery();

    //     $typeMap = [
    //         'surat_perintah' => 'Surat Perintah',
    //         'surat_tugas' => 'Surat Tugas',
    //         'surat_telegram' => 'Surat Telegram',
    //         'nota_dinas' => 'Nota Dinas',
    //         'surat' => 'Surat',
    //         'surat_pengantar' => 'Surat Pengantar',
    //         'surat_undangan' => 'Surat Undangan',
    //     ];

    //     $subtypeMap = [
    //         'kapolda' => 'Kapolda',
    //         'direktur' => 'Direktur',
    //         'kasubbagrenmin' => 'Kasubbagrenmin',
    //         'urkeu' => 'Urkeu',
    //         'urmintu' => 'Urmintu',
    //         'urren' => 'Urren',
    //     ];

    //     return $query
    //         ->when(request('menu'), fn($q, $menu) => $q->where('satker', $menu))
    //         ->when(request('type'), fn($q, $type) => 
    //             $q->where('kategori_surat', $typeMap[$type] ?? $type)
    //         )
    //         ->when(request('subtype'), fn($q, $subtype) => 
    //             $q->where('pejabat_penerbit', $subtypeMap[$subtype] ?? $subtype)
    //         );
    // }
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $typeMap = [
            'surat_perintah' => 'Surat Perintah',
            'surat_tugas' => 'Surat Tugas',
            'surat_telegram' => 'Surat Telegram',
            'nota_dinas' => 'Nota Dinas',
            'surat' => 'Surat',
            'surat_pengantar' => 'Surat Pengantar',
            'surat_undangan' => 'Surat Undangan',
        ];

        $subtypeMap = [
            'kapolda' => 'Kapolda',
            'direktur' => 'Direktur',
            'kasubbagrenmin' => 'Kasubbagrenmin',
            'urkeu' => 'Urkeu',
            'urmintu' => 'Urmintu',
            'urren' => 'Urren',
        ];

        return $query
            ->when(request('menu'), fn($q, $menu) => $q->where('satker', $menu))
            ->when(request('type'), fn($q, $type) => 
                $q->where('kategori_surat', $typeMap[$type] ?? $type)
            )
            ->when(request('subtype'), function ($q, $subtype) use ($subtypeMap) {
                // mapping ka* → Kaur
                if (str_starts_with($subtype, 'ka') && !isset($subtypeMap[$subtype])) {
                    $mappedSubtype = 'Kaur';
                } else {
                    $mappedSubtype = $subtypeMap[$subtype] ?? $subtype;
                }
                
                $q->where('pejabat_penerbit', $mappedSubtype);
            });
    }


}
