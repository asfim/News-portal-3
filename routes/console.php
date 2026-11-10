<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('news:publish-scheduled')->everyMinute()->withoutOverlapping();
Schedule::command('news:import-feeds')->everyFiveMinutes()->withoutOverlapping();
Schedule::command('news:send-daily-digest')->dailyAt('07:00')->withoutOverlapping();
