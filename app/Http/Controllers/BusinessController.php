<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Creator;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;

class BusinessController extends Controller
{
    public function home(Request $request)
    {
        // Real DB with fallback for fresh install
        try {
            $companies = Company::where('is_active', true)->orderBy('id')->get();
            $creators = Creator::where('is_verified', true)->orderByDesc('is_featured')->orderByDesc('rating')->limit(12)->get();
            $services = Service::with('creator')->whereHas('creator', fn($q)=>$q->where('is_verified', true))->orderByDesc('sold_count')->limit(12)->get();
            $pros = Creator::where('is_featured', true)->where('is_verified', true)->limit(3)->get();
            // If DB empty, fallback to seeded arrays
            if ($companies->isEmpty()) $companies = collect($this->fallbackCompanies());
            if ($creators->isEmpty()) $creators = collect($this->fallbackCreators());
            if ($services->isEmpty()) $services = collect($this->fallbackServices());
            if ($pros->isEmpty()) $pros = collect($this->fallbackPros());
        } catch (\Throwable $e) {
            $companies = collect($this->fallbackCompanies());
            $creators = collect($this->fallbackCreators());
            $services = collect($this->fallbackServices());
            $pros = collect($this->fallbackPros());
        }

        $currentId = $request->session()->get('company_id', $companies->first()['id'] ?? 1);
        $current = $companies->firstWhere('id', $currentId) ?? $companies->first();

        // Handle both Model and array
        $currentArray = $current instanceof \Illuminate\Database\Eloquent\Model ? $current->toArray() : $current;
        // Normalize for view
        if (isset($currentArray['person_name']) && !isset($currentArray['person'])) $currentArray['person'] = $currentArray['person_name'];
        if (isset($currentArray['name']) && !isset($currentArray['company'])) $currentArray['company'] = $currentArray['name'];

        // — Transform DB models into view shape expected by prototype (no dummy, real DB) —
        $isModelCreators = $creators->first() instanceof \App\Models\Creator;
        if ($isModelCreators) {
            $creators = $creators->map(function($c){
                return [
                    'id'=>$c->id,
                    'name'=>$c->name,
                    'handle'=>$c->handle,
                    'img'=>$c->avatarUrl(),
                    'followers'=> $c->reviews_count ? number_format($c->reviews_count) : '—',
                    'posts'=> $c->orders_count ? $c->orders_count : '0',
                    'is_verified'=>$c->is_verified,
                    'available'=>$c->is_available,
                ];
            });
        } else {
            // fallback already has img, but ensure
            $creators = $creators->map(function($c){
                if (is_array($c) && isset($c['avatar']) && !isset($c['img'])) $c['img']=$c['avatar'];
                if (!isset($c['followers'])) $c['followers']='399K';
                if (!isset($c['posts'])) $c['posts']='139';
                return $c;
            });
        }

        $isModelServices = $services->first() instanceof \App\Models\Service || ($services->first() && is_object($services->first()) && isset($services->first()->cover));
        if ($isModelServices && $services->first() instanceof \App\Models\Service) {
            $services = $services->map(function($s){
                $c = $s->creator;
                return [
                    'id'=>$s->id,
                    'title'=>$s->title,
                    'category'=>$s->category,
                    'profile_type'=>$s->profile_type,
                    'price_type'=>$s->price_type,
                    'display_price'=>$s->displayPrice(),
                    'img'=> filter_var($s->cover, FILTER_VALIDATE_URL) ? $s->cover : ($s->cover ? asset('storage/'.$s->cover) : 'https://images.unsplash.com/photo-1574717025058-2f8737d2e2b7?w=400&q=80'),
                    'cimg'=> $c ? $c->avatarUrl() : 'https://i.pravatar.cc/100?img=5',
                    'creator'=> $c ? $c->name : 'Verified Creator',
                    'handle'=> $c ? $c->handle : '@creator',
                    'price'=>$s->price,
                    'barter_value'=>$s->barter_value,
                    'rating'=>number_format($s->rating,1),
                    'sold'=> $s->sold_count ? $s->sold_count.'+' : '0',
                    'time'=>$s->delivery_days.' Day',
                    'badge'=> $s->badge ?: ($s->price_type==='barter' ? 'Barter • Product' : ($s->category==='UGC Video' ? 'UGC • Paid' : 'Best seller')),
                ];
            });
        } else {
            // fallback already has img/cimg etc — normalize cover→img
            $services = $services->map(function($s){
                if (is_array($s) && isset($s['cover']) && !isset($s['img'])) $s['img']=$s['cover'];
                if (!isset($s['cimg'])) $s['cimg']='https://i.pravatar.cc/100?img=5';
                if (!isset($s['creator'])) $s['creator']='Verified Creator';
                if (!isset($s['handle'])) $s['handle']='@creator';
                if (!isset($s['sold'])) $s['sold']='1k+';
                if (!isset($s['time'])) $s['time']='1 Day';
                return $s;
            });
        }

        // Pros for Available now section
        $isModelPros = $pros->first() instanceof \App\Models\Creator;
        if ($isModelPros) {
            $pros = $pros->map(function($p){
                return [
                    'name'=>$p->name,
                    'handle'=>$p->handle,
                    'img'=>$p->avatarUrl(),
                    'avail'=>$p->is_available ? 'on' : 'soon',
                    'label'=>$p->is_available ? 'Available now' : 'In 2 hours',
                ];
            });
        }

        // Ensure services is array for @json and array_slice in Blade
        $servicesArray = $services instanceof \Illuminate\Support\Collection ? $services->toArray() : (array)$services;

        return view('business.home', [
            'companies' => $companies,
            'current' => $currentArray,
            'creators' => $creators,
            'services' => $servicesArray,
            'pros' => $pros,
        ]);
    }

    public function switch(Request $request)
    {
        $id = $request->input('company_id', 1);
        // validate exists
        if (Company::where('id', $id)->exists()) {
            $request->session()->put('company_id', $id);
        }
        return back()->with('toast', 'Switched company');
    }

    // — Business Profile — fully working, persisted to DB + file upload (Hostinger: public/uploads fallback)
    public function profile(Request $request)
    {
        $companyId = $request->session()->get('company_id', 1);
        $company = Company::findOrFail($companyId);
        return view('business.profile', compact('company'));
    }

    public function updateProfile(Request $request)
    {
        $companyId = $request->session()->get('company_id', 1);
        $company = Company::findOrFail($companyId);

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

    // Fallbacks for fresh install before migrate
    private function fallbackCompanies(){ return [
        ['id'=>1, 'company'=>'Avante Studio', 'name'=>'Avante Studio','person'=>'Rohan Sharma','person_name'=>'Rohan Sharma', 'initials'=>'AS', 'plan'=>'Pro'],
        ['id'=>2, 'company'=>'BrandScale Media', 'name'=>'BrandScale Media','person'=>'Priya Kapoor','person_name'=>'Priya Kapoor', 'initials'=>'BS', 'plan'=>'Team'],
        ['id'=>3, 'company'=>'GrowthX Labs', 'name'=>'GrowthX Labs','person'=>'Aman Verma','person_name'=>'Aman Verma', 'initials'=>'GX', 'plan'=>'Starter'],
    ];}
    private function fallbackCreators(){ return [
        ['name'=>'Dev Taneja','handle'=>'@devtalksbusiness','avatar'=>'https://i.pravatar.cc/100?img=11','followers'=>'399K','posts'=>'139','is_verified'=>true],
        ['name'=>'Himani Negi','handle'=>'@himaniexplores','avatar'=>'https://i.pravatar.cc/100?img=32','is_verified'=>true],
        ['name'=>'Sudhanshu','handle'=>'@aikabubble','avatar'=>'https://i.pravatar.cc/100?img=33','is_verified'=>true],
        ['name'=>'Priya Sharma','handle'=>'@priyaedits','avatar'=>'https://i.pravatar.cc/100?img=5','is_verified'=>true],
        ['name'=>'Ankit Jha','handle'=>'@fullstackmodiji','avatar'=>'https://i.pravatar.cc/100?img=15','is_verified'=>true],
    ];}
    private function fallbackServices(){ return [
        ['id'=>'reel1','title'=>'Engaging Talking-Head Reel','cover'=>'https://images.unsplash.com/photo-1574717025058-2f8737d2e2b7?w=400&q=80','delivery_days'=>1,'price'=>2499,'mrp'=>3299,'rating'=>'4.9','sold_count'=>'5k+','badge'=>'Best seller','creator'=> (object)['name'=>'Priya Sharma','handle'=>'@priyaedits','avatar'=>'https://i.pravatar.cc/100?img=5']],
        ['id'=>'reel2','title'=>'Advanced Retention Reel','cover'=>'https://images.unsplash.com/photo-1536243287037-7f1444775910?w=400&q=80','delivery_days'=>1,'price'=>3999,'rating'=>'4.7','creator'=> (object)['name'=>'Rahul Verma','handle'=>'@rahulcuts','avatar'=>'https://i.pravatar.cc/100?img=12']],
    ];}
    private function fallbackPros(){ return [
        ['name'=>'Priya Sharma','handle'=>'@priyaedits','headline'=>'Talking-Head • For @devtalksbusiness','rating'=>'4.9','price_from'=>2499,'is_available'=>true,'avatar'=>'https://i.pravatar.cc/100?img=5'],
        ['name'=>'Rahul Verma','handle'=>'@rahulcuts','headline'=>'Retention • For @priyanksingh','rating'=>'4.9','price_from'=>2499,'is_available'=>true,'avatar'=>'https://i.pravatar.cc/100?img=12'],
    ];}
}
