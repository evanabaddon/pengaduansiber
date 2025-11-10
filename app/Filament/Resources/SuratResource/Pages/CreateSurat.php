<?php

namespace App\Filament\Resources\SuratResource\Pages;

use Filament\Actions;
use App\Helpers\TemplateResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Filament\Resources\SuratResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSurat extends CreateRecord
{
    protected static string $resource = SuratResource::class;

    protected static ?string $title = 'Buat Naskah Dinas';

    protected function getRedirectUrl(): string
    {
        if ($this->record) {
            return route('onlyoffice.loading', ['id' => $this->record->id]);
        }

        return $this->previousUrl ?? static::getResource()::getUrl('index');
    }

    public function mount(): void
    {
        parent::mount();

        // Ambil parameter dari URL
        $jenisDokumen = request('jenis_dokumen');
        $menu = request('menu');
        $panel = request()->segment(1);

        // Pastikan panel & satker selalu terisi
        $this->form->fill([
            'panel' => request()->segment(1) ?? null,
            'satker' => request('menu') ?? null,
            'jenis_dokumen' => $jenisDokumen ?? null,
        ]);

        // Jika ada jenis_dokumen dari URL, set template otomatis
        if ($jenisDokumen) {
            $this->data['jenis_dokumen'] = $jenisDokumen;
        }
    }

    public function mutateFormDataBeforeCreate(array $data): array
    {
         // pastikan selalu ada panel & satker
        $data['panel'] = $data['panel'] ?? request('panel');
        $data['satker'] = $data['satker'] ?? request('menu');
        $data['jenis_dokumen'] = $data['jenis_dokumen'] ?? request('jenis_dokumen');

        // dd($data);

        // gunakan pilihan user jika ada
        if (!empty($data['template_version'])) {
            $data['template_path'] = "NASKAH DINAS/1. SURAT PERINTAH/" . $data['template_version'];
        } else {
            // fallback otomatis
            $data['template_path'] = TemplateResolver::resolve(
                $data['jenis_dokumen'] ?? 'naskah_dinas', // Default ke naskah_dinas
                $data['kategori_surat'],
                $data['pejabat_penerbit']
            );
        }

        /**
         * 💾 Buat nama file otomatis berdasarkan nama dokumen user
         * contoh: "Surat_Penugasan_1731246072.docx"
         */
        $baseName = str_replace(' ', '_', $data['nama_dokumen']);
        $timestamp = now()->format('Ymd_His');
        $data['nama_dokumen'] = "{$baseName}_{$timestamp}.docx";

        // Tambahkan status awal
        $data['status'] = 'draft';

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = static::getModel()::create($data);

        // 📄 Simpan file template ke lokasi sementara
        Storage::copy("templates/{$record->template_path}", "drafts/{$record->nama_dokumen}");

        return $record;
    }

}
