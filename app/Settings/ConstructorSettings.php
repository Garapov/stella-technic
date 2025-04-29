<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ConstructorSettings extends Settings
{
    public ?int $deck_low_slim;
    public ?int $deck_high_slim;
    public ?int $deck_low_wide;
    public ?int $deck_high_wide;

    public static function group(): string
    {
        return "constructor";
    }
}
