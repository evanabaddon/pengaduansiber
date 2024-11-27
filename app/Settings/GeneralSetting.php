<?php

namespace App\Settings;

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Settings;

class GeneralSetting extends Settings
{

    public static function group(): string
    {
        return 'default';
    }

    public static function getDatabase(): \Illuminate\Database\Query\Builder 
    {
        return DB::table('settings');
    }

    public static function getPayload(): string
    {
        return 'payload';
    }

    public static function getLocked(): string
    {
        return 'locked';
    }

    public static function getGroup(): string
    {
        return 'default';
    }

    public static function getBrandName(): string
    {
        // GET NAME FROM DATABASE
        $name = self::getDatabase()->where('name', 'app_brand_name')->first();
        return $name->payload;
    }

    public static function getBrandLogo(): string
    {
        $logo = self::getDatabase()->where('name', 'app_brand_logo')->first();
        return str_replace('"', '', $logo->payload);
    }

}