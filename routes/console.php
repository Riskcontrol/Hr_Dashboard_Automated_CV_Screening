<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Laravel 12 loads schedules from this route file.
\Illuminate\Support\Facades\Schedule::command('queue:work --stop-when-empty --timeout=50')
    ->everyMinute()->withoutOverlapping()->runInBackground();
\Illuminate\Support\Facades\Schedule::command('queue:prune-failed --hours=48')->daily();
\Illuminate\Support\Facades\Schedule::command('birthdays:remind')
    ->dailyAt('07:00')->timezone(config('app.timezone'));
