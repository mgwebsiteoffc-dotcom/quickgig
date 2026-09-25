<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Creator;
use App\Models\Order;
use App\Models\Service;
use App\Models\Task;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    /** Business dashboard — orders, escrow and quick actions. */
    public function home(Request $request)
    {
        $company = $this->company($request);

        $orders = Order::with(['creator', 'service'])
            ->where('company_id', $company->id)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $all = Order::where('company_id', $company->id);

        $stats = [
            'active'    => (clone $all)->whereIn('status', ['working', 'review'])->count(),
            'escrow'    => (int) (clone $all)->where('escrow_status', 'held')->sum('total'),
            'delivered' => (clone $all)->where('status', 'delivered')->count(),
            'spend'     => (int) (clone $all)->where('escrow_status', 'released')->sum('total'),
        ];

        $recommended = Service::with('creator')->where('is_active', true)
            ->orderByDesc('rating')->orderByDesc('sold_count')->limit(3)->get();

        $availableNow = Creator::where('is_verified', true)->where('is_available', true)
            ->orderByDesc('rating')->limit(5)->get();

        $tasks = Task::where('company_id', $company->id)->get();
        $board = [
            'open'      => $tasks->whereNotIn('status', ['done'])->count(),
            'overdue'   => $tasks->filter->isOverdue()->count(),
            'by_status' => collect(Task::STATUSES)->map(fn ($l, $k) => $tasks->where('status', $k)->count())->all(),
            'next'      => $tasks->whereNotIn('status', ['done'])->sortBy('due_on')->first(),
        ];

        return view('dashboard.business', [
            'company'      => $company,
            'orders'       => $orders,
            'stats'        => $stats,
            'recommended'  => $recommended,
            'board'        => $board,
            'availableNow' => $availableNow,
            'seo'          => ['title' => 'Dashboard — Quick GIGS', 'canonical' => url('/business')],
        ]);
    }

    public function switch(Request $request)
    {
        $id = (int) $request->input('company_id');

        if (Company::whereKey($id)->exists()) {
            $request->session()->put('company_id', $id);
            $request->user()?->update(['company_id' => $id]);
            return back()->with('toast', 'Workspace switched.');
        }

        return back()->with('toast', 'That workspace is not available.');
    }

    public function profile(Request $request)
    {
        return view('business.profile', [
            'company' => $this->company($request),
            'seo'     => ['title' => 'Business profile — Quick GIGS', 'canonical' => url('/business/profile')],
        ]);
    }

    public function updateProfile(Request $request)
    {
        $company = $this->company($request);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:80'],
            'person_name' => ['required', 'string', 'max:80'],
            'email'       => ['nullable', 'email', 'max:120'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'website'     => ['nullable', 'url', 'max:120'],
            'gstin'       => ['nullable', 'string', 'max:20'],
            'bio'         => ['nullable', 'string', 'max:500'],
            'address'     => ['nullable', 'string', 'max:200'],
            'city'        => ['nullable', 'string', 'max:60'],
            'state'       => ['nullable', 'string', 'max:60'],
            'pincode'     => ['nullable', 'string', 'max:10'],
            'industry'    => ['nullable', 'string', 'max:60'],
            'team_size'   => ['nullable', 'integer', 'min:1', 'max:10000'],
            'logo'        => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->storeUpload($request->file('logo'), 'companies');
        } else {
            unset($data['logo']);
        }

        $company->update($data);

        return back()->with('toast', 'Business profile saved.');
    }

    /* ───────────────────────── helpers ───────────────────────── */

    private function company(Request $request): Company
    {
        $user = $request->user();

        if ($user?->company_id && ($c = Company::find($user->company_id))) {
            return $c;
        }

        if (($id = $request->session()->get('company_id')) && ($c = Company::find($id))) {
            return $c;
        }

        $company = Company::create([
            'name'        => $user ? $user->name . "'s workspace" : 'My workspace',
            'person_name' => $user->name ?? 'Owner',
            'email'       => $user->email ?? null,
            'is_active'   => true,
        ]);

        $user?->update(['company_id' => $company->id]);
        $request->session()->put('company_id', $company->id);

        return $company;
    }

    /** Store an upload on the public disk, falling back to public/uploads. */
    private function storeUpload($file, string $folder): string
    {
        try {
            return $file->store($folder, 'public');
        } catch (\Throwable $e) {
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/' . $folder), $filename);
            return 'uploads/' . $folder . '/' . $filename;
        }
    }
}
