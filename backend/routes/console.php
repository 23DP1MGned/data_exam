<?php

use App\Services\SessionTemplateService;
use App\Services\NotificationService;
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

Artisan::command('notifications:sync-parent-payments', function () {
    app(NotificationService::class)->syncParentPaymentNotifications();

    $this->comment('Parent payment notifications synchronized.');
})->purpose('Create payment due and overdue notifications for parents');

Schedule::command('sessions:sync-statuses')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('sessions:generate-upcoming')
    ->hourly()
    ->withoutOverlapping();

Schedule::command('notifications:sync-parent-payments')
    ->hourly()
    ->withoutOverlapping();
