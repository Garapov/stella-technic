<?php

namespace App\Services;

class ShortHashService
{
    /**
     * Генерирует короткий хеш фиксированной длины.
     */
    public function make(string $input, int $length = 12, ?string $key = null): string
    {
        // если задан ключ — используем HMAC, иначе обычный sha256
        $hash = $key
            ? hash_hmac('sha256', $input, $key, true)
            : hash('sha256', $input, true);

        // base64url без + / =
        $encoded = rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');

        return substr($encoded, 0, $length);
    }
}
