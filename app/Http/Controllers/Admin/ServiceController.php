<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Creator;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    private function categories()
    {
        return ['Reel','Thumbnail','AI Video','UGC Video','Barter Collab','Bundle'];
    }

    public function index(Request $request)
    {
        $cat = $request->query('category');
        $profile = $request->query('profile_type');
        $priceType = $request->query('price_type');
        $q = $request->query('q');

        try {
            $query = Service::with('creator')->orderByDesc('updated_at');
            if ($cat && $cat!=='all') $query->where('category', $cat);
            if ($profile && $profile!=='all') $query->where('profile_type', $profile);
            if ($priceType && $priceType!=='all') $query->where('price_type', $priceType);
            if ($q) $query->where('title','like',"%$q%");
            $services = $query->paginate(12)->withQueryString();
            $creators = Creator::where('is_verified',true)->orderBy('name')->get(['id','name','handle','profile_type']);
            return view('admin.services.index', [
                'services'=>$services,
                'creators'=>$creators,
                'cat'=>$cat ?? 'all',
                'profile'=>$profile ?? 'all',
                'priceType'=>$priceType ?? 'all',
                'q'=>$q,
                'categories'=>$this->categories(),
                'isDb'=>true
            ]);
        } catch (\Throwable $e) {
            // Fallback dummy for fresh install
            $services = collect([
                ['id'=>'reel1','title'=>'Engaging Talking-Head Reel','cat'=>'Reel','profile_type'=>'video_editor','price'=>2499,'price_type'=>'paid','time'=>'1 Day','orders'=>1240,'rating'=>4.9,'active'=>true,'img'=>'https://images.unsplash.com/photo-1574717025058-2f8737d2e2b7?w=400&q=80'],
                ['id'=>'ugc1','title'=>'UGC Video — Unboxing + Testimonial (30s)','cat'=>'UGC Video','profile_type'=>'ugc_creator','price'=>1999,'price_type'=>'paid','time'=>'1 Day','orders'=>340,'rating'=>4.8,'active'=>true,'img'=>'https://images.unsplash.com/photo-1526948128573-703ee1aeb6fa?w=600&q=80'],
                ['id'=>'barter1','title'=>'Barter Collab — Reel + Story','cat'=>'Barter Collab','profile_type'=>'influencer','price'=>0,'price_type'=>'barter','barter_value'=>2000,'time'=>'3 Days','orders'=>89,'rating'=>4.7,'active'=>true,'img'=>'https://images.unsplash.com/photo-1557838923-2985c318be48?w=600&q=80'],
            ]);
            if ($cat && $cat!=='all') $services = $services->where('cat',$cat);
            return view('admin.services.index', ['services'=>$services,'creators'=>collect(),'cat'=>$cat??'all','profile'=>'all','priceType'=>'all','q'=>$q,'categories'=>$this->categories(),'isDb'=>false]);
        }
    }

    public function create()
    {
        $creators = Creator::where('is_verified',true)->orderBy('name')->get(['id','name','handle','profile_type']);
        $categories = $this->categories();
        return view('admin.services.create', compact('creators','categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'creator_id'=>'required|exists:creators,id',
            'title'=>'required|string|max:140',
            'slug'=>'nullable|string|max:160|unique:services,slug',
            'description'=>'nullable|string|max:2000',
            'category'=>'required|in:Reel,Thumbnail,AI Video,UGC Video,Barter Collab,Bundle',
            'profile_type'=>'required|in:video_editor,ugc_creator,influencer,designer,hybrid,any',
            'price_type'=>'required|in:paid,barter,hybrid',
            'price'=>'required_if:price_type,paid,hybrid|nullable|numeric|min:0|max:100000',
            'mrp'=>'nullable|numeric|min:0',
            'barter_value'=>'nullable|numeric|min:0',
            'delivery_days'=>'required|integer|min:1|max:30',
            'deliverables'=>'nullable|string|max:500',
            'revision_count'=>'nullable|integer|min:0|max:10',
            'collab_terms'=>'nullable|string|max:500',
            'badge'=>'nullable|string|max:20',
            'cover'=>'nullable|image|max:3072',
        ]);

        if (empty($data['slug'])) $data['slug'] = Str::slug($data['title']);
        if (isset($data['deliverables'])) $data['deliverables'] = array_values(array_filter(array_map('trim', explode(',', $data['deliverables']))));
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_barter'] = $data['price_type']==='barter';
        if ($data['price_type']==='barter') $data['price']=0;

        if ($request->hasFile('cover')) {
            try { $data['cover'] = $request->file('cover')->store('services', 'public'); }
            catch(\Throwable $e){ $fn=time().'_'.preg_replace('/[^a-zA-Z0-9._-]/','',$request->file('cover')->getClientOriginalName()); $request->file('cover')->move(public_path('uploads/services'), $fn); $data['cover']='uploads/services/'.$fn; }
        }

        Service::create($data);
        return redirect()->route('admin.services.index')->with('toast','Service created — dynamic, live on marketplace ✓');
    }

    public function edit(string $id)
    {
        $service = Service::with('creator')->findOrFail($id);
        $creators = Creator::where('is_verified',true)->orderBy('name')->get(['id','name','handle','profile_type']);
        $categories = $this->categories();
        return view('admin.services.edit', compact('service','creators','categories'));
    }

    public function update(Request $request, string $id)
    {
        $service = Service::findOrFail($id);
        $data = $request->validate([
            'creator_id'=>'required|exists:creators,id',
            'title'=>'required|string|max:140',
            'slug'=>'required|string|max:160|unique:services,slug,'.$service->id,
            'description'=>'nullable|string|max:2000',
            'category'=>'required|in:Reel,Thumbnail,AI Video,UGC Video,Barter Collab,Bundle',
            'profile_type'=>'required|in:video_editor,ugc_creator,influencer,designer,hybrid,any',
            'price_type'=>'required|in:paid,barter,hybrid',
            'price'=>'nullable|numeric|min:0|max:100000',
            'mrp'=>'nullable|numeric|min:0',
            'barter_value'=>'nullable|numeric|min:0',
            'delivery_days'=>'required|integer|min:1|max:30',
            'deliverables'=>'nullable|string|max:500',
            'revision_count'=>'nullable|integer|min:0|max:10',
            'collab_terms'=>'nullable|string|max:500',
            'badge'=>'nullable|string|max:20',
            'cover'=>'nullable|image|max:3072',
            'is_active'=>'nullable|boolean',
        ]);
        if (isset($data['deliverables'])) $data['deliverables'] = array_values(array_filter(array_map('trim', explode(',', $data['deliverables']))));
        $data['is_active'] = $request->boolean('is_active');
        $data['is_barter'] = $data['price_type']==='barter';
        if ($data['price_type']==='barter') $data['price']=0;
        if ($request->hasFile('cover')) {
            try { $data['cover'] = $request->file('cover')->store('services', 'public'); }
            catch(\Throwable $e){ $fn=time().'_'.preg_replace('/[^a-zA-Z0-9._-]/','',$request->file('cover')->getClientOriginalName()); $request->file('cover')->move(public_path('uploads/services'), $fn); $data['cover']='uploads/services/'.$fn; }
        } else unset($data['cover']);
        $service->update($data);
        return redirect()->route('admin.services.index')->with('toast',"Service '{$service->title}' updated ✓");
    }

    public function toggle(string $id)
    {
        $s = Service::findOrFail($id);
        $s->update(['is_active'=>!$s->is_active]);
        return back()->with('toast', $s->title.' — '.($s->is_active?'Active':'Hidden'));
    }

    public function destroy(string $id)
    {
        Service::findOrFail($id)->delete();
        return back()->with('toast','Service deleted');
    }
}
