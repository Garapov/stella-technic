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
    public ?int $box_small_red;
    public ?int $box_small_green;
    public ?int $box_small_blue;
    public ?int $box_small_yellow;
    public ?int $box_small_gray;
    public ?int $box_medium_red;
    public ?int $box_medium_green;
    public ?int $box_medium_blue;
    public ?int $box_medium_yellow;
    public ?int $box_medium_gray;
    public ?int $box_large_red;
    public ?int $box_large_green;
    public ?int $box_large_blue;
    public ?int $box_large_yellow;
    public ?int $box_large_gray;
    public ?int $deck_bracing;
    public ?int $deck_stand;

    public static function group(): string
    {
        return "constructor";
    }
}
