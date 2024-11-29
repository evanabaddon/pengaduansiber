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
        // dd($data);
        // Update data utama LaporanInformasi
        $record->update($data);

        // Update data Pelapor
        $record->pelapors()->update([
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

        // Update data Korban
        $record->korbans()->update([
            'identity_no' => $data['korbans']['identity_no'],
            'nama' => $data['korbans']['nama'],
            'kontak' => $data['korbans']['kontak'],
            'usia' => $data['korbans']['usia'],
            'tempat_lahir' => $data['korbans']['tempat_lahir'],
            'tanggal_lahir' => $data['korbans']['tanggal_lahir'],
            'jenis_kelamin' => $data['korbans']['jenis_kelamin'],
            'agama' => $data['korbans']['agama'],
            'kewarganegaraan' => $data['korbans']['kewarganegaraan'],
            'pekerjaan' => $data['korbans']['pekerjaan'],
            'kontak_2' => $data['korbans']['kontak_2'],
            'province_id' => $data['korbans']['province_id'] ?? null,
            'city_id' => $data['korbans']['city_id'] ?? null,
            'district_id' => $data['korbans']['district_id'] ?? null,
            'subdistrict_id' => $data['korbans']['subdistrict_id'] ?? null,
            'alamat' => $data['korbans']['alamat'],
            'alamat_2' => $data['korbans']['alamat_2'] ?? null,
            'province_id_2' => $data['korbans']['province_id_2'] ?? null,
            'city_id_2' => $data['korbans']['city_id_2'] ?? null,
            'district_id_2' => $data['korbans']['district_id_2'] ?? null,
            'subdistrict_id_2' => $data['korbans']['subdistrict_id_2'] ?? null,
            
        ]);

        // Update data Terlapor
        $record->terlapors()->update([
            'identity_no' => $data['terlapors']['identity_no'],
            'nama' => $data['terlapors']['nama'],
            'kontak' => $data['terlapors']['kontak'],
            'kontak_2' => $data['terlapors']['kontak_2'] ?? null,
            'jenis_kelamin' => $data['terlapors']['jenis_kelamin'],
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

        return $record;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load ulang record dengan relationships
        $this->record = $this->record->fresh(['pelapors', 'korbans', 'terlapors']);
        
        // Debug untuk melihat data
        // dd([
        //     'record' => $this->record->toArray(),
        //     'pelapors' => $this->record->pelapors,
        //     'korbans' => $this->record->korbans,
        //     'terlapors' => $this->record->terlapors,
        // ]);

        if ($this->record->pelapors) {
            $data['pelapors'] = $this->record->pelapors->toArray();
        }
        if ($this->record->korbans) {
            $data['korbans'] = $this->record->korbans->toArray();
        }
        if ($this->record->terlapors) {
            $data['terlapors'] = $this->record->terlapors->toArray();
        }

        return $data;
    }
}