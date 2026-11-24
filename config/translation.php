<?php

return [
    /*
    | Terjemahan otomatis untuk teks yang diisi pengelola (tiket, galeri, konten, aturan kunjungan).
    | Hanya mengisi sisi bahasa yang masih kosong; tidak pernah menimpa isian manual.
    | Bila layanan gagal/kuota habis, data tetap tersimpan dan sisi kosong jatuh ke bahasa lain.
    */
    'enabled' => (bool) env('TRANSLATE_ENABLED', true),

    // mymemory (gratis, tanpa API key) | libretranslate (open source, bisa self-host)
    'driver' => env('TRANSLATE_DRIVER', 'mymemory'),

    'timeout' => (int) env('TRANSLATE_TIMEOUT', 6),
    'cache_days' => (int) env('TRANSLATE_CACHE_DAYS', 90),

    'mymemory' => [
        'url' => env('TRANSLATE_MYMEMORY_URL', 'https://api.mymemory.translated.net/get'),
        // Opsional: email kontak mengangkat kuota anonim 5.000 -> 50.000 karakter/hari.
        'email' => env('TRANSLATE_MYMEMORY_EMAIL'),
    ],

    'libretranslate' => [
        'url' => env('TRANSLATE_LIBRE_URL', 'https://libretranslate.com'),
        'key' => env('TRANSLATE_LIBRE_KEY'),
    ],
];
