<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class BackToAdminWidget extends Widget
{
    protected static string $view = 'filament.widgets.back-to-admin-widget';

    public function getViewData(): array
    {
        return [
            'adminUrl' => filament()->getPanel('admin')->getUrl(),
            'currentPanel' => filament()->getCurrentPanel()->getId(),
        ];
    }
}
