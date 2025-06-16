<?php

use App\Console\Commands\SendReminderEmails;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(SendReminderEmails::class)->everyMinute()->withoutOverlapping(5)->runInBackground();
