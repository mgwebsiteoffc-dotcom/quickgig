<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    public function index()
    {
        $all = Setting::all_settings();

        $key = (string) config('services.razorpayx.key');

        $settings = [
            'platform_fee'  => (int) ($all['platform_fee'] ?? 5),
            'creator_fee'   => (int) ($all['creator_fee'] ?? 10),
            'escrow_hours'  => (int) ($all['escrow_hours'] ?? 48),
            'support_email' => $all['support_email'] ?? '',
            'support_phone' => $all['support_phone'] ?? '',
            'razorpay_key'  => $key ? substr($key, 0, 8).'… (hidden)' : 'not configured',
            'maintenance'   => (bool) ($all['maintenance'] ?? false),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'platform_fee'  => 'required|numeric|min:0|max:30',
            'creator_fee'   => 'required|numeric|min:0|max:30',
            'escrow_hours'  => 'required|numeric|min:12|max:168',
            'support_email' => 'required|email',
            'support_phone' => 'nullable|string|max:20',
        ]);

        $data['maintenance'] = $request->boolean('maintenance');

        Setting::putMany($data);

        Artisan::call('config:clear');

        return back()->with('toast', 'Settings saved ✓');
    }
}
