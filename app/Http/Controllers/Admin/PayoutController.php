<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Services\RazorpayXService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayoutController extends Controller
{
    public function index()
    {
        $payouts = Payout::with('creator')->latest()->paginate(50);

        $queue = collect($payouts->items())->map(fn (Payout $p) => [
            'id'         => $p->id,
            'creator'    => $p->creator->name ?? 'Creator #'.$p->creator_id,
            'handle'     => $p->creator->handle ?? '',
            'upi'        => $p->upi_id ?: ($p->creator->upi_id ?? '—'),
            'amount'     => (int) $p->amount,
            'orders'     => 1,
            'hold_until' => $p->hold_until?->format('Y-m-d') ?? '—',
            'status'     => $p->status,
            'utr'        => $p->utr,
        ]);

        $stats = [
            'hold'  => '₹'.number_format((int) Payout::hold()->sum('amount')).' held',
            'ready' => '₹'.number_format((int) Payout::ready()->sum('amount')).' ready',
            'paid'  => '₹'.number_format((int) Payout::paid()->where('paid_at', '>=', now()->startOfMonth())->sum('amount')).' paid this month',
        ];

        return view('admin.payouts.index', compact('queue', 'stats', 'payouts'));
    }

    public function markPaid(string $id, RazorpayXService $razorpayx)
    {
        $payout = Payout::findOrFail($id);

        if ($payout->isFinal()) {
            return back()->with('toast', 'Payout already '.$payout->status);
        }

        try {
            $result = $razorpayx->createPayout($payout);
            $payout->update([
                'status'    => $result['status'] ?? Payout::STATUS_PROCESSING,
                'payout_id' => $result['id'] ?? null,
                'utr'       => $result['utr'] ?? null,
                'paid_at'   => ($result['status'] ?? null) === Payout::STATUS_PAID ? now() : null,
            ]);

            return back()->with('toast', "Payout #{$payout->id} sent to RazorpayX ({$payout->status}).");
        } catch (\Throwable $e) {
            Log::error('RazorpayX payout failed', ['payout' => $payout->id, 'error' => $e->getMessage()]);
            $payout->update(['status' => Payout::STATUS_FAILED, 'failure_reason' => $e->getMessage()]);

            return back()->with('toast', 'Payout failed: '.$e->getMessage());
        }
    }

    public function hold(string $id)
    {
        $payout = Payout::findOrFail($id);

        if ($payout->isFinal()) {
            return back()->with('toast', 'Cannot hold a payout that is already '.$payout->status);
        }

        $payout->update([
            'status'     => Payout::STATUS_HOLD,
            'hold_until' => now()->addHours((int) \App\Models\Setting::get('escrow_hours', 48)),
        ]);

        return back()->with('toast', "Payout #{$payout->id} put on hold");
    }
}
