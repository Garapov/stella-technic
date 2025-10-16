<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add("constructor.box_small_red");
        $this->migrator->add("constructor.box_small_green");
        $this->migrator->add("constructor.box_small_blue");
        $this->migrator->add("constructor.box_small_yellow");
        $this->migrator->add("constructor.box_small_gray");
        $this->migrator->add("constructor.box_medium_red");
        $this->migrator->add("constructor.box_medium_green");
        $this->migrator->add("constructor.box_medium_blue");
        $this->migrator->add("constructor.box_medium_yellow");
        $this->migrator->add("constructor.box_medium_gray");
        $this->migrator->add("constructor.box_large_red");
        $this->migrator->add("constructor.box_large_green");
        $this->migrator->add("constructor.box_large_blue");
        $this->migrator->add("constructor.box_large_yellow");
        $this->migrator->add("constructor.box_large_gray");
        $this->migrator->add("constructor.deck_bracing");
        $this->migrator->add("constructor.deck_stand");
    }
};
