<?php

use App\Models\Skill;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () { $this->comment(Inspiring::quote()); })->purpose('Display an inspiring quote');

Artisan::command('skills:recount', function () {
    Skill::recount();
    $this->info('Skill usage counts rebuilt.');
})->purpose('Rebuild the usage counter on every skill');

/*
|--------------------------------------------------------------------------
| Schedule
|--------------------------------------------------------------------------
| One cron entry drives all of this:
|
|   * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
|
| QUEUE_CONNECTION=database, so the worker is scheduled here too rather than
| needing a supervisor process — it drains the queue and exits every minute.
*/

// Confirm with RazorpayX what happened to payouts still marked processing.
Schedule::command('payouts:reconcile')
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/payouts.log'));

// Drain the database queue. --max-time keeps it inside the minute.
Schedule::command('queue:work --stop-when-empty --tries=3 --max-time=55')
    ->everyMinute()
    ->withoutOverlapping();

// Roll subscription credits on the renewal date.
Schedule::command('subscriptions:roll')->dailyAt('00:10');

// Housekeeping.
Schedule::command('skills:recount')->dailyAt('02:00');
Schedule::command('queue:prune-failed --hours=336')->weekly();
Schedule::command('auth:clear-resets')->daily();
