<?php

namespace App\Providers\Filament;


use Filament\Pages;
use Filament\Panel;
use App\Models\User;
use Filament\Widgets;
use Pages\ComingSoon;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Facades\Filament;
use Filament\Pages\Auth\Login;
use Filament\Support\Assets\Js;
use Kenepa\Banner\BannerPlugin;
use App\Settings\GeneralSetting;
use Filament\Support\Assets\Css;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Illuminate\Support\HtmlString;
use Filament\View\PanelsRenderHook;
use App\Filament\Widgets\MapsWidget;
use App\Http\Responses\LoginResponse;
use Illuminate\Support\Facades\Schema;
use Filament\Navigation\NavigationItem;
use Illuminate\Database\Eloquent\Model;
use Filament\Navigation\NavigationGroup;
use Filament\Notifications\Notification;
use Orion\FilamentGreeter\GreeterPlugin;
use App\Filament\Widgets\AllLaporanWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\NavigationBuilder;
use Filament\Notifications\Actions\Action;
use App\Filament\Pages\Profile\EditProfile;
use App\Filament\Widgets\SidebarPanelSwitch;
use App\Filament\Widgets\LatestLaporanWidget;
use Illuminate\Session\Middleware\StartSession;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Auth\User as AuthUser;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Filament\Widgets\LatestLaporanInformasiWidget;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use App\Filament\Resources\LaporanResource\Widgets\LaporanStatusOverview;
use App\Filament\Resources\PengaduanResource\Widgets\PengaduanStatusOverview;
use App\Filament\Resources\LaporanInfoResource\Widgets\LaporanInfoStatusOverview;
use App\Filament\Resources\LaporanPolisiResource\Widgets\LaporanPolisiStatusOverview;
use App\Filament\Resources\LaporanInformasiResource\Widgets\LaporanInformasiStatusOverview;
use App\Http\Middleware\RedirectToRolePanel;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {


        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
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
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverResources(in: app_path('Filament/Bagbinopsnal/Resources'), for: 'App\\Filament\\Bagbinopsnal\\Resources')
            ->discoverResources(in: app_path('Filament/Bagwassidik/Resources'), for: 'App\\Filament\\Bagwassidik\\Resources')
            ->discoverResources(in: app_path('Filament/Sikorwas/Resources'), for: 'App\\Filament\\Sikorwas\\Resources')
            ->discoverResources(in: app_path('Filament/Subbagrenmin/Resources'), for: 'App\\Filament\\Subbagrenmin\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                EditProfile::class
            ])
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // all laporan full width
                AllLaporanWidget::class,
                // maps widget full width
                MapsWidget::class,
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
                RedirectToRolePanel::class,
                // \Hasnayeen\Themes\Http\Middleware\SetTheme::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            // ->collapsibleNavigationGroups(false)
            ->plugins([
                FilamentShieldPlugin::make(),
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
                // \JoaoPaulolndev\FilamentEditProfile\FilamentEditProfilePlugin::make()
                // ->editProfileComponents([
                //     'editProfile' => EditProfile::class
                // ])
            ])
            ->maxContentWidth('full')
            ->spa()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            // ->brandLogo(function() {
            //     if (Schema::hasTable('settings')) {
            //         return GeneralSetting::getBrandLogo() ?? asset('images/logo-siber-polri.png');
            //     }
            //     return asset('images/logo-siber-polri.png');
            // })
            // ->sidebarWidth('25rem')
            // ->sidebarCollapsibleOnDesktop()
            ->favicon(app()->environment('local') ? secure_asset('images/favicon.ico') : asset('images/favicon.ico'))
            ->brandName(function() {
                if (Schema::hasTable('settings')) {
                    return GeneralSetting::getBrandName() ?? 'Ditressiber Polda Jatim';
                }
                return 'Ditressiber Polda Jatim';
            })
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
                Css::make(
                    'filament-custom',
                    app()->environment('local') ? secure_asset('css/custom.css') : asset('css/custom.css')
                )
                
            ]);
    }
}
