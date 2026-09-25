<?php

namespace Tests\Feature;

use App\Jobs\ReconcilePayout;
use App\Models\Creator;
use App\Models\Payout;
use App\Models\Setting;
use App\Notifications\PayoutReleased;
use App\Services\Payments\RazorpayGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\RefreshesDatabase;
use Tests\TestCase;

class PayoutReconciliationTest extends TestCase
{
    use RefreshesDatabase;

    private function configureRazorpayX(): void
    {
        Setting::put('payments.razorpay.key_id', 'rzp_test_abc');
        Setting::put('payments.razorpay.key_secret', 'secret', true);
        Setting::put('payments.razorpayx.account_number', '2323230000000001');
    }

    private function processingPayout(array $overrides = []): Payout
    {
        $creator = Creator::create([
            'name' => 'Priya', 'handle' => '@priya' . uniqid(), 'email' => 'priya' . uniqid() . '@example.com',
            'profile_type' => 'video_editor', 'is_available' => true, 'is_verified' => true, 'upi_id' => 'priya@upi',
        ]);

        return Payout::create(array_merge([
            'creator_id'  => $creator->id,
            'gross'       => 2000,
            'fee'         => 200,
            'amount'      => 1800,
            'status'      => 'processing',
            'provider'    => 'razorpayx',
            'reference'   => 'pout_ABC123',
            'destination' => 'priya@upi',
        ], $overrides));
    }

    public function test_a_processed_payout_is_marked_paid_and_the_utr_is_stored(): void
    {
        Notification::fake();
        $this->configureRazorpayX();

        Http::fake(['api.razorpay.com/v1/payouts/pout_ABC123' => Http::response([
            'id' => 'pout_ABC123', 'status' => 'processed', 'utr' => 'UTR778899',
        ])]);

        $payout = $this->processingPayout();

        (new ReconcilePayout($payout->id))->handle(app(RazorpayGateway::class));

        $payout->refresh();

        $this->assertSame('paid', $payout->status);
        $this->assertSame('UTR778899', $payout->reference);
        $this->assertNotNull($payout->processed_at);

        Notification::assertSentTimes(PayoutReleased::class, 1);
    }

    public function test_a_reversal_moves_the_payout_to_failed_with_the_reason(): void
    {
        Notification::fake();
        $this->configureRazorpayX();

        Http::fake(['api.razorpay.com/v1/payouts/pout_ABC123' => Http::response([
            'id' => 'pout_ABC123', 'status' => 'reversed', 'failure_reason' => 'Beneficiary VPA invalid',
        ])]);

        $payout = $this->processingPayout();

        (new ReconcilePayout($payout->id))->handle(app(RazorpayGateway::class));

        $payout->refresh();

        $this->assertSame('failed', $payout->status);
        $this->assertStringContainsString('Beneficiary VPA invalid', (string) $payout->notes);

        Notification::assertNothingSent();
    }

    public function test_a_payout_still_queued_at_the_provider_stays_processing(): void
    {
        $this->configureRazorpayX();

        Http::fake(['api.razorpay.com/v1/payouts/pout_ABC123' => Http::response([
            'id' => 'pout_ABC123', 'status' => 'queued',
        ])]);

        $payout = $this->processingPayout();

        (new ReconcilePayout($payout->id))->handle(app(RazorpayGateway::class));

        $this->assertSame('processing', $payout->fresh()->status);
    }

    public function test_settled_payouts_are_never_touched_again(): void
    {
        $this->configureRazorpayX();
        Http::fake();

        $payout = $this->processingPayout(['status' => 'paid', 'reference' => 'UTR1']);

        (new ReconcilePayout($payout->id))->handle(app(RazorpayGateway::class));

        $this->assertSame('UTR1', $payout->fresh()->reference);
        Http::assertNothingSent();
    }

    public function test_manual_transfers_are_left_for_finance(): void
    {
        Http::fake();   // RazorpayX deliberately not configured

        $payout = $this->processingPayout(['provider' => 'manual']);

        (new ReconcilePayout($payout->id))->handle(app(RazorpayGateway::class));

        $this->assertSame('processing', $payout->fresh()->status);
        Http::assertNothingSent();
    }

    public function test_the_status_map_covers_every_state_razorpayx_reports(): void
    {
        $gateway = app(RazorpayGateway::class);

        $this->assertSame('paid', $gateway->mapPayoutStatus('processed'));
        $this->assertSame('failed', $gateway->mapPayoutStatus('reversed'));
        $this->assertSame('failed', $gateway->mapPayoutStatus('cancelled'));
        $this->assertSame('failed', $gateway->mapPayoutStatus('rejected'));
        $this->assertSame('processing', $gateway->mapPayoutStatus('queued'));
        $this->assertSame('processing', $gateway->mapPayoutStatus(null));
    }

    public function test_the_reconcile_command_only_picks_up_provider_payouts_in_flight(): void
    {
        $this->configureRazorpayX();

        Http::fake(['api.razorpay.com/v1/payouts/*' => Http::response(['id' => 'pout_ABC123', 'status' => 'processed', 'utr' => 'UTRX'])]);

        $inFlight = $this->processingPayout();
        $manual   = $this->processingPayout(['provider' => 'manual', 'reference' => 'MANUAL-1']);
        $done     = $this->processingPayout(['status' => 'paid', 'reference' => 'pout_DONE']);

        $this->artisan('payouts:reconcile --sync')->assertSuccessful();

        $this->assertSame('paid', $inFlight->fresh()->status);
        $this->assertSame('processing', $manual->fresh()->status);
        $this->assertSame('paid', $done->fresh()->status);
    }
}
