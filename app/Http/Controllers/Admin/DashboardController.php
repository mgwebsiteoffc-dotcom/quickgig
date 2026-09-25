<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Creator;
use App\Models\Order;
use App\Models\Payout;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalOrders   = Order::count();
        $weekOrders    = Order::where('created_at', '>=', now()->subWeek())->count();
        $escrowHeld    = (int) Order::where('escrow_status', 'held')->sum('total');
        $revenue       = (int) Order::whereNotIn('status', ['cancelled'])->sum('total');
        $activeCreators = Creator::count();
        $availableNow  = Creator::available()->count();
        $pendingReview = Order::whereIn('status', ['pending', 'review'])->count();

        $stats = [
            ['label'=>'Total Orders','value'=>number_format($totalOrders),'change'=>'+'.$weekOrders.' this week','icon'=>'shopping-bag','color'=>'blue'],
            ['label'=>'Revenue (Escrow)','value'=>$this->money($revenue),'change'=>$this->money($escrowHeld).' held now','icon'=>'wallet','color'=>'green'],
            ['label'=>'Active Creators','value'=>number_format($activeCreators),'change'=>$availableNow.' available now','icon'=>'users','color'=>'violet'],
            ['label'=>'Pending Review','value'=>number_format($pendingReview),'change'=>$pendingReview ? 'Needs action' : 'All clear','icon'=>'clock','color'=>'amber'],
        ];

        $recentOrders = Order::with(['company','creator','service'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (Order $o) => [
                'id'      => $o->uid,
                'company' => $o->company->name ?? '—',
                'service' => $o->service->title ?? '—',
                'creator' => $o->creator->name ?? 'Unassigned',
                'amount'  => (int) $o->total,
                'status'  => $o->status,
                'time'    => $o->created_at?->diffForHumans(null, true).' ago',
            ])
            ->all();

        $payoutQueue = Payout::with('creator')
            ->whereIn('status', [Payout::STATUS_HOLD, Payout::STATUS_READY])
            ->selectRaw('MIN(id) as id, creator_id, SUM(amount) as amount, COUNT(*) as orders')
            ->groupBy('creator_id')
            ->orderByDesc('amount')
            ->limit(3)
            ->get()
            ->map(fn (Payout $p) => [
                'creator' => $p->creator->name ?? 'Creator #'.$p->creator_id,
                'handle'  => $p->creator->handle ?? '',
                'amount'  => (int) $p->amount,
                'orders'  => (int) $p->orders,
                'upi'     => $p->creator->upi_id ?? '—',
            ])
            ->all();

        $role = $request->user()->role ?? 'admin';

        return view('admin.dashboard', compact('stats', 'recentOrders', 'payoutQueue', 'role'));
    }

    private function money(int $rupees): string
    {
        if ($rupees >= 10000000) return '₹'.round($rupees / 10000000, 1).'Cr';
        if ($rupees >= 100000)   return '₹'.round($rupees / 100000, 1).'L';
        if ($rupees >= 1000)     return '₹'.round($rupees / 1000, 1).'k';
        return '₹'.number_format($rupees);
    }
}
