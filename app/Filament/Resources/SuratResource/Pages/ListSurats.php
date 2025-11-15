<?php

namespace App\Filament\Resources\SuratResource\Pages;

use Filament\Actions;
use Filament\Navigation\NavigationItem;
use App\Filament\Resources\SuratResource;
use App\Filament\Widgets\CollapseSidebarWidget;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Concerns\HasSubNavigation;

class ListSurats extends ListRecords
{
    protected static string $resource = SuratResource::class;

    protected function getHeaderActions(): array
    {
        // Ambil parameter dari session sebagai fallback
        $menu = request('menu') ?? session('surat_menu');
        $type = request('type') ?? session('surat_type');
        $subtype = request('subtype') ?? session('surat_subtype');
        $jenisDokumen = request('jenis_dokumen') ?? session('surat_jenis_dokumen');

        // Simpan parameter ke session untuk backup
        if (request('menu')) session(['surat_menu' => request('menu')]);
        if (request('jenis_dokumen')) session(['surat_jenis_dokumen' => request('jenis_dokumen')]);
        if (request('type')) session(['surat_type' => request('type')]);
        if (request('subtype')) session(['surat_subtype' => request('subtype')]);

        // Pastikan semua parameter required ada
        if (!$jenisDokumen || !$menu) {
            \Log::warning('Missing parameters for Surat header actions', [
                'menu' => $menu,
                'jenis_dokumen' => $jenisDokumen,
                'session_data' => session()->all()
            ]);
            return [];
        }

        return [
            Actions\CreateAction::make()
                ->label('Buat ' . urldecode($jenisDokumen))
                ->url(fn() => SuratResource::getUrl('create', [
                    'menu' => $menu,
                    'type' => $type,
                    'subtype' => $subtype,
                    'jenis_dokumen' => $jenisDokumen,
                ]))
                ->visible(fn() => !empty($jenisDokumen)),
        ];
    }

    public function mount(): void
    {
        // Validasi parameter required dengan session fallback
        $jenisDokumen = request('jenis_dokumen') ?? session('surat_jenis_dokumen');
        $menu = request('menu') ?? session('surat_menu');
        
        if (!$jenisDokumen || !$menu) {
            $this->js(<<<JS
                setTimeout(() => {
                    alert('Parameter tidak lengkap. Silakan akses melalui menu sidebar.');
                    window.location.href = '/admin';
                }, 100);
            JS);
            return;
        }
        
        parent::mount();

        // Script untuk sidebar collapse
        $this->js(<<<'JS'
            const toggle = document.querySelector('[data-collapse-sidebar-button]');
            if (toggle && !document.body.classList.contains('fi-sidebar-collapsed')) {
                toggle.click();
            }
        JS);
    }

    // method untuk handle setelah delete
    public function afterDelete(): void
    {
        $menu = session('surat_menu');
        $jenisDokumen = session('surat_jenis_dokumen');
        
        if ($menu && $jenisDokumen) {
            // Redirect dengan parameter yang sama
            $this->redirect(SuratResource::getUrl('index', [
                'menu' => $menu,
                'jenis_dokumen' => $jenisDokumen,
                'type' => session('surat_type'),
                'subtype' => session('surat_subtype'),
            ]));
        }
    }

}
