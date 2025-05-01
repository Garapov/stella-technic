<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add("constructor.box_small");
        $this->migrator->add("constructor.box_medium");
        $this->migrator->add("constructor.box_large");
    }
};
