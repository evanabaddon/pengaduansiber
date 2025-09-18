<?php

namespace App\Filament\Subbagrenmin\Pages;

use App\Filament\Resources\SuratResource;
use App\Models\Surat;
use Filament\Pages\Page;
use AymanAlhattami\FilamentPageWithSidebar\PageNavigationItem;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;

class Persuratan extends Page
{
    // use HasPageSidebar; 
    
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // protected static string $view = 'filament.subbagrenmin.pages.persuratan';
    protected static string $view = 'filament.subbagrenmin.pages.persuratan-list';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    // public static function sidebar(): FilamentPageSidebar
    // {
    //     $menu = request('menu');   // urkeu / urmintu / urren
    //     $type = request('type');   // surat / surat_perintah / str / nota_dinas
    //     $notaDinasLabel = match($menu) {
    //         'urkeu' => 'Nota Dinas Kaurkeu',
    //         'urmintu' => 'Nota Dinas Kaurmintu',
    //         'urren' => 'Nota Dinas Kaurren',
    //         default => 'Nota Dinas Kaurren',
    //     };


    //     // Mapping level 2 → level 3 items
    //     $level3Items = [
    //         'surat' => [
    //             'Surat Kapolda / a.n. Kapolda' => ['type' => 'surat', 'subtype' => 'kapolda'],
    //             'Surat Direktur / a.n. Direktur' => ['type' => 'surat', 'subtype' => 'direktur'],
    //         ],
    //         'surat_perintah' => [
    //             'Surat Kapolda / a.n. Kapolda' => ['type' => 'surat_perintah', 'subtype' => 'kapolda'],
    //             'Surat Direktur / a.n. Direktur' => ['type' => 'surat_perintah', 'subtype' => 'direktur'],
    //         ],
    //         'str' => [
    //             'Surat Kapolda / a.n. Kapolda' => ['type' => 'str', 'subtype' => 'kapolda'],
    //             'Surat Direktur / a.n. Direktur' => ['type' => 'str', 'subtype' => 'direktur'],
    //         ],
    //         'nota_dinas' => [
    //             'Nota Dinas Direktur' => ['type' => 'nota_dinas', 'subtype' => 'direktur'],
    //             'Nota Dinas Kasubbagrenmin' => ['type' => 'nota_dinas', 'subtype' => 'kasubbagrenmin'],
    //             $notaDinasLabel => ['type' => 'nota_dinas', 'subtype' => 'kaur_'.$menu],
    //         ],
    //     ];

    //     $items = [];

    //     if ($type && isset($level3Items[$type])) {
    //         foreach ($level3Items[$type] as $label => $params) {
    //             $url = SuratResource::getUrl() . '?' . http_build_query([
    //                 'panel' => 'subbagrenmin',
    //                 'menu' => $menu,
    //                 'type' => $params['type'],
    //                 'subtype' => $params['subtype'],
    //             ]);

    //             $items[] = PageNavigationItem::make($label)
    //                 ->translateLabel()
    //                 ->url($url)
    //                 ->icon('heroicon-o-document')
    //                 ->visible(true)
    //                 ->isActiveWhen(fn() => 
    //                     request('panel') === 'subbagrenmin' &&
    //                     request('menu') === $menu &&
    //                     request('type') === $params['type'] &&
    //                     request('subtype') === $params['subtype']
    //                 );
    //         }
    //     }

    //     $menuLabel = match($menu) {
    //         'urkeu' => 'Urkeu',
    //         'urmintu' => 'Urmintu',
    //         'urren' => 'Urren',
    //         default => 'Subbagrenmin',
    //     };
        
    //     $typeLabel = match($type) {
    //         'surat' => 'Surat',
    //         'surat_perintah' => 'Surat Perintah',
    //         'str' => 'STR',
    //         'nota_dinas' => 'Nota Dinas',
    //         default => '',
    //     };
        
    //     $description = trim("$typeLabel $menuLabel");

    //     return FilamentPageSidebar::make()
    //         ->setDescription($description)
    //         ->setNavigationItems($items);
    // }
    // public static function sidebar(): FilamentPageSidebar
    // {
    //     $menu = request('menu');   // urkeu / urmintu / urren
    //     $type = request('type');   // surat / surat_perintah / str / nota_dinas
    
    //     $notaDinasLabel = match($menu) {
    //         'urkeu' => 'Nota Dinas Kaurkeu',
    //         'urmintu' => 'Nota Dinas Kaurmintu',
    //         'urren' => 'Nota Dinas Kaurren',
    //         default => 'Nota Dinas Kaurren',
    //     };
    
    //     // Mapping level 2 → level 3
    //     $level3Items = [
    //         'surat' => [
    //             'Surat Kapolda / a.n. Kapolda' => ['type' => 'surat', 'subtype' => 'kapolda'],
    //             'Surat Direktur / a.n. Direktur' => ['type' => 'surat', 'subtype' => 'direktur'],
    //         ],
    //         'surat_perintah' => [
    //             'Surat Kapolda / a.n. Kapolda' => ['type' => 'surat_perintah', 'subtype' => 'kapolda'],
    //             'Surat Direktur / a.n. Direktur' => ['type' => 'surat_perintah', 'subtype' => 'direktur'],
    //         ],
    //         'str' => [
    //             'Surat Kapolda / a.n. Kapolda' => ['type' => 'str', 'subtype' => 'kapolda'],
    //             'Surat Direktur / a.n. Direktur' => ['type' => 'str', 'subtype' => 'direktur'],
    //         ],
    //         'nota_dinas' => [
    //             'Nota Dinas Direktur' => ['type' => 'nota_dinas', 'subtype' => 'direktur'],
    //             'Nota Dinas Kasubbagrenmin' => ['type' => 'nota_dinas', 'subtype' => 'kasubbagrenmin'],
    //             $notaDinasLabel => ['type' => 'nota_dinas', 'subtype' => 'kaur_'.$menu],
    //         ],
    //     ];
    
    //     $items = [];
    
    //     foreach ($level3Items as $typeKey => $children) {
    //         // Level 2 (Surat / Surat Perintah / STR / Nota Dinas)
    //         $parentItem = PageNavigationItem::make(ucwords(str_replace('_', ' ', $typeKey)))
    //             ->icon('heroicon-o-folder')
    //             ->isActiveWhen(fn() => request('type') === $typeKey && request('menu') === $menu);
    
    //         $childItems = [];
    //         foreach ($children as $label => $params) {
    //             $url = SuratResource::getUrl() . '?' . http_build_query([
    //                 'panel' => 'subbagrenmin',
    //                 'menu' => $menu,
    //                 'type' => $params['type'],
    //                 'subtype' => $params['subtype'],
    //             ]);
    
    //             $childItems[] = PageNavigationItem::make($label)
    //                 ->url($url)
    //                 ->icon('heroicon-o-document')
    //                 ->isActiveWhen(fn() => 
    //                     request('panel') === 'subbagrenmin' &&
    //                     request('menu') === $menu &&
    //                     request('type') === $params['type'] &&
    //                     request('subtype') === $params['subtype']
    //                 );
    //         }
    
    //         $parentItem->childItems($childItems);
    //         $items[] = $parentItem;
    //     }
    
    //     $menuLabel = match($menu) {
    //         'urkeu' => 'Urkeu',
    //         'urmintu' => 'Urmintu',
    //         'urren' => 'Urren',
    //         default => 'Subbagrenmin',
    //     };
    
    //     return FilamentPageSidebar::make()
    //         ->setDescription("Persuratan $menuLabel")
    //         ->setNavigationItems($items);
    // }
    public static function sidebarData(): array
    {
        $menu = request('menu', 'urkeu');

        return [
            'Surat' => [
                ['label' => 'Kapolda', 'type' => 'surat', 'subtype' => 'kapolda'],
                ['label' => 'Direktur', 'type' => 'surat', 'subtype' => 'direktur'],
            ],
            'Surat Perintah' => [
                ['label' => 'Kapolda', 'type' => 'surat_perintah', 'subtype' => 'kapolda'],
                ['label' => 'Direktur', 'type' => 'surat_perintah', 'subtype' => 'direktur'],
            ],
            'STR' => [
                ['label' => 'Kapolda', 'type' => 'str', 'subtype' => 'kapolda'],
                ['label' => 'Direktur', 'type' => 'str', 'subtype' => 'direktur'],
            ],
            'Nota Dinas' => [
                ['label' => 'Direktur', 'type' => 'nota_dinas', 'subtype' => 'direktur'],
                ['label' => 'Kasubbagrenmin', 'type' => 'nota_dinas', 'subtype' => 'kasubbagrenmin'],
                ['label' => 'Kaur ' . ucfirst($menu), 'type' => 'nota_dinas', 'subtype' => 'kaur_'.$menu],
            ],
        ];
    }

}
