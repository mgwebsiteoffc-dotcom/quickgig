<?php

namespace App\Http\Controllers;

use App\Models\Payout;
use App\Services\RazorpayXService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * RazorpayX payout webhook.
     * Configure the endpoint as https://yourdomain.com/webhooks/razorpayx
     * and set RAZORPAYX_WEBHOOK_SECRET to the secret shown in the dashboard.
     */
    public function razorpayx(Request $request, RazorpayXService $razorpayx)
    {
        $signature = $request->header('X-Razorpay-Signature');

        if (! $razorpayx->verifyWebhook($request->getContent(), $signature)) {
            Log::warning('RazorpayX webhook rejected — bad signature', ['ip' => $request->ip()]);

            return response()->json(['ok' => false], 401);
        }

        $entity = $request->input('payload.payout.entity', []);
        $id     = $entity['id'] ?? null;
        $ref    = $entity['reference_id'] ?? null;

        if (! $id && ! $ref) {
            return response()->json(['ok' => true, 'ignored' => true]);
        }

        $payout = Payout::when($id, fn ($q) => $q->where('payout_id', $id))
            ->when(! $id && $ref, fn ($q) => $q->where('reference', $ref))
            ->first();

        if (! $payout) {
            Log::info('RazorpayX webhook for unknown payout', ['id' => $id, 'ref' => $ref]);

            return response()->json(['ok' => true, 'unknown' => true]);
        }

        if ($payout->isFinal()) {
            return response()->json(['ok' => true, 'already' => $payout->status]);
        }

        $status = $razorpayx->mapStatus($entity['status'] ?? null);

        $payout->forceFill([
            'status'         => $status,
            'payout_id'      => $id ?: $payout->payout_id,
            'utr'            => $entity['utr'] ?? $payout->utr,
            'failure_reason' => $entity['failure_reason'] ?? null,
            'paid_at'        => $status === Payout::STATUS_PAID ? now() : null,
        ])->save();

        return response()->json(['ok' => true, 'status' => $payout->status]);
    }
}
