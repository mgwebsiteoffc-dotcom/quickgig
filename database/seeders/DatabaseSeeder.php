<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Company;
use App\Models\Creator;
use App\Models\Faq;
use App\Models\Order;
use App\Models\Payout;
use App\Models\PortfolioItem;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->staff();

        $companies = $this->companies();
        $creators  = $this->creators();
        $services  = $this->services($creators);

        $this->orders($companies, $creators, $services);
        $this->content();
    }

    private function staff(): void
    {
        // The install migration already inserts a super admin; top up the other roles.
        foreach ([
            ['Finance Team', 'finance@example.com', 'finance'],
            ['Ops Manager',  'manager2@example.com', 'manager'],
        ] as [$name, $email, $role]) {
            User::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'role' => $role, 'password' => Hash::make(Str::random(16)), 'is_active' => true]
            );
        }
    }

    private function companies()
    {
        return collect([
            ['name' => 'Avante Studio',    'person_name' => 'Rohan Sharma', 'email' => 'rohan@avante.test',      'phone' => '9876543210', 'plan' => 'Pro',     'city' => 'Gurugram'],
            ['name' => 'BrandScale Media', 'person_name' => 'Priya Kapoor', 'email' => 'priya@brandscale.test',  'phone' => '9876543211', 'plan' => 'Team',    'city' => 'Mumbai'],
            ['name' => 'GrowthX Labs',     'person_name' => 'Aman Verma',   'email' => 'aman@growthx.test',      'phone' => '9876543212', 'plan' => 'Starter', 'city' => 'Bengaluru'],
        ])->map(fn ($c) => Company::firstOrCreate(
            ['email' => $c['email']],
            $c + ['is_active' => true, 'is_verified' => true]
        ));
    }

    private function creators()
    {
        return collect([
            ['name' => 'Priya Sharma', 'handle' => '@priyaedits',  'email' => 'priya@creators.test',  'profile_type' => 'video_editor', 'rating' => 4.9, 'price_from' => 1999, 'upi_id' => 'priya@upi',  'is_verified' => true,  'is_featured' => true],
            ['name' => 'Rahul Verma',  'handle' => '@rahulcuts',   'email' => 'rahul@creators.test',  'profile_type' => 'video_editor', 'rating' => 4.8, 'price_from' => 2499, 'upi_id' => 'rahul@upi',  'is_verified' => true,  'is_featured' => false],
            ['name' => 'Neha Jain',    'handle' => '@nehacreates', 'email' => 'neha@creators.test',   'profile_type' => 'designer',     'rating' => 4.7, 'price_from' => 999,  'upi_id' => 'neha@upi',   'is_verified' => true,  'is_featured' => true],
            ['name' => 'Riya Malhotra','handle' => '@ugc_riya',    'email' => 'riya@creators.test',   'profile_type' => 'ugc_creator',  'rating' => 4.8, 'price_from' => 3499, 'upi_id' => 'riya@upi',   'is_verified' => true,  'is_featured' => false, 'barter_available' => true],
        ])->map(fn ($c) => Creator::firstOrCreate(
            ['handle' => $c['handle']],
            $c + [
                'is_available'     => true,
                'headline'         => 'Fast, retention-first content',
                'bio'              => 'Verified QuickContent creator. Same-day turnarounds.',
                'skills'           => ['Reels', 'Captions', 'Retention'],
                'languages'        => ['Hindi', 'English'],
                'location'         => 'India',
                'reviews_count'    => random_int(40, 900),
                'orders_count'     => random_int(20, 300),
                'response_minutes' => random_int(4, 20),
                'on_time_rate'     => random_int(92, 99),
                'repeat_rate'      => random_int(20, 55),
                'verified_at'      => now(),
            ]
        ))->each(function (Creator $c) {
            if ($c->portfolio()->count() === 0) {
                PortfolioItem::create([
                    'creator_id'  => $c->id,
                    'title'       => 'Sample work — '.$c->name,
                    'slug'        => Str::slug($c->name).'-sample-'.$c->id,
                    'category'    => 'Reel',
                    'description' => 'A representative piece from this creator.',
                    'is_published'=> true,
                ]);
            }
        });
    }

    private function services($creators)
    {
        $catalog = [
            ['Talking-Head Reel',   'Reel',        2499, 1, 'video_editor'],
            ['Retention Reel Edit', 'Reel',        3999, 2, 'video_editor'],
            ['High CTR Thumbnail',  'Thumbnail',   1299, 1, 'designer'],
            ['UGC Unboxing Video',  'UGC Video',   3499, 2, 'ugc_creator'],
        ];

        return collect($catalog)->map(function ($row, $i) use ($creators) {
            [$title, $category, $price, $days, $type] = $row;

            $creator = $creators->firstWhere('profile_type', $type) ?? $creators[$i % $creators->count()];

            return Service::firstOrCreate(
                ['title' => $title, 'creator_id' => $creator->id],
                [
                    'description'    => $title.' delivered in '.$days.' day(s) with unlimited minor tweaks.',
                    'price'          => $price,
                    'mrp'            => (int) round($price * 1.25),
                    'delivery_days'  => $days,
                    'category'       => $category,
                    'profile_type'   => $type,
                    'price_type'     => 'paid',
                    'is_active'      => true,
                    'deliverables'   => ['Source file', 'Burned-in captions', '1 revision'],
                    'revision_count' => 1,
                    'sold_count'     => random_int(5, 120),
                    'rating'         => 4.8,
                ]
            );
        });
    }

    private function orders($companies, $creators, $services): void
    {
        if (Order::count() > 0) {
            return;
        }

        $statuses = ['pending', 'working', 'review', 'delivered', 'approved'];

        foreach (range(0, 7) as $i) {
            $service = $services[$i % $services->count()];
            $company = $companies[$i % $companies->count()];

            $subtotal = (int) $service->price;
            $fee      = (int) round($subtotal * 0.05);
            $status   = $statuses[$i % count($statuses)];

            $order = Order::create([
                'company_id'    => $company->id,
                'creator_id'    => $service->creator_id,
                'service_id'    => $service->id,
                'brief'         => 'Hook in the first two seconds, 9:16, burned-in captions, brand colours.',
                'turnaround'    => $service->delivery_days.' Day',
                'subtotal'      => $subtotal,
                'fee'           => $fee,
                'discount'      => 0,
                'total'         => $subtotal + $fee,
                'status'        => $status,
                'escrow_status' => $status === 'approved' ? 'released' : 'held',
                'progress'      => $status === 'approved' ? 100 : 40,
                'due_at'        => now()->addDays($service->delivery_days),
            ]);

            $order->forceFill(['created_at' => now()->subDays(8 - $i)])->save();

            if ($status === 'approved') {
                Payout::firstOrCreate(['order_id' => $order->id], [
                    'creator_id' => $order->creator_id,
                    'amount'     => (int) round($order->total * 0.9),
                    'upi_id'     => $order->creator->upi_id ?? null,
                    'status'     => $i % 2 ? Payout::STATUS_READY : Payout::STATUS_HOLD,
                    'hold_until' => now()->addHours(48),
                ]);
            }
        }
    }

    private function content(): void
    {
        foreach ([
            ['How fast can I get a reel edited?', 'Most QuickContent orders are delivered within 24 hours; same-day is common for 30-second reels.'],
            ['How does escrow work?', 'Your payment is held securely and only released to the creator after you approve the delivery.'],
            ['Do you support barter collaborations?', 'Yes — influencers and UGC creators can accept product-for-content barter deals.'],
        ] as $i => [$q, $a]) {
            Faq::firstOrCreate(['question' => $q], ['answer' => $a, 'sort_order' => $i, 'is_published' => true, 'category' => 'General']);
        }

        $cat = BlogCategory::firstOrCreate(['slug' => 'playbooks'], ['name' => 'Playbooks']);

        Blog::firstOrCreate(
            ['slug' => 'how-to-brief-a-video-editor'],
            [
                'category_id'      => $cat->id,
                'title'            => 'How to brief a video editor so you get it right the first time',
                'excerpt'          => 'A short, repeatable brief template that cuts revision rounds in half.',
                'content'          => '<p>Start with the outcome, not the edit. Say what the viewer should feel in the first two seconds.</p>',
                'is_published'     => true,
                'published_at'     => now()->subDays(3),
            ]
        );
    }
}
