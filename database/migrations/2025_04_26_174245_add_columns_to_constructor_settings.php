<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add("constructor.deck_low_slim");
        $this->migrator->add("constructor.deck_high_slim");
        $this->migrator->add("constructor.deck_low_wide");
        $this->migrator->add("constructor.deck_high_wide");
    }
};
