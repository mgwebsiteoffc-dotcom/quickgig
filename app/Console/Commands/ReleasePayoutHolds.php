<?php

namespace App\Console\Commands;

use App\Jobs\ReconcilePayoutJob;
use App\Models\Payout;
use Illuminate\Console\Command;

class ReleasePayoutHolds extends Command
{
    protected $signature = 'payouts:release {--dry-run : Report what would change without writing}';

    protected $description = 'Move payouts whose escrow hold has elapsed from hold → ready, and re-poll pending RazorpayX payouts';

    public function handle(): int
    {
        $releasable = Payout::releasable()->get();

        $this->info($releasable->count().' payout(s) eligible for release.');

        if (! $this->option('dry-run')) {
            foreach ($releasable as $payout) {
                $payout->update(['status' => Payout::STATUS_READY]);
                $this->line("  #{$payout->id} → ready (₹{$payout->amount})");
            }
        }

        $pending = Payout::processing()->whereNotNull('payout_id')->get();

        $this->info($pending->count().' payout(s) awaiting reconciliation.');

        if (! $this->option('dry-run')) {
            foreach ($pending as $payout) {
                ReconcilePayoutJob::dispatch($payout->id);
            }
        }

        return self::SUCCESS;
    }
}
