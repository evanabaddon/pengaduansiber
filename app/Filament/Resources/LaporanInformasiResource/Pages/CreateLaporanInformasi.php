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
                \Log::warning('Form belum dimount, skip auto save');
                return;
            }

            // Ambil state form tanpa validasi
            $state = $this->form->getRawState();
            
            \Log::info('Auto save triggered', ['state' => $state]);
            
            // Filter data yang kosong
            $filteredMainData = collect($state)
                ->except(['pelapors', 'korbans', 'terlapors'])
                ->filter(function ($value) {
                    return $value !== null && $value !== '';
                })
                ->toArray();
                
            // Filter data pelapor yang tidak kosong
            $pelaporData = collect($state['pelapors'] ?? [])
                ->filter(function ($value) {
                    return $value !== null && $value !== '';
                })
                ->toArray();
                
            // Filter data korban yang tidak kosong
            $korbanData = collect($state['korbans'] ?? [])
                ->filter(function ($value) {
                    return $value !== null && $value !== '';
                })
                ->toArray();
                
            // Filter data terlapor yang tidak kosong
            $terlaporData = collect($state['terlapors'] ?? [])
                ->filter(function ($value) {
                    return $value !== null && $value !== '';
                })
                ->toArray();

            // Jika semua data kosong, skip penyimpanan
            if (empty($filteredMainData) && empty($pelaporData) && empty($korbanData) && empty($terlaporData)) {
                \Log::info('Semua data kosong, skip auto save');
                return;
            }

            // Gabungkan dengan data draft yang sudah ada
            $existingDraft = FormDraft::where('user_id', auth()->id())
                ->where('form_type', 'laporan_informasi')
                ->first();

            $draftData = [
                'user_id' => auth()->id(),
                'form_type' => 'laporan_informasi',
                'main_data' => $existingDraft 
                    ? array_merge($existingDraft->main_data ?? [], $filteredMainData)
                    : $filteredMainData,
                'pelapor_data' => $existingDraft 
                    ? array_merge($existingDraft->pelapor_data ?? [], $pelaporData)
                    : $pelaporData,
                'korban_data' => $existingDraft 
                    ? array_merge($existingDraft->korban_data ?? [], $korbanData)
                    : $korbanData,
                'terlapor_data' => $existingDraft 
                    ? array_merge($existingDraft->terlapor_data ?? [], $terlaporData)
                    : $terlaporData,
                'current_step' => $this->getActiveStep()
            ];

            \Log::info('Saving draft data', ['draft' => $draftData]);

            $this->currentDraft = FormDraft::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'form_type' => 'laporan_informasi'
                ],
                $draftData
            );

            // Notification::make()
            //     ->success()
            //     ->title('Draft tersimpan ' . now()->format('H:i:s'))
            //     ->duration(3000)
            //     ->send();
            
            \Log::info('Draft saved successfully', ['draft_id' => $this->currentDraft->id]);
        } catch (\Exception $e) {
            \Log::error('Error saving draft: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            Notification::make()
                ->danger()
                ->title('Gagal menyimpan draft')
                ->body($e->getMessage())
                ->duration(5000)
                ->send();
        }
    }

    public function mount(): void
    {
        parent::mount();
        $this->loadExistingDraft();
        
        // \Log::info('Component mounted, initializing auto-save');÷
        \Log::info('Component mounted, auto-save disabled');
    
    // Commented out auto-save initialization
        $this->dispatch('init-auto-save', interval: 10000);
    }

    protected function loadExistingDraft(): void
    {
        try {
            $draft = FormDraft::where('user_id', auth()->id())
                ->where('form_type', 'laporan_informasi')
                ->first();

            if ($draft) {
                $this->currentDraft = $draft;
                $this->currentStep = $draft->current_step;
                
                // Pastikan data korban adalah array yang sesuai untuk repeater
                $korbanData = is_array($draft->korban_data) 
                    ? array_values($draft->korban_data)  // Jika array, pastikan indeks berurutan
                    : [];                                // Jika bukan array, gunakan array kosong
                
                $formData = [
                    ...$draft->main_data ?? [],
                    'pelapors' => $draft->pelapor_data ?? [],
                    'korbans' => $korbanData,  // Data untuk repeater
                    'terlapors' => $draft->terlapor_data ?? [],
                ];
                
                $this->form->fill($formData);
                
                $this->notify('success', 'Draft terakhir berhasil dimuat');
            }
        } catch (\Exception $e) {
            Log::error('Error loading draft: ' . $e->getMessage());
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
