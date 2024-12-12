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

    // tambahkan tombol untuk clear history form draft
    protected function getActions(): array
    {
        return [
            Actions\Action::make('clearHistory')
                ->label('Bersihkan Draft')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->action(function() {
                    FormDraft::where('user_id', auth()->id())->delete();
                    redirect(request()->header('Referer'));
                })
        ];
    }

    // hidden save button
    protected function getFormActions(): array
    {
        return [
            //$this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function filterEmptyValues(array $data): array
    {
        return array_filter($data, function ($value) {
            if (is_array($value)) {
                return !empty(array_filter($value));
            }
            return $value !== null && $value !== '';
        });
    }

    public function mount(): void
    {
        parent::mount();
        
        try {
            $this->loadExistingDraft();
            \Log::info('Form mounted successfully');
        } catch (\Exception $e) {
            \Log::error('Mount failed: ' . $e->getMessage());
        }
    }

    
    protected function loadExistingDraft(): void
    {
        try {
            $draft = FormDraft::where('user_id', auth()->id())
                ->where('form_type', 'laporan_informasi')
                ->first();

            if (!$draft) {
                return;
            }

            $this->currentDraft = $draft;
            $this->currentStep = $draft->current_step;
            
            $formData = [];
            
            // Load main data - decode JSON string to array first
            if ($draft->main_data) {
                $mainData = is_string($draft->main_data) ? json_decode($draft->main_data, true) : $draft->main_data;
                
                // Handle barangBuktis specifically if it exists in main data
                if (isset($mainData['barangBuktis'])) {
                    $formData['barangBuktis'] = array_map(function($item) {
                        return [
                            'jumlah' => $item['jumlah'] ?? null,
                            'satuan' => $item['satuan'] ?? null,
                            'nama_barang' => $item['nama_barang'] ?? null,
                        ];
                    }, $mainData['barangBuktis']);
                }
                
                $formData = array_merge($formData, $mainData ?? []);
            }
            
            // Load pelapor data
            if ($draft->pelapor_data) {
                $formData['pelapors'] = json_decode($draft->pelapor_data, true);
            }
            
            // Load korban data
            if ($draft->korban_data) {
                $formData['korbans'] = json_decode($draft->korban_data, true);
            }
            
            // Load terlapor data
            if ($draft->terlapor_data) {
                $formData['terlapors'] = json_decode($draft->terlapor_data, true);
            }
            
            $this->form->fill($formData);

        } catch (\Exception $e) {
            \Log::error('Draft load error: ' . $e->getMessage());
        }
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

            // simpan barang bukti, saya menggunakan morphMany
            if (isset($data['barangBuktis'])) {
                $laporanInformasi->barangBuktis()->createMany($data['barangBuktis']);
            }

            // Hapus draft setelah berhasil menyimpan
            FormDraft::where('user_id', auth()->id())
                ->where('form_type', 'laporan_informasi')
                ->delete();
            
            // Kirim event untuk membersihkan storage
            $this->dispatch('clear-draft-storage');

            // Notification::make()
            //     ->success()
            //     ->title('Data berhasil disimpan')
            //     ->duration(3000)
            //     ->send();

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

    protected function configureNavigationActions(): array
    {
        return [
            'nextAction' => fn() => $this->nextStepAndSaveDraft(),
            'previousAction' => fn() => $this->previousStep(),
        ];
    }

    protected function nextStepAndSaveDraft(): void 
    {
        try {
            // Save draft first
            $this->saveDraft();
            
            // Then proceed to next step
            $this->nextStep();
            
        } catch (\Exception $e) {
            \Log::error('Failed to save draft while moving to next step: ' . $e->getMessage());
            
            $this->dispatch('draft-save-failed', [
                'message' => 'Gagal menyimpan progress: ' . $e->getMessage()
            ]);
        }
    }

    protected function saveDraft(): void
    {
        try {
            if (!$this->form) {
                return;
            }

            $state = $this->form->getRawState();
            if (empty($state)) {
                return;
            }

            // Initialize filtered data array
            $filteredData = [
                'user_id' => auth()->id(),
                'form_type' => 'laporan_informasi',
                'current_step' => $this->getActiveStep()
            ];

            // Process main data
            $mainFields = array_diff_key($state, array_flip(['pelapors', 'korbans', 'terlapors']));
            if (!empty($mainFields)) {
                $filteredData['main_data'] = array_filter($mainFields, function ($value) {
                    return $value !== null && $value !== '';
                });
            }

            // Process related data efficiently
            foreach (['pelapors', 'korbans', 'terlapors'] as $relation) {
                if (!empty($state[$relation])) {
                    $filteredData["{$relation}_data"] = array_filter($state[$relation], function ($value) {
                        return $value !== null && $value !== '';
                    });
                }
            }

            // Skip if no data to save
            if (count($filteredData) <= 3) { // Only has default fields
                return;
            }

            // Save draft
            $this->currentDraft = FormDraft::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'form_type' => 'laporan_informasi'
                ],
                $filteredData
            );

            \Log::info('Draft saved on step change', [
                'draft_id' => $this->currentDraft->id,
                'step' => $this->getActiveStep()
            ]);

            $this->dispatch('draft-saved', [
                'message' => 'Progress berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to save draft: ' . $e->getMessage());
            throw $e; // Re-throw to be handled by parent
        }
    }
}
