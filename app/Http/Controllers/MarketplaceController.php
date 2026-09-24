<?php

namespace App\Http\Controllers;

use App\Models\Creator;
use App\Models\Service;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public const SORTS = [
        'popular'    => 'Most popular',
        'rating'     => 'Top rated',
        'price_low'  => 'Price: low to high',
        'price_high' => 'Price: high to low',
        'fastest'    => 'Fastest delivery',
    ];

    public function index(Request $request)
    {
        $q        = trim((string) $request->query('q', ''));
        $category = (string) $request->query('category', '');
        $sort     = array_key_exists($request->query('sort'), self::SORTS) ? $request->query('sort') : 'popular';
        $maxPrice = (int) $request->query('max', 0);
        $fast     = $request->boolean('fast');

        $query = Service::query()->with('creator')->where('is_active', true);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('category', 'like', "%{$q}%");
            });
        }

        if ($category !== '' && $category !== 'All') {
            $query->where('category', $category);
        }

        if ($maxPrice > 0) {
            $query->where('price', '<=', $maxPrice);
        }

        if ($fast) {
            $query->where('delivery_days', '<=', 1);
        }

        match ($sort) {
            'rating'     => $query->orderByDesc('rating')->orderByDesc('sold_count'),
            'price_low'  => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'fastest'    => $query->orderBy('delivery_days')->orderByDesc('sold_count'),
            default      => $query->orderByDesc('sold_count')->orderByDesc('rating'),
        };

        $gigs = $query->paginate(9)->withQueryString();

        $categories = Service::where('is_active', true)
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->pluck('total', 'category');

        $availableNow = Creator::where('is_verified', true)->where('is_available', true)
            ->orderByDesc('rating')->limit(6)->get();

        return view('marketplace.index', [
            'gigs'         => $gigs,
            'categories'   => $categories,
            'availableNow' => $availableNow,
            'filters'      => compact('q', 'category', 'sort', 'maxPrice', 'fast'),
            'sorts'        => self::SORTS,
            'totals'       => [
                'gigs'     => Service::where('is_active', true)->count(),
                'creators' => Creator::where('is_verified', true)->count(),
                'online'   => Creator::where('is_verified', true)->where('is_available', true)->count(),
            ],
            'seo' => [
                'title'       => 'Marketplace — fixed-price gigs from verified creators | Quick GIGS',
                'description' => 'Browse ready-to-buy gigs: reels, thumbnails, AI video ads, UGC and design. Fixed prices, verified creators, escrow-protected delivery from ₹1,299.',
                'canonical'   => url('/marketplace'),
            ],
        ]);
    }

    public function show(Request $request, $id)
    {
        $gig = Service::with(['creator.portfolio'])->findOrFail($id);

        $related = Service::with('creator')
            ->where('is_active', true)
            ->where('id', '!=', $gig->id)
            ->where('category', $gig->category)
            ->orderByDesc('sold_count')
            ->limit(3)
            ->get();

        if ($related->count() < 3) {
            $related = $related->concat(
                Service::with('creator')->where('is_active', true)
                    ->whereNotIn('id', $related->pluck('id')->push($gig->id)->all())
                    ->orderByDesc('sold_count')->limit(3 - $related->count())->get()
            );
        }

        return view('marketplace.show', [
            'gig'     => $gig,
            'related' => $related,
            'seo'     => [
                'title'       => $gig->title . ' — ' . $gig->displayPrice() . ' | Quick GIGS',
                'description' => \Illuminate\Support\Str::limit(strip_tags($gig->description ?: $gig->title), 150),
                'canonical'   => route('gigs.show', $gig->id),
                'image'       => $gig->coverUrl(),
            ],
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => url('/')],
                ['name' => 'Marketplace', 'url' => route('marketplace')],
                ['name' => $gig->title, 'url' => route('gigs.show', $gig->id)],
            ],
        ]);
    }
}
