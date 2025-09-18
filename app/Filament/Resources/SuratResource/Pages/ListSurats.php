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

    use HasSubNavigation;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
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

    
    public function getSubNavigation(): array
    {
        $menu = request('menu', 'urkeu');

        // Struktur data → definisi group & item
        $groups = [
            'Surat' => [
                ['label' => 'Kapolda / a.n. Kapolda', 'type' => 'surat', 'subtype' => 'kapolda'],
                ['label' => 'Direktur / a.n. Direktur', 'type' => 'surat', 'subtype' => 'direktur'],
            ],
            'Surat Perintah' => [
                ['label' => 'Kapolda / a.n. Kapolda', 'type' => 'surat_perintah', 'subtype' => 'kapolda'],
                ['label' => 'Direktur / a.n. Direktur', 'type' => 'surat_perintah', 'subtype' => 'direktur'],
            ],
            'TR/STR' => [
                ['label' => 'Kapolda / a.n. Kapolda', 'type' => 'str', 'subtype' => 'kapolda'],
                ['label' => 'Direktur / a.n. Direktur', 'type' => 'str', 'subtype' => 'direktur'],
            ],
            'Nota Dinas' => [
                ['label' => 'Direktur', 'type' => 'nota_dinas', 'subtype' => 'direktur'],
                ['label' => 'Kasubbagrenmin', 'type' => 'nota_dinas', 'subtype' => 'kasubbagrenmin'],
                ['label' => 'Ka' . $menu, 'type' => 'nota_dinas', 'subtype' => 'ka'.$menu],
            ],
        ];

        $items = [];

        // Looping untuk generate NavigationItem
        foreach ($groups as $groupLabel => $children) {
            foreach ($children as $child) {
                $items[] = NavigationItem::make($child['label'])
                    ->url(SuratResource::getUrl('index', panel: 'subbagrenmin', parameters: [
                        'menu' => $menu,
                        'type' => $child['type'],
                        'subtype' => $child['subtype'],
                    ]))
                    ->group($groupLabel)
                    ->isActiveWhen(fn () =>
                        request('menu') === $menu &&
                        request('type') === $child['type'] &&
                        request('subtype') === $child['subtype']
                    );
            }
        }

        return $items;
    }

}
