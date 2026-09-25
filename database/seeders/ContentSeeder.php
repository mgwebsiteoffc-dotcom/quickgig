<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Faq;
use Illuminate\Database\Seeder;

/** FAQs (feed the landing page FAQPage JSON-LD) and starter blog posts. */
class ContentSeeder extends Seeder
{
    public const FAQS = [
        ['How fast can I get a reel edited?', 'Most QuickContent orders are delivered within 24 hours; same-day is common for 30-second reels.'],
        ['How does escrow work?', 'Your payment is held securely and only released to the creator after you approve the delivery.'],
        ['Do you support barter collaborations?', 'Yes — influencers and UGC creators can accept product-for-content barter deals instead of cash.'],
        ['What if I am not happy with the delivery?', 'Every order includes at least one revision. Escrow is not released until you approve.'],
        ['When do creators get paid?', 'Payouts move from hold to ready once the escrow window elapses, then go out over UPI via RazorpayX.'],
    ];

    public const POSTS = [
        [
            'how-to-brief-a-video-editor',
            'How to brief a video editor so you get it right the first time',
            'A short, repeatable brief template that cuts revision rounds in half.',
            '<p>Start with the outcome, not the edit. Say what the viewer should feel in the first two seconds, then list the hard constraints: aspect ratio, caption style, brand colours and the deadline.</p>',
        ],
        [
            'ugc-vs-influencer-content',
            'UGC versus influencer content: which one should you buy?',
            'They look similar in the feed and behave very differently in the funnel.',
            '<p>UGC is ad creative you own and can run as paid media. Influencer content borrows someone else\'s audience. Most brands need both, in different ratios.</p>',
        ],
    ];

    public function run(): void
    {
        foreach (self::FAQS as $i => [$q, $a]) {
            Faq::firstOrCreate(
                ['question' => $q],
                ['answer' => $a, 'sort_order' => $i, 'is_published' => true, 'category' => 'General']
            );
        }

        $category = BlogCategory::firstOrCreate(['slug' => 'playbooks'], ['name' => 'Playbooks']);

        foreach (self::POSTS as $i => [$slug, $title, $excerpt, $content]) {
            Blog::firstOrCreate(
                ['slug' => $slug],
                [
                    'category_id'      => $category->id,
                    'title'            => $title,
                    'excerpt'          => $excerpt,
                    'content'          => $content,
                    'is_published'     => true,
                    'published_at'     => now()->subDays(3 + $i),
                    'reading_minutes'  => 3,
                ]
            );
        }

        $this->command?->info(count(self::FAQS).' FAQs and '.count(self::POSTS).' blog posts seeded.');
    }
}
