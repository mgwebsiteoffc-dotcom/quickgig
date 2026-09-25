<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Creator;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Payments\RazorpayGateway;
use App\Support\Notifier;
use App\Notifications\OrderPlaced;
use App\Notifications\PayoutReleased;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public const PLATFORM_FEE = 0.10;

    /** Fee comes from the admin console; the constant is the fallback. */
    public static function feeRate(): float
    {
        $pct = (float) setting('platform.fee_percent', self::PLATFORM_FEE * 100);

        return max(0, min(30, $pct)) / 100;
    }

    /** Speed lanes offered at checkout. */
    public const LANES = [
        'express'  => ['label' => 'Express · 3 hours',   'mult' => 1.6,  'days' => 1],
        'standard' => ['label' => 'Standard · 24 hours', 'mult' => 1.0,  'days' => 1],
        'relaxed'  => ['label' => 'Relaxed · 48 hours',  'mult' => 0.85, 'days' => 2],
    ];

    /** Create an order from a marketplace gig. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'brief'      => ['required', 'string', 'min:12', 'max:2000'],
            'lane'       => ['required', 'in:express,standard,relaxed'],
        ], [
            'brief.min' => 'Give the freelancer a little more detail (at least 12 characters).',
        ]);

        $user = Auth::user();
        $service = Service::with('creator')->findOrFail($data['service_id']);
        $company = $this->companyFor($request, $user);

        $lane     = self::LANES[$data['lane']];
        $subtotal = (int) round($service->price * $lane['mult']);
        $fee      = (int) round($subtotal * self::feeRate());

        $creatorId = $service->creator_id ?: Creator::where('is_verified', true)
            ->where('is_available', true)->orderByDesc('rating')->value('id');

        $order = Order::create([
            'company_id'    => $company->id,
            'creator_id'    => $creatorId,
            'service_id'    => $service->id,
            'brief'         => $data['brief'],
            'turnaround'    => $lane['label'],
            'subtotal'      => $subtotal,
            'fee'           => $fee,
            'discount'      => 0,
            'total'         => $subtotal,
            'status'        => 'working',
            'escrow_status' => 'held',
            'progress'      => 15,
            'due_at'        => now()->addDays($lane['days']),
        ]);

        $service->increment('sold_count');

        Notifier::toUserOf($company, new OrderPlaced($order, 'buyer'));
        Notifier::toFreelancer($order->creator, new OrderPlaced($order, 'freelancer'));

        $gateway = app(RazorpayGateway::class);

        if ($gateway->enabled()) {
            try {
                $gateway->createOrder($order);

                return redirect()->route('orders.show', $order->uid)
                    ->with('toast', 'Gig booked — pay ₹' . number_format($subtotal) . ' to move it into escrow.');
            } catch (\Throwable $e) {
                Log::warning('razorpay.order_failed', ['order' => $order->uid, 'message' => $e->getMessage()]);
            }
        }

        // demo mode: no gateway configured, so escrow is simulated
        $order->forceFill(['payment_provider' => 'demo', 'payment_status' => 'paid', 'paid_at' => now()])->save();

        return redirect()->route('orders.show', $order->uid)
            ->with('toast', 'Gig booked — ₹' . number_format($subtotal) . ' is held in escrow until you approve.');
    }

    public function show(Request $request, $order)
    {
        $o = Order::with(['company', 'creator', 'service'])
            ->where('uid', $order)->orWhere('id', $order)->firstOrFail();

        $this->authorizeOrder($request, $o);

        return view('orders.show', [
            'order' => $o,
            'seo'   => [
                'title'       => 'Order ' . $o->uid . ' — Quick GIGS',
                'description' => 'Track your Quick GIGS order live, chat with the freelancer and release escrow when you approve.',
                'canonical'   => route('orders.show', $o->uid),
            ],
        ]);
    }

    /**
     * Demo simulation: move the order one step forward through the pipeline
     * (assigned → in production → delivered) without waiting for real work.
     */
    public function simulate(Request $request, $order)
    {
        $o = Order::where('uid', $order)->orWhere('id', $order)->firstOrFail();
        $this->authorizeOrder($request, $o);

        [$status, $progress, $message] = match (true) {
            $o->progress < 40  => ['working',   45,  'Freelancer started production — files are being cut now.'],
            $o->progress < 80  => ['working',   85,  'Sound, captions and colour pass complete.'],
            $o->status !== 'review' && $o->status !== 'delivered' => ['review', 100, 'Delivery uploaded — review it and approve to release escrow.'],
            default            => [$o->status,  100, 'This order is already waiting for your approval.'],
        };

        $o->update(['status' => $status, 'progress' => $progress]);

        if ($request->expectsJson()) {
            return response()->json([
                'status'        => $o->status,
                'progress'      => $o->progress,
                'escrow_status' => $o->escrow_status,
                'message'       => $message,
            ]);
        }

        return back()->with('toast', $message);
    }

    public function approve(Request $request, $order)
    {
        $o = Order::where('uid', $order)->orWhere('id', $order)->firstOrFail();
        $this->authorizeOrder($request, $o);

        if ($o->escrow_status === 'released') {
            return $request->expectsJson()
                ? response()->json(['status' => $o->status, 'progress' => $o->progress, 'escrow_status' => $o->escrow_status, 'message' => 'This order was already approved.'])
                : back()->with('toast', 'This order was already approved.');
        }

        $o->update([
            'status'        => 'delivered',
            'escrow_status' => 'released',
            'progress'      => 100,
        ]);

        if ($o->creator) {
            $o->creator->increment('orders_count');
        }

        $payout = \App\Models\Payout::raiseFor($o);

        if ($payout) {
            $o->forceFill(['payout_reference' => $payout->uid])->save();
            Notifier::toFreelancer($o->creator, new PayoutReleased($payout));
        }

        $payout  = $o->total - $o->fee;
        $message = 'Approved — ₹' . number_format($payout) . ' released to the freelancer.';

        if ($request->expectsJson()) {
            return response()->json([
                'status'        => $o->status,
                'progress'      => $o->progress,
                'escrow_status' => $o->escrow_status,
                'message'       => $message,
            ]);
        }

        return back()->with('toast', $message);
    }

    /** Checkout callback — verify the signature before trusting anything. */
    public function verifyPayment(Request $request, RazorpayGateway $gateway, $order)
    {
        $o = Order::where('uid', $order)->orWhere('id', $order)->firstOrFail();
        $this->authorizeOrder($request, $o);

        $data = $request->validate([
            'razorpay_order_id'   => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature'  => ['required', 'string'],
        ]);

        $valid = $gateway->verifyPaymentSignature(
            $data['razorpay_order_id'], $data['razorpay_payment_id'], $data['razorpay_signature']
        );

        if (! $valid) {
            Log::warning('razorpay.bad_signature', ['order' => $o->uid]);

            return $request->expectsJson()
                ? response()->json(['ok' => false, 'message' => 'Payment signature did not verify.'], 422)
                : back()->with('toast', 'Payment signature did not verify — nothing was charged.');
        }

        $o->forceFill([
            'payment_id'     => $data['razorpay_payment_id'],
            'payment_status' => 'paid',
            'paid_at'        => now(),
            'escrow_status'  => 'held',
        ])->save();

        $message = '₹' . number_format($o->total) . ' captured and held in escrow.';

        return $request->expectsJson()
            ? response()->json(['ok' => true, 'message' => $message, 'payment_status' => 'paid'])
            : back()->with('toast', $message);
    }

    /** Server-to-server confirmation from Razorpay. */
    public function webhook(Request $request, RazorpayGateway $gateway)
    {
        $payload   = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');

        if (! $gateway->verifyWebhookSignature($payload, $signature)) {
            return response()->json(['ok' => false], 400);
        }

        $event   = (string) $request->input('event');
        $entity  = (array) $request->input('payload.payment.entity', []);
        $receipt = (string) ($entity['notes']['order_uid'] ?? '');

        $order = $receipt ? Order::where('uid', $receipt)->first() : null;

        if ($order) {
            match ($event) {
                'payment.captured' => $order->forceFill([
                    'payment_id' => $entity['id'] ?? $order->payment_id,
                    'payment_status' => 'paid', 'paid_at' => now(), 'escrow_status' => 'held',
                ])->save(),
                'payment.failed' => $order->forceFill(['payment_status' => 'failed'])->save(),
                'refund.processed' => $order->forceFill(['payment_status' => 'refunded', 'escrow_status' => 'refunded'])->save(),
                default => null,
            };
        }

        Log::info('razorpay.webhook', ['event' => $event, 'order' => $receipt]);

        return response()->json(['ok' => true]);
    }

    public function message(Request $request, $order)
    {
        $request->validate(['message' => ['required', 'string', 'max:500']]);

        $o = Order::where('uid', $order)->orWhere('id', $order)->firstOrFail();
        $this->authorizeOrder($request, $o);

        $thread = session('thread.' . $o->uid, []);
        $thread[] = [
            'from' => 'you',
            'text' => $request->input('message'),
            'at'   => now()->format('H:i'),
        ];
        $thread[] = [
            'from' => 'creator',
            'text' => 'Got it — noted in the brief. I will share the first cut shortly.',
            'at'   => now()->addMinute()->format('H:i'),
        ];

        session(['thread.' . $o->uid => $thread]);

        return back();
    }

    /* ───────────────────────── helpers ───────────────────────── */

    private function companyFor(Request $request, $user): Company
    {
        if ($user && $user->company_id && ($c = Company::find($user->company_id))) {
            return $c;
        }

        if ($id = $request->session()->get('company_id')) {
            if ($c = Company::find($id)) return $c;
        }

        $company = Company::create([
            'name'        => $user->name . "'s workspace",
            'person_name' => $user->name,
            'email'       => $user->email,
            'is_active'   => true,
        ]);

        $user?->update(['company_id' => $company->id]);
        $request->session()->put('company_id', $company->id);

        return $company;
    }

    /** Buyer, assigned freelancer or staff only. */
    private function authorizeOrder(Request $request, Order $order): void
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Log in to view this order.');
        }

        $allowed = $user->isAdmin()
            || ($user->company_id && $user->company_id === $order->company_id)
            || ($user->creator_id && $user->creator_id === $order->creator_id)
            || $request->session()->get('company_id') === $order->company_id;

        abort_unless($allowed, 403, 'This order belongs to another account.');
    }
}
