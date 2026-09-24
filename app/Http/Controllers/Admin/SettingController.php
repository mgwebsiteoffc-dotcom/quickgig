<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'platform_fee' => 5,
            'creator_fee' => 10,
            'escrow_hours' => 48,
            'support_email' => 'support@quickcontent.in',
            'support_phone' => '+91 98765 43210',
            'razorpay_key' => 'rzp_live_xxx (hidden)',
            'maintenance' => false,
        ];
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'platform_fee'=>'required|numeric|min:0|max:30',
            'creator_fee'=>'required|numeric|min:0|max:30',
            'escrow_hours'=>'required|numeric|min:12|max:168',
            'support_email'=>'required|email',
        ]);
        // TODO: Setting::updateOrCreate(...)
        return back()->with('toast','Settings saved — file cache cleared');
    }
}
