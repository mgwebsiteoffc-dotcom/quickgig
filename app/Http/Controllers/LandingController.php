<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Blog;
use App\Models\Creator;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        // Try DB, fallback to hardcoded for fresh install before migrate
        try {
            $faqs = Faq::published()->ordered()->get();
            if ($faqs->isEmpty()) throw new \Exception('empty');
        } catch (\Throwable $e) {
            $faqs = collect([
                (object)['question'=>'How is this different from Unjob.ai, Fiverr or Upwork?','answer'=>'Unjob is instant-assign only with no choice. Fiverr takes 20% + bidding chaos, Upwork is 50+ proposals & pay-to-bid. QuickContent gives you 3 paths (Instant / Choose Pro / Prompt-a-Team), live tracking — easy like ordering food — and escrow — pay only when you approve. Verified creators with live availability, not random freelancers.'],
                (object)['question'=>'How fast is delivery really?','answer'=>'Talking-head reels & thumbnails: next-day delivery is standard. Instant-assign orders are assigned in ~12 minutes. AI videos & team packs: 2 days. You get a countdown + live updates like ordering food.'],
                (object)['question'=>'What if I don’t like the work?','answer'=>'You get 2 free revisions in every order. Payment is held in Razorpay escrow — we release to the creator only when you click Approve. 100% guarantee.'],
                (object)['question'=>'Who are the creators? Are they verified?','answer'=>'All pros are ID-verified, portfolio-checked, and rated by real companies (Avante, BrandScale etc). Blue tick = verified. You see live availability (green dot) before you book.'],
                (object)['question'=>'Can I run this on Hostinger shared hosting?','answer'=>'Yes. Built for shared hosting: no Redis, no Node, no Supervisor needed. File cache, database queue (cron), Tailwind via CDN. Works on Hostinger Single/Premium/Business.'],
                (object)['question'=>'What does it cost?','answer'=>'Flat, upfront pricing: Reels from ₹1,299, Thumbnails from ₹1,299, AI Ads from ₹6,499. Platform fee 5%. No bidding, no Connects, no hidden 20%. Creators keep 90%.'],
            ]);
        }

        try {
            $blogs = Blog::published()->orderByDesc('is_featured')->orderByDesc('published_at')->limit(3)->get();
        } catch (\Throwable $e) { $blogs = collect(); }

        try {
            $creatorsRaw = Creator::where('is_verified',true)->orderByDesc('is_featured')->orderByDesc('rating')->limit(4)->get();
            if ($creatorsRaw->isEmpty()) throw new \Exception('empty');
            // Map to landing view shape: img, available, role, price, name, handle
            $creators = $creatorsRaw->map(function($c){
                return [
                    'name'=>$c->name,
                    'handle'=>$c->handle,
                    'img'=>$c->avatarUrl(),
                    'role'=>$c->headline ?: ($c->bio ? \Illuminate\Support\Str::limit($c->bio,40) : 'Verified Creator'),
                    'price'=>'₹'.number_format($c->price_from),
                    'available'=>$c->is_available,
                    'is_verified'=>$c->is_verified,
                ];
            });
        } catch (\Throwable $e) {
            $creators = collect([
                ['name'=>'Priya Sharma','handle'=>'@priyaedits','role'=>'Talking-Head • 4.9★ • For @devtalksbusiness','price'=>'₹2,499','img'=>'https://i.pravatar.cc/150?img=5','available'=>true],
                ['name'=>'Rahul Verma','handle'=>'@rahulcuts','role'=>'Retention Editing • 4.9★ • For @priyanksingh','price'=>'₹2,499','img'=>'https://i.pravatar.cc/150?img=12','available'=>true],
                ['name'=>'Aman Khan','handle'=>'@amanmotion','role'=>'Motion + AI • 4.8★ • For @fullstackmodiji','price'=>'₹2,799','img'=>'https://i.pravatar.cc/150?img=15','available'=>false],
                ['name'=>'Neha Jain','handle'=>'@nehacreates','role'=>'Thumbnail CTR • 4.9★ • 2k delivered','price'=>'₹1,299','img'=>'https://i.pravatar.cc/150?img=9','available'=>true],
            ]);
        }

        $stats = [
            ['value' => '12 min', 'label' => 'Avg. assign time'],
            ['value' => '1,400+', 'label' => 'Videos delivered'],
            ['value' => '4.8/5', 'label' => 'Avg. rating'],
            ['value' => '200+', 'label' => 'Companies trust us'],
        ];
        $logos = ['Avante Studio','BrandScale','GrowthX','Razorpay Rize','Jindal Steel','ConcertPass'];
        $paths = [
            ['id'=>'instant','badge'=>'Most Popular','icon'=>'zap','title'=>'Instant Assign','time'=>'~12 min','price'=>'From ₹1,299','desc'=>'Describe what you need → we assign the best verified pro in 12 minutes. As easy as ordering food.','for'=>'Urgent reels, thumbnails, fixes'],
            ['id'=>'choose','badge'=>'Full Control','icon'=>'users','title'=>'Choose Your Pro','time'=>'~1 hour','price'=>'From ₹1,299','desc'=>'See top 3 matched pros with portfolio + rating. Pick who you trust. No bidding, no noise.','for'=>'When you want to choose'],
            ['id'=>'team','badge'=>'Best Value','icon'=>'layers','title'=>'Prompt-a-Team','time'=>'~2 hours','price'=>'From ₹6,499','desc'=>'One prompt → Reel + Thumbnail + Captions. A micro-team (editor + designer + AI) works as one.','for'=>'YouTube packs, campaigns'],
        ];
        $services = [
            ['title'=>'Talking-Head Reel','price'=>'₹1,299','time'=>'1 Day','img'=>'https://images.unsplash.com/photo-1574717025058-2f8737d2e2b7?w=500&q=80','badge'=>'Best seller','orders'=>'5k+'],
            ['title'=>'Retention Reel','price'=>'₹2,499','time'=>'1 Day','img'=>'https://images.unsplash.com/photo-1536243287037-7f1444775910?w=500&q=80','badge'=>'Popular','orders'=>'1k+'],
            ['title'=>'AI UGC Ad','price'=>'₹6,499','time'=>'2 Days','img'=>'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=500&q=80','badge'=>'AI','orders'=>'420'],
            ['title'=>'High CTR Thumbnail','price'=>'₹1,299','time'=>'1 Day','img'=>'https://images.unsplash.com/photo-1611224923853-80b023f02d71?w=500&q=80','badge'=>'Thumbnail','orders'=>'2k+'],
            ['title'=>'UGC Unboxing 30s','price'=>'₹1,999','time'=>'1 Day','img'=>'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?w=500&q=80','badge'=>'UGC • Real use','orders'=>'860'],
            ['title'=>'Barter Reel Collab','price'=>'Barter','time'=>'2 Days','img'=>'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=500&q=80','badge'=>'Barter • Product','orders'=>'310'],
        ];
        $testimonials = [
            ['name'=>'Rohan Sharma','company'=>'Avante Studio','text'=>'We went from 3-day delays to same-day reels. Live tracking — easy like ordering food — is genius. I know exactly when my reel will land.','rating'=>5],
            ['name'=>'Priya Kapoor','company'=>'BrandScale Media','text'=>'No more Fiverr spam. 3 verified pros, pick one, escrow till I approve. Fees are half of Upwork.','rating'=>5],
            ['name'=>'Aman Verma','company'=>'GrowthX Labs','text'=>'Prompt-a-Team saved us 8 hours/week. One brief → reel + thumb + caption. Insane value.','rating'=>5],
        ];

        // SEO/AEO for landing — no brand mentions, food-ordering analogy
        $seo = [
            'title' => "QuickContent — India's First Quick Content Delivery Platform | Work, Delivered. In Hours, Not Weeks.",
            'description' => "India's First Quick Content Delivery — as easy as ordering food. Get Reels, Thumbnails & AI Videos in hours with 12-min matching, live tracking, and escrow — pay only when you approve. From ₹1,299. Hostinger-ready.",
            'canonical' => url('/'),
            'image' => url('/og-home.jpg'),
            'type' => 'website',
            'keywords' => 'quick content delivery, reels, thumbnails, AI video, Unjob alternative, Fiverr alternative, Hostinger',
        ];

        return view('landing', compact('stats','logos','paths','creators','services','testimonials','faqs','blogs','seo'));
    }
}
