<?php

namespace App\Providers;

<<<<<<< HEAD
class RouteServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';
=======
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/redirect-after-login';
>>>>>>> 4fc3119610d2876abe1ba0e13bd601a6b63bd54e
}
