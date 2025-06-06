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

    public ?string $politics = null;

    public ?string $cookies = null;

    public static function group(): string
    {
        return "sites";
    }
}
