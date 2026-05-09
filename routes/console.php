<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily at midnight - update top musicians cache
Schedule::command('musicians:update-top-plays')->dailyAt('00:00');

// Recalculate rankings every 15 minutes based on real-time plays
Schedule::command('rankings:recalculate')->everyFifteenMinutes();

// Daily at 00:05 - purge concerts whose date has already passed
Schedule::command('concerts:purge-expired')->dailyAt('00:05');

