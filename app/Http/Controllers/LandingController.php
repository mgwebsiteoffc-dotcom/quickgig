<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Blog;
use App\Models\Creator;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        $faqs   = $this->faqs();
        $blogs  = $this->blogs();
        $gigs   = $this->gigs();
        $people = $this->creators();

        $stats = [
            'match_time'      => '4 min 12 sec',
            'online_creators' => '1,284',
            'gig_count'       => \App\Models\Service::where('is_active', true)->count() ?: 19,
        ];

        // Product differentiators — each links to the page that proves it.
        $usps = [
            [
                'title' => 'Brief engine', 'tag' => 'Free tool', 'tagTone' => 'bg-violet/15 text-violet-soft',
                'body'  => 'One sentence becomes hooks, a timed beat sheet, deliverables and a spec — before a freelancer is even matched.',
                'cta'   => 'Write a brief', 'href' => route('brief-builder'),
                'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 5h16M4 10h16M4 15h9"/><circle cx="18" cy="17" r="4"/><path d="M18 15.5v3"/></svg>',
            ],
            [
                'title' => 'Explainable matching', 'tag' => 'Auditable', 'tagTone' => 'bg-violet/15 text-violet-soft',
                'body'  => 'A score out of 100 with the five factors behind it. Re-weight what matters and watch the ranking change.',
                'cta'   => 'See the scoring', 'href' => route('ai'),
                'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 19V5M9 19v-8M14 19v-5M19 19V9"/></svg>',
            ],
            [
                'title' => 'Automated QA gate', 'tag' => '6 checks', 'tagTone' => 'bg-lime/15 text-lime',
                'body'  => 'Hook timing, caption coverage, loudness, aspect, resolution and licensing are verified before you ever see the file.',
                'cta'   => 'Run the gate', 'href' => route('ai').'#m02',
                'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3l8 3v6c0 4.5-3.2 7.9-8 9-4.8-1.1-8-4.5-8-9V6z"/><path d="m9 12 2 2 4-4"/></svg>',
            ],
            [
                'title' => 'Revision translator', 'tag' => 'Fewer rounds', 'tagTone' => 'bg-violet/15 text-violet-soft',
                'body'  => '"Make it punchier" becomes timestamped instructions an editor can execute without a single call.',
                'cta'   => 'Try it live', 'href' => route('ai').'#m03',
                'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M21 11.5a8.5 8.5 0 0 1-12.5 7.5L3 21l2-5.5A8.5 8.5 0 1 1 21 11.5z"/></svg>',
            ],
            [
                'title' => 'Auto-repurpose', 'tag' => '6 formats', 'tagTone' => 'bg-violet/15 text-violet-soft',
                'body'  => 'One approved master forks into vertical, square, 16:9, thumbnail frames and caption files automatically.',
                'cta'   => 'See the pipeline', 'href' => route('how-it-works'),
                'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="13" width="8" height="8" rx="2"/><path d="M13 7h5a3 3 0 0 1 3 3M11 17H6a3 3 0 0 1-3-3"/></svg>',
            ],
            [
                'title' => 'Escrow with SLA credit', 'tag' => 'No lock-in', 'tagTone' => 'bg-lime/15 text-lime',
                'body'  => 'No commitment fee and no retainer. Miss the promised window and the express premium is credited back.',
                'cta'   => 'See pricing', 'href' => route('pricing'),
                'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="8" width="18" height="12" rx="3"/><path d="M8 8V6a4 4 0 0 1 8 0v2M12 13v3"/></svg>',
            ],
        ];

        $heroStats = [
            ['value' => '4 min',   'label' => 'Average match time'],
            ['value' => '18,400+', 'label' => 'Gigs delivered'],
            ['value' => '4.9/5',   'label' => 'Client rating'],
            ['value' => '100%',    'label' => 'Escrow protected'],
        ];

        $logos = ['Avante Studio', 'BrandScale', 'GrowthX Labs', 'Nova Foods', 'ConcertPass', 'Lumen AI', 'Peppermint', 'Studio 91'];

        $steps = [
            [
                'title' => 'Describe the gig',
                'body'  => 'One short brief — format, deadline, references. Takes about 40 seconds. No calls, no proposals to read.',
                'meta'  => 'Free to post',
                'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 5h16M4 10h16M4 15h9"/><circle cx="18" cy="17" r="4"/><path d="M18 15.5v3"/></svg>',
            ],
            [
                'title' => 'We match the right pro',
                'body'  => 'Our engine ranks verified freelancers on skill, live availability, on-time record and rating, then locks in the best fit.',
                'meta'  => 'Matched in minutes',
                'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="3"/><path d="M12 2v4M12 18v4M2 12h4M18 12h4M5 5l2.5 2.5M16.5 16.5 19 19M19 5l-2.5 2.5M7.5 16.5 5 19"/></svg>',
            ],
            [
                'title' => 'Approve, then pay',
                'body'  => 'Watch production live, request up to 2 free revisions, and release escrow only when the delivery is right.',
                'meta'  => 'Escrow protected',
                'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3l8 3v6c0 4.5-3.2 7.9-8 9-4.8-1.1-8-4.5-8-9V6z"/><path d="m9 12 2 2 4-4"/></svg>',
            ],
        ];

        // Interactive demo simulation config (prices in ₹)
        $sim = [
            'pool' => 2431,
            'categories' => [
                ['id' => 'reel',      'label' => 'Short-form reel',   'base' => 2499],
                ['id' => 'thumbnail', 'label' => 'Thumbnail pack',    'base' => 1299],
                ['id' => 'ai_ad',     'label' => 'AI video ad',       'base' => 6499],
                ['id' => 'ugc',       'label' => 'UGC product video', 'base' => 3999],
            ],
            'speeds' => [
                ['id' => 'express',  'label' => 'Express · 3 hours',  'mult' => 1.6, 'eta' => '3h 00m'],
                ['id' => 'standard', 'label' => 'Standard · 24 hours','mult' => 1.0, 'eta' => '24h 00m'],
                ['id' => 'relaxed',  'label' => 'Relaxed · 48 hours', 'mult' => 0.85,'eta' => '48h 00m'],
            ],
            'addons' => [
                ['id' => 'captions', 'label' => 'Burned-in captions', 'price' => 299],
                ['id' => 'hooks',    'label' => '3 hook variants',    'price' => 499],
                ['id' => 'raw',      'label' => 'Raw project files',  'price' => 699],
                ['id' => 'vertical', 'label' => 'Vertical + square',  'price' => 399],
            ],
            'talent' => $people->take(4)->map(fn ($c) => [
                'name'   => $c['name'],
                'role'   => Str::limit($c['role'], 38),
                'img'    => $c['img'],
                'rating' => '4.9',
            ])->values()->all(),
        ];

        // Who we are actually built for — colour-coded segments.
        $audiences = [
            [
                'label' => 'D2C brands', 'tone' => 'accent',
                'body'  => 'Launch creatives, offer reels and product videos shipped the same week you plan them.',
                'stat'  => '4.2×', 'statLabel' => 'more creatives per month',
                'icon'  => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 9l1.5-5h15L21 9M3 9h18M3 9v10a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V9M9 13h6"/></svg>',
            ],
            [
                'label' => 'Agencies', 'tone' => 'accent',
                'body'  => 'Absorb client spikes without hiring. White-label delivery, one invoice, your brand on top.',
                'stat'  => '0', 'statLabel' => 'new salaries needed',
                'icon'  => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 21V7l9-4 9 4v14"/><path d="M9 21v-6h6v6M7 11h.01M12 11h.01M17 11h.01"/></svg>',
            ],
            [
                'label' => 'Founders & solo teams', 'tone' => 'accent',
                'body'  => 'You film it, we finish it. Hooks, captions and thumbnails without touching an editor.',
                'stat'  => '3 h', 'statLabel' => 'from raw file to post',
                'icon'  => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6 8-6s8 2 8 6"/></svg>',
            ],
            [
                'label' => 'Educators & coaches', 'tone' => 'accent',
                'body'  => 'Turn one long lesson into a month of shorts, carousels and thumbnails.',
                'stat'  => '18', 'statLabel' => 'assets from one recording',
                'icon'  => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M22 10 12 5 2 10l10 5 10-5z"/><path d="M6 12v5c0 1 2.7 2.5 6 2.5s6-1.5 6-2.5v-5"/></svg>',
            ],
        ];

        // Browse tiles — the whole freelancer catalogue, not just video.
        $categories = [
            ['name' => 'Video & editing',  'count' => '420 gigs', 'from' => '₹1,299', 'q' => 'Reel',       'icon' => 'M2 5h14v14H2zM16 11l6-4v10l-6-4'],
            ['name' => 'Design & brand',   'count' => '310 gigs', 'from' => '₹999',   'q' => 'Thumbnail',  'icon' => 'M12 19l7-7-4-4-7 7v4zM3 21h6M5 3l2 4 4 2-4 2-2 4-2-4-4-2 4-2z'],
            ['name' => 'Content & copy',   'count' => '190 gigs', 'from' => '₹899',   'q' => 'Writing',    'icon' => 'M4 5h16M4 10h16M4 15h10M4 20h7'],
            ['name' => 'Web & app dev',    'count' => '165 gigs', 'from' => '₹4,999', 'q' => 'Development','icon' => 'm8 8-4 4 4 4M16 8l4 4-4 4M13 5l-2 14'],
            ['name' => 'AI & automation',  'count' => '95 gigs',  'from' => '₹6,499', 'q' => 'AI Video',   'icon' => 'M12 3a4 4 0 0 1 4 4v1a4 4 0 0 1 0 8v1a4 4 0 0 1-8 0v-1a4 4 0 0 1 0-8V7a4 4 0 0 1 4-4zM12 3v18'],
            ['name' => 'Marketing & UGC',  'count' => '260 gigs', 'from' => '₹3,999', 'q' => 'UGC Video',  'icon' => 'M3 11v2a1 1 0 0 0 1 1h3l4 4V6L7 10H4a1 1 0 0 0-1 1zM16 8a5 5 0 0 1 0 8'],
        ];

        // Recently delivered strip.
        $gallery = [
            ['img' => 'https://images.unsplash.com/photo-1574717025058-2f8737d2e2b7?w=500&q=80', 'label' => 'Launch reel',      'meta' => '3h 12m'],
            ['img' => 'https://images.unsplash.com/photo-1611224923853-80b023f02d71?w=500&q=80', 'label' => 'Thumbnail pack',   'meta' => '5h 40m'],
            ['img' => 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?w=500&q=80', 'label' => 'UGC unboxing',     'meta' => '1 day'],
            ['img' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=500&q=80', 'label' => 'AI product ad',    'meta' => '2 days'],
            ['img' => 'https://images.unsplash.com/photo-1478737270239-2f02b77fc618?w=500&q=80', 'label' => 'Podcast clips',    'meta' => '1 day'],
            ['img' => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?w=500&q=80', 'label' => 'Brand kit',        'meta' => '2 days'],
        ];

        $plans = [
            [
                'slug' => 'starter', 'name' => 'Starter', 'price' => 1299, 'retainer' => 9999, 'unit' => '/ gig',
                'tagline' => 'Thumbnails, edits and quick fixes.',
                'features' => ['1-day delivery', '2 free revisions', 'Verified freelancer + chat', 'Escrow protection'],
                'cta' => 'Start at ₹1,299', 'featured' => false,
            ],
            [
                'slug' => 'pro', 'name' => 'Pro', 'price' => 2499, 'retainer' => 24999, 'unit' => '/ gig',
                'tagline' => 'Retention reels and UGC that convert.',
                'features' => ['Express lane — from 3 hours', 'Priority AI matching', 'Live production tracking', 'Dedicated freelancer shortlist', 'Escrow protection'],
                'cta' => 'Get matched now', 'featured' => true,
            ],
            [
                'slug' => 'studio', 'name' => 'Studio', 'price' => 8999, 'retainer' => 74999, 'unit' => '/ pack',
                'tagline' => 'A micro-team for full campaigns.',
                'features' => ['Editor + designer + AI pipeline', 'Reel + thumbnail + captions', '2-day turnaround', 'Account manager on chat', 'Volume pricing'],
                'cta' => 'Build a team pack', 'featured' => false,
            ],
        ];

        $feeNotes = [
            ['title' => '10% platform fee', 'body' => 'Freelancers keep 90% of every gig. No connects, no bidding credits, no listing fees.'],
            ['title' => 'Escrow by default', 'body' => 'Funds are held the moment you order and released to the freelancer only after you approve.'],
            ['title' => 'Free to post',      'body' => 'Posting briefs, browsing freelancers and getting matched costs nothing.'],
        ];

        $testimonials = [
            ['name' => 'Rohan Sharma',  'company' => 'Avante Studio',    'text' => 'We went from three-day turnarounds to same-day reels. The live pipeline means I never have to ask for a status update again.'],
            ['name' => 'Priya Kapoor',  'company' => 'BrandScale Media', 'text' => 'No proposal spam, no negotiating. I pick the gig, the price is on the card, and the money only moves when I approve.'],
            ['name' => 'Aman Verma',    'company' => 'GrowthX Labs',     'text' => 'The team pack saves us about eight hours a week. One brief comes back as a reel, a thumbnail and captions.'],
        ];

        $seo = [
            'title'       => 'Quick GIGS — Hire verified freelancers in minutes, not weeks',
            'description' => 'Quick GIGS writes your brief in one second and matches it to a verified freelancer or creator in minutes — video, design, copy, code, voice, UGC and marketing. Live tracking, flat pricing from ₹1,299, escrow-protected payments.',
            'canonical'   => url('/'),
            'image'       => url('/og-home.jpg'),
            'type'        => 'website',
            'keywords'    => 'quick gigs, gig marketplace india, hire video editor, reel editing, thumbnail design, AI video ads, UGC freelancers, escrow freelance',
        ];

        return view('landing', [
            'stats'        => $stats,
            'usps'         => $usps,
            'audiences'    => $audiences,
            'categories'   => $categories,
            'gallery'      => $gallery,
            'comparison'   => PageController::comparison(),
            'heroStats'    => $heroStats,
            'logos'        => $logos,
            'steps'        => $steps,
            'sim'          => $sim,
            'gigs'         => $gigs,
            'creators'     => $people,
            'plans'        => $plans,
            'feeNotes'     => $feeNotes,
            'testimonials' => $testimonials,
            'faqs'         => $faqs,
            'blogs'        => $blogs,
            'seo'          => $seo,
            'liveOrderId'  => 4820,
        ]);
    }

    /** FAQs from DB with a sensible fallback before seeding. */
    private function faqs()
    {
        try {
            $faqs = Faq::published()->ordered()->get();
            if ($faqs->isNotEmpty()) return $faqs;
        } catch (\Throwable $e) {
            // table not migrated yet
        }

        return collect([
            (object) ['question' => 'How is Quick GIGS different from a normal freelance site?', 'answer' => 'You never post a job and wait for proposals. You pick a fixed-price gig or post a brief, and our matching engine assigns a verified freelancer in minutes. Payment sits in escrow until you approve the delivery.'],
            (object) ['question' => 'How fast is delivery, really?', 'answer' => 'Express gigs start in minutes and land in about three hours. Standard reels and thumbnails are next-day. Team packs and AI ads take up to two days.'],
            (object) ['question' => 'What if I do not like the work?', 'answer' => 'Every gig includes two free revisions. If the delivery still misses the brief, raise a dispute before you approve and the escrow is refunded.'],
            (object) ['question' => 'How are freelancers verified?', 'answer' => 'Every freelancer submits ID, portfolio and past client references. Our team reviews each profile manually and tracks on-time delivery, rating and response time after that.'],
            (object) ['question' => 'What does it cost?', 'answer' => 'Gigs start at ₹1,299. The platform fee is a flat 10% — freelancers keep 90%. Posting briefs and browsing freelancers is free.'],
            (object) ['question' => 'How do freelancers get paid?', 'answer' => 'The moment you approve a delivery, the escrow is released and paid out to the freelancer’s UPI or bank account, usually within minutes.'],
        ]);
    }

    private function blogs()
    {
        try {
            return Blog::published()->orderByDesc('is_featured')->orderByDesc('published_at')->limit(3)->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /** Featured marketplace gigs, DB-first with a demo fallback. */
    private function gigs()
    {
        try {
            $services = Service::with('creator')->where('is_active', true)->orderByDesc('sold_count')->limit(4)->get();
            if ($services->isNotEmpty()) {
                return $services->map(fn ($s) => [
                    'id'     => $s->id,
                    'title'  => $s->title,
                    'price'  => $s->displayPrice(),
                    'mrp'    => $s->compareAt() ? '₹' . number_format($s->compareAt()) : null,
                    'off'    => $s->discountPercent(),
                    'time'   => $s->delivery_days . ($s->delivery_days > 1 ? ' days' : ' day'),
                    'img'    => $s->coverUrl(),
                    'badge'  => $s->badge ?: $s->category,
                    'rating' => number_format((float) $s->rating, 1),
                    'sold'   => ($s->sold_count ?: 0) . ' sold',
                ])->values()->all();
            }
        } catch (\Throwable $e) {
            // fall through
        }

        return [
            ['id' => 1, 'title' => 'Talking-head reel with retention cuts', 'price' => '₹2,499', 'time' => '1 day',  'img' => 'https://images.unsplash.com/photo-1574717025058-2f8737d2e2b7?w=600&q=80', 'badge' => 'Best seller', 'rating' => '4.9', 'sold' => '5.1k sold'],
            ['id' => 2, 'title' => 'High-CTR thumbnail pack (3 variants)',  'price' => '₹1,299', 'time' => '1 day',  'img' => 'https://images.unsplash.com/photo-1611224923853-80b023f02d71?w=600&q=80', 'badge' => 'Design',      'rating' => '4.8', 'sold' => '2.4k sold'],
            ['id' => 3, 'title' => 'AI-generated product ad, 30 seconds',   'price' => '₹6,499', 'time' => '2 days', 'img' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=600&q=80', 'badge' => 'AI',          'rating' => '4.9', 'sold' => '420 sold'],
            ['id' => 4, 'title' => 'UGC unboxing video by a real freelancer',  'price' => '₹3,999', 'time' => '1 day',  'img' => 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?w=600&q=80', 'badge' => 'UGC',         'rating' => '4.9', 'sold' => '860 sold'],
        ];
    }

    /** Verified freelancers, DB-first with a demo fallback. */
    private function creators()
    {
        try {
            $creators = Creator::where('is_verified', true)->orderByDesc('is_featured')->orderByDesc('rating')->limit(4)->get();
            if ($creators->isNotEmpty()) {
                return $creators->map(fn ($c) => [
                    'name'       => $c->name,
                    'discipline' => $c->profileLabel(),
                    'img'       => $c->avatarUrl(),
                    'role'      => $c->headline ?: 'Verified creator',
                    'price'     => '₹' . number_format($c->price_from),
                    'available' => (bool) $c->is_available,
                ])->values();
            }
        } catch (\Throwable $e) {
            // fall through
        }

        return collect([
            ['name' => 'Priya Sharma', 'discipline' => 'Video editor',   'role' => 'Talking-head & retention editing',   'price' => '₹2,499', 'img' => 'https://i.pravatar.cc/200?img=5',  'available' => true],
            ['name' => 'Rahul Verma',  'discipline' => 'Video editor',    'role' => 'Long-form to short-form repurposing','price' => '₹2,499', 'img' => 'https://i.pravatar.cc/200?img=12', 'available' => true],
            ['name' => 'Aman Khan',    'discipline' => 'Motion designer',   'role' => 'Motion graphics & AI video',         'price' => '₹2,799', 'img' => 'https://i.pravatar.cc/200?img=15', 'available' => false],
            ['name' => 'Neha Jain',    'discipline' => 'Designer',  'role' => 'Thumbnails with 12% avg CTR lift',   'price' => '₹1,299', 'img' => 'https://i.pravatar.cc/200?img=9',  'available' => true],
        ]);
    }
}
