<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // In production replace with Eloquent counts
        $stats = [
            ['label'=>'Total Orders','value'=>'1,247','change'=>'+12% this week','icon'=>'shopping-bag','color'=>'blue'],
            ['label'=>'Revenue (Escrow)','value'=>'₹8.4L','change'=>'₹1.2L held now','icon'=>'wallet','color'=>'green'],
            ['label'=>'Active Creators','value'=>'48','change'=>'12 available now','icon'=>'users','color'=>'violet'],
            ['label'=>'Pending Review','value'=>'23','change'=>'Needs action','icon'=>'clock','color'=>'amber'],
        ];

        $recentOrders = [
            ['id'=>'QC-1829','company'=>'Avante Studio','service'=>'Talking-Head Reel','creator'=>'Priya Sharma','amount'=>2499,'status'=>'working','time'=>'2h ago'],
            ['id'=>'QC-1828','company'=>'BrandScale','service'=>'AI UGC Ad','creator'=>'Sahil Dua','amount'=>6499,'status'=>'review','time'=>'4h ago'],
            ['id'=>'QC-1827','company'=>'GrowthX Labs','service'=>'High CTR Thumbnail','creator'=>'Neha Jain','amount'=>1299,'status'=>'delivered','time'=>'6h ago'],
            ['id'=>'QC-1826','company'=>'Avante Studio','service'=>'Retention Reel','creator'=>'Rahul Verma','amount'=>3999,'status'=>'pending','time'=>'8h ago'],
            ['id'=>'QC-1825','company'=>'ConcertPass','service'=>'Prompt-a-Team Pack','creator'=>'Team: Priya+Neha','amount'=>8999,'status'=>'working','time'=>'1d ago'],
        ];

        $payoutQueue = [
            ['creator'=>'Priya Sharma','handle'=>'@priyaedits','amount'=>22490,'orders'=>9,'upi'=>'priya@upi'],
            ['creator'=>'Rahul Verma','handle'=>'@rahulcuts','amount'=>18750,'orders'=>7,'upi'=>'rahul@upi'],
            ['creator'=>'Neha Jain','handle'=>'@nehacreates','amount'=>14200,'orders'=>11,'upi'=>'neha@upi'],
        ];

        $role = $request->user()->role ?? 'admin';

        return view('admin.dashboard', compact('stats','recentOrders','payoutQueue','role'));
    }
}
