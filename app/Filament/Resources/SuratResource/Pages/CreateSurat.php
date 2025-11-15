<?php

namespace App\Filament\Resources\SuratResource\Pages;

use Filament\Actions;
use App\Helpers\TemplateResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\SuratResource;

class CreateSurat extends CreateRecord
{
    protected static string $resource = SuratResource::class;

    public static function getLabel(): string
    {
        $jenis = request()->query('jenis_dokumen') ?? session('surat_jenis_dokumen');
        return $jenis ? urldecode($jenis) : 'Persuratan';
    }

    protected function getRedirectUrl(): string
    {
        if ($this->record) {
            return route('onlyoffice.loading', ['id' => $this->record->id]);
        }

        return $this->previousUrl ?? static::getResource()::getUrl('index');
    }

    // OVERRIDE BUKA NEW TAB
    protected function getCreatedNotificationMessage(): ?string
    {
        return null; 
    }

    // OVERRIDE: Redirect dengan JavaScript untuk buka new tab
    protected function afterCreate(): void
    {
        if ($this->record) {
            $editorUrl = route('onlyoffice.loading', ['id' => $this->record->id]);
            
            // JavaScript untuk buka new tab
            $this->js(<<<JS
                setTimeout(() => {
                    window.open('{$editorUrl}', '_blank');
                    
                    // Redirect kembali ke halaman list setelah buka editor
                    const listUrl = '{$this->getResource()::getUrl('index')}?' + new URLSearchParams({
                        menu: '{$this->record->satker}',
                        jenis_dokumen: '{$this->record->jenis_dokumen}'
                    });
                    window.location.href = listUrl;
                }, 100);
            JS);
        }
    }

    public function mount(): void
    {
        parent::mount();

        $jenisDokumen = request('jenis_dokumen');
        $menu = request('menu');
        $panel = request()->segment(1);

        $this->form->fill([
            'panel' => request()->segment(1) ?? null,
            'satker' => request('menu') ?? null,
            'jenis_dokumen' => $jenisDokumen ?? null,
        ]);

        if ($jenisDokumen) {
            $this->data['jenis_dokumen'] = $jenisDokumen;
        }
    }

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['panel'] = $data['panel'] ?? request('panel');
        $data['satker'] = $data['satker'] ?? request('menu');
        $data['jenis_dokumen'] = $data['jenis_dokumen'] ?? request('jenis_dokumen');

        // ✅ AUTO SET USER ID
        $data['user_id'] = auth()->id();

        if (!empty($data['template_version'])) {
            $data['template_path'] = "NASKAH DINAS/1. SURAT PERINTAH/" . $data['template_version'];
        } else {
            $data['template_path'] = TemplateResolver::resolve(
                $data['jenis_dokumen'] ?? 'naskah_dinas',
                $data['kategori_surat'],
                $data['pejabat_penerbit']
            );
        }

        $baseName = str_replace(' ', '_', $data['nama_dokumen']);
        $timestamp = now()->format('Ymd_His');
        $data['nama_dokumen'] = "{$baseName}_{$timestamp}.docx";

        $data['status'] = 'draft';

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = static::getModel()::create($data);
        Storage::copy("templates/{$record->template_path}", "drafts/{$record->nama_dokumen}");
        return $record;
    }
}