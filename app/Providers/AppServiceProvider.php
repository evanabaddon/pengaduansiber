<?php

namespace App\Providers;

use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\URL;
use Filament\Forms\Components\Select;
use Illuminate\Support\ServiceProvider;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Helper untuk membaca file JSON
        $this->app->singleton('wilayah', function () {
            return new class {
                public function getProvinsi()
                {
                    $path = public_path('data-indonesia/provinsi.json');
                    $data = json_decode(file_get_contents($path), true);
                    return collect($data)->pluck('nama', 'id')->toArray();
                }

                public function getKabupaten($provinsiId)
                {
                    $path = public_path("data-indonesia/kabupaten/{$provinsiId}.json");
                    if (!file_exists($path)) return [];
                    $data = json_decode(file_get_contents($path), true);
                    return collect($data)->pluck('nama', 'id')->toArray();
                }

                public function getKecamatan($kabupatenId)
                {
                    $path = public_path("data-indonesia/kecamatan/{$kabupatenId}.json");
                    if (!file_exists($path)) return [];
                    $data = json_decode(file_get_contents($path), true);
                    return collect($data)->pluck('nama', 'id')->toArray();
                }

                public function getKelurahan($kecamatanId)
                {
                    $path = public_path("data-indonesia/kelurahan/{$kecamatanId}.json");
                    if (!file_exists($path)) return [];
                    $data = json_decode(file_get_contents($path), true);
                    return collect($data)->pluck('nama', 'id')->toArray();
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ngrok https localhost
        // if (config('app.env') === 'local' && !str_contains(request()->getHost(), 'localhost') && !str_contains(request()->getHost(), '127.0.0.1')) {
        //     URL::forceScheme('https');
        // }

        if (config('app.env') === 'local' && str_contains(request()->getHost(), 'ngrok-free.app')) {
            URL::forceScheme('https');
        }
        

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['id','en'])->circular(); // also accepts a closure
        });

        Infolist::$defaultNumberLocale = 'id';

        // Macro untuk Select Wilayah dengan mapping field yang berbeda dengan support relationship
        Select::macro('provinsi', function () {
            return $this->options(fn () => app('wilayah')->getProvinsi());
        });

        Select::macro('kabupaten', function () {
            return $this->options(function (callable $get) {
                $isSecondAddress = str_contains($this->getName(), '_2');
                $modelName = str_contains($this->getName(), 'terlapor') ? 'terlapors' : 'pelapors';
                
                $provinsiId = $isSecondAddress 
                    ? $get($modelName . '.province_id_2') 
                    : $get($modelName . '.province_id');
                
                return $provinsiId 
                    ? app('wilayah')->getKabupaten($provinsiId) 
                    : [];
            });
        });

        Select::macro('kecamatan', function () {
            return $this->options(function (callable $get) {
                $isSecondAddress = str_contains($this->getName(), '_2');
                $modelName = str_contains($this->getName(), 'terlapor') ? 'terlapors' : 'pelapors';
                
                $kabupatenId = $isSecondAddress 
                    ? $get($modelName . '.city_id_2') 
                    : $get($modelName . '.city_id');
                
                return $kabupatenId 
                    ? app('wilayah')->getKecamatan($kabupatenId) 
                    : [];
            });
        });

        Select::macro('kelurahan', function () {
            return $this->options(function (callable $get) {
                $isSecondAddress = str_contains($this->getName(), '_2');
                $modelName = str_contains($this->getName(), 'terlapor') ? 'terlapors' : 'pelapors';
                
                $kecamatanId = $isSecondAddress 
                    ? $get($modelName . '.district_id_2') 
                    : $get($modelName . '.district_id');
                
                return $kecamatanId 
                    ? app('wilayah')->getKelurahan($kecamatanId) 
                    : [];
            });
        });

        // Macro untuk TKP
        Select::macro('kabupatenTkp', function () {
            return $this->options(function (callable $get) {
                $provinsiId = $get('province_id');
                return $provinsiId ? app('wilayah')->getKabupaten($provinsiId) : [];
            });
        });

        Select::macro('kecamatanTkp', function () {
            return $this->options(function (callable $get) {
                $kabupatenId = $get('city_id');
                return $kabupatenId ? app('wilayah')->getKecamatan($kabupatenId) : [];
            });
        });

        Select::macro('kelurahanTkp', function () {
            return $this->options(function (callable $get) {
                $kecamatanId = $get('district_id');
                return $kecamatanId ? app('wilayah')->getKelurahan($kecamatanId) : [];
            });
        });

        // PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            
        //     $panelSwitch->renderHook('panels::sidebar.nav.end')
        //         ->simple()
        //         ->visible(fn (): bool =>
        //         auth()->user()?->hasRole('super_admin') // tampil hanya di panel admin
        //         );

        // });

        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch
                ->renderHook('panels::sidebar.nav.end')
                ->simple()
                ->panels(['subbagrenmin', 'bagbinopsnal', 'bagwassidik', 'sikorwas'])
                ->labels([
                    'admin' => 'Panel Option',
                    'subbagrenmin' => 'Panel Subbagrenmin',
                    'bagbinopsnal' => 'Panel Bagbinopsnal',
                    'bagwassidik' => 'Panel Bagwassidik',
                    'sikorwas' => 'Panel Sikorwas PPNS'
                    ]) 
                ->visible(function () {
                    return auth()->user() ? auth()->user()->hasRole('super_admin') : false;
                })
                ->excludes(['admin']); // ini akan selalu sembunyikan panel admin
        });
        
       
    }
}
