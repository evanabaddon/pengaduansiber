<?php

namespace App\Filament\Resources\LaporanInformasiResource\Pages;

use App\Filament\Resources\LaporanInformasiResource;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use App\Services\OllamaService;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;

class ViewAnalysis extends Page
{
    use InteractsWithRecord;

    protected static string $resource = LaporanInformasiResource::class;

    protected static string $view = 'filament.resources.laporan.view-analysis';

    protected static ?string $title = 'Hasil AI Analisis SiberBOT';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->record->load('analysis');

        // Redirect jika belum ada analisis
        if (!$this->record->analysis()->exists()) {
            $this->redirect($this->getResource()::getUrl('view', ['record' => $record]));
        }
    }

    protected function getActions(): array
    {
        return [
            Action::make('reanalyze')
                ->label('Analisis Ulang')
                ->action(function (OllamaService $ollamaService) {
                    try {
                        $record = $this->record;
                        
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

                        // Dapatkan analisis dari Ollama
                        $analysis = $ollamaService->analyze($data);

                        if ($analysis['success']) {
                            $data = $analysis['data'];
                            
                            // jika format tidak sesuai, maka lakukan analisis kembali
                            if (!isset($data['ringkasan_kronologi']) || !isset($data['analisis_hukum']) || !isset($data['langkah_penyidikan']) || !isset($data['tingkat_urgensi'])) {
                                throw new \Exception('Format analisis tidak sesuai');
                                // lakukan analisis kembali
                                $analysis = $ollamaService->analyze($data);
                            }

                            // Log data untuk debugging
                            \Log::info('Data dari AI:', $data);

                            try {
                                // Validasi struktur data
                                if (!isset($data['tingkat_urgensi'])) {
                                    throw new \Exception('Data tingkat urgensi tidak ditemukan dalam response AI');
                                }

                                // Format data untuk disimpan
                                $kronologiAnalysis = [
                                    'ringkasan' => trim($data['ringkasan_kronologi'] ?? '')
                                ];

                                $possibleLaws = [
                                    'pidana_umum' => trim($data['analisis_hukum']['pidana_umum'] ?? ''),
                                    'teknologi_informasi' => trim($data['analisis_hukum']['teknologi_informasi'] ?? ''),
                                    'perundangan_lain' => trim($data['analisis_hukum']['perundangan_lain'] ?? '')
                                ];

                                $investigationSteps = [
                                    'barang_bukti_digital' => $data['langkah_penyidikan']['barang_bukti_digital'] ?? [],
                                    'analisis_forensik' => $data['langkah_penyidikan']['analisis_forensik'] ?? [],
                                    'penelusuran_pelaku' => $data['langkah_penyidikan']['penelusuran_pelaku'] ?? [],
                                    'tindakan_penyidikan' => $data['langkah_penyidikan']['tindakan_penyidikan'] ?? []
                                ];

                                // Hitung level prioritas dengan validasi
                                $priorityLevels = [
                                    $data['tingkat_urgensi']['dampak_kejadian']['level'] ?? 'Sedang',
                                    $data['tingkat_urgensi']['nilai_kerugian']['level'] ?? 'Sedang',
                                    $data['tingkat_urgensi']['tingkat_kompleksitas']['level'] ?? 'Sedang',
                                    $data['tingkat_urgensi']['potensi_dampak']['level'] ?? 'Sedang'
                                ];
                                
                                $levelCounts = array_count_values($priorityLevels);
                                arsort($levelCounts);
                                
                                $priorityData = [
                                    'calculated_level' => key($levelCounts),
                                    'level_counts' => $levelCounts,
                                    'urgensi' => [
                                        'dampak_kejadian' => $data['tingkat_urgensi']['dampak_kejadian'] ?? ['level' => 'Sedang', 'analisis' => ''],
                                        'nilai_kerugian' => $data['tingkat_urgensi']['nilai_kerugian'] ?? ['level' => 'Sedang', 'analisis' => ''],
                                        'tingkat_kompleksitas' => $data['tingkat_urgensi']['tingkat_kompleksitas'] ?? ['level' => 'Sedang', 'analisis' => ''],
                                        'potensi_dampak' => $data['tingkat_urgensi']['potensi_dampak'] ?? ['level' => 'Sedang', 'analisis' => '']
                                    ]
                                ];

                                // Persiapkan data yang akan disimpan
                                $dataToSave = [
                                    'laporan_id' => $record->id,
                                    'kronologi_analysis' => json_encode($kronologiAnalysis, JSON_PRETTY_PRINT),
                                    'possible_laws' => json_encode($possibleLaws, JSON_PRETTY_PRINT),
                                    'investigation_steps' => json_encode($investigationSteps, JSON_PRETTY_PRINT),
                                    'priority_level' => json_encode($priorityData, JSON_PRETTY_PRINT),
                                    'raw_response' => json_encode($data, JSON_PRETTY_PRINT)
                                ];

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
                                    ->body('Laporan berhasil dianalisis ulang')
                                    ->send();

                                return redirect()->to(
                                    LaporanInformasiResource::getUrl('view-analysis', ['record' => $record])
                                );

                            } catch (\Exception $e) {
                                \Log::error('Error saat menyimpan analisis:', [
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString(),
                                    'data' => $data ?? null
                                ]);
                                
                                throw new \Exception('Gagal menyimpan hasil analisis: ' . $e->getMessage());
                            }
                        } else {
                            throw new \Exception('Gagal mendapatkan analisis dari AI');

                            // lakukan analisis kembali
                            $analysis = $ollamaService->analyze($data);
                            
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal Menganalisis Ulang')
                            ->danger()
                            ->body($e->getMessage())
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Analisis Ulang Laporan')
                ->modalDescription('Apakah Anda yakin ingin menganalisis ulang laporan ini? Hasil analisis sebelumnya akan ditimpa.')
                ->modalSubmitActionLabel('Ya, Analisis Ulang')
                ->color('primary')
                ->icon('heroicon-o-cpu-chip'),
            Action::make('delete')
                ->label('Hapus Analisis AI')
                ->action(function () {
                    $this->record->analysis()->delete();
                    // redirect ke halaman list laporan
                    Notification::make()
                        ->title('Analisis AI Berhasil Dihapus')
                        ->success()
                        ->body('Analisis AI berhasil dihapus')
                        ->send();
                    return redirect()->to(LaporanInformasiResource::getUrl('index'));
                })
                ->requiresConfirmation()
                ->modalHeading('Hapus Analisis AI')
                ->modalDescription('Apakah Anda yakin ingin menghapus analisis AI ini?')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->color('danger')
                ->icon('heroicon-o-trash'),
        ];
    }

    // protected function getHeaderWidgets(): array
    // {
    //     return [];
    // }

    // protected function getFooterWidgets(): array
    // {
    //     return [];
    // }

}