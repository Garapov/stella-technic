<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SitesSettings extends Settings
{
    public string $site_name;

    public ?string $site_description = null;

    public ?string $site_keywords = null;

    public ?string $site_worktime = null;

    public mixed $site_profile = null;

    public mixed $site_logo = null;

    public ?string $site_author = null;

    public ?string $site_email = null;

    public ?string $site_phone = null;

    public ?string $site_secondphone = null;

    public ?array $site_social = [];

    public ?string $site_message = null;

    public ?string $head_scripts = null;

    public ?string $body_scripts = null;

    public ?string $body_end_scripts = null;

    public ?string $politics = null;

    public ?string $cookies = null;

    public ?string $points_callback = null;

    public ?string $points_subscribe = null;

    public ?string $points_catalog = null;

    public ?string $points_cart = null;

    public ?string $points_start_order = null;

    public ?string $points_end_order = null;

    public ?string $points_end_order_yur = null;

    public ?string $add_to_cart = null;

    public ?string $open_one_click = null;

    public ?string $open_deadlines = null;

    public ?string $open_preorder = null;

    public static function group(): string
    {
        return 'sites';
    }
}
