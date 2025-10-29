<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string make(string $input, int $length = 12, ?string $key = null)
 *
 * @see \App\Services\ShortHashService
 */
class ShortHash extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'short-hash';
    }
}
