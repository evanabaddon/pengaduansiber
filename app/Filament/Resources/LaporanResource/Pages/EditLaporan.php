<?php

namespace App\Filament\Resources\LaporanResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\LaporanResource;

class EditLaporan extends EditRecord
{
    
    protected static string $resource = LaporanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Update data utama Laporan
        $record->update($data);
        
        // Update data Pelapor
        $record->pelapors()->update([
            'identity_no' => $data['pelapors']['identity_no'],
            'usia' => $data['pelapors']['usia'],
            'nama' => $data['pelapors']['nama'],
            'tempat_lahir' => $data['pelapors']['tempat_lahir'],
            'tanggal_lahir' => $data['pelapors']['tanggal_lahir'],
            'jenis_kelamin' => $data['pelapors']['jenis_kelamin'],
            'pekerjaan' => $data['pelapors']['pekerjaan'],
            'kontak' => $data['pelapors']['kontak'],
            'domestic' => $data['pelapors']['domestic'],
            'province_id' => $data['pelapors']['province_id'] ?? null,
            'city_id' => $data['pelapors']['city_id'] ?? null,
            'district_id' => $data['pelapors']['district_id'] ?? null,
            'subdistrict_id' => $data['pelapors']['subdistrict_id'] ?? null,
            'alamat' => $data['pelapors']['alamat'],
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
            'pekerjaan' => $data['korbans']['pekerjaan'],
            'domestic' => $data['korbans']['domestic'],
            'province_id' => $data['korbans']['province_id'] ?? null,
            'city_id' => $data['korbans']['city_id'] ?? null,
            'district_id' => $data['korbans']['district_id'] ?? null,
            'subdistrict_id' => $data['korbans']['subdistrict_id'] ?? null,
            'alamat' => $data['korbans']['alamat'],
        ]);

        // Update data Terlapor
        $record->terlapors()->update([
            'identity_no' => $data['terlapors']['identity_no'],
            'nama' => $data['terlapors']['nama'],
            'kontak' => $data['terlapors']['kontak'],
            'jenis_kelamin' => $data['terlapors']['jenis_kelamin'],
            'usia' => $data['terlapors']['usia'],
            'domestic' => $data['terlapors']['domestic'],
            'province_id' => $data['terlapors']['province_id'] ?? null,
            'city_id' => $data['terlapors']['city_id'] ?? null,
            'district_id' => $data['terlapors']['district_id'] ?? null,
            'subdistrict_id' => $data['terlapors']['subdistrict_id'] ?? null,
            'alamat' => $data['terlapors']['alamat'],
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
