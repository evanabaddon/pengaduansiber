<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\LaporanInformasi;
use Filament\Tables\Table;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColumnGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestLaporanInformasiWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    // title
    public function getTitle(): string
    {
        return 'Informasi / Surat Masyarakat Terbaru (7 Hari Terakhir)';
    }

    public function table(Table $table): Table
    {
        $query = LaporanInformasi::query()
            ->whereBetween('tanggal_lapor', [now()->subDays(7), now()]);

        // Filter berdasarkan role
        if (auth()->user()->hasRole('subdit')) {
            $query->where('subdit_id', auth()->user()->subdit_id); 
        } elseif (auth()->user()->hasRole('unit')) {
            $query->whereNotNull('unit_id')->where('unit_id', auth()->user()->unit_id);
        } elseif (auth()->user()->hasRole('penyidik')) {
            $query->whereNotNull('penyidik_id')->where('penyidik_id', auth()->user()->id);
        }
        
        // Urutkan data berdasarkan tanggal_lapor terbaru
        $query->latest('tanggal_lapor');

        return $table
            ->heading('Informasi / Surat Masyarakat Terbaru (7 Hari Terakhir)')
            ->striped()
            ->query($query)
            ->columns([
                TextColumn::make('tanggal_lapor')
                    ->label('TGL. LAPOR')
                    ->dateTime('d M Y')
                    ->sortable(),
                TextColumn::make('pelapors.nama')
                    ->label('PELAPOR')
                    ->searchable(),
                TextColumn::make('perkara')
                    ->label('PERKARA')
                    ->limit(30),
                ColumnGroup::make('YANG MENANGANI')
                    ->wrapHeader()
                    ->alignment(Alignment::Center)
                    ->columns([
                        TextColumn::make('subdit.name')
                            ->alignment(Alignment::Center)
                            ->label('SUBDIT'),
                        TextColumn::make('unit.name')
                            ->alignment(Alignment::Center)
                            ->label('UNIT'),
                        TextColumn::make('penyidik.name')
                            ->alignment(Alignment::Center)
                            ->label('PENYIDIK'),
                    ]),
                TextColumn::make('status')
                    ->alignment(Alignment::Center)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Terlapor' => 'gray',
                        'Proses' => 'warning', 
                        'Selesai' => 'success',
                        'Terkendala' => 'danger',
                    }),
            ])
            ->actions([
                
            ])
            ->paginated(false);
    }
}