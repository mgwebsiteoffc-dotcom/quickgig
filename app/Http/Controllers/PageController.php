<?php

namespace App\Http\Controllers;

use App\Models\Creator;
use App\Models\Lead;
use App\Models\Service;
use App\Services\MatchEngine;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /** The seven-stage pipeline every gig runs through — used on several pages. */
    public static function pipeline(): array
    {
        return [
            [
                'key' => 'brief', 'stage' => 'Stage 01', 'title' => 'Brief engine',
                'time' => '40 seconds',
                'body' => 'One line in, a production-ready brief out: objective, three hook options, a timed beat sheet, deliverables and the technical spec. Vague briefs are the number one cause of revisions — this removes them before a creator is even matched.',
                'proof'=> 'Try it free, no signup',
                'link' => 'brief-builder',
            ],
            [
                'key' => 'match', 'stage' => 'Stage 02', 'title' => 'Explainable matching',
                'time' => '4 minutes',
                'body' => 'Every available creator is scored on skill fit, live availability, on-time record, rating and budget fit. You see the score and the reasons behind it — not a black box that says "matched".',
                'proof'=> 'Score breakdown on every match',
                'link' => 'ai',
            ],
            [
                'key' => 'escrow', 'stage' => 'Stage 03', 'title' => 'Escrow, not commitment fees',
                'time' => 'instant',
                'body' => 'Funds are held the moment you order and released only when you approve. No non-refundable commitment money, no retainer lock-in, no invoice chasing.',
                'proof'=> '100% refundable before approval',
                'link' => 'pricing',
            ],
            [
                'key' => 'copilot', 'stage' => 'Stage 04', 'title' => 'Creator copilot',
                'time' => 'during production',
                'body' => 'The assigned creator gets the beat sheet, shot list and reference frames generated from your brief, plus live spec checks while they work. Less guessing means fewer rounds.',
                'proof'=> 'Shipped with every gig',
                'link' => 'for-creators',
            ],
            [
                'key' => 'qa', 'stage' => 'Stage 05', 'title' => 'Automated QA gate',
                'time' => '90 seconds',
                'body' => 'Before anything reaches you, the delivery is checked against the brief: hook timing, caption coverage, loudness, aspect ratio, resolution and licensing. Failed checks bounce back to the creator automatically.',
                'proof'=> '6 checks per delivery',
                'link' => 'ai',
            ],
            [
                'key' => 'revision', 'stage' => 'Stage 06', 'title' => 'Revision translator',
                'time' => 'seconds',
                'body' => '"Make it punchier" becomes timestamped, actionable notes an editor can execute without a call. Two free revisions on every gig, and both sides see the same list.',
                'proof'=> '2 free revisions included',
                'link' => 'ai',
            ],
            [
                'key' => 'repurpose', 'stage' => 'Stage 07', 'title' => 'Auto-repurpose',
                'time' => 'on approval',
                'body' => 'One approved master forks into the formats you actually publish: vertical, square, 16:9 teaser, thumbnail frames and caption files — without re-briefing anybody.',
                'proof'=> 'Up to 6 formats per master',
                'link' => 'marketplace',
            ],
        ];
    }

    /** Head-to-head positioning used on /compare and teased on the landing page. */
    public static function comparison(): array
    {
        return [
            'columns' => ['Quick GIGS', 'Quick-commerce gig apps', 'Managed agencies', 'Bidding marketplaces'],
            'rows' => [
                ['Time to a working creator',   'Matched in ~4 min',              '"Seconds" to checkout, then a queue', '30 min call, then onboarding', '2–5 days of proposals'],
                ['Brief quality',               'Generated for you, free',         'You write it',                        'Discovery call required',      'You write it, 40 times'],
                ['Why this creator?',           'Score + reasons shown',           'Hidden',                              'Account manager picks',        'You guess from portfolios'],
                ['Money up front',              'Escrow, refundable',              'Prepaid checkout',                    'Commitment fee / retainer',    'Escrow + connects fees'],
                ['Pre-delivery QA',             '6 automated checks',              'None',                                'Manual, variable',             'None'],
                ['Revisions',                   '2 free, translated to notes',     'Paid add-on',                         'Scoped in contract',           'Negotiated per gig'],
                ['Creator take-home',           '90%',                             '~70–80%',                             'Undisclosed',                  '~80% after fees'],
                ['Repurposing',                 'Automatic on approval',           'New order',                           'New line item',                'New gig'],
            ],
        ];
    }

    public function howItWorks()
    {
        return view('pages.how-it-works', [
            'pipeline' => self::pipeline(),
            'seo' => [
                'title'       => 'How Quick GIGS works — brief, match, escrow, QA, delivery',
                'description' => 'The seven-stage Quick GIGS pipeline: brief engine, explainable matching, escrow, creator copilot, automated QA gate, revision translator and auto-repurposing.',
                'canonical'   => route('how-it-works'),
            ],
        ]);
    }

    public function ai()
    {
        $creators = Creator::where('is_verified', true)->limit(12)->get();
        $ranked   = (new MatchEngine)->rank($creators, [
            'category' => 'Reel',
            'skills'   => ['reels', 'captions', 'retention editing'],
            'budget'   => 3000,
            'urgency'  => 'express',
        ], 6);

        return view('pages.ai', [
            'ranked'   => $ranked,
            'weights'  => MatchEngine::WEIGHTS,
            'pipeline' => self::pipeline(),
            'seo' => [
                'title'       => 'The Quick GIGS engine — explainable matching, QA gate, revision translator',
                'description' => 'Inside the Quick GIGS engine: transparent match scores, an automated quality gate that checks every delivery, and a revision translator that turns vague feedback into editor-ready notes.',
                'canonical'   => route('ai'),
            ],
        ]);
    }

    public function pricing()
    {
        return view('pages.pricing', [
            'comparison' => self::comparison(),
            'seo' => [
                'title'       => 'Pricing — flat gig prices, 10% platform fee, escrow included | Quick GIGS',
                'description' => 'Transparent Quick GIGS pricing: gigs from ₹1,299, a flat 10% platform fee, creators keep 90%, escrow on every order and no subscription or commitment fee.',
                'canonical'   => route('pricing'),
            ],
        ]);
    }

    public function forCreators()
    {
        $count = Creator::where('is_verified', true)->count();

        return view('pages.for-creators', [
            'creatorCount' => max($count, 1),
            'seo' => [
                'title'       => 'Work on Quick GIGS — keep 90%, get matched, get paid on approval',
                'description' => 'Join Quick GIGS as a creator: no bidding, no connects, no proposals. Get matched by skill and availability, work from a generated brief and keep 90% of every gig.',
                'canonical'   => route('for-creators'),
            ],
        ]);
    }

    public function forBusiness()
    {
        return view('pages.for-business', [
            'tiers' => \App\Models\Company::TIERS,
            'categories' => [
                ['Video editing',   'Social cuts, ads, YouTube',        'from-violet to-violet-deep'],
                ['Graphic design',  'Posts, banners, packaging',        'from-pink to-pink-soft'],
                ['AI content',      'Product images, AI video ads',     'from-teal to-cyan'],
                ['Motion graphics', 'Explainers, animated logos',       'from-amber to-pink'],
                ['Presentations',   'Pitch decks, sales one-pagers',    'from-cyan to-violet'],
                ['Branding',        'Logos, identity, guidelines',      'from-violet to-pink'],
                ['UI design',       'Apps, SaaS screens, landing pages','from-lime to-teal'],
                ['UGC & influencer','Creators on camera, testimonials', 'from-pink to-amber'],
            ],
            'seo' => [
                'title'       => 'Quick GIGS for Business — your creative team on a subscription',
                'description' => 'Queue creative tasks on a shared board, get a verified specialist assigned in minutes and approve the work. Team seats, SLAs, escrow and one invoice — from ₹24,999 a month.',
                'canonical'   => route('for-business'),
            ],
        ]);
    }

    public function compare()
    {
        return view('pages.compare', [
            'comparison' => self::comparison(),
            'seo' => [
                'title'       => 'Quick GIGS vs gig apps, agencies and bidding marketplaces',
                'description' => 'An honest comparison: matching speed, brief quality, transparency, escrow terms, QA, revisions and creator take-home across Quick GIGS, quick-commerce gig apps, managed agencies and bidding marketplaces.',
                'canonical'   => route('compare'),
            ],
        ]);
    }

    public function enterprise()
    {
        return view('pages.enterprise', [
            'seo' => [
                'title'       => 'Quick GIGS for teams — content pods, SLAs and consolidated billing',
                'description' => 'Run always-on content with dedicated creator pods, brand-locked briefs, SLA-backed turnaround, seat-based approvals and a single monthly invoice.',
                'canonical'   => route('enterprise'),
            ],
        ]);
    }

    public function about()
    {
        return view('pages.about', [
            'stats' => [
                'gigs'     => max(Service::count(), 1),
                'creators' => max(Creator::count(), 1),
            ],
            'seo' => [
                'title'       => 'About Quick GIGS — the marketplace that removes the waiting',
                'description' => 'Quick GIGS is building the fastest honest way to get creative work made: generated briefs, explainable matching, escrow payments and automated quality checks.',
                'canonical'   => route('about'),
            ],
        ]);
    }

    public function contact()
    {
        return view('pages.contact', [
            'seo' => [
                'title'       => 'Contact Quick GIGS',
                'description' => 'Talk to the Quick GIGS team about gigs, creator verification, enterprise pods or partnerships.',
                'canonical'   => route('contact'),
            ],
        ]);
    }

    public function storeLead(Request $request)
    {
        $data = $request->validate([
            'type'    => ['nullable', 'in:contact,enterprise,creator'],
            'name'    => ['required', 'string', 'max:80'],
            'email'   => ['required', 'email', 'max:120'],
            'company' => ['nullable', 'string', 'max:80'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'volume'  => ['nullable', 'string', 'max:40'],
            'message' => ['nullable', 'string', 'max:1200'],
        ]);

        $data['type']   = $data['type'] ?? 'contact';
        $data['source'] = $request->input('source', url()->previous());

        Lead::create($data);

        return back()->with('toast', 'Thanks ' . explode(' ', $data['name'])[0] . ' — we reply within one working day.');
    }
}
