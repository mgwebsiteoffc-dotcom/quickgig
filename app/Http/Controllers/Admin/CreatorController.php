<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Creator;
use App\Models\Service;

class CreatorController extends Controller
{
    private function baseQuery(Request $request)
    {
        $q = $request->query('q');
        $filter = $request->query('filter','all');
        $profile = $request->query('profile_type');

        $query = Creator::query()->orderByDesc('updated_at');

        if ($filter==='available') $query->where('is_available', true);
        if ($filter==='pending') $query->where('is_verified', false);
        if ($filter==='verified') $query->where('is_verified', true);
        if ($filter==='barter') $query->where('barter_available', true);
        if ($profile && $profile!=='all') $query->where('profile_type', $profile);
        if ($q) $query->where(fn($qq)=> $qq->where('name','like',"%$q%")->orWhere('handle','like',"%$q%")->orWhere('email','like',"%$q%"));

        return $query;
    }

    public function index(Request $request)
    {
        // Try DB, fallback to dummy for fresh install
        try {
            $query = $this->baseQuery($request);
            $creators = $query->paginate(12)->withQueryString();
            // if no table yet, fallback
            if ($creators->total()===0 && !Creator::exists()) throw new \Exception('empty');
            $profileTypes = \App\Models\Creator::PROFILE_TYPES;
            return view('admin.creators.index', [
                'creators'=>$creators,
                'q'=>$request->query('q'),
                'filter'=>$request->query('filter','all'),
                'profile'=>$request->query('profile_type','all'),
                'profileTypes'=>$profileTypes,
                'isDb'=>true
            ]);
        } catch (\Throwable $e) {
            // Fallback dummy for fresh install before migrate
            $all = collect([
                ['id'=>1,'name'=>'Priya Sharma','handle'=>'@priyaedits','email'=>'priya@quickcontent.in','avatar'=>'https://i.pravatar.cc/150?img=5','rating'=>4.9,'orders'=>142,'earned'=>'₹3.2L','verified'=>true,'available'=>true,'skills'=>'Talking-Head, Retention','joined'=>'2024-02-14','profile_type'=>'video_editor'],
                ['id'=>2,'name'=>'Rahul Verma','handle'=>'@rahulcuts','email'=>'rahul@quickcontent.in','avatar'=>'https://i.pravatar.cc/150?img=12','rating'=>4.9,'orders'=>98,'earned'=>'₹2.1L','verified'=>true,'available'=>true,'skills'=>'Retention, Captions','joined'=>'2024-03-02','profile_type'=>'video_editor'],
                ['id'=>5,'name'=>'Riya Malhotra','handle'=>'@ugc_riya','email'=>'riya.ugc@quickcontent.in','avatar'=>'https://i.pravatar.cc/150?img=26','rating'=>4.8,'orders'=>120,'earned'=>'₹89k','verified'=>true,'available'=>true,'skills'=>'UGC, Unboxing','joined'=>'2024-05-18','profile_type'=>'ugc_creator','barter'=>true],
                ['id'=>6,'name'=>'Aman Influencer','handle'=>'@barter_aman','email'=>'aman.barter@quickcontent.in','avatar'=>'https://i.pravatar.cc/150?img=18','rating'=>4.7,'orders'=>89,'earned'=>'—','verified'=>false,'available'=>true,'skills'=>'Reels, Reviews','joined'=>'2024-06-01','profile_type'=>'influencer','barter'=>true],
            ]);
            $filter = $request->query('filter','all');
            $q = $request->query('q');
            $creators = $all;
            if ($filter==='available') $creators = $creators->where('available',true);
            if ($filter==='pending') $creators = $creators->where('verified',false);
            if ($q) $creators = $creators->filter(fn($c)=> str_contains(strtolower($c['name'].$c['handle'].$c['skills']), strtolower($q)));
            return view('admin.creators.index', ['creators'=>$creators,'q'=>$q,'filter'=>$filter,'profile'=>'all','profileTypes'=>\App\Models\Creator::PROFILE_TYPES,'isDb'=>false]);
        }
    }

    public function edit(string $id)
    {
        return view('admin.creators.edit', ['creator' => Creator::findOrFail($id)]);
    }

    public function update(Request $request, string $id)
    {
        $creator = Creator::findOrFail($id);
        $data = $request->validate([
            'name'=>'required|string|max:120','email'=>'required|email|max:160','phone'=>'nullable|string|max:30',
            'handle'=>'nullable|string|max:80','headline'=>'nullable|string|max:180','bio'=>'nullable|string|max:2000',
            'location'=>'nullable|string|max:120','price_from'=>'nullable|numeric|min:0','skills'=>'nullable|string|max:500',
            'portfolio_url'=>'nullable|url|max:255','is_available'=>'nullable|boolean','is_verified'=>'nullable|boolean',
        ]);
        $data['skills'] = array_values(array_filter(array_map('trim', explode(',', (string) ($data['skills'] ?? '')))));
        $data['is_available'] = $request->boolean('is_available');
        $data['is_verified'] = $request->boolean('is_verified');
        $creator->update($data);
        return redirect()->route('admin.creators.show', $creator->id)->with('toast', 'Creator profile updated.');
    }

    public function show(string $id)
    {
        $creator = Creator::with(['portfolio','services','orders'])->findOrFail($id);
        $services = $creator->services()->orderByDesc('created_at')->get();
        $orders = $creator->orders()->with('company')->orderByDesc('created_at')->limit(6)->get();
        return view('admin.creators.show', compact('creator','services','orders'));
    }

    public function toggleVerify(Request $request, string $id)
    {
        $creator = Creator::findOrFail($id);
        $new = !$creator->is_verified;
        $creator->update([
            'is_verified'=>$new,
            'verified_at'=>$new ? now() : null,
            'verification_notes'=>$request->input('verification_notes') ?: $creator->verification_notes,
            'rejection_reason'=> $new ? null : $request->input('rejection_reason'),
        ]);
        return back()->with('toast', $creator->name.' — '.($new ? 'Verified ✓ (blue tick live)' : 'Unverified'));
    }

    public function updateProfileType(Request $request, string $id)
    {
        $request->validate(['profile_type'=>'required|in:video_editor,ugc_creator,influencer,designer,hybrid,agency','agency_name'=>'nullable|string|max:120','team_size'=>'nullable|integer|min:1|max:500','team_description'=>'nullable|string|max:1000','team_services'=>'nullable|string|max:500']);
        $creator = Creator::findOrFail($id);
        $creator->update([
            'profile_type'=>$request->profile_type,
            'account_kind'=>$request->profile_type === 'agency' ? 'agency' : 'individual',
            'agency_name'=>$request->input('agency_name'),
            'team_size'=>$request->input('team_size'),
            'team_services'=>array_values(array_filter(array_map('trim', explode(',', (string) $request->input('team_services'))))),
            'team_description'=>$request->input('team_description'),
            'barter_available'=>$request->boolean('barter_available'),
            'collab_type'=>$request->input('collab_type','paid'),
        ]);
        return back()->with('toast','Profile type updated to '.$creator->profileLabel());
    }

    public function toggleAvailability(string $id)
    {
        $creator = Creator::findOrFail($id);
        $creator->update(['is_available'=>!$creator->is_available]);
        return back()->with('toast',$creator->name.' is now '.($creator->is_available?'Available':'Busy'));
    }

    public function toggleFeatured(string $id)
    {
        $c = Creator::findOrFail($id);
        $c->update(['is_featured'=>!$c->is_featured]);
        return back()->with('toast',$c->name.' featured '.($c->is_featured?'enabled':'disabled'));
    }

    public function togglePortfolio(string $creator, string $portfolio)
    {
        $item = \App\Models\PortfolioItem::where('creator_id', $creator)->findOrFail($portfolio);
        $item->update(['is_published' => ! $item->is_published]);
        return back()->with('toast', 'Work reference '.($item->is_published ? 'published' : 'hidden').' from the marketplace.');
    }

    public function featurePortfolio(string $creator, string $portfolio)
    {
        $item = \App\Models\PortfolioItem::where('creator_id', $creator)->findOrFail($portfolio);
        $item->update(['is_featured' => ! $item->is_featured]);
        return back()->with('toast', 'Work reference '.($item->is_featured ? 'featured' : 'unfeatured').'.');
    }

    public function destroyPortfolio(string $creator, string $portfolio)
    {
        \App\Models\PortfolioItem::where('creator_id', $creator)->findOrFail($portfolio)->delete();
        return back()->with('toast', 'Work reference deleted.');
    }

    public function destroy(string $id)
    {
        Creator::findOrFail($id)->delete();
        return back()->with('toast','Creator #'.$id.' removed');
    }
}
