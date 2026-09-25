<?php

namespace App\Http\Controllers;

use App\Models\Creator;
use App\Models\Order;
use App\Models\Payout;
use App\Support\Notifier;
use App\Notifications\DeliverySubmitted;
use App\Models\PortfolioItem;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CreatorController extends Controller
{
    /** Freelancer studio — gigs, earnings, availability. */
    public function dashboard(Request $request)
    {
        $creator = $this->creator($request);

        $orders = Order::with(['company', 'service'])
            ->where('creator_id', $creator->id)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $base = Order::where('creator_id', $creator->id);

        $payouts = Payout::where('creator_id', $creator->id)->latest('id')->get();

        $stats = [
            'active'    => (clone $base)->whereIn('status', ['working', 'review'])->count(),
            'pending'   => (int) $payouts->whereIn('status', ['pending', 'on_hold', 'processing'])->sum('amount'),
            'earned'    => (int) $payouts->where('status', 'paid')->sum('amount'),
            'rating'    => number_format((float) $creator->rating, 1),
        ];

        return view('dashboard.creator', [
            'creator'   => $creator,
            'orders'    => $orders,
            'stats'     => $stats,
            'portfolio' => $creator->portfolio()->limit(6)->get(),
            'payouts'   => $payouts->take(6),
            'seo'       => ['title' => 'Freelancer studio — Quick GIGS', 'canonical' => url('/creator')],
        ]);
    }

    public function profile(Request $request)
    {
        $creator = $this->creator($request);

        return view('creator.profile', [
            'creator'     => $creator,
            'skillGroups' => Skill::grouped(),
            'portfolio'   => $creator->portfolio()->orderBy('sort_order')->get(),
            'seo'       => ['title' => 'Freelancer profile — Quick GIGS', 'canonical' => url('/creator/profile')],
        ]);
    }

    public function updateProfile(Request $request)
    {
        $creator = $this->creator($request);

        $data = $request->validate([
            'name'            => ['required', 'string', 'max:80'],
            'handle'          => ['required', 'string', 'max:40', 'unique:creators,handle,' . $creator->id],
            'email'           => ['nullable', 'email', 'max:120', 'unique:creators,email,' . $creator->id],
            'phone'           => ['nullable', 'string', 'max:20'],
            'bio'             => ['nullable', 'string', 'max:800'],
            'headline'        => ['nullable', 'string', 'max:120'],
            'location'        => ['nullable', 'string', 'max:80'],
            'price_from'      => ['nullable', 'integer', 'min:0', 'max:100000'],
            'skills'          => ['nullable', 'array', 'max:12'],
            'skills.*'        => ['string', 'max:60'],
            'languages'       => ['nullable', 'string', 'max:200'],
            'upi_id'          => ['nullable', 'string', 'max:60'],
            'portfolio_url'   => ['nullable', 'url', 'max:200'],
            'instagram'       => ['nullable', 'url', 'max:200'],
            'youtube'         => ['nullable', 'url', 'max:200'],
            'avatar'          => ['nullable', 'image', 'max:2048'],
            'cover'           => ['nullable', 'image', 'max:3072'],
            'profile_type'    => ['nullable', 'in:video_editor,ugc_creator,influencer,designer,hybrid'],
            'collab_type'     => ['nullable', 'in:paid,barter,both'],
            'barter_available'=> ['nullable', 'boolean'],
            'followers_count' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'ugc_niches'      => ['nullable', 'string', 'max:300'],
        ]);

        // skills arrive from the picker as an array; languages and niches are still free text
        if (array_key_exists('skills', $data)) {
            $data['skills'] = array_values(array_unique(array_filter(array_map('trim', (array) $data['skills']))));
        }

        foreach (['languages', 'ugc_niches'] as $listField) {
            if (isset($data[$listField])) {
                $data[$listField] = array_values(array_filter(array_map('trim', explode(',', $data[$listField]))));
            }
        }

        $data['barter_available'] = $request->boolean('barter_available');
        if (empty($data['profile_type'])) unset($data['profile_type']);
        if (empty($data['collab_type']))  unset($data['collab_type']);

        foreach (['avatar', 'cover'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $this->storeUpload($request->file($field), 'creators');
            } else {
                unset($data[$field]);
            }
        }

        if (! str_starts_with($data['handle'], '@')) {
            $data['handle'] = '@' . $data['handle'];
        }

        $creator->update($data);

        return back()->with('toast', 'Freelancer profile saved.');
    }

    public function toggleAvailability(Request $request)
    {
        $creator = $this->creator($request);
        $creator->update(['is_available' => ! $creator->is_available]);

        if ($request->expectsJson()) {
            return response()->json([
                'is_available' => $creator->is_available,
                'toast'        => $creator->is_available ? 'You are available for gigs.' : 'You are marked busy.',
            ]);
        }

        return back()->with('toast', $creator->is_available ? 'You are available for gigs.' : 'You are marked busy.');
    }

    public function order(Request $request, $order)
    {
        $creator = $this->creator($request);

        $o = Order::with(['company', 'service'])
            ->where('creator_id', $creator->id)
            ->where(fn ($q) => $q->where('uid', $order)->orWhere('id', $order))
            ->firstOrFail();

        return view('creator.order', [
            'o'   => $o,
            'seo' => ['title' => 'Gig ' . $o->uid . ' — Quick GIGS'],
        ]);
    }

    public function deliver(Request $request, $order)
    {
        $request->validate([
            'delivery_url' => ['required', 'url'],
            'note'         => ['nullable', 'string', 'max:500'],
        ]);

        $creator = $this->creator($request);

        $o = Order::where('creator_id', $creator->id)
            ->where(fn ($q) => $q->where('uid', $order)->orWhere('id', $order))
            ->firstOrFail();

        $o->update(['status' => 'review', 'progress' => 100]);

        Notifier::toUserOf($o->company, new DeliverySubmitted($o));

        return back()->with('toast', 'Delivered for review — the client has been notified.');
    }

    /* ───────────────────────── portfolio ───────────────────────── */

    public function storePortfolio(Request $request)
    {
        $creator = $this->creator($request);

        $data = $request->validate([
            'title'        => ['required', 'string', 'max:120'],
            'description'  => ['nullable', 'string', 'max:800'],
            'video_url'    => ['nullable', 'url', 'max:300'],
            'external_url' => ['nullable', 'url', 'max:300'],
            'category'     => ['nullable', 'string', 'max:40'],
            'tags'         => ['nullable', 'string', 'max:200'],
            'cover'        => ['nullable', 'image', 'max:3072'],
        ]);

        if (isset($data['tags'])) {
            $data['tags'] = array_values(array_filter(array_map('trim', explode(',', $data['tags']))));
        }

        if ($request->hasFile('cover')) {
            $data['cover'] = $this->storeUpload($request->file('cover'), 'portfolio');
        }

        $data['creator_id'] = $creator->id;
        $data['slug'] = Str::slug($data['title']) . '-' . time();

        PortfolioItem::create($data);

        return back()->with('toast', 'Portfolio item added.');
    }

    public function destroyPortfolio(Request $request, $id)
    {
        $creator = $this->creator($request);
        PortfolioItem::where('creator_id', $creator->id)->where('id', $id)->delete();

        return back()->with('toast', 'Portfolio item removed.');
    }

    /* ───────────────────────── helpers ───────────────────────── */

    /** Resolve the freelancer profile of the logged-in user, creating one if needed. */
    private function creator(Request $request): Creator
    {
        $user = $request->user();

        if ($user?->creator_id && ($c = Creator::find($user->creator_id))) return $c;
        if ($user && ($c = Creator::where('user_id', $user->id)->first())) return $c;
        if (($id = $request->session()->get('creator_id')) && ($c = Creator::find($id))) return $c;

        $handle = '@' . Str::of($user->name ?? 'creator')->slug('')->lower()->limit(24, '');

        $creator = Creator::create([
            'user_id'      => $user?->id,
            'name'         => $user->name ?? 'New creator',
            'handle'       => Creator::where('handle', $handle)->exists() ? $handle . rand(10, 99) : $handle,
            'email'        => $user->email ?? null,
            'headline'     => 'New on Quick GIGS',
            'profile_type' => 'video_editor',
            'price_from'   => 1299,
            'rating'       => 5.0,
            'is_available' => true,
            'is_verified'  => false,
        ]);

        $user?->update(['creator_id' => $creator->id]);
        $request->session()->put('creator_id', $creator->id);

        return $creator;
    }

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
