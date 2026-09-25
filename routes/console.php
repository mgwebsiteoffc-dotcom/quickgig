<?php
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
Artisan::command('inspire', function () { $this->comment(Inspiring::quote()); })->purpose('Display an inspiring quote');
Schedule::command('payouts:reconcile')->everyFiveMinutes()->withoutOverlapping();
Schedule::command('content:publish-scheduled')->everyMinute()->withoutOverlapping();
Schedule::call(fn () => cache()->forget('quickgig:sitemap'))->daily()->name('refresh-sitemap');
