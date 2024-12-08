<?php

namespace App\Providers\Filament;


use Filament\Pages;
use Filament\Panel;
use App\Models\User;
use Filament\Widgets;
use Pages\ComingSoon;
use Filament\PanelProvider;
use Filament\Pages\Auth\Login;
use Kenepa\Banner\BannerPlugin;
use App\Settings\GeneralSetting;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Orion\FilamentGreeter\GreeterPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Notifications\Actions\Action;
use App\Filament\Pages\Profile\EditProfile;
use App\Filament\Widgets\LatestLaporanWidget;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Auth\User as AuthUser;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Filament\Widgets\LatestLaporanInformasiWidget;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use App\Filament\Resources\LaporanResource\Widgets\LaporanStatusOverview;
use App\Filament\Resources\LaporanInformasiResource\Widgets\LaporanInformasiStatusOverview;

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
            ->brandName('Ditressiber Polda Jatim') 
            // ->brandLogo(asset('images/logo-siber-polri.png')) // Logo berada di folder public/images/
            ->brandLogoHeight('5rem') // Atur tinggi logo
            // ->profile(isSimple:false)
            ->userMenuItems([
                'profile' => MenuItem::make()->url(fn (): string => EditProfile::getUrl())
            ])
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                EditProfile::class
            ])
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                LaporanInformasiStatusOverview::class,
                LatestLaporanInformasiWidget::class,
                // LaporanStatusOverview::class,
                // LatestLaporanWidget::class,
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
                \Hasnayeen\Themes\Http\Middleware\SetTheme::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                    
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
            ->sidebarWidth('25rem')
            ->sidebarCollapsibleOnDesktop()
            ->favicon(asset('images/favicon.ico'))
            ->brandName(function() {
                if (Schema::hasTable('settings')) {
                    return GeneralSetting::getBrandName() ?? 'Ditressiber Polda Jatim';
                }
                return 'Ditressiber Polda Jatim';
            })
            ->plugins([
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
                            return 'Selamat Datang ' . auth()->user()->name . ' di Ditressiber Polda Jatim';
                        }
                        return 'Selamat Datang di Ditressiber Polda Jatim';
                    }),
                    \Hasnayeen\Themes\ThemesPlugin::make()
                ])
            ->renderHook(
                // custom footer
                PanelsRenderHook::FOOTER,
                // function () {
                //     return view('customFooter');
                // }
                fn () => view('components.filament.auto-save-scripts')
            );
    }
}
