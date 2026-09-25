<?php

namespace App\Services;

use App\Models\Payout;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Thin RazorpayX client — no SDK required (Hostinger shared safe, uses Laravel HTTP client).
 *
 * Config lives in config/services.php → services.razorpayx
 * When credentials are absent the service runs in "sandbox" mode and simulates
 * a successful payout so local/demo environments keep working.
 */
class RazorpayXService
{
    private const BASE = 'https://api.razorpay.com/v1';

    public function __construct(
        private ?string $key = null,
        private ?string $secret = null,
        private ?string $accountNumber = null,
    ) {
        $this->key           ??= config('services.razorpayx.key');
        $this->secret        ??= config('services.razorpayx.secret');
        $this->accountNumber ??= config('services.razorpayx.account_number');
    }

    public function configured(): bool
    {
        return filled($this->key) && filled($this->secret) && filled($this->accountNumber)
            && ! str_contains((string) $this->key, 'xxx');
    }

    /**
     * Create a UPI payout. Idempotent via the payout reference header.
     *
     * @return array{id:?string,status:string,utr:?string}
     */
    public function createPayout(Payout $payout): array
    {
        $upi = $payout->upi_id ?: $payout->creator->upi_id ?? null;

        if (blank($upi)) {
            throw new RuntimeException('Creator has no UPI ID on file.');
        }

        if (! $this->configured()) {
            Log::warning('RazorpayX not configured — simulating payout', ['payout' => $payout->id]);

            return ['id' => 'sim_'.$payout->reference, 'status' => Payout::STATUS_PROCESSING, 'utr' => null];
        }

        $response = Http::withBasicAuth($this->key, $this->secret)
            ->withHeaders(['X-Payout-Idempotency' => $payout->reference])
            ->timeout(20)
            ->retry(2, 500)
            ->post(self::BASE.'/payouts', [
                'account_number' => $this->accountNumber,
                'amount'         => $payout->amount * 100, // paise
                'currency'       => 'INR',
                'mode'           => 'UPI',
                'purpose'        => 'payout',
                'queue_if_low_balance' => true,
                'reference_id'   => $payout->reference,
                'narration'      => 'QuickContent payout '.$payout->reference,
                'fund_account'   => [
                    'account_type' => 'vpa',
                    'vpa'          => ['address' => $upi],
                    'contact'      => [
                        'name'    => $payout->creator->name ?? 'Creator',
                        'email'   => $payout->creator->email ?? null,
                        'contact' => $payout->creator->phone ?? null,
                        'type'    => 'vendor',
                    ],
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException($response->json('error.description') ?? 'RazorpayX request failed ('.$response->status().')');
        }

        return [
            'id'     => $response->json('id'),
            'status' => $this->mapStatus($response->json('status')),
            'utr'    => $response->json('utr'),
        ];
    }

    /** Poll a single payout for reconciliation. */
    public function fetchPayout(string $payoutId): array
    {
        if (! $this->configured() || str_starts_with($payoutId, 'sim_')) {
            return ['id' => $payoutId, 'status' => Payout::STATUS_PAID, 'utr' => 'SIMUTR'.substr($payoutId, -6)];
        }

        $response = Http::withBasicAuth($this->key, $this->secret)
            ->timeout(20)
            ->get(self::BASE.'/payouts/'.$payoutId);

        if ($response->failed()) {
            throw new RuntimeException('RazorpayX fetch failed ('.$response->status().')');
        }

        return [
            'id'     => $response->json('id'),
            'status' => $this->mapStatus($response->json('status')),
            'utr'    => $response->json('utr'),
            'failure_reason' => $response->json('failure_reason'),
        ];
    }

    /** RazorpayX status → our payout status. */
    public function mapStatus(?string $status): string
    {
        return match ($status) {
            'processed'                       => Payout::STATUS_PAID,
            'reversed', 'cancelled', 'rejected', 'failed' => Payout::STATUS_FAILED,
            default                           => Payout::STATUS_PROCESSING,
        };
    }

    /** Verify an inbound webhook signature. */
    public function verifyWebhook(string $rawBody, ?string $signature): bool
    {
        $secret = config('services.razorpayx.webhook_secret');

        if (blank($secret) || blank($signature)) {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $rawBody, $secret), $signature);
    }
}
