<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Creator;
use App\Models\Order;
use App\Models\Payout;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    private const STATUSES = ['pending', 'working', 'review', 'delivered', 'approved', 'cancelled'];

    public function index(Request $request)
    {
        $status = $request->query('status');
        $q      = $request->query('q');

        $orders = Order::with(['company', 'creator', 'service'])
            ->when($status && $status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($q, fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('uid', 'like', "%$q%")
                  ->orWhereHas('company', fn ($c) => $c->where('name', 'like', "%$q%"))
                  ->orWhereHas('service', fn ($s) => $s->where('title', 'like', "%$q%"));
            }))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $rows = collect($orders->items())->map(fn (Order $o) => $this->row($o));

        $raw = Order::select('status', DB::raw('count(*) as c'))->groupBy('status')->pluck('c', 'status');

        $counts = [
            'all'       => (int) $raw->sum(),
            'pending'   => (int) ($raw['pending'] ?? 0),
            'working'   => (int) ($raw['working'] ?? 0),
            'review'    => (int) ($raw['review'] ?? 0),
            'delivered' => (int) ($raw['delivered'] ?? 0),
        ];

        return view('admin.orders.index', [
            'orders'    => $rows,
            'paginator' => $orders,
            'counts'    => $counts,
            'status'    => $status,
            'q'         => $q,
        ]);
    }

    public function show(string $id)
    {
        $model = $this->find($id);
        $order = $this->row($model);

        $created = $model->created_at;

        $timeline = [
            ['t' => 'Order placed by '.($model->company->name ?? 'company'), 'time' => $created?->format('d M, g:i A') ?? '—', 'done' => true],
            ['t' => 'Payment held in escrow (Razorpay)', 'time' => $created?->addMinute()->format('d M, g:i A') ?? '—', 'done' => $model->escrow_status !== 'none'],
            ['t' => $model->creator ? 'Assigned to '.$model->creator->name.' ('.$model->creator->handle.')' : 'Awaiting creator assignment', 'time' => $model->creator ? ($created?->format('d M, g:i A') ?? '—') : '—', 'done' => (bool) $model->creator_id],
            ['t' => 'Creator started work', 'time' => $model->status !== 'pending' ? ($model->updated_at?->format('d M, g:i A') ?? '—') : '—', 'done' => $model->status !== 'pending'],
            ['t' => 'Delivered for review', 'time' => in_array($model->status, ['review', 'delivered', 'approved'], true) ? ($model->updated_at?->format('d M, g:i A') ?? '—') : '—', 'done' => in_array($model->status, ['review', 'delivered', 'approved'], true)],
            ['t' => 'Approved → payout released', 'time' => $model->escrow_status === 'released' ? ($model->updated_at?->format('d M, g:i A') ?? '—') : '—', 'done' => $model->escrow_status === 'released'],
        ];

        $creators = Creator::orderBy('name')->get(['id', 'name', 'handle']);

        return view('admin.orders.show', compact('order', 'timeline', 'creators', 'model'));
    }

    public function updateStatus(Request $request, string $id)
    {
        $request->validate(['status' => 'required|in:'.implode(',', self::STATUSES)]);

        $order = $this->find($id);

        $progress = match ($request->status) {
            'pending'   => 5,
            'working'   => 40,
            'review'    => 90,
            'delivered', 'approved' => 100,
            default     => $order->progress,
        };

        $order->update(['status' => $request->status, 'progress' => $progress]);

        return back()->with('toast', "Order {$order->uid} → {$request->status}");
    }

    public function assign(Request $request, string $id)
    {
        $request->validate(['creator_id' => 'required|exists:creators,id']);

        $order = $this->find($id);
        $order->update(['creator_id' => $request->creator_id, 'status' => $order->status === 'pending' ? 'working' : $order->status]);

        return back()->with('toast', "Assigned {$order->uid} to ".($order->creator->name ?? '#'.$request->creator_id));
    }

    /**
     * Release escrow and queue a creator payout.
     * Idempotent: an order can only produce one payout.
     */
    public function releaseEscrow(string $id)
    {
        $order = $this->find($id);

        if ($order->escrow_status === 'released') {
            return back()->with('toast', "Escrow for {$order->uid} was already released.");
        }

        if (! $order->creator_id) {
            return back()->with('error', 'Assign a creator before releasing escrow.');
        }

        $creatorFee = (int) Setting::get('creator_fee', 10);
        $net        = (int) round($order->total * (100 - $creatorFee) / 100);
        $holdHours  = (int) Setting::get('escrow_hours', 48);

        DB::transaction(function () use ($order, $net, $holdHours) {
            Payout::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'creator_id' => $order->creator_id,
                    'amount'     => $net,
                    'upi_id'     => $order->creator->upi_id ?? null,
                    'status'     => Payout::STATUS_HOLD,
                    'hold_until' => now()->addHours($holdHours),
                ]
            );

            $order->update(['escrow_status' => 'released', 'status' => 'approved', 'progress' => 100]);
        });

        return back()->with('toast', "Escrow released for {$order->uid} — ₹".number_format($net).' payout queued');
    }

    private function find(string $id): Order
    {
        return Order::with(['company', 'creator', 'service'])
            ->where('uid', $id)
            ->orWhere('id', $id)
            ->firstOrFail();
    }

    private function row(Order $o): array
    {
        return [
            'id'      => $o->uid,
            'company' => $o->company->name ?? '—',
            'person'  => $o->company->person_name ?? '—',
            'service' => $o->service->title ?? '—',
            'creator' => $o->creator->name ?? 'Unassigned',
            'amount'  => (int) $o->total,
            'status'  => $o->status,
            'escrow'  => $o->escrow_status,
            'created' => $o->created_at?->format('Y-m-d H:i') ?? '—',
            'brief'   => $o->brief,
            'turnaround' => $o->turnaround,
        ];
    }
}
