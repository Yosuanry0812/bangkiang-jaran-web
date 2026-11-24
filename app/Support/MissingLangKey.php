<?php

namespace App\Support;

use App\Services\AutoTranslator;
use Illuminate\Support\Facades\Cache;

/**
 * Jaring pengaman: kunci lang/ yang ada di satu bahasa (ID atau EN) tetapi belum di bahasa aktif
 * diterjemahkan otomatis saat dibutuhkan, lalu di-cache. Hasil permanen ditulis ke berkas lang
 * lewat `php artisan translate:fill --lang`.
 */
class MissingLangKey
{
    private const LOCALES = ['id', 'en'];

    private static bool $busy = false;

    public static function handle(string $key, array $replace, ?string $locale, bool $fallback): string
    {
        $translator = app(AutoTranslator::class);
        $locale = $locale ?: app()->getLocale();
        if (self::$busy || ! $translator->enabled() || ! in_array($locale, self::LOCALES, true) || ! str_contains($key, '.')) {
            return $key;
        }

        self::$busy = true;
        try {
            $source = $locale === 'en' ? 'id' : 'en';
            $original = app('translator')->get($key, [], $source, false);
            if (! is_string($original) || $original === $key || $original === '') {
                return $key;
            }

            $negative = 'lang-miss:'.sha1("$locale|$key|$original");
            if (Cache::has($negative)) {
                return $original;
            }

            $translated = $translator->translate($original, $source, $locale, 4);
            if ($translated === null) {
                Cache::put($negative, 1, now()->addMinutes(10));

                return $original; // layanan gagal: tampilkan bahasa asal, jangan kunci mentah
            }

            return $translated;
        } finally {
            self::$busy = false;
        }
    }
}
