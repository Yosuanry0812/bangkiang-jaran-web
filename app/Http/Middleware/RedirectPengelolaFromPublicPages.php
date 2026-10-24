<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectPengelolaFromPublicPages
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role === 'pengelola') {
            return redirect()->route('pengelola.dashboard');
        }

        return $next($request);
    }
}
