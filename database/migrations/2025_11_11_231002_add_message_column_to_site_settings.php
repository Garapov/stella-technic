<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('sites.site_message', '');
        $this->migrator->add('sites.head_scripts', '');
        $this->migrator->add('sites.body_scripts', '');
        $this->migrator->add('sites.body_end_scripts', '');
        $this->migrator->add('sites.points_callback', '');
        $this->migrator->add('sites.points_subscribe', '');
        $this->migrator->add('sites.points_catalog', '');
        $this->migrator->add('sites.points_cart', '');
        $this->migrator->add('sites.points_start_order', '');
        $this->migrator->add('sites.points_end_order', '');
        $this->migrator->add('sites.points_end_order_yur', '');
        $this->migrator->add('sites.add_to_cart', '');
        $this->migrator->add('sites.open_one_click', '');
    }
};
