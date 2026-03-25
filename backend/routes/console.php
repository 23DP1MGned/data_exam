<?php

use App\Services\SessionTemplateService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('sessions:sync-statuses', function () {
    app(SessionTemplateService::class)->syncAutomaticSessionStatuses();

    $this->comment('Session statuses synchronized.');
})->purpose('Automatically mark finished planned sessions as completed');

Artisan::command('sessions:generate-upcoming', function () {
    app(SessionTemplateService::class)->ensureUpcomingSessionsGenerated();

    $this->comment('Upcoming sessions generated.');
})->purpose('Generate upcoming recurring session instances for active templates');

Schedule::command('sessions:sync-statuses')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('sessions:generate-upcoming')
    ->hourly()
    ->withoutOverlapping();
