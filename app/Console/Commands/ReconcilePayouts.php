<?php

namespace App\Console\Commands;

use App\Jobs\ReconcilePayout;
use App\Models\Payout;
use App\Services\Payments\RazorpayGateway;
use Illuminate\Console\Command;

class ReconcilePayouts extends Command
{
    protected $signature = 'payouts:reconcile
                            {--sync : Resolve inline instead of queueing the jobs}
                            {--dry-run : List what would be checked and exit}';

    protected $description = 'Ask RazorpayX for the final state of every payout still marked processing';

    public function handle(RazorpayGateway $gateway): int
    {
        $pending = Payout::where('status', 'processing')
            ->where('provider', 'razorpayx')
            ->whereNotNull('reference')
            ->get();

        if ($pending->isEmpty()) {
            $this->info('Nothing to reconcile.');

            return self::SUCCESS;
        }

        $this->info($pending->count() . ' payout(s) awaiting confirmation.');

        if (! $gateway->payoutsEnabled()) {
            $this->warn('RazorpayX is not configured — leaving them for finance to settle manually.');

            return self::SUCCESS;
        }

        foreach ($pending as $payout) {
            $this->line("  {$payout->uid} → {$payout->reference} (₹" . number_format($payout->amount) . ')');

            if ($this->option('dry-run')) {
                continue;
            }

            $this->option('sync')
                ? (new ReconcilePayout($payout->id))->handle($gateway)
                : ReconcilePayout::dispatch($payout->id);
        }

        if (! $this->option('dry-run')) {
            $this->info($this->option('sync') ? 'Reconciled inline.' : 'Queued for reconciliation.');
        }

        return self::SUCCESS;
    }
}
