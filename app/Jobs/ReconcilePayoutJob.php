<?php

namespace App\Jobs;

use App\Models\Payout;
use App\Services\RazorpayXService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Polls RazorpayX for the final state of a submitted payout.
 * Re-queues itself with backoff until the payout reaches a terminal state
 * or the attempt budget runs out (database queue — no Redis needed).
 */
class ReconcilePayoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public const MAX_POLLS = 12;

    public function __construct(public int $payoutId) {}

    public function handle(RazorpayXService $razorpayx): void
    {
        $payout = Payout::find($this->payoutId);

        if (! $payout || $payout->isFinal() || blank($payout->payout_id)) {
            return;
        }

        try {
            $remote = $razorpayx->fetchPayout($payout->payout_id);
        } catch (\Throwable $e) {
            Log::warning('Payout reconciliation failed', ['payout' => $payout->id, 'error' => $e->getMessage()]);
            $this->requeue($payout);

            return;
        }

        $payout->forceFill([
            'status'         => $remote['status'],
            'utr'            => $remote['utr'] ?? $payout->utr,
            'failure_reason' => $remote['failure_reason'] ?? null,
            'paid_at'        => $remote['status'] === Payout::STATUS_PAID ? now() : null,
            'poll_attempts'  => $payout->poll_attempts + 1,
        ])->save();

        if (! $payout->isFinal()) {
            $this->requeue($payout);
        }
    }

    private function requeue(Payout $payout): void
    {
        if ($payout->poll_attempts >= self::MAX_POLLS) {
            Log::error('Payout reconciliation gave up', ['payout' => $payout->id]);

            return;
        }

        $payout->increment('poll_attempts');

        self::dispatch($payout->id)->delay(now()->addMinutes(min(30, 2 ** $payout->poll_attempts)));
    }
}
