<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// The command itself checks each source site's configured polling interval,
// so running this every 15 minutes is just the scheduler's own resolution.
Schedule::command('veille:run')->everyFifteenMinutes()->withoutOverlapping();

Schedule::command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();
