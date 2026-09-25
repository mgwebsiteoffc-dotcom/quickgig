<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Creator;
use App\Models\PortfolioItem;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Concerns\ResolvesActor;

class CreatorController extends Controller
{
    use ResolvesActor;

    public function dashboard(Request $request)
    {
        $creator = $this->currentCreator($request);
        $creator->load(['portfolio' => fn($q) => $q->where('is_published', true)->limit(6)]);

        $activeOrders = Order::with(['company','service'])
            ->where('creator_id', $creator->id)
            ->whereIn('status', ['working','review'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $stats = [
            'orders'   => $creator->orders_count,
            'rating'   => $creator->rating,
            'response' => $creator->response_minutes.'m',
            'on_time'  => $creator->on_time_rate.'%',
        ];

        return view('creator.dashboard', compact('creator','activeOrders','stats'));
    }

    /** Public, unauthenticated creator profile (SEO). */
    public function publicProfile($id)
    {
        $c = Creator::with(['portfolio' => fn($q) => $q->where('is_published', true)])->findOrFail($id);

        return view('creator.public', compact('c'));
    }

    public function profile(Request $request)
    {
        $creator = $this->currentCreator($request);
        $portfolio = $creator->portfolio()->orderBy('sort_order')->get();
        return view('creator.profile', compact('creator','portfolio'));
    }

    public function updateProfile(Request $request)
    {
        $creator = $this->currentCreator($request);

        $data = $request->validate([
            'name' => 'required|string|max:80',
            'handle' => 'required|string|max:40|unique:creators,handle,'.$creator->id,
            'email' => 'nullable|email|max:120|unique:creators,email,'.$creator->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:800',
            'headline' => 'nullable|string|max:120',
            'location' => 'nullable|string|max:80',
            'price_from' => 'nullable|integer|min:0|max:100000',
            'skills' => 'nullable|string|max:300', // comma separated
            'languages' => 'nullable|string|max:200',
            'upi_id' => 'nullable|string|max:60',
            'portfolio_url' => 'nullable|url|max:200',
            'instagram' => 'nullable|url|max:200',
            'youtube' => 'nullable|url|max:200',
            'avatar' => 'nullable|image|max:2048',
            'cover' => 'nullable|image|max:3072',
            // dynamic
            'profile_type' => 'nullable|in:video_editor,ugc_creator,influencer,designer,hybrid',
            'collab_type' => 'nullable|in:paid,barter,both',
            'barter_available' => 'nullable|boolean',
            'followers_count' => 'nullable|integer|min:0|max:10000000',
            'ugc_niches' => 'nullable|string|max:300',
        ]);

        // Handle skills/languages/ugc_niches comma → json + barter boolean
        if (isset($data['skills'])) {
            $data['skills'] = array_values(array_filter(array_map('trim', explode(',', $data['skills']))));
        }
        if (isset($data['languages'])) {
            $data['languages'] = array_values(array_filter(array_map('trim', explode(',', $data['languages']))));
        }
        if (isset($data['ugc_niches'])) {
            $data['ugc_niches'] = array_values(array_filter(array_map('trim', explode(',', $data['ugc_niches']))));
        }
        $data['barter_available'] = $request->boolean('barter_available');
        if (empty($data['profile_type'])) unset($data['profile_type']);
        if (empty($data['collab_type'])) unset($data['collab_type']);

        foreach (['avatar','cover'] as $field) {
            if ($request->hasFile($field)) {
                try {
                    $path = $request->file($field)->store('creators', 'public');
                    $data[$field] = $path;
                } catch (\Throwable $e) {
                    $filename = time().'_'.$field.'_'.preg_replace('/[^a-zA-Z0-9._-]/','',$request->file($field)->getClientOriginalName());
                    $request->file($field)->move(public_path('uploads/creators'), $filename);
                    $data[$field] = 'uploads/creators/'.$filename;
                }
            } else {
                unset($data[$field]);
            }
        }

        // Ensure handle starts with @
        if (!str_starts_with($data['handle'], '@')) $data['handle'] = '@'.$data['handle'];

        $creator->update($data);

        return back()->with('toast', 'Creator profile updated ✓');
    }

    public function toggleAvailability(Request $request)
    {
        $creator = $this->currentCreator($request);
        $creator->update(['is_available' => ! $creator->is_available]);

        return response()->json([
            'is_available' => $creator->is_available,
            'toast'        => $creator->is_available ? 'You are now Available ●' : 'Set to Busy',
        ]);
    }

    public function order(Request $request, $order)
    {
        $creatorId = $this->currentCreator($request)->id;

        $o = Order::with(['company','service','deliveries'])
            ->where('creator_id', $creatorId)
            ->where(fn($q) => $q->where('uid', $order)->orWhere('id', $order))
            ->firstOrFail();

        return view('creator.order', compact('o'));
    }

    public function deliver(Request $request, $order)
    {
        $data = $request->validate([
            'delivery_url' => 'required_without:file|nullable|url|max:500',
            'note'         => 'nullable|string|max:500',
            'file'         => 'nullable|file|max:51200|mimes:mp4,mov,webm,zip,png,jpg,jpeg,pdf,srt',
        ]);

        $creatorId = $this->currentCreator($request)->id;

        $o = Order::where('creator_id', $creatorId)
            ->where(fn($q) => $q->where('uid', $order)->orWhere('id', $order))
            ->firstOrFail();

        $payload = [
            'order_id'     => $o->id,
            'creator_id'   => $creatorId,
            'delivery_url' => $data['delivery_url'] ?? null,
            'note'         => $data['note'] ?? null,
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            try {
                $path = $file->store('deliveries/'.$o->id, 'public');
            } catch (\Throwable $e) {
                $safe = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                $file->move(public_path('uploads/deliveries'), $safe);
                $path = 'uploads/deliveries/'.$safe;
            }

            $payload += [
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime'      => $file->getClientMimeType(),
                'size'      => $file->getSize(),
            ];
        }

        \App\Models\OrderDelivery::create($payload);

        $o->update(['status' => 'review', 'progress' => 90]);

        return back()->with('toast', 'Delivered for review — company notified');
    }

    // Portfolio CRUD — fully working
    public function storePortfolio(Request $request)
    {
        $creator = $this->currentCreator($request);
        $data = $request->validate([
            'title'=>'required|string|max:120',
            'description'=>'nullable|string|max:800',
            'video_url'=>'nullable|url|max:300',
            'external_url'=>'nullable|url|max:300',
            'category'=>'nullable|string|max:40',
            'tags'=>'nullable|string|max:200',
            'cover'=>'nullable|image|max:3072',
        ]);
        if (isset($data['tags'])) $data['tags'] = array_values(array_filter(array_map('trim', explode(',', $data['tags']))));
        if ($request->hasFile('cover')) {
            try { $data['cover'] = $request->file('cover')->store('portfolio', 'public'); }
            catch(\Throwable $e){ $fn=time().'_'.preg_replace('/[^a-zA-Z0-9._-]/','',$request->file('cover')->getClientOriginalName()); $request->file('cover')->move(public_path('uploads/portfolio'), $fn); $data['cover']='uploads/portfolio/'.$fn; }
        }
        $data['creator_id']=$creator->id;
        $data['slug']=\Illuminate\Support\Str::slug($data['title']).'-'.time();
        PortfolioItem::create($data);
        return back()->with('toast','Portfolio item added ✓');
    }

    public function destroyPortfolio(Request $request, $id)
    {
        $creatorId = $this->currentCreator($request)->id;
        PortfolioItem::where('creator_id',$creatorId)->where('id',$id)->delete();
        return back()->with('toast','Portfolio item removed');
    }
}
