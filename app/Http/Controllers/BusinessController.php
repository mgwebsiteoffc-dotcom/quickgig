<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Creator;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Concerns\ResolvesActor;

class BusinessController extends Controller
{
    use ResolvesActor;

    public function home(Request $request)
    {
        $companies = $this->selectableCompanies($request);
        $current   = $this->currentCompany($request);

        $currentArray = $current->toArray();
        $currentArray['person']  = $current->person_name;
        $currentArray['company'] = $current->name;

        $creators = Creator::verified()
            ->orderByDesc('is_featured')
            ->orderByDesc('rating')
            ->limit(12)
            ->get()
            ->map(fn (Creator $c) => [
                'id'           => $c->id,
                'name'         => $c->name,
                'handle'       => $c->handle,
                'img'          => $c->avatarUrl(),
                'followers'    => $c->reviews_count ? number_format($c->reviews_count) : '—',
                'posts'        => $c->orders_count ?: '0',
                'is_verified'  => $c->is_verified,
                'available'    => $c->is_available,
            ]);

        $services = Service::with('creator')
            ->active()
            ->whereHas('creator', fn ($q) => $q->where('is_verified', true))
            ->orderByDesc('sold_count')
            ->limit(12)
            ->get()
            ->map(function (Service $s) {
                $c = $s->creator;

                return [
                    'id'            => $s->id,
                    'title'         => $s->title,
                    'category'      => $s->category,
                    'profile_type'  => $s->profile_type,
                    'price_type'    => $s->price_type,
                    'display_price' => $s->displayPrice(),
                    'img'           => $s->cover
                        ? (filter_var($s->cover, FILTER_VALIDATE_URL) ? $s->cover : asset('storage/'.$s->cover))
                        : null,
                    'cimg'          => $c?->avatarUrl(),
                    'creator'       => $c?->name ?? 'Verified Creator',
                    'handle'        => $c?->handle ?? '@creator',
                    'price'         => $s->price,
                    'barter_value'  => $s->barter_value,
                    'rating'        => number_format($s->rating, 1),
                    'sold'          => $s->sold_count ? $s->sold_count.'+' : '0',
                    'time'          => $s->delivery_days.' Day',
                    'badge'         => $s->badge ?: ($s->price_type === 'barter' ? 'Barter • Product' : null),
                ];
            });

        $pros = Creator::verified()
            ->featured()
            ->limit(3)
            ->get()
            ->map(fn (Creator $p) => [
                'name'   => $p->name,
                'handle' => $p->handle,
                'img'    => $p->avatarUrl(),
                'avail'  => $p->is_available ? 'on' : 'soon',
                'label'  => $p->is_available ? 'Available now' : 'Busy',
            ]);

        return view('business.home', [
            'companies' => $companies,
            'current'   => $currentArray,
            'creators'  => $creators,
            'services'  => $services->toArray(),
            'pros'      => $pros,
        ]);
    }

    public function switch(Request $request)
    {
        $request->validate(['company_id' => 'required|integer']);

        $allowed = $this->selectableCompanies($request)->pluck('id');

        abort_unless($allowed->contains((int) $request->input('company_id')), 403, 'That business is not yours.');

        $request->session()->put('company_id', (int) $request->input('company_id'));

        return back()->with('toast', 'Switched company');
    }

    public function profile(Request $request)
    {
        $company = $this->currentCompany($request);

        return view('business.profile', compact('company'));
    }

    public function updateProfile(Request $request)
    {
        $company = $this->currentCompany($request);

        $data = $request->validate([
            'name' => 'required|string|max:80',
            'person_name' => 'required|string|max:80',
            'email' => 'nullable|email|max:120',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:120',
            'gstin' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'address' => 'nullable|string|max:200',
            'city' => 'nullable|string|max:60',
            'state' => 'nullable|string|max:60',
            'pincode' => 'nullable|string|max:10',
            'industry' => 'nullable|string|max:60',
            'team_size' => 'nullable|integer|min:1|max:10000',
            'logo' => 'nullable|image|max:2048', // 2MB
        ]);

        if ($request->hasFile('logo')) {
            // Hostinger shared: try storage/public, fallback to public/uploads
            try {
                $path = $request->file('logo')->store('companies', 'public');
                $data['logo'] = $path;
            } catch (\Throwable $e) {
                $filename = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/','',$request->file('logo')->getClientOriginalName());
                $request->file('logo')->move(public_path('uploads/companies'), $filename);
                $data['logo'] = 'uploads/companies/'.$filename;
            }
        } else {
            unset($data['logo']);
        }

        $company->update($data);

        return back()->with('toast', 'Business profile updated ✓');
    }

}
