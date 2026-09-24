<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public function index()
    {
        $queue = collect([
            ['id'=>1,'creator'=>'Priya Sharma','handle'=>'@priyaedits','upi'=>'priya@upi','amount'=>22490,'orders'=>9,'hold_until'=>'2026-09-26','status'=>'hold'],
            ['id'=>2,'creator'=>'Rahul Verma','handle'=>'@rahulcuts','upi'=>'rahul@upi','amount'=>18750,'orders'=>7,'hold_until'=>'2026-09-25','status'=>'ready'],
            ['id'=>3,'creator'=>'Neha Jain','handle'=>'@nehacreates','upi'=>'neha@upi','amount'=>14200,'orders'=>11,'hold_until'=>'2026-09-24','status'=>'ready'],
            ['id'=>4,'creator'=>'Aman Khan','handle'=>'@amanmotion','upi'=>'aman@upi','amount'=>8900,'orders'=>4,'hold_until'=>'—','status'=>'paid'],
        ]);
        $stats = ['hold'=>'₹41,240 held','ready'=>'₹32,950 ready','paid'=>'₹8.1L paid this month'];
        return view('admin.payouts.index', compact('queue','stats'));
    }

    public function markPaid(string $id)
    {
        return back()->with('toast',"Payout #$id marked as paid (RazorpayX).");
    }

    public function hold(string $id)
    {
        return back()->with('toast',"Payout #$id put on hold");
    }
}
