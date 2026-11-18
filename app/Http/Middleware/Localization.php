<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Throwable;

class Localization
{
    private const SUPPORTED = ['id', 'en'];

    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale');
        $user = $request->user();

        if (! in_array($locale, self::SUPPORTED, true) && $user && in_array($user->locale ?? null, self::SUPPORTED, true)) {
            $locale = $user->locale;
            session()->put('locale', $locale);
        }

        if (in_array($locale, self::SUPPORTED, true)) {
            App::setLocale($locale);

            // Simpan pilihan bahasa agar email transaksional memakai bahasa pengguna.
            if ($user && ($user->locale ?? null) !== $locale) {
                try {
                    $user->forceFill(['locale' => $locale])->saveQuietly();
                } catch (Throwable) {
                    // Kolom locale belum dimigrasi; abaikan.
                }
            }
        }

        return $next($request);
    }
}
