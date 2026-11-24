<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('pemesanan:expire')->everyMinute()->withoutOverlapping();
Schedule::command('tiket:auto-checkout')->hourly()->withoutOverlapping();
// Ulangi terjemahan otomatis isian pengelola yang tadinya gagal (kuota habis / layanan down).
Schedule::command('translate:fill')->dailyAt('03:00')->withoutOverlapping();

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
