<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add("constructor.deck_bracing_big");
        $this->migrator->add("constructor.deck_stand_big");
        $this->migrator->add("constructor.deck_prop");
        $this->migrator->add("constructor.deck_prop_double");
        $this->migrator->add("constructor.rotating_wheel");
        $this->migrator->add("constructor.static_wheel");
        $this->migrator->add("constructor.wall_mount");
    }
};
