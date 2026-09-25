<?php

namespace App\Jobs;

use App\Models\Payout;
use App\Notifications\PayoutReleased;
use App\Services\Payments\RazorpayGateway;
use App\Support\Notifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Ask RazorpayX what actually happened to a payout we submitted.
 *
 * A payout sits in `processing` from the moment it is queued with the provider.
 * Nothing moved it out of that state before this job existed, so a reversal or a
 * silent failure looked identical to money on its way. This closes that gap and
 * keeps re-checking with a widening delay until the payout reaches a final state.
 */
class ReconcilePayout implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Stop polling after this many rounds (~11 hours with the backoff below). */
    public const MAX_ROUNDS = 10;

    public int $tries = 2;

    public function __construct(public int $payoutId, public int $round = 0) {}

    public function handle(RazorpayGateway $gateway): void
    {
        $payout = Payout::with('creator')->find($this->payoutId);

        if (! $payout || $payout->status !== 'processing' || ! $payout->reference) {
            return;
        }

        if (! $gateway->payoutsEnabled()) {
            return;   // manual/demo transfers are settled by finance, not by us
        }

        try {
            $remote = $gateway->fetchPayout($payout->reference);
        } catch (\Throwable $e) {
            Log::warning('payout.reconcile.failed', ['payout' => $payout->uid, 'error' => $e->getMessage()]);
            $this->again();

            return;
        }

        $status = $gateway->mapPayoutStatus($remote['status'] ?? null);

        if ($status === 'processing') {
            $this->again();

            return;
        }

        $payout->forceFill([
            'status'       => $status,
            'reference'    => $remote['utr'] ?: $payout->reference,
            'processed_at' => now(),
            'notes'        => $status === 'failed'
                ? Str::limit((string) ($remote['failure_reason'] ?? $remote['status_details']['description'] ?? 'Reversed by RazorpayX'), 250)
                : $payout->notes,
        ])->save();

        Log::info('payout.reconciled', ['payout' => $payout->uid, 'status' => $status]);

        if ($status === 'paid') {
            Notifier::toFreelancer($payout->creator, new PayoutReleased($payout));
        }
    }

    private function again(): void
    {
        if ($this->round >= self::MAX_ROUNDS) {
            Log::error('payout.reconcile.gave_up', ['payout_id' => $this->payoutId]);

            return;
        }

        self::dispatch($this->payoutId, $this->round + 1)
            ->delay(now()->addMinutes(min(240, 5 * (2 ** $this->round))));
    }
}
