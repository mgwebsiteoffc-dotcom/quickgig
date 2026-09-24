<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private function allOrders()
    {
        return collect([
            ['id'=>'QC-1829','company'=>'Avante Studio','person'=>'Rohan Sharma','service'=>'Talking-Head Reel','creator'=>'Priya Sharma','amount'=>2499,'status'=>'working','escrow'=>'held','created'=>'2026-09-24 10:30'],
            ['id'=>'QC-1828','company'=>'BrandScale Media','person'=>'Priya Kapoor','service'=>'AI UGC Ad','creator'=>'Sahil Dua','amount'=>6499,'status'=>'review','escrow'=>'held','created'=>'2026-09-24 09:12'],
            ['id'=>'QC-1827','company'=>'GrowthX Labs','person'=>'Aman Verma','service'=>'High CTR Thumbnail','creator'=>'Neha Jain','amount'=>1299,'status'=>'delivered','escrow'=>'released','created'=>'2026-09-23 18:00'],
            ['id'=>'QC-1826','company'=>'Avante Studio','person'=>'Rohan Sharma','service'=>'Retention Reel','creator'=>'Rahul Verma','amount'=>3999,'status'=>'pending','escrow'=>'held','created'=>'2026-09-23 14:20'],
            ['id'=>'QC-1825','company'=>'ConcertPass','person'=>'Karan Mehta','service'=>'Prompt-a-Team Pack','creator'=>'Team','amount'=>8999,'status'=>'working','escrow'=>'held','created'=>'2026-09-22 11:00'],
            ['id'=>'QC-1824','company'=>'Avante Studio','person'=>'Rohan Sharma','service'=>'Basic Thumbnail','creator'=>'Neha Jain','amount'=>1299,'status'=>'approved','escrow'=>'released','created'=>'2026-09-21 16:40'],
        ]);
    }

    public function index(Request $request)
    {
        $status = $request->query('status');
        $q = $request->query('q');
        $orders = $this->allOrders();
        if ($status && $status!=='all') $orders = $orders->where('status',$status);
        if ($q) $orders = $orders->filter(fn($o)=> str_contains(strtolower($o['id'].$o['company'].$o['service']), strtolower($q)));
        $counts = [
            'all'=>$this->allOrders()->count(),
            'pending'=>$this->allOrders()->where('status','pending')->count(),
            'working'=>$this->allOrders()->where('status','working')->count(),
            'review'=>$this->allOrders()->where('status','review')->count(),
            'delivered'=>$this->allOrders()->where('status','delivered')->count(),
        ];
        return view('admin.orders.index', compact('orders','counts','status','q'));
    }

    public function show(string $id)
    {
        $order = $this->allOrders()->firstWhere('id',$id) ?? $this->allOrders()->first();
        $timeline = [
            ['t'=>'Order placed by Avante Studio','time'=>'24 Sep, 10:30 AM','done'=>true],
            ['t'=>'Payment held in escrow (Razorpay)','time'=>'24 Sep, 10:31 AM','done'=>true],
            ['t'=>'Assigned to Priya Sharma (@priyaedits)','time'=>'24 Sep, 10:42 AM','done'=>true],
            ['t'=>'Creator started work','time'=>'24 Sep, 11:05 AM','done'=> $order['status']!=='pending'],
            ['t'=>'Delivered for review','time'=>'—','done'=> in_array($order['status'],['review','delivered','approved'])],
            ['t'=>'Approved → payout released','time'=>'—','done'=> $order['status']==='approved'],
        ];
        return view('admin.orders.show', compact('order','timeline'));
    }

    public function updateStatus(Request $request, string $id)
    {
        $request->validate(['status'=>'required|in:pending,working,review,delivered,approved,cancelled']);
        // TODO: Order::where('order_code',$id)->update(['status'=>$request->status])
        // For shared hosting: no queue, just DB update + mail()
        return back()->with('toast', "Order $id → ".$request->status);
    }

    public function assign(Request $request, string $id)
    {
        $request->validate(['creator_id'=>'required']);
        return back()->with('toast', "Assigned $id to creator #".$request->creator_id);
    }

    public function releaseEscrow(string $id)
    {
        // TODO: RazorpayX payout API
        return back()->with('toast', "Escrow released for $id — payout queued");
    }
}
