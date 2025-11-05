<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Facades\Filament;
use Filament\Support\Assets\Js;
use Kenepa\Banner\BannerPlugin;
use Filament\Support\Assets\Css;
use App\Filament\Pages\Dashboard;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Support\HtmlString;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;
use Filament\Widgets\FilamentInfoWidget;
use Orion\FilamentGreeter\GreeterPlugin;
use App\Filament\Resources\SuratResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\NavigationBuilder;
use App\Filament\Pages\Profile\EditProfile;
use Illuminate\Session\Middleware\StartSession;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use App\Filament\Subbagrenmin\Resources\AnggaranResource;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class SubbagrenminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('subbagrenmin')
            ->path('subbagrenmin')
            ->brandName('DITRESSIBER POLDA JATIM')
            ->userMenuItems([
                'profile' => MenuItem::make()->url(fn (): string => EditProfile::getUrl())
            ])
            ->colors([
                'primary' => '#1e2754', 
            ])
            ->navigationItems([
                // ---------------- Urkeu ----------------
                NavigationItem::make('Anggaran')
                    ->url('/subbagrenmin/anggaran')
                    ->group('Urkeu'),
                
                NavigationItem::make('Persuratan')
                    ->url(url('/subbagrenmin/surats?menu=urkeu'))
                    ->icon('heroicon-o-document-text')
                    ->group('Urkeu')
                    ->isActiveWhen(fn () => request('menu') === 'urkeu'),

                // ---------------- Urmintu ----------------
                NavigationItem::make('Personel')
                    // ->url('/subbagrenmin/pimpinans')
                    ->url('/subbagrenmin/personel')
                    ->icon('heroicon-o-users')
                    ->group('Urmintu')
                    ->isActiveWhen(fn() => request()->is('subbagrenmin/personel') || request()->is('subbagrenmin/pimpinans') || request()->is('subbagrenmin/staff')),

                // NavigationItem::make('Surat Masuk')
                //     ->url('/subbagrenmin/surat-masuks')
                //     ->icon('heroicon-o-document-arrow-down')
                //     ->group('Urmintu')
                //     ->isActiveWhen(fn() => request()->is('subbagrenmin/surat-masuks')),

                NavigationItem::make('Persuratan')
                    ->url(url('/subbagrenmin/surats?menu=urmintu'))
                    ->icon('heroicon-o-document-text')
                    ->group('Urmintu')
                    ->isActiveWhen(fn () => request('menu') === 'urmintu'),

                // ---------------- Urren ----------------
                NavigationItem::make('Persuratan')
                    ->url(url('/subbagrenmin/surats?menu=urren'))
                    ->icon('heroicon-o-document-text')
                    ->group('Urren')
                    ->isActiveWhen(fn () => request('menu') === 'urren'),
            ])
            ->login()
            ->homeUrl('/subbagrenmin')
            ->discoverResources(in: app_path('Filament/Subbagrenmin/Resources'), for: 'App\\Filament\\Subbagrenmin\\Resources')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Subbagrenmin/Pages'), for: 'App\\Filament\\Subbagrenmin\\Pages')
            ->pages([
                Pages\Dashboard::class,
                EditProfile::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Subbagrenmin/Widgets'), for: 'App\\Filament\\Subbagrenmin\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()->url(fn (): string => EditProfile::getUrl())
            ])
            ->spa()
            ->maxContentWidth('full')
            ->plugins([
                EasyFooterPlugin::make()
                    ->withFooterPosition('footer')
                    ->hiddenFromPagesEnabled()
                    ->hiddenFromPages(['admin/login'])
                    ->withSentence(new HtmlString('<img src="'.asset('images/logo-siber-polri.png').'" alt="Ditressiber Polda Jatim" style="width: 20px; height: 20px;"> <b>Ditressiber Polda Jatim</b>'))
                    ->withBorder(),
                FilamentBackgroundsPlugin::make()
                    ->imageProvider(
                        MyImages::make()->directory('images/backgrounds')
                    )
                    ->showAttribution(false)
                    ->showAttribution(false),
                BannerPlugin::make()
                    ->navigationGroup('Setting')
                    ->bannerManagerAccessPermission('super_admin'),
                GreeterPlugin::make()
                    ->columnSpan('full')
                    ->title(fn () => 'Selamat Datang di Ditressiber Polda Jatim')
                    ->message(function() {
                        if (Schema::hasTable('users') && auth()->check()) {
                            return 'Hai ' ;
                        }
                        return 'Hai ...';
                    }),
                ])
            ->renderHook(
                // custom footer
                PanelsRenderHook::FOOTER,
                fn () => view('components.filament.auto-save-scripts')
            )
            ->renderHook(PanelsRenderHook::SIDEBAR_NAV_END, function () {
                $currentPanel = Filament::getCurrentPanel()->getId();
                $user = auth()->user();
            
                // Jika panel bukan admin DAN user punya role super_admin / admin
                if ($currentPanel !== 'admin' && $user && $user->hasAnyRole(['super_admin', 'admin'])) {
                    // $adminUrl = Filament::getPanel('admin')->getUrl();

                    $adminUrl = '/admin';

                    return new HtmlString(
                        Blade::render('
                            <div class="flex justify-center items-center py-4">
                                <x-filament::button 
                                    href="'.$adminUrl.'" 
                                    tag="a"
                                    color="primary"
                                    size="sm"
                                    icon="heroicon-o-backward"
                                >
                                    Back / Home
                                </x-filament::button>
                            </div>
                        ')
                    );
                }

                return '';
            })
            
            ->assets([
                Js::make('highcharts', 'https://code.highcharts.com/highcharts.js'),
                Js::make('highcharts-map', 'https://code.highcharts.com/maps/modules/map.js'),
                Js::make('highcharts-id-map', 'https://code.highcharts.com/mapdata/countries/id/id-all.js'),
                // Js::make('proj4', asset('js/highcharts/proj4.js')),
                // Js::make('highcharts', asset('js/highcharts/highcharts.js')),
                // Js::make('highcharts-accessibility', asset('js/highcharts/accessibility.js')),
                // Js::make('highcharts-map', asset('js/highcharts/map.js')),
                // Js::make('highcharts-id-map', asset('js/highcharts/id-all.js')),
                // Js::make('highcharts-exporting', asset('js/highcharts/exporting.js')),

                Css::make('highcharts-dashboard', 'https://code.highcharts.com/dashboards/css/dashboards.css'),
                Css::make('highcharts-custom', asset('css/highcharts-custom.css')),
                // Css::make('filament-custom', asset('css/custom.css')),
                // Load Proj4js first
                Js::make('proj4', app()->environment('local') ? secure_asset('js/highcharts/proj4.js') : asset('js/highcharts/proj4.js')),
                
                // Then load Highcharts core
                Js::make('highcharts', app()->environment('local') ? secure_asset('js/highcharts/highcharts.js') : asset('js/highcharts/highcharts.js')),
                
                // Load accessibility module
                Js::make('highcharts-accessibility', app()->environment('local') ? secure_asset('js/highcharts/accessibility.js') : asset('js/highcharts/accessibility.js')),
                
                // Then load the map module
                Js::make('highcharts-map', app()->environment('local') ? secure_asset('js/highcharts/map.js') : asset('js/highcharts/map.js')),
                
                // Then load the Indonesia map data
                Js::make('highcharts-id-map', app()->environment('local') ? secure_asset('js/highcharts/id-all.js') : asset('js/highcharts/id-all.js')),
                
                // Additional modules can be loaded last
                Js::make('highcharts-exporting', app()->environment('local') ? secure_asset('js/highcharts/exporting.js') : asset('js/highcharts/exporting.js')),

                Css::make('highcharts-dashboard', app()->environment('local') ? secure_asset('https://code.highcharts.com/dashboards/css/dashboards.css') : asset('https://code.highcharts.com/dashboards/css/dashboards.css')),

                Css::make('highcharts-custom', app()->environment('local') ? secure_asset('css/highcharts-custom.css') : asset('css/highcharts-custom.css')),

                // Custom Style
                Css::make(
                    'filament-custom',
                    app()->environment('local') ? secure_asset('css/custom.css') : asset('css/custom.css')
                )
                
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
