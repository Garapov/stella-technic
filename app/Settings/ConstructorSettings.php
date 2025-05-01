<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ConstructorSettings extends Settings
{
    public ?int $deck_low_slim;
    public ?int $deck_high_slim;
    public ?int $deck_low_wide;
    public ?int $deck_high_wide;
    public ?int $box_small;
    public ?int $box_medium;
    public ?int $box_large;

    public static function group(): string
    {
        return "constructor";
    }
}
