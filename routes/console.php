<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('contracts:send-expiry-reminders')->dailyAt('08:00');

// تذكيرات الموافقات
Schedule::job(new \App\Jobs\SendApprovalReminders)->everyFourHours();

Schedule::command('leave:accrue-monthly')->monthlyOn(1, '01:00');
Schedule::job(new \App\Jobs\SendDocumentExpiryReminders)->dailyAt('07:30');
Schedule::job(new \App\Jobs\RunScheduledReports)->dailyAt('06:00');

// انتهاء التفويضات التلقائي
Schedule::call(function () {
    \App\Models\ApprovalDelegation::where('status', 'active')
        ->where('end_date', '<', now())
        ->update(['status' => 'expired']);
})->dailyAt('00:00');
