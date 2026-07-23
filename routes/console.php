<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('kurti:send-reminders')
    ->dailyAt(config('kurti.reminder.time'))
    ->timezone(config('kurti.reminder.timezone'))
    ->when(fn () => config('kurti.reminder.enabled'))
    ->withoutOverlapping();
