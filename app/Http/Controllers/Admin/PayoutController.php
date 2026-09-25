<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Services\Payments\RazorpayGateway;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public function index(Request $request, RazorpayGateway $gateway)
    {
        $status = (string) $request->query('status', 'open');

        $query = Payout::with(['creator', 'order'])->latest('id');

        match ($status) {
            'ready'  => $query->ready(),
            'paid'   => $query->settled(),
            'failed' => $query->where('status', 'failed'),
            'all'    => null,
            default  => $query->open(),
        };

        $all = Payout::query();

        return view('admin.payouts.index', [
            'payouts' => $query->paginate(25)->withQueryString(),
            'status'  => $status,
            'stats'   => [
                'held'    => (int) (clone $all)->where('status', 'pending')->where('available_at', '>', now())->sum('amount'),
                'ready'   => (int) Payout::ready()->sum('amount'),
                'paid'    => (int) (clone $all)->where('status', 'paid')->whereMonth('processed_at', now()->month)->sum('amount'),
                'failed'  => (int) (clone $all)->where('status', 'failed')->count(),
                'fees'    => (int) (clone $all)->sum('fee'),
            ],
            'autoPayouts' => $gateway->payoutsEnabled(),
        ]);
    }

    /** Send through RazorpayX when it is configured, otherwise record a manual transfer. */
    public function markPaid(Request $request, RazorpayGateway $gateway, string $id)
    {
        $payout = Payout::findOrFail($id);

        $data = $request->validate([
            'reference' => ['nullable', 'string', 'max:60'],
            'notes'     => ['nullable', 'string', 'max:300'],
        ]);

        if ($gateway->payoutsEnabled() && ! $data['reference']) {
            try {
                $result = $gateway->createPayout($payout);

                $payout->update([
                    'status'       => 'processing',
                    'provider'     => 'razorpayx',
                    'reference'    => $result['id'] ?? null,
                    'notes'        => $data['notes'] ?? null,
                    'processed_at' => now(),
                ]);

                return back()->with('toast', 'Payout queued with RazorpayX — ' . ($result['id'] ?? 'no reference') . '.');
            } catch (\Throwable $e) {
                $payout->update(['status' => 'failed', 'notes' => \Illuminate\Support\Str::limit($e->getMessage(), 250)]);

                return back()->with('toast', 'RazorpayX rejected it: ' . \Illuminate\Support\Str::limit($e->getMessage(), 90));
            }
        }

        $payout->update([
            'status'       => 'paid',
            'provider'     => 'manual',
            'reference'    => $data['reference'] ?: 'MANUAL-' . now()->format('ymdHis'),
            'notes'        => $data['notes'] ?? null,
            'processed_at' => now(),
        ]);

        return back()->with('toast', '₹' . number_format($payout->amount) . ' marked paid to ' . ($payout->creator->name ?? 'freelancer') . '.');
    }

    public function hold(string $id)
    {
        $payout = Payout::findOrFail($id);
        $payout->update(['status' => $payout->status === 'on_hold' ? 'pending' : 'on_hold']);

        return back()->with('toast', $payout->status === 'on_hold' ? 'Payout held.' : 'Payout back in the queue.');
    }

    public function retry(string $id)
    {
        $payout = Payout::findOrFail($id);
        $payout->update(['status' => 'pending', 'notes' => null]);

        return back()->with('toast', 'Payout returned to the queue for another attempt.');
    }
}
