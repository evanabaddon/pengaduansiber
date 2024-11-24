<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.app_name', 'DITRES SIBER');
        $this->migrator->add('general.app_locale', 'id');
        $this->migrator->add('general.app_timezone', 'Asia/Jakarta');
        $this->migrator->add('general.app_dark_mode', false);
        $this->migrator->add('general.app_favicon', 'favicon.ico');
        $this->migrator->add('general.app_brand_logo', 'logo-siber-polri.png');
        $this->migrator->add('general.app_brand_name', 'DITRES SIBER');
    }
};
