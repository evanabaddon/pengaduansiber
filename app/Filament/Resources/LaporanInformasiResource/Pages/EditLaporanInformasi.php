<?php

namespace App\Filament\Resources\LaporanInformasiResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\LaporanInformasiResource;
use Illuminate\Database\Eloquent\Model;

class EditLaporanInformasi extends EditRecord
{
    
    protected static string $resource = LaporanInformasiResource::class;

    // redirect ke halaman list setelah edit
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Update data utama LaporanInformasi
        $record->update($data);

        // Update data Pelapor
        $pelapor = $record->pelapors;
        $pelapor->update([
            'identity_no' => $data['pelapors']['identity_no'],
            'usia' => $data['pelapors']['usia'],
            'nama' => $data['pelapors']['nama'],
            'tempat_lahir' => $data['pelapors']['tempat_lahir'],
            'tanggal_lahir' => $data['pelapors']['tanggal_lahir'],
            'jenis_kelamin' => $data['pelapors']['jenis_kelamin'],
            'agama' => $data['pelapors']['agama'],
            'kewarganegaraan' => $data['pelapors']['kewarganegaraan'],
            'pekerjaan' => $data['pelapors']['pekerjaan'],
            'kontak' => $data['pelapors']['kontak'],
            'kontak_2' => $data['pelapors']['kontak_2'],
            'province_id' => $data['pelapors']['province_id'] ?? null,
            'city_id' => $data['pelapors']['city_id'] ?? null,
            'district_id' => $data['pelapors']['district_id'] ?? null,
            'subdistrict_id' => $data['pelapors']['subdistrict_id'] ?? null,
            'alamat' => $data['pelapors']['alamat'],
            'alamat_2' => $data['pelapors']['alamat_2'] ?? null,
            'province_id_2' => $data['pelapors']['province_id_2'] ?? null,
            'city_id_2' => $data['pelapors']['city_id_2'] ?? null,
            'district_id_2' => $data['pelapors']['district_id_2'] ?? null,
            'subdistrict_id_2' => $data['pelapors']['subdistrict_id_2'] ?? null,
        ]);

        // Update data tambahan Pelapor
        if (!empty($data['pelapors']['data_tambahan'])) {
            foreach ($data['pelapors']['data_tambahan'] as $dataTambahan) {
                if (isset($dataTambahan['created_at'])) {
                    $dataTambahan['created_at'] = \Carbon\Carbon::parse($dataTambahan['created_at'])->format('Y-m-d H:i:s');
                }
                if (isset($dataTambahan['updated_at'])) {
                    $dataTambahan['updated_at'] = \Carbon\Carbon::parse($dataTambahan['updated_at'])->format('Y-m-d H:i:s');
                }
                \App\Models\DataTambahan::updateOrCreate(
                    [
                        'id' => $dataTambahan['id'] ?? null,
                        'recordable_id' => $pelapor->id,
                        'recordable_type' => 'App\\Models\\Pelapor'
                    ],
                    $dataTambahan
                );
            }
        }

        // Update data Korban
        if (!empty($data['korbans'])) {
            // Kumpulkan ID korban yang ada di form
            $existingKorbanIds = collect($data['korbans'])
                ->pluck('id')
                ->filter()
                ->toArray();

            // Hapus korban yang tidak ada di form
            $record->korbans()
                ->whereNotIn('id', $existingKorbanIds)
                ->delete();

            // Update atau buat korban baru
            foreach ($data['korbans'] as $korbanData) {
                $korban = $record->korbans()->updateOrCreate(
                    [
                        'id' => $korbanData['id'] ?? null,
                    ],
                    [
                        'identity_no' => $korbanData['korbans']['identity_no'],
                        'nama' => $korbanData['korbans']['nama'],
                        'kontak' => $korbanData['korbans']['kontak'],
                        'usia' => $korbanData['korbans']['usia'],
                        'tempat_lahir' => $korbanData['korbans']['tempat_lahir'],
                        'tanggal_lahir' => $korbanData['korbans']['tanggal_lahir'],
                        'jenis_kelamin' => $korbanData['korbans']['jenis_kelamin'],
                        'agama' => $korbanData['korbans']['agama'],
                        'kewarganegaraan' => $korbanData['korbans']['kewarganegaraan'],
                        'pekerjaan' => $korbanData['korbans']['pekerjaan'],
                        'kontak_2' => $korbanData['korbans']['kontak_2'] ?? null,
                        'province_id' => $korbanData['korbans']['province_id'] ?? null,
                        'city_id' => $korbanData['korbans']['city_id'] ?? null,
                        'district_id' => $korbanData['korbans']['district_id'] ?? null,
                        'subdistrict_id' => $korbanData['korbans']['subdistrict_id'] ?? null,
                        'alamat' => $korbanData['korbans']['alamat'],
                        'alamat_2' => $korbanData['korbans']['alamat_2'] ?? null,
                        'province_id_2' => $korbanData['korbans']['province_id_2'] ?? null,
                        'city_id_2' => $korbanData['korbans']['city_id_2'] ?? null,
                        'district_id_2' => $korbanData['korbans']['district_id_2'] ?? null,
                        'subdistrict_id_2' => $korbanData['korbans']['subdistrict_id_2'] ?? null,
                    ]
                );

                // Update data tambahan Korban
                if (!empty($korbanData['data_tambahan'])) {
                    // Hapus data tambahan yang lama
                    \App\Models\DataTambahan::where('recordable_id', $korban->id)
                        ->where('recordable_type', 'App\\Models\\Korban')
                        ->delete();

                    foreach ($korbanData['data_tambahan'] as $dataTambahan) {
                        if (isset($dataTambahan['created_at'])) {
                            $dataTambahan['created_at'] = \Carbon\Carbon::parse($dataTambahan['created_at'])->format('Y-m-d H:i:s');
                        }
                        if (isset($dataTambahan['updated_at'])) {
                            $dataTambahan['updated_at'] = \Carbon\Carbon::parse($dataTambahan['updated_at'])->format('Y-m-d H:i:s');
                        }
                        \App\Models\DataTambahan::create([
                            'recordable_id' => $korban->id,
                            'recordable_type' => 'App\\Models\\Korban',
                            ...$dataTambahan
                        ]);
                    }
                }
            }
        } else {
            // Jika tidak ada korban di form, hapus semua korban
            $record->korbans()->delete();
        }

        // Update data Terlapor
        $terlapor = $record->terlapors;
        $terlapor->update([
            'identity_no' => $data['terlapors']['identity_no'],
            'nama' => $data['terlapors']['nama'],
            'kontak' => $data['terlapors']['kontak'],
            'kontak_2' => $data['terlapors']['kontak_2'] ?? null,
            'jenis_kelamin' => $data['terlapors']['jenis_kelamin'],
            'tempat_lahir' => $data['terlapors']['tempat_lahir'],
            'tanggal_lahir' => $data['terlapors']['tanggal_lahir'],
            'usia' => $data['terlapors']['usia'],
            'agama' => $data['terlapors']['agama'],
            'kewarganegaraan' => $data['terlapors']['kewarganegaraan'],
            'pekerjaan' => $data['terlapors']['pekerjaan'],
            'data_tambahan' => $data['terlapors']['data_tambahan'] ?? null,
            'province_id' => $data['terlapors']['province_id'] ?? null,
            'city_id' => $data['terlapors']['city_id'] ?? null,
            'district_id' => $data['terlapors']['district_id'] ?? null,
            'subdistrict_id' => $data['terlapors']['subdistrict_id'] ?? null,
            'alamat' => $data['terlapors']['alamat'],
            'alamat_2' => $data['terlapors']['alamat_2'] ?? null,
            'province_id_2' => $data['terlapors']['province_id_2'] ?? null,
            'city_id_2' => $data['terlapors']['city_id_2'] ?? null,
            'district_id_2' => $data['terlapors']['district_id_2'] ?? null,
            'subdistrict_id_2' => $data['terlapors']['subdistrict_id_2'] ?? null,
        ]);

        // Update data tambahan Terlapor
        if (!empty($data['terlapors']['data_tambahan'])) {
            foreach ($data['terlapors']['data_tambahan'] as $dataTambahan) {
                if (isset($dataTambahan['created_at'])) {
                    $dataTambahan['created_at'] = \Carbon\Carbon::parse($dataTambahan['created_at'])->format('Y-m-d H:i:s');
                }
                if (isset($dataTambahan['updated_at'])) {
                    $dataTambahan['updated_at'] = \Carbon\Carbon::parse($dataTambahan['updated_at'])->format('Y-m-d H:i:s');
                }
                \App\Models\DataTambahan::updateOrCreate(
                    [
                        'id' => $dataTambahan['id'] ?? null,
                        'recordable_id' => $terlapor->id,
                        'recordable_type' => 'App\\Models\\Terlapor'
                    ],
                    $dataTambahan
                );
            }
        }

        return $record;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load ulang record dengan relasi
        $this->record = $this->record->fresh(['pelapors', 'korbans', 'terlapors']);

        if ($this->record->pelapors) {
            $pelapor = $this->record->pelapors;
            $data['pelapors'] = $pelapor->attributesToArray();
            $dataTambahanPelapor = \App\Models\DataTambahan::where('recordable_type', 'App\\Models\\Pelapor')
                ->where('recordable_id', $pelapor->getKey())
                ->get();
            $data['pelapors']['data_tambahan'] = $dataTambahanPelapor->toArray();
        }

        // Modifikasi bagian korban untuk mendukung multiple records
        if ($this->record->korbans) {
            $data['korbans'] = $this->record->korbans->map(function($korban) {
                return [
                    'korbans' => [
                        'nama' => $korban->nama,
                        'identity_no' => $korban->identity_no,
                        'kontak' => $korban->kontak,
                        'kontak_2' => $korban->kontak_2,
                        'kewarganegaraan' => $korban->kewarganegaraan,
                        'tempat_lahir' => $korban->tempat_lahir,
                        'tanggal_lahir' => $korban->tanggal_lahir,
                        'jenis_kelamin' => $korban->jenis_kelamin,
                        'pekerjaan' => $korban->pekerjaan,
                        'usia' => $korban->usia,
                        'agama' => $korban->agama,
                        'alamat' => $korban->alamat,
                        'province_id' => $korban->province_id,
                        'city_id' => $korban->city_id,
                        'district_id' => $korban->district_id,
                        'subdistrict_id' => $korban->subdistrict_id,
                        'alamat_2' => $korban->alamat_2,
                        'province_id_2' => $korban->province_id_2,
                        'city_id_2' => $korban->city_id_2,
                        'district_id_2' => $korban->district_id_2,
                        'subdistrict_id_2' => $korban->subdistrict_id_2,
                        'domestic' => $korban->domestic,
                    ],
                    'data_tambahan' => \App\Models\DataTambahan::where('recordable_type', 'App\\Models\\Korban')
                        ->where('recordable_id', $korban->getKey())
                        ->get()
                        ->toArray()
                ];
            })->values()->toArray();
        }

        if ($this->record->terlapors) {
            $terlapor = $this->record->terlapors;
            $data['terlapors'] = $terlapor->attributesToArray();
            $dataTambahanTerlapor = \App\Models\DataTambahan::where('recordable_type', 'App\\Models\\Terlapor')
                ->where('recordable_id', $terlapor->getKey())
                ->get();
            $data['terlapors']['data_tambahan'] = $dataTambahanTerlapor->toArray();
        }

        // Debug final data structure
        // dd([
        //     'Final data structure:' => $data,
        //     'Korbans data:' => $data['korbans'] ?? null,
        // ]);

        return $data;
    }

}
