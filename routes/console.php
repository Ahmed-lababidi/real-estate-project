<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('reservations:expire-apartments')->everyMinute();
Schedule::command('reservations:expire-farms')->everyMinute();
Schedule::command('reservations:expire-lands')->everyMinute();

Schedule::command('notifications:send-scheduled')->everyMinute();
