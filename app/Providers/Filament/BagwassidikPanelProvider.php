<?php

namespace App\Providers\Filament;

use App\Filament\Bagwassidik\Pages\Dashboard;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Facades\Filament;
use Filament\Pages\Auth\Login;
use Filament\Support\Assets\Js;
use Kenepa\Banner\BannerPlugin;
use Filament\Support\Assets\Css;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Illuminate\Support\HtmlString;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Orion\FilamentGreeter\GreeterPlugin;
use Filament\Http\Middleware\Authenticate;
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
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class BagwassidikPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('bagwassidik')
            ->path('bagwassidik')
            ->login()
            ->darkMode(false)
            ->login(Login::class)
            ->brandName('DITRESSIBER POLDA JATIM')
            // ->brandLogo(asset('images/logo-siber-polri.png')) // Logo berada di folder public/images/
            ->brandLogoHeight('5rem') // Atur tinggi logo
            // ->profile(isSimple:false)
            ->userMenuItems([
                'profile' => MenuItem::make()->url(fn (): string => EditProfile::getUrl())
            ])
            ->colors([
                'primary' => '#1e2754', 
            ])
            ->discoverResources(in: app_path('Filament/Bagwassidik/Resources'), for: 'App\\Filament\\Bagwassidik\\Resources')
            ->discoverResources(in: app_path('Filament/Bagbinopsnal/Resources'), for: 'App\\Filament\\Bagbinopsnal\\Resources')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Bagwassidik/Pages'), for: 'App\\Filament\\Bagwassidik\\Pages')
            ->pages([
                Dashboard::class,
                EditProfile::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Bagwassidik/Widgets'), for: 'App\\Filament\\Bagwassidik\\Widgets')
            ->widgets([
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()->url(fn (): string => EditProfile::getUrl())
            ])
            ->maxContentWidth('full')
            ->plugins([
                // FilamentShieldPlugin::make(),
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
                    ->message(function() {
                        if (Schema::hasTable('users') && auth()->check()) {
                            return 'Hai ' . auth()->user()->name . ', Selamat Datang di Ditressiber Polda Jatim';
                        }
                        return 'Selamat Datang di Ditressiber Polda Jatim';
                    }),
                    \Hasnayeen\Themes\ThemesPlugin::make()
                ])
            ->renderHook(
                // custom footer
                PanelsRenderHook::FOOTER,
                fn () => view('components.filament.auto-save-scripts')
            )
            ->assets([
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
                Css::make('filament-custom', app()->environment('local') ? secure_asset('css/custom.css') : asset('css/custom.css')),
            ])
            ->renderHook(PanelsRenderHook::SIDEBAR_NAV_END, function () {
                $currentPanel = Filament::getCurrentPanel()->getId();
            
                // Tampilkan tombol Back / Home jika bukan panel admin
                if ($currentPanel !== 'admin') {
                    $adminUrl = Filament::getPanel('admin')->getUrl();
            
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
