<?php

namespace App\Providers;

use App\Http\Middleware\EnsureExplicitLogin;
use App\Services\DestinationSettings;
use App\Support\WindowsProcessDriver;
use Illuminate\Auth\Events\Login;
use Illuminate\Concurrency\ConcurrencyServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Process\Factory as ProcessFactory;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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
        $this->app->scoped(DestinationSettings::class, fn () => new DestinationSettings);
        $this->app->register(ConcurrencyServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useTailwind();
        View::composer(['layouts.app', 'wisatawan.landing', 'wisatawan.eticket-pdf', 'wisatawan.pembayaran'], function ($view) {
            $view->with('destination', app(DestinationSettings::class));
        });

        if (PHP_OS_FAMILY === 'Windows') {
            Concurrency::extend('process', fn ($app) => new WindowsProcessDriver($app->make(ProcessFactory::class)));
        }

        Event::listen(Login::class, function (Login $event): void {
            if ($event->guard === 'web' && ! $event->remember && request()->hasSession()) {
                request()->session()->put(EnsureExplicitLogin::SESSION_KEY, (string) $event->user->getAuthIdentifier());
            }
        });

        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https' || str_contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
