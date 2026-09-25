<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Creator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class OnboardingController extends Controller
{
    // — Business onboarding (beautiful 3-step) —
    public function business()
    {
        $seo = [
            'title' => "Get Started as a Business — As Easy as Ordering Food | QuickContent",
            'description' => "Business onboarding in 45 seconds — tell us what you need, get a verified creator in ~12 minutes, track live and pay only when you approve. India's First Quick Content Delivery.",
            'canonical' => url('/onboarding/business'),
            'image' => url('/og-onboarding-business.jpg'),
            'type' => 'website',
        ];
        return view('onboarding.business', compact('seo'));
    }

    public function storeBusiness(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:80',
            'person_name' => 'required|string|max:80',
            'email' => 'required|email|max:120',
            'phone' => 'required|string|max:20',
            'industry' => 'nullable|string|max:60',
            'team_size' => 'nullable|integer|min:1|max:10000',
            'need' => 'required|string|max:500',
            'category' => 'required|string|in:Reel,Thumbnail,AI Video,UGC Video,Barter Collab,Bundle,Custom',
            'budget' => 'nullable|string|max:20',
            'timeline' => 'required|string|max:20',
            'logo' => 'nullable|image|max:2048',
        ]);

        // Handle logo upload Hostinger-safe
        $logoPath = null;
        if ($request->hasFile('logo')) {
            try {
                $logoPath = $request->file('logo')->store('companies', 'public');
            } catch (\Throwable $e) {
                $fn = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/','',$request->file('logo')->getClientOriginalName());
                $request->file('logo')->move(public_path('uploads/companies'), $fn);
                $logoPath = 'uploads/companies/'.$fn;
            }
        }

        // Create company
        $company = Company::create([
            'name' => $data['company_name'],
            'slug' => Str::slug($data['company_name']).'-'.time(),
            'person_name' => $data['person_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'industry' => $data['industry'] ?? null,
            'team_size' => $data['team_size'] ?? null,
            'logo' => $logoPath,
            'initials' => strtoupper(substr($data['company_name'],0,2)),
            'plan' => 'Pro',
            'is_verified' => true,
            'is_active' => true,
            'bio' => $data['need'],
        ]);

        // Also create a business user for login (optional)
        try {
            if (!User::where('email',$data['email'])->exists()) {
                User::create([
                    'name' => $data['person_name'],
                    'email' => $data['email'],
                    'password' => Hash::make(Str::random(12)),
                    'role' => 'business',
                    'company_id' => $company->id,
                    'is_active' => true,
                ]);
            }
        } catch (\Throwable $e) {}

        $request->session()->put('company_id', $company->id);

        // If need is custom, we could create an order stub — for now just toast
        return redirect()->route('business.home')->with('toast', 'Welcome, '.$company->name.'! Your workspace is ready — as easy as ordering food. ✓');
    }

    // — Creator onboarding (beautiful 3-step) —
    public function creator()
    {
        $seo = [
            'title' => "Join as a Creator — Earn 90%, Weekly UPI | QuickContent",
            'description' => "Creator onboarding in 60 seconds — build your verified profile, showcase portfolio, get matched and earn 90% with weekly UPI. No bidding. Hostinger-ready.",
            'canonical' => url('/onboarding/creator'),
            'image' => url('/og-onboarding-creator.jpg'),
            'type' => 'website',
        ];
        return view('onboarding.creator', compact('seo'));
    }

    public function storeCreator(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:80',
            'handle' => 'required|string|max:40|unique:creators,handle',
            'email' => 'required|email|max:120|unique:creators,email',
            'phone' => 'required|string|max:20',
            'headline' => 'nullable|string|max:120',
            'bio' => 'nullable|string|max:800',
            'profile_type' => 'required|string|in:video_editor,ugc_creator,influencer,designer,hybrid',
            'skills' => 'required|string|max:300',
            'price_from' => 'required|integer|min:0|max:100000',
            'collab_type' => 'nullable|string|in:paid,barter,both',
            'barter_available' => 'nullable|boolean',
            'followers_count' => 'nullable|integer|min:0|max:10000000',
            'ugc_niches' => 'nullable|string|max:300',
            'upi_id' => 'nullable|string|max:60',
            'portfolio_url' => 'nullable|url|max:300',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if (!str_starts_with($data['handle'], '@')) $data['handle'] = '@'.$data['handle'];
        $data['skills'] = array_values(array_filter(array_map('trim', explode(',', $data['skills']))));
        $data['ugc_niches'] = isset($data['ugc_niches']) ? array_values(array_filter(array_map('trim', explode(',', $data['ugc_niches'])))) : null;
        $data['barter_available'] = $request->boolean('barter_available') || in_array($data['collab_type'] ?? 'paid', ['barter','both']);

        if ($request->hasFile('avatar')) {
            try {
                $data['avatar'] = $request->file('avatar')->store('creators', 'public');
            } catch (\Throwable $e) {
                $fn = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/','',$request->file('avatar')->getClientOriginalName());
                $request->file('avatar')->move(public_path('uploads/creators'), $fn);
                $data['avatar'] = 'uploads/creators/'.$fn;
            }
        }

        $creator = Creator::create([
            'name' => $data['name'],
            'handle' => $data['handle'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'headline' => $data['headline'] ?? 'Verified Creator',
            'bio' => $data['bio'] ?? null,
            'profile_type' => $data['profile_type'],
            'skills' => $data['skills'],
            'price_from' => $data['price_from'],
            'collab_type' => $data['collab_type'] ?? 'paid',
            'barter_available' => $data['barter_available'] ?? false,
            'ugc_niches' => $data['ugc_niches'] ?? null,
            'followers_count' => $data['followers_count'] ?? 0,
            'upi_id' => $data['upi_id'] ?? null,
            'portfolio_url' => $data['portfolio_url'] ?? null,
            'avatar' => $data['avatar'] ?? null,
            'is_available' => true,
            'is_verified' => false, // admin verifies — shows check details
            'is_featured' => false,
            'rating' => 5.0,
            'reviews_count' => 0,
            'orders_count' => 0,
        ]);

        // Create user link
        try {
            if (!User::where('email',$data['email'])->exists()) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make(Str::random(12)),
                    'role' => 'creator',
                    'creator_id' => $creator->id,
                    'is_active' => true,
                ]);
                $creator->update(['user_id'=>$user->id]);
            }
        } catch (\Throwable $e) {}

        $request->session()->put('creator_id', $creator->id);

        return redirect()->route('creator.dashboard')->with('toast', 'Welcome, '.$creator->name.'! Profile created — we’ll verify your tick (14px perfect circle) shortly. You’re now discoverable. ✓');
    }
}
