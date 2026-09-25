<?php

namespace Tests\Feature;

use App\Jobs\ReconcilePayoutJob;
use App\Models\Payout;
use App\Services\RazorpayXService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PayoutReconciliationTest extends TestCase
{
    use RefreshDatabase;

    private function configure(): void
    {
        config([
            'services.razorpayx.key'            => 'rzp_test_key',
            'services.razorpayx.secret'         => 'secret',
            'services.razorpayx.account_number' => '2323230000000000',
            'services.razorpayx.webhook_secret' => 'whsec',
        ]);
    }

    private function payout(array $overrides = []): Payout
    {
        $order = $this->makeOrder();

        return Payout::create(array_merge([
            'creator_id' => $order->creator_id,
            'order_id'   => $order->id,
            'amount'     => 945,
            'upi_id'     => 'tc@upi',
            'status'     => Payout::STATUS_READY,
        ], $overrides));
    }

    public function test_a_payout_gets_a_unique_idempotency_reference(): void
    {
        $a = $this->payout();
        $b = $this->payout();

        $this->assertNotNull($a->reference);
        $this->assertNotSame($a->reference, $b->reference);
    }

    public function test_marking_paid_calls_razorpayx_and_stores_the_payout_id(): void
    {
        $this->configure();

        Http::fake(['api.razorpay.com/v1/payouts' => Http::response(['id' => 'pout_123', 'status' => 'processing'], 200)]);

        $payout = $this->payout();

        $this->actingAs($this->admin('finance'))
            ->post('/admin/payouts/'.$payout->id.'/paid')
            ->assertRedirect();

        $payout->refresh();

        $this->assertSame('pout_123', $payout->payout_id);
        $this->assertSame(Payout::STATUS_PROCESSING, $payout->status);

        Http::assertSent(fn ($request) => $request->hasHeader('X-Payout-Idempotency', $payout->reference)
            && $request['amount'] === 94500);
    }

    public function test_a_failed_razorpayx_call_marks_the_payout_failed(): void
    {
        $this->configure();

        Http::fake(['api.razorpay.com/*' => Http::response(['error' => ['description' => 'Insufficient balance']], 400)]);

        $payout = $this->payout();

        $this->actingAs($this->admin('finance'))->post('/admin/payouts/'.$payout->id.'/paid');

        $payout->refresh();

        $this->assertSame(Payout::STATUS_FAILED, $payout->status);
        $this->assertStringContainsString('Insufficient balance', $payout->failure_reason);
    }

    public function test_reconciliation_job_marks_a_processed_payout_as_paid(): void
    {
        $this->configure();

        Http::fake(['api.razorpay.com/v1/payouts/pout_123' => Http::response(['id' => 'pout_123', 'status' => 'processed', 'utr' => 'UTR999'], 200)]);

        $payout = $this->payout(['status' => Payout::STATUS_PROCESSING, 'payout_id' => 'pout_123']);

        (new ReconcilePayoutJob($payout->id))->handle(app(RazorpayXService::class));

        $payout->refresh();

        $this->assertSame(Payout::STATUS_PAID, $payout->status);
        $this->assertSame('UTR999', $payout->utr);
        $this->assertNotNull($payout->paid_at);
    }

    public function test_release_command_moves_elapsed_holds_to_ready(): void
    {
        $due    = $this->payout(['status' => Payout::STATUS_HOLD, 'hold_until' => now()->subHour()]);
        $notDue = $this->payout(['status' => Payout::STATUS_HOLD, 'hold_until' => now()->addDay()]);

        $this->artisan('payouts:release')->assertSuccessful();

        $this->assertSame(Payout::STATUS_READY, $due->fresh()->status);
        $this->assertSame(Payout::STATUS_HOLD, $notDue->fresh()->status);
    }

    public function test_webhook_rejects_a_bad_signature(): void
    {
        $this->configure();

        $this->postJson('/webhooks/razorpayx', ['payload' => ['payout' => ['entity' => ['id' => 'pout_123']]]])
            ->assertStatus(401);
    }

    public function test_webhook_updates_the_payout_when_the_signature_is_valid(): void
    {
        $this->configure();

        $payout = $this->payout(['status' => Payout::STATUS_PROCESSING, 'payout_id' => 'pout_123']);

        $body = json_encode([
            'event'   => 'payout.processed',
            'payload' => ['payout' => ['entity' => ['id' => 'pout_123', 'status' => 'processed', 'utr' => 'UTR123']]],
        ]);

        $this->call('POST', '/webhooks/razorpayx', [], [], [], [
            'CONTENT_TYPE'            => 'application/json',
            'HTTP_X_RAZORPAY_SIGNATURE' => hash_hmac('sha256', $body, 'whsec'),
        ], $body)->assertOk();

        $payout->refresh();

        $this->assertSame(Payout::STATUS_PAID, $payout->status);
        $this->assertSame('UTR123', $payout->utr);
    }
}
