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
        $menu = request('menu');
        $type = request('type');
        $subtype = request('subtype');
        $jenisDokumen = request('jenis_dokumen'); // AMBIL PARAMETER JENIS_DOKUMEN

        if(!$jenisDokumen){
            return [];
        }

        return [
            Actions\CreateAction::make()
                ->label('Buat') // LABEL DINAMIS
                ->url(fn() => SuratResource::getUrl('create', [
                    'menu' => $menu,
                    'type' => $type,
                    'subtype' => $subtype,
                    'jenis_dokumen' => $jenisDokumen, // TAMBAHKAN JENIS_DOKUMEN
                ])),
        ];
    }

    public function mount(): void
    {
        parent::mount();

        $this->js(<<<'JS'
            const toggle = document.querySelector('[data-collapse-sidebar-button]');
            if (toggle && !document.body.classList.contains('fi-sidebar-collapsed')) {
                toggle.click();
            }
        JS);
    }

    // public function getSubNavigation(): array
    // {
    //     $menu = request('menu', 'urkeu');

    //     // Struktur data → definisi group & item
    //     $groups = [
    //         'Surat Perintah' => [
    //             ['label' => 'Kapolda / a.n. Kapolda', 'type' => 'surat_perintah', 'subtype' => 'kapolda'],
    //             ['label' => 'Direktur / a.n. Direktur', 'type' => 'surat_perintah', 'subtype' => 'direktur'],
    //         ],
    //         'Surat Tugas' => [
    //             ['label' => 'Kapolda / a.n. Kapolda', 'type' => 'surat_tugas', 'subtype' => 'kapolda'],
    //             ['label' => 'Direktur / a.n. Direktur', 'type' => 'surat_tugas', 'subtype' => 'direktur'],
    //         ],
    //         'Nota Dinas' => [
    //             ['label' => 'Direktur', 'type' => 'nota_dinas', 'subtype' => 'direktur'],
    //             ['label' => 'Kasubbagrenmin', 'type' => 'nota_dinas', 'subtype' => 'kasubbagrenmin'],
    //             ['label' => 'Ka' . $menu, 'type' => 'nota_dinas', 'subtype' => 'ka'.$menu],
    //         ],
    //         'Surat Telegram' => [
    //             ['label' => 'Kapolda / a.n. Kapolda', 'type' => 'surat_telegram', 'subtype' => 'kapolda'],
    //         ],
    //         'Surat' => [
    //             ['label' => 'Kapolda / a.n. Kapolda', 'type' => 'surat', 'subtype' => 'kapolda'],
    //             ['label' => 'Direktur / a.n. Direktur', 'type' => 'surat', 'subtype' => 'direktur'],
    //         ],
    //         'Surat Pengantar' => [
    //             ['label' => 'Kapolda / a.n. Kapolda', 'type' => 'surat_pengantar', 'subtype' => 'kapolda'],
    //             ['label' => 'Direktur / a.n. Direktur', 'type' => 'surat_pengantar', 'subtype' => 'direktur'],
    //         ],
    //         'Surat Undangan' => [
    //             ['label' => 'Kapolda / a.n. Kapolda', 'type' => 'surat_undangan', 'subtype' => 'kapolda'],
    //             ['label' => 'Direktur / a.n. Direktur', 'type' => 'surat_undangan', 'subtype' => 'direktur'],
    //         ],
            
    //     ];

    //     $items = [];
    //     foreach ($groups as $groupLabel => $children) {
    //         foreach ($children as $child) {
    //             $items[] = NavigationItem::make($child['label'])
    //                 ->url(SuratResource::getUrl('index', panel: request('panel'), parameters: [
    //                     'menu' => $menu,
    //                     'type' => $child['type'],
    //                     'subtype' => $child['subtype'],
    //                 ]))
    //                 ->group($groupLabel)
    //                 ->isActiveWhen(fn () =>
    //                     request('menu') === $menu &&
    //                     request('type') === $child['type'] &&
    //                     request('subtype') === $child['subtype']
    //                 );
    //         }
    //     }

    //     return $items;
    // }

}
