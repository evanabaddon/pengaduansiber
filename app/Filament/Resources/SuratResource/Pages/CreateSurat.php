<?php

namespace App\Filament\Resources\SuratResource\Pages;

use Filament\Actions;
use App\Helpers\TemplateResolver;
use App\Filament\Resources\SuratResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSurat extends CreateRecord
{
    protected static string $resource = SuratResource::class;

    // public function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $data['template_path'] = TemplateResolver::resolve(
    //         $data['jenis_dokumen'],
    //         $data['kategori_surat'],
    //         $data['pejabat_penerbit']
    //     );
    
    //     return $data;
    // }
    public function mutateFormDataBeforeCreate(array $data): array
    {
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
