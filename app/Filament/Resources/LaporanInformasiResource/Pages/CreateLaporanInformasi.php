<?php

namespace App\Filament\Resources\LaporanInformasiResource\Pages;

use App\Models\FormDraft;
use Filament\Actions;
use App\Models\Korban;
use App\Models\Pelapor;
use App\Models\Terlapor;
use App\Models\LaporanInformasi;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\LaporanInformasiResource;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Actions\Action;

class CreateLaporanInformasi extends CreateRecord
{
    protected static string $resource = LaporanInformasiResource::class;
    protected static bool $canCreateAnother = false;
    protected ?FormDraft $currentDraft = null;
    public $currentStep = 1;
    
    protected $listeners = ['autoSaveDraft' => 'autoSaveDraft'];

    public function autoSaveDraft(): void
    {
        try {
            if (!$this->form) {
                return;
            }

            $state = $this->form->getRawState();
            
            // Memory-efficient data filtering
            $filteredData = [];
            
            // Process main data
            if ($mainData = $this->filterEmptyValues($state, ['pelapors', 'korbans', 'terlapors'])) {
                $filteredData['main_data'] = $mainData;
            }
            
            // Process related data separately
            if (!empty($state['pelapors'])) {
                $filteredData['pelapor_data'] = $this->filterEmptyValues($state['pelapors']);
            }
            
            if (!empty($state['korbans'])) {
                $filteredData['korban_data'] = $this->filterEmptyValues($state['korbans']);
            }
            
            if (!empty($state['terlapors'])) {
                $filteredData['terlapor_data'] = $this->filterEmptyValues($state['terlapors']);
            }

            // Skip if no data to save
            if (empty($filteredData)) {
                return;
            }

            // Add required fields
            $filteredData['user_id'] = auth()->id();
            $filteredData['form_type'] = 'laporan_informasi';
            $filteredData['current_step'] = $this->getActiveStep();

            // Efficient update/create
            $this->currentDraft = FormDraft::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'form_type' => 'laporan_informasi'
                ],
                $filteredData
            );

        } catch (\Exception $e) {
            \Log::error('Auto-save failed: ' . $e->getMessage());
        }
    }

    protected function filterEmptyValues(array $data, array $excludeKeys = []): array
    {
        // More memory efficient filtering
        $filtered = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $excludeKeys)) {
                continue;
            }
            if ($value !== null && $value !== '') {
                $filtered[$key] = $value;
            }
        }
        return $filtered;
    }

    public function mount(): void
    {
        // Disable any unnecessary parent initialization
        $this->loadExistingDraft();
        $this->dispatch('init-auto-save', interval: 60000);
    }

    protected function loadExistingDraft(): void
    {
        try {
            // Only load essential fields initially
            $draft = FormDraft::select('id', 'current_step')
                ->where('user_id', auth()->id())
                ->where('form_type', 'laporan_informasi')
                ->first();

            if (!$draft) {
                return;
            }

            $this->currentDraft = $draft;
            $this->currentStep = $draft->current_step;

            // Load data in chunks
            $formData = [];
            
            // Load main data separately
            $mainData = FormDraft::select('main_data')
                ->where('id', $draft->id)
                ->value('main_data');
            if ($mainData) {
                $formData = $mainData;
            }

            // Load related data separately with minimal memory usage
            $relatedData = FormDraft::select('pelapor_data', 'korban_data', 'terlapor_data')
                ->where('id', $draft->id)
                ->first();

            if ($relatedData) {
                if (!empty($relatedData->pelapor_data)) {
                    $formData['pelapors'] = $relatedData->pelapor_data;
                }
                
                if (!empty($relatedData->korban_data)) {
                    // Handle korban data more efficiently
                    $formData['korbans'] = is_array($relatedData->korban_data) 
                        ? array_values($relatedData->korban_data)
                        : [];
                }
                
                if (!empty($relatedData->terlapor_data)) {
                    $formData['terlapors'] = $relatedData->terlapor_data;
                }
            }

            // Fill form in chunks if data is large
            if (count($formData) > 1000) {
                collect($formData)->chunk(500)->each(function ($chunk) {
                    $this->form->fill($chunk->toArray());
                });
            } else {
                $this->form->fill($formData);
            }

            // Minimize logging
            \Log::info('Draft loaded', ['id' => $draft->id]);
            
        } catch (\Exception $e) {
            \Log::error('Draft load failed', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
        }
    }

    protected function getActiveStep(): int
    {
        return $this->currentStep ?? 1;
    }

    public function handleRecordCreation(array $data): LaporanInformasi
    {
        try {
            // Simpan data utama
            $data['status'] = 'Proses';

            if (auth()->user()->subdit_id) {
                $data['subdit_id'] = auth()->user()->subdit_id;
            }
            if (auth()->user()->unit_id) {
                $data['unit_id'] = auth()->user()->unit_id;
            }

            $laporanInformasi = LaporanInformasi::create($data);

            // Simpan Pelapor dengan data tambahan
            if (isset($data['pelapors'])) {
                $pelaporData = collect($data['pelapors'])->except('data_tambahan')->toArray();
                $pelapor = Pelapor::create([
                    'laporan_informasi_id' => $laporanInformasi->id,
                    ...$pelaporData
                ]);
                
                if (!empty($data['pelapors']['data_tambahan'])) {
                    foreach ($data['pelapors']['data_tambahan'] as $dataTambahan) {
                        $pelapor->dataTambahan()->create($dataTambahan);
                    }
                }
            }

            // Simpan Korban dengan data tambahan
            // if (isset($data['korbans'])) {
            //     $korbanData = collect($data['korbans'])->except('data_tambahan')->toArray();
            //     $korban = Korban::create([
            //         'laporan_informasi_id' => $laporanInformasi->id,
            //         ...$korbanData
            //     ]);
                
            //     if (!empty($data['korbans']['data_tambahan'])) {
            //         foreach ($data['korbans']['data_tambahan'] as $dataTambahan) {
            //             $korban->dataTambahan()->create($dataTambahan);
            //         }
            //     }
            // }

            // dd($data['korbans']);

            // Simpan multiple Korban dengan data tambahan
            if (isset($data['korbans']) && is_array($data['korbans'])) {
                foreach ($data['korbans'] as $korbanItem) {
                    // Ambil data korban yang benar dari struktur nested
                    $korbanData = collect($korbanItem['korbans'])->except('data_tambahan')->toArray();
                    
                    $korban = Korban::create([
                        'laporan_informasi_id' => $laporanInformasi->id,
                        ...$korbanData
                    ]);
                    
                    // Cek data_tambahan dari struktur yang benar
                    if (!empty($korbanItem['korbans']['data_tambahan'])) {
                        foreach ($korbanItem['korbans']['data_tambahan'] as $dataTambahan) {
                            $korban->dataTambahan()->create($dataTambahan);
                        }
                    }
                }
            }

            // Simpan Terlapor dengan data tambahan
            if (isset($data['terlapors'])) {
                $data['terlapors']['nama'] = $data['terlapors']['nama'] ?: 'null';
                $terlaporData = collect($data['terlapors'])->except('data_tambahan')->toArray();
                $terlapor = Terlapor::create([
                    'laporan_informasi_id' => $laporanInformasi->id,
                    ...$terlaporData
                ]);
                
                if (!empty($data['terlapors']['data_tambahan'])) {
                    foreach ($data['terlapors']['data_tambahan'] as $dataTambahan) {
                        $terlapor->dataTambahan()->create($dataTambahan);
                    }
                }
            }

            // Hapus draft setelah berhasil menyimpan
            FormDraft::where('user_id', auth()->id())
                ->where('form_type', 'laporan_informasi')
                ->delete();
            
            // Kirim event untuk membersihkan storage
            $this->dispatch('clear-draft-storage');

            Notification::make()
                ->success()
                ->title('Data berhasil disimpan')
                ->duration(3000)
                ->send();

            return $laporanInformasi;

        } catch (\Exception $e) {
            Log::error('Error creating record: ' . $e->getMessage());
            
            Notification::make()
                ->danger()
                ->title('Gagal menyimpan data')
                ->body($e->getMessage())
                ->duration(5000)
                ->send();
            
            throw $e;
        }
    }
}
