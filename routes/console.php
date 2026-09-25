<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () { $this->comment(Inspiring::quote()); })->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduler — Hostinger shared hosting
|--------------------------------------------------------------------------
| Add ONE cron entry in hPanel:
|   * * * * * /usr/bin/php /home/uXXXXXXXX/domains/yourdomain.com/artisan schedule:run >> /dev/null 2>&1
| Everything below is driven by that single tick.
*/

// Escrow holds → ready, and re-poll RazorpayX for pending payouts.
Schedule::command('payouts:release')
    ->hourly()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/payouts.log'));

// Database queue worker — drains and exits, so it is safe under cron.
Schedule::command('queue:work --stop-when-empty --tries=3 --max-time=50')
    ->everyMinute()
    ->withoutOverlapping();

// Housekeeping.
Schedule::command('queue:prune-failed --hours=168')->daily();
Schedule::command('cache:prune-stale-tags')->hourly();
