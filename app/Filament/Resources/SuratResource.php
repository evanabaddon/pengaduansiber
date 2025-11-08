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
use Filament\Navigation\NavigationItem;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SuratResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SuratResource\RelationManagers;

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
                Forms\Components\Section::make('Klasifikasi Surat')
                    ->schema([
                        // 🧭 LEVEL 1
                        Forms\Components\Select::make('jenis_dokumen')
                            ->label('Jenis Dokumen')
                            ->options(collect($klaster)->pluck('nama', 'nama'))
                            ->reactive()
                            ->afterStateUpdated(fn($set) => $set('kategori_surat', null)),

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


                        Forms\Components\TextInput::make('nomor_surat')
                            ->label('Nomor Surat')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('perihal')
                            ->label('Perihal')
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
                Tables\Columns\TextColumn::make('nomor_surat')->searchable()->label('Nomor Surat'),
                Tables\Columns\TextColumn::make('kategori_surat')->label('Kategori'),
                Tables\Columns\TextColumn::make('pejabat_penerbit')->label('Pejabat'),
                Tables\Columns\TextColumn::make('perihal')->label('Perihal')->limit(50),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('open_onlyoffice')
                    ->label('Edit Dokumen')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn(Surat $record) => route('onlyoffice.edit', $record))
                    ->openUrlInNewTab(),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
            // ->when(request('panel'), fn($q, $panel) => $q->where('panel', $panel))
            // ->when(request('menu'), fn($q, $menu) => $q->where('menu', $menu))
            // ->when(request('submenu'), fn($q, $submenu) => $q->where('submenu', $submenu))
            // ->when(request('type'), fn($q, $type) => $q->where('type', $type));
    }
    
}
