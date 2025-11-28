<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('forms.deadlines');
        $this->migrator->add('forms.preorder');
        $this->migrator->add('sites.open_deadlines', '');
        $this->migrator->add('sites.open_preorder', '');
    }
};
