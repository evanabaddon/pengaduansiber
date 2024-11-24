<?php

namespace App\Providers;

use Filament\Infolists\Infolist;
use Filament\Forms\Components\Select;
use Illuminate\Support\ServiceProvider;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;

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
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['id','en'])->circular(); // also accepts a closure
        });

        Infolist::$defaultNumberLocale = 'id';

        // Macro untuk Select Wilayah dengan mapping field yang berbeda dengan support relationship
        Select::macro('provinsi', function () {
            return $this
                ->options(fn() => app('wilayah')->getProvinsi())
                ->searchable()
                ->preload()
                ->live();
        });

        Select::macro('kabupaten', function () {
            return $this
                ->options(function (callable $get, $state) {
                    $path = explode('.', $this->getName());
                    $prefix = count($path) > 1 ? $path[0] . '.' : '';
                    
                    // Cek field province_id atau provinsi_id
                    $provinsiId = $get($prefix . 'province_id') ?? $get($prefix . 'provinsi_id');
                    if (!$provinsiId) return [];
                    return app('wilayah')->getKabupaten($provinsiId);
                })
                ->searchable()
                ->preload()
                ->live();
        });

        Select::macro('kecamatan', function () {
            return $this
                ->options(function (callable $get, $state) {
                    $path = explode('.', $this->getName());
                    $prefix = count($path) > 1 ? $path[0] . '.' : '';
                    
                    // Cek field city_id atau kabupaten_id
                    $kabupatenId = $get($prefix . 'city_id') ?? $get($prefix . 'kabupaten_id');
                    if (!$kabupatenId) return [];
                    return app('wilayah')->getKecamatan($kabupatenId);
                })
                ->searchable()
                ->preload()
                ->live();
        });

        Select::macro('kelurahan', function () {
            return $this
                ->options(function (callable $get, $state) {
                    $path = explode('.', $this->getName());
                    $prefix = count($path) > 1 ? $path[0] . '.' : '';
                    
                    // Cek field district_id atau kecamatan_id
                    $kecamatanId = $get($prefix . 'district_id') ?? $get($prefix . 'kecamatan_id');
                    if (!$kecamatanId) return [];
                    return app('wilayah')->getKelurahan($kecamatanId);
                })
                ->searchable()
                ->preload();
        });

       
    }
}
