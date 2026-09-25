<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Creator;
use App\Models\Company;
use App\Models\Order;
use App\Http\Controllers\Concerns\ResolvesActor;

class OrderController extends Controller
{
    use ResolvesActor;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'creator_id' => 'nullable|exists:creators,id',
            'company_id' => 'required|integer',
            'brief' => 'required|string|min:8',
            'turnaround' => 'nullable|string',
        ]);

        // You may only order on behalf of a business you belong to.
        $allowed = $this->selectableCompanies($request)->pluck('id');
        abort_unless($allowed->contains((int) $validated['company_id']), 403, 'That business is not yours.');

        // Try DB-backed order for real flow (supports UGC/Barter same flow)
        try {
            $service = Service::with('creator')->find($validated['service_id']);
            // fallback if id is dummy string like reel1
            if (!$service && is_numeric($validated['service_id'])) $service = Service::find($validated['service_id']);
            if ($service) {
                $companyId = $validated['company_id'];
                // pick creator: explicit or service creator or available by profile_type
                $creatorId = $validated['creator_id'] ?? $service->creator_id;
                if (!$creatorId) {
                    $q = Creator::where('is_verified', true)->where('is_available', true);
                    if (in_array($service->profile_type, ['ugc_creator','influencer','video_editor','designer','hybrid'])) {
                        $q->where('profile_type', $service->profile_type);
                    }
                    if ($service->isBarter()) $q->where('barter_available', true);
                    $creatorId = $q->value('id') ?? Creator::where('is_verified', true)->value('id');
                }

                // Price handling: barter = 0 escrow, hybrid = price, paid = price
                $subtotal = (int) $service->price;
                $isBarter = $service->isBarter() && $service->price_type === 'barter';
                if ($isBarter) $subtotal = 0;
                $fee = $isBarter ? 0 : (int) round($subtotal * 0.05);
                $total = $subtotal + $fee;

                $order = Order::create([
                    'company_id' => $companyId,
                    'creator_id' => $creatorId,
                    'service_id' => $service->id,
                    'brief' => $validated['brief'],
                    'turnaround' => $validated['turnaround'] ?? $service->delivery_days.' Day',
                    'subtotal' => $subtotal,
                    'fee' => $fee,
                    'discount' => 0,
                    'total' => $total,
                    'status' => 'working',
                    'escrow_status' => $isBarter ? 'barter' : 'held',
                    'progress' => 25,
                    'due_at' => now()->addDays($service->delivery_days),
                ]);

                $orderId = $order->uid ?? $order->id;
                $msg = $isBarter
                    ? 'Barter collab booked — creator in ~12 min (ship product worth ₹'.number_format($service->barter_value).')'
                    : 'Booked for company — creator in ~12 min ⚡ (₹'.number_format($total).' held in escrow)';
                return redirect()->route('orders.show', $orderId)->with('toast', $msg);
            }
        } catch (\Throwable $e) {
            // fall through to dummy
        }

        return back()->with('error', 'That service is not available right now. Please pick another.');
    }

    public function storeTeam(Request $request)
    {
        $validated = $request->validate([
            'prompt' => 'required|string|min:5',
            'company_id' => 'required|integer',
        ]);

        $allowed = $this->selectableCompanies($request)->pluck('id');
        abort_unless($allowed->contains((int) $validated['company_id']), 403, 'That business is not yours.');

        // Prompt-a-team is not wired to a real fulfilment flow yet — see docs/status.md.
        return back()->with('toast', 'Prompt received — our team will confirm scope and price shortly.');
    }

    public function show(Request $request, $order)
    {
        $o = Order::with(['company','creator','service'])
            ->where(fn($q) => $q->where('uid', $order)->orWhere('id', $order))
            ->firstOrFail();

        $this->authorizeOrder($request, $o);

        return view('orders.show', [
            'order' => [
                'id'      => $o->uid ?? $o->id,
                'company' => [
                    'name'     => $o->company->name ?? 'Company',
                    'person'   => $o->company->person_name ?? 'Owner',
                    'initials' => strtoupper(substr($o->company->name ?? 'C', 0, 2)),
                ],
                'creator' => $o->creator ? [
                    'name'      => $o->creator->name,
                    'handle'    => $o->creator->handle,
                    'img'       => $o->creator->avatarUrl(),
                    'rating'    => number_format($o->creator->rating, 1),
                    'available' => $o->creator->is_available,
                ] : null,
                'service' => [
                    'title'         => $o->service->title ?? 'Service',
                    'price'         => $o->service->price ?? $o->total,
                    'time'          => $o->turnaround ?? '1 Day',
                    'category'      => $o->service->category ?? '',
                    'price_type'    => $o->service->price_type ?? 'paid',
                    'display_price' => $o->service ? $o->service->displayPrice() : '₹'.number_format($o->total),
                ],
                'total'         => $o->total,
                'escrow_status' => $o->escrow_status,
                'status'        => $o->status,
                'progress'      => $o->progress,
                'brief'         => $o->brief,
            ],
        ]);
    }

    public function approve(Request $request, $order)
    {
        $o = Order::where(fn($q) => $q->where('uid', $order)->orWhere('id', $order))->firstOrFail();

        $this->authorizeOrder($request, $o);

        // Only the buying business (or staff) may approve a delivery.
        $user = $request->user();
        abort_unless($user->isAdmin() || $user->company_id === $o->company_id, 403, 'Only the buyer can approve this order.');

        if ($o->escrow_status === 'barter') {
            $o->update(['status' => 'delivered', 'escrow_status' => 'barter_done']);

            return back()->with('toast', 'Barter approved — collaboration complete ✓ (no escrow)');
        }

        $o->update(['status' => 'delivered', 'escrow_status' => 'released']);

        return back()->with('toast', 'Approved — ₹'.number_format($o->total).' released to creator (escrow)');
    }

    public function message(Request $request, $order)
    {
        $request->validate(['message' => 'required|string|min:1|max:2000']);

        $o = Order::where(fn($q) => $q->where('uid', $order)->orWhere('id', $order))->firstOrFail();

        $this->authorizeOrder($request, $o);

        // TODO: persist to an order_messages table once real-time chat lands.
        return back();
    }
}
