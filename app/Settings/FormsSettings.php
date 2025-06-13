<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class FormsSettings extends Settings
{
    public ?int $callback = null;
    public ?int $map = null;
    public ?string $buy_one_click = null;

    public static function group(): string
    {
        return 'forms';
    }
}
