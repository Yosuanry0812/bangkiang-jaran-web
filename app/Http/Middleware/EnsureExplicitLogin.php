<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureExplicitLogin
{
    public const SESSION_KEY = 'auth.explicit_user_id';

    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('web');

        // Legacy sessions and remember cookies must not silently restore a login.
        if ($guard->check() && ($guard->viaRemember()
            || $request->session()->get(self::SESSION_KEY) !== (string) $guard->id())) {
            $locale = $request->session()->get('locale');
            $guard->logoutCurrentDevice();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($locale !== null) {
                $request->session()->put('locale', $locale);
            }
        }

        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-store, private');

        return $response;
    }
}
