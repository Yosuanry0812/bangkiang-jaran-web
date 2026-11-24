<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Penerjemah otomatis ID <-> EN. Tidak pernah melempar exception: bila gagal mengembalikan null.
 */
class AutoTranslator
{
    private const CHUNK = 420;

    public function enabled(): bool
    {
        return (bool) config('translation.enabled');
    }

    public function translate(?string $text, string $from, string $to, ?int $timeout = null): ?string
    {
        $text = trim((string) $text);
        if ($text === '' || $from === $to || ! $this->enabled()) {
            return null;
        }

        $key = 'autotranslate:'.config('translation.driver').":$from>$to:".sha1($text);
        if (($cached = Cache::get($key)) !== null) {
            return $cached;
        }

        try {
            [$protected, $tokens] = $this->protect($text);
            $parts = [];
            foreach ($this->chunks($protected) as $chunk) {
                if (trim($chunk) === '') {
                    $parts[] = $chunk;

                    continue;
                }
                $result = $this->request($chunk, $from, $to, $timeout);
                if ($result === null) {
                    return null;
                }
                $parts[] = $result;
            }
            $out = $this->restore(implode('', $parts), $tokens);
            if ($out === null || trim($out) === '') {
                return null;
            }
        } catch (Throwable $e) {
            Log::warning('Terjemahan otomatis gagal.', ['driver' => config('translation.driver'), 'error' => get_class($e)]);

            return null;
        }

        Cache::put($key, $out, now()->addDays((int) config('translation.cache_days')));

        return $out;
    }

    private function request(string $text, string $from, string $to, ?int $timeout = null): ?string
    {
        $http = Http::timeout($timeout ?? (int) config('translation.timeout'))->acceptJson();
        if ($timeout === null) {
            $http = $http->retry(1, 300, throw: false);
        }

        if (config('translation.driver') === 'libretranslate') {
            $cfg = config('translation.libretranslate');
            $res = $http->post(rtrim($cfg['url'], '/').'/translate', array_filter([
                'q' => $text, 'source' => $from, 'target' => $to, 'format' => 'text', 'api_key' => $cfg['key'],
            ]));

            return $res->successful() ? ($res->json('translatedText') ?: null) : null;
        }

        $cfg = config('translation.mymemory');
        $res = $http->get($cfg['url'], array_filter([
            'q' => $text, 'langpair' => "$from|$to", 'de' => $cfg['email'],
        ]));
        $translated = $res->json('responseData.translatedText');
        if (! $res->successful() || (int) $res->json('responseStatus') !== 200 || ! is_string($translated)) {
            return null;
        }
        // Kuota habis / peringatan layanan dikirim sebagai teks biasa.
        if (stripos($translated, 'MYMEMORY WARNING') !== false || stripos($translated, 'INVALID') === 0) {
            return null;
        }

        return html_entity_decode($translated, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /** Lindungi placeholder Laravel (:name) agar tidak ikut diterjemahkan. */
    private function protect(string $text): array
    {
        $tokens = [];
        $out = preg_replace_callback('/:[A-Za-z_][A-Za-z0-9_]*/', function ($m) use (&$tokens) {
            $tokens[] = $m[0];

            return 'ZXQ'.(count($tokens) - 1).'QXZ';
        }, $text);

        return [$out, $tokens];
    }

    private function restore(string $text, array $tokens): ?string
    {
        foreach ($tokens as $i => $token) {
            if (! preg_match('/ZXQ\s*'.$i.'\s*QXZ/i', $text)) {
                return null; // placeholder hilang: lebih aman tidak memakai hasil
            }
            $text = preg_replace('/ZXQ\s*'.$i.'\s*QXZ/i', $token, $text);
        }

        return $text;
    }

    /** Pecah teks panjang per kalimat/baris agar muat batas ±500 byte layanan gratis. */
    private function chunks(string $text): array
    {
        if (strlen($text) <= self::CHUNK) {
            return [$text];
        }
        $pieces = preg_split('/(?<=[.!?\n])(\s+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $chunks = [];
        $current = '';
        foreach ($pieces as $piece) {
            if ($current !== '' && strlen($current.$piece) > self::CHUNK) {
                $chunks[] = $current;
                $current = '';
            }
            $current .= $piece;
        }
        if ($current !== '') {
            $chunks[] = $current;
        }

        return $chunks;
    }
}
