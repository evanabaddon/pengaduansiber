<?php

namespace App\Filament\Resources\SuratResource\Pages;

use Filament\Actions;
use App\Helpers\TemplateResolver;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SuratResource;

class EditSurat extends EditRecord
{
    protected static string $resource = SuratResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function mutateFormDataBeforeSave(array $data): array
    {
        // pastikan panel & satker tetap ada (opsional)
        $data['panel'] = $data['panel'] ?? request()->segment(1);
        $data['satker'] = $data['satker'] ?? request('menu');
        
        // gunakan pilihan user jika ada
        if (!empty($data['template_version'])) {
            $data['template_path'] = "NASKAH DINAS/1. SURAT PERINTAH/" . $data['template_version'];
        } else {
            // fallback otomatis
            $data['template_path'] = TemplateResolver::resolve(
                $data['jenis_dokumen'],
                $data['kategori_surat'],
                $data['pejabat_penerbit']
            );
        }

        return $data;
    }
}
