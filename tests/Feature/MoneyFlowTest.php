<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Creator;
use App\Models\Order;
use App\Models\Payout;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\DeliverySubmitted;
use App\Notifications\OrderPlaced;
use App\Notifications\PayoutReleased;
use App\Services\Payments\RazorpayGateway;
use Tests\RefreshesDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MoneyFlowTest extends TestCase
{
    use RefreshesDatabase;

    private function buyer(): User
    {
        $company = Company::create([
            'name' => 'Avante Studio', 'slug' => 'avante-' . uniqid(),
            'person_name' => 'Rohan', 'email' => 'rohan@example.com', 'is_active' => true,
        ]);

        return User::create([
            'name' => 'Rohan', 'email' => 'rohan' . uniqid() . '@example.com',
            'password' => Hash::make('secret123'), 'role' => 'business',
            'company_id' => $company->id, 'is_active' => true,
        ]);
    }

    private function gig(): Service
    {
        $creator = Creator::create([
            'name' => 'Priya', 'handle' => '@priya' . uniqid(), 'email' => 'priya' . uniqid() . '@example.com',
            'profile_type' => 'video_editor', 'price_from' => 2499, 'rating' => 4.9,
            'is_available' => true, 'is_verified' => true, 'upi_id' => 'priya@upi',
        ]);

        return Service::create([
            'creator_id' => $creator->id, 'title' => 'Launch reel', 'slug' => 'launch-reel-' . uniqid(),
            'price' => 2000, 'delivery_days' => 1, 'category' => 'Reel', 'is_active' => true, 'price_type' => 'paid',
        ]);
    }

    public function test_order_holds_money_in_demo_mode_and_notifies_both_sides(): void
    {
        Notification::fake();

        $user = $this->buyer();
        $gig  = $this->gig();

        $this->actingAs($user)->post('/orders', [
            'service_id' => $gig->id,
            'lane'       => 'standard',
            'brief'      => 'A launch reel for the new protein bar, founder on camera.',
        ])->assertRedirect();

        $order = Order::latest('id')->first();

        $this->assertSame('demo', $order->payment_provider);
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('held', $order->escrow_status);
        $this->assertSame(2000, $order->total);
        $this->assertSame(200, $order->fee, 'default platform fee is 10%');

        Notification::assertSentTimes(OrderPlaced::class, 2);   // buyer + freelancer
    }

    public function test_platform_fee_follows_the_admin_setting(): void
    {
        Setting::put('platform.fee_percent', 15);

        $user = $this->buyer();
        $gig  = $this->gig();

        $this->actingAs($user)->post('/orders', [
            'service_id' => $gig->id, 'lane' => 'standard', 'brief' => 'Fee should follow the setting.',
        ]);

        $this->assertSame(300, Order::latest('id')->first()->fee);
    }

    public function test_express_lane_is_priced_higher_than_standard(): void
    {
        $user = $this->buyer();
        $gig  = $this->gig();

        $this->actingAs($user)->post('/orders', [
            'service_id' => $gig->id, 'lane' => 'express', 'brief' => 'Express please, tonight if possible.',
        ]);

        $this->assertSame(3200, Order::latest('id')->first()->total);   // 2000 × 1.6
    }

    public function test_approval_releases_escrow_and_raises_exactly_one_payout(): void
    {
        Notification::fake();

        $user = $this->buyer();
        $gig  = $this->gig();

        $this->actingAs($user)->post('/orders', [
            'service_id' => $gig->id, 'lane' => 'standard', 'brief' => 'Payout should be raised on approval.',
        ]);

        $order = Order::latest('id')->first();

        $this->actingAs($user)->post("/orders/{$order->uid}/approve")->assertRedirect();
        $this->actingAs($user)->post("/orders/{$order->uid}/approve");   // double click

        $order->refresh();

        $this->assertSame('released', $order->escrow_status);
        $this->assertSame(1, Payout::where('order_id', $order->id)->count(), 'approving twice must not pay twice');

        $payout = Payout::first();
        $this->assertSame(1800, $payout->amount);       // 2000 − 200 fee
        $this->assertSame('priya@upi', $payout->destination);
        $this->assertSame($payout->uid, $order->payout_reference);

        Notification::assertSentTimes(PayoutReleased::class, 1);
    }

    public function test_freelancer_delivery_notifies_the_buyer(): void
    {
        Notification::fake();

        $user = $this->buyer();
        $gig  = $this->gig();

        $this->actingAs($user)->post('/orders', [
            'service_id' => $gig->id, 'lane' => 'standard', 'brief' => 'Delivery notification check.',
        ]);

        $order   = Order::latest('id')->first();
        $creator = $order->creator;

        $freelancer = User::create([
            'name' => $creator->name, 'email' => 'f' . uniqid() . '@example.com',
            'password' => Hash::make('secret123'), 'role' => 'creator',
            'creator_id' => $creator->id, 'is_active' => true,
        ]);

        $this->actingAs($freelancer)
            ->post("/creator/orders/{$order->uid}/deliver", ['delivery_url' => 'https://drive.google.com/file/x'])
            ->assertRedirect();

        $this->assertSame('review', $order->fresh()->status);
        Notification::assertSentTimes(DeliverySubmitted::class, 1);
    }

    public function test_another_workspace_cannot_open_the_order(): void
    {
        $user = $this->buyer();
        $gig  = $this->gig();

        $this->actingAs($user)->post('/orders', [
            'service_id' => $gig->id, 'lane' => 'standard', 'brief' => 'Authorisation boundary check.',
        ]);

        $order    = Order::latest('id')->first();
        $outsider = $this->buyer();

        $this->actingAs($outsider)->get("/orders/{$order->uid}")->assertForbidden();
    }

    public function test_payout_is_held_for_the_configured_window(): void
    {
        Setting::put('platform.escrow_hours', 72);

        $user = $this->buyer();
        $gig  = $this->gig();

        $this->actingAs($user)->post('/orders', [
            'service_id' => $gig->id, 'lane' => 'standard', 'brief' => 'Hold window should be respected.',
        ]);

        $order = Order::latest('id')->first();
        $this->actingAs($user)->post("/orders/{$order->uid}/approve");

        $payout = Payout::first();

        $this->assertFalse($payout->isReady(), 'a fresh payout is inside the hold window');
        $this->assertTrue($payout->available_at->greaterThan(now()->addHours(71)));
    }

    public function test_razorpay_signature_verification(): void
    {
        Setting::put('payments.razorpay.key_id', 'rzp_test_abc');
        Setting::put('payments.razorpay.key_secret', 'shhh_secret', true);

        $gateway = app(RazorpayGateway::class);

        $this->assertTrue($gateway->enabled());
        $this->assertSame('test', $gateway->mode());
        $this->assertFalse($gateway->payoutsEnabled(), 'payouts need a RazorpayX account number');

        $valid = hash_hmac('sha256', 'order_1|pay_1', 'shhh_secret');

        $this->assertTrue($gateway->verifyPaymentSignature('order_1', 'pay_1', $valid));
        $this->assertFalse($gateway->verifyPaymentSignature('order_1', 'pay_1', 'not-the-signature'));
    }

    public function test_enabling_razorpay_creates_a_gateway_order_at_checkout(): void
    {
        Http::fake(['api.razorpay.com/*' => Http::response(['id' => 'order_LIVE9', 'status' => 'created'], 200)]);

        Setting::put('payments.razorpay.key_id', 'rzp_test_abc');
        Setting::put('payments.razorpay.key_secret', 'shhh_secret', true);

        $user = $this->buyer();
        $gig  = $this->gig();

        $this->actingAs($user)->post('/orders', [
            'service_id' => $gig->id, 'lane' => 'standard', 'brief' => 'Should create a gateway order.',
        ]);

        $order = Order::latest('id')->first();

        $this->assertSame('razorpay', $order->payment_provider);
        $this->assertSame('order_LIVE9', $order->payment_order_id);
        $this->assertSame('unpaid', $order->payment_status, 'money is not held until the buyer pays');

        Http::assertSent(fn ($request) => str_contains($request->url(), '/orders')
            && $request['amount'] === 200000          // ₹2,000 in paise
            && $request['currency'] === 'INR'
            && $request['receipt'] === $order->uid);
    }

    public function test_webhook_rejects_an_unsigned_payload(): void
    {
        Setting::put('payments.razorpay.webhook_secret', 'hook_secret', true);

        $this->postJson('/webhooks/razorpay', ['event' => 'payment.captured'])->assertStatus(400);
    }

    public function test_webhook_marks_the_order_paid_when_signed(): void
    {
        // never touch the network from a test
        Http::fake(['api.razorpay.com/*' => Http::response(['id' => 'order_TEST1', 'status' => 'created'], 200)]);

        Setting::put('payments.razorpay.key_id', 'rzp_test_abc');
        Setting::put('payments.razorpay.key_secret', 'shhh_secret', true);
        Setting::put('payments.razorpay.webhook_secret', 'hook_secret', true);

        $user = $this->buyer();
        $gig  = $this->gig();

        $this->actingAs($user)->post('/orders', [
            'service_id' => $gig->id, 'lane' => 'standard', 'brief' => 'Webhook should capture this order.',
        ]);

        $order = Order::latest('id')->first();
        $order->forceFill(['payment_provider' => 'razorpay', 'payment_status' => 'unpaid'])->save();

        $payload = json_encode([
            'event'   => 'payment.captured',
            'payload' => ['payment' => ['entity' => ['id' => 'pay_hook1', 'notes' => ['order_uid' => $order->uid]]]],
        ]);

        $request = \Illuminate\Http\Request::create(
            '/webhooks/razorpay', 'POST', [], [], [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_X_RAZORPAY_SIGNATURE' => hash_hmac('sha256', $payload, 'hook_secret')],
            $payload
        );

        $response = app(\App\Http\Controllers\OrderController::class)
            ->webhook($request, app(RazorpayGateway::class));

        $this->assertSame(200, $response->getStatusCode());

        $order->refresh();

        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('pay_hook1', $order->payment_id);
        $this->assertSame('held', $order->escrow_status);
    }
}
