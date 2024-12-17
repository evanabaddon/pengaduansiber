<?php

namespace App\Filament\Resources\PengaduanResource\Pages;

use Filament\Actions;
use App\Models\Korban;
use App\Models\Pelapor;
use App\Models\Terlapor;
use App\Models\FormDraft;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PengaduanResource;

class CreatePengaduan extends CreateRecord
{
    protected static string $resource = PengaduanResource::class;

    protected static bool $canCreateAnother = false;
    protected ?FormDraft $currentDraft = null;
    public $currentStep = 1;

    public function mount(): void
    {
        parent::mount();
        $this->loadExistingDraft();
    }

    protected function loadExistingDraft(): void
    {
        try {
            $draft = FormDraft::where('user_id', auth()->id())
                ->where('form_type', 'pengaduan')
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
                            'jumlah' => is_numeric($item['jumlah']) ? (int)$item['jumlah'] : 1,
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

    public function handleRecordCreation(array $data): Pengaduan
    {
        try {
            $data['status'] = 'Proses';

            if (auth()->user()->subdit_id) {
                $data['subdit_id'] = auth()->user()->subdit_id;
            }
            if (auth()->user()->unit_id) {
                $data['unit_id'] = auth()->user()->unit_id;
            }

            $pengaduan = Pengaduan::create($data);

            // Simpan Pelapor dengan data tambahan
            if (isset($data['pelapors'])) {
                $pelaporData = collect($data['pelapors'])->except('data_tambahan')->toArray();
                $pelapor = Pelapor::create([
                    'pengaduan_id' => $pengaduan->id,
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
                        'pengaduan_id' => $pengaduan->id,
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
                    'pengaduan_id' => $pengaduan->id,
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
                $pengaduan->barangBuktis()->createMany($data['barangBuktis']);
            }

            // Hapus draft setelah berhasil menyimpan
            FormDraft::where('user_id', auth()->id())
                ->where('form_type', 'pengaduan')
                ->delete();

            // Kirim event untuk membersihkan storage
            $this->dispatch('clear-draft-storage');

            return $pengaduan;
        } catch (\Exception $e) {
            \Log::error('Draft load error: ' . $e->getMessage());

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

