<?php

namespace App\Providers;

use App\Http\Middleware\EnsureExplicitLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useTailwind();

        Event::listen(Login::class, function (Login $event): void {
            if ($event->guard === 'web' && !$event->remember && request()->hasSession()) {
                request()->session()->put(EnsureExplicitLogin::SESSION_KEY, (string) $event->user->getAuthIdentifier());
            }
        });

        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https' || str_contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
