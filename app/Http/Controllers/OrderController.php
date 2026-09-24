<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Creator;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public const PLATFORM_FEE = 0.10;

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
            'brief.min' => 'Give the creator a little more detail (at least 12 characters).',
        ]);

        $user = Auth::user();
        $service = Service::with('creator')->findOrFail($data['service_id']);
        $company = $this->companyFor($request, $user);

        $lane     = self::LANES[$data['lane']];
        $subtotal = (int) round($service->price * $lane['mult']);
        $fee      = (int) round($subtotal * self::PLATFORM_FEE);

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
                'description' => 'Track your Quick GIGS order live, chat with the creator and release escrow when you approve.',
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
            $o->progress < 40  => ['working',   45,  'Creator started production — files are being cut now.'],
            $o->progress < 80  => ['working',   85,  'Sound, captions and colour pass complete.'],
            $o->status !== 'review' && $o->status !== 'delivered' => ['review', 100, 'Delivery uploaded — review it and approve to release escrow.'],
            default            => [$o->status,  100, 'This order is already waiting for your approval.'],
        };

        $o->update(['status' => $status, 'progress' => $progress]);

        return back()->with('toast', $message);
    }

    public function approve(Request $request, $order)
    {
        $o = Order::where('uid', $order)->orWhere('id', $order)->firstOrFail();
        $this->authorizeOrder($request, $o);

        if ($o->escrow_status === 'released') {
            return back()->with('toast', 'This order was already approved.');
        }

        $o->update([
            'status'        => 'delivered',
            'escrow_status' => 'released',
            'progress'      => 100,
        ]);

        if ($o->creator) {
            $o->creator->increment('orders_count');
        }

        $payout = $o->total - $o->fee;

        return back()->with('toast', 'Approved — ₹' . number_format($payout) . ' released to the creator.');
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

    /** Buyer, assigned creator or staff only. */
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
