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
        $request->validate(['profile_type'=>'required|in:video_editor,ugc_creator,influencer,designer,hybrid']);
        $creator = Creator::findOrFail($id);
        $creator->update([
            'profile_type'=>$request->profile_type,
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

    public function destroy(string $id)
    {
        Creator::findOrFail($id)->delete();
        return back()->with('toast','Creator #'.$id.' removed');
    }
}
