<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/**
 * Razorpay escrow-style checkout.
 *
 * Keys come from the admin console first (encrypted in the settings table) and
 * fall back to .env. With no keys configured the platform stays in demo mode:
 * orders are marked held without money moving, so the product still works.
 */
class RazorpayGateway
{
    public const API = 'https://api.razorpay.com/v1';

    public function enabled(): bool
    {
        return filled($this->keyId()) && filled($this->keySecret());
    }

    public function keyId(): ?string
    {
        return setting('payments.razorpay.key_id') ?: env('RAZORPAY_KEY');
    }

    private function keySecret(): ?string
    {
        return setting('payments.razorpay.key_secret') ?: env('RAZORPAY_SECRET');
    }

    private function webhookSecret(): ?string
    {
        return setting('payments.razorpay.webhook_secret') ?: env('RAZORPAY_WEBHOOK_SECRET');
    }

    public function mode(): string
    {
        return Str::startsWith((string) $this->keyId(), 'rzp_live') ? 'live' : 'test';
    }

    /** Create the gateway order that the checkout widget opens. */
    public function createOrder(Order $order): array
    {
        $this->assertEnabled();

        $response = $this->request('post', '/orders', [
            'amount'   => $order->total * 100,          // paise
            'currency' => 'INR',
            'receipt'  => $order->uid,
            'notes'    => [
                'order_uid' => $order->uid,
                'company'   => (string) ($order->company->name ?? ''),
                'gig'       => (string) ($order->service->title ?? ''),
            ],
        ]);

        $order->forceFill([
            'payment_provider' => 'razorpay',
            'payment_order_id' => $response['id'] ?? null,
            'payment_status'   => 'unpaid',
        ])->save();

        return $response;
    }

    /** Checkout callback: confirm the signature really came from Razorpay. */
    public function verifyPaymentSignature(string $razorpayOrderId, string $paymentId, string $signature): bool
    {
        $expected = hash_hmac('sha256', $razorpayOrderId . '|' . $paymentId, (string) $this->keySecret());

        return hash_equals($expected, $signature);
    }

    public function verifyWebhookSignature(string $payload, ?string $signature): bool
    {
        if (! filled($this->webhookSecret()) || ! filled($signature)) {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $payload, (string) $this->webhookSecret()), $signature);
    }

    public function fetchPayment(string $paymentId): array
    {
        $this->assertEnabled();

        return $this->request('get', '/payments/' . $paymentId);
    }

    /** RazorpayX payouts need a funding account on top of the checkout keys. */
    public function payoutsEnabled(): bool
    {
        return $this->enabled() && filled(setting('payments.razorpayx.account_number'));
    }

    /**
     * Push a payout through RazorpayX. Falls over loudly so the admin screen can
     * mark the row failed rather than silently pretending it went out.
     */
    public function createPayout(\App\Models\Payout $payout): array
    {
        if (! $this->payoutsEnabled()) {
            throw new RuntimeException('RazorpayX is not configured — add the funding account number in Settings.');
        }

        $creator = $payout->creator;
        $upi     = $payout->destination ?: $creator?->upi_id;

        if (! $upi) {
            throw new RuntimeException('This freelancer has no UPI id on file.');
        }

        return $this->request('post', '/payouts', [
            'account_number'        => setting('payments.razorpayx.account_number'),
            'amount'                => $payout->amount * 100,
            'currency'              => 'INR',
            'mode'                  => 'UPI',
            'purpose'               => 'payout',
            'queue_if_low_balance'  => true,
            'reference_id'          => $payout->uid,
            'narration'             => 'Quick GIGS payout',
            'fund_account'          => [
                'account_type' => 'vpa',
                'vpa'          => ['address' => $upi],
                'contact'      => [
                    'name'  => $creator->name ?? 'Freelancer',
                    'email' => $creator->email ?? null,
                    'type'  => 'vendor',
                ],
            ],
        ]);
    }

    /** Release from escrow. Refunds are real; payouts need RazorpayX and are recorded as intents. */
    public function refund(Order $order, ?int $amount = null): array
    {
        $this->assertEnabled();

        if (! $order->payment_id) {
            throw new RuntimeException('This order has no captured payment to refund.');
        }

        return $this->request('post', '/payments/' . $order->payment_id . '/refund', array_filter([
            'amount' => $amount ? $amount * 100 : null,
            'notes'  => ['order_uid' => $order->uid],
        ]));
    }

    /** Cheap credential check for the admin console. */
    public function ping(): array
    {
        $this->assertEnabled();

        $started = microtime(true);
        $this->request('get', '/payments?count=1');

        return [
            'ok'         => true,
            'mode'       => $this->mode(),
            'key_id'     => $this->keyId(),
            'latency_ms' => (int) round((microtime(true) - $started) * 1000),
        ];
    }

    /* ───────────────────────── internals ───────────────────────── */

    private function assertEnabled(): void
    {
        if (! $this->enabled()) {
            throw new RuntimeException('Razorpay is not configured. Add the key id and secret in Settings.');
        }
    }

    private function base(): string
    {
        return rtrim((string) (setting('payments.razorpay.base_url') ?: env('RAZORPAY_BASE_URL', self::API)), '/');
    }

    private function request(string $method, string $path, array $payload = []): array
    {
        try {
            $request = Http::withBasicAuth((string) $this->keyId(), (string) $this->keySecret())
                ->timeout(25)
                ->retry(2, 400, fn ($e) => $this->retryable($e), throw: false);

            $response = $method === 'get'
                ? $request->get($this->base() . $path)
                : $request->post($this->base() . $path, $payload);
        } catch (Throwable $e) {
            throw new RuntimeException('Razorpay request failed: ' . $e->getMessage());
        }

        if ($response->failed()) {
            $message = data_get($response->json(), 'error.description') ?? Str::limit($response->body(), 180);
            Log::warning('razorpay.failed', ['status' => $response->status(), 'path' => $path]);

            throw new RuntimeException('Razorpay HTTP ' . $response->status() . ': ' . $message);
        }

        return (array) $response->json();
    }

    private function retryable($exception): bool
    {
        $status = $exception instanceof \Illuminate\Http\Client\RequestException ? $exception->response->status() : null;

        return $status === null || $status === 429 || $status >= 500;
    }
}
