<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Creator;
use App\Models\Faq;
use App\Models\Order;
use App\Models\Service;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Idempotent demo data for Quick GIGS.
     * Run with: php artisan migrate --seed  (or php artisan db:seed)
     */
    public function run(): void
    {
        $this->staff();
        $this->creatorStats();
        $this->extraFreelancers();
        $this->faqs();
        $creator = $this->creatorAccount();
        $company = $this->businessAccount();
        $this->extraGigs();
        $this->sampleOrders($company, $creator);
        $this->sampleBoard($company, $creator);

        $this->command?->info('Quick GIGS demo data ready — log in with business@quickgigs.in / Business@123');
    }

    /* ── platform staff ── */
    private function staff(): void
    {
        $staff = [
            ['name' => 'Super Admin', 'email' => 'admin@quickgigs.in',   'role' => 'super_admin', 'password' => 'Admin@12345'],
            ['name' => 'Ops Manager', 'email' => 'manager@quickgigs.in', 'role' => 'manager',     'password' => 'Manager@123'],
            ['name' => 'Support',     'email' => 'support@quickgigs.in', 'role' => 'support',     'password' => 'Support@123'],
            ['name' => 'Finance',     'email' => 'finance@quickgigs.in', 'role' => 'finance',     'password' => 'Finance@123'],
        ];

        foreach ($staff as $s) {
            User::updateOrCreate(
                ['email' => $s['email']],
                [
                    'name'      => $s['name'],
                    'role'      => $s['role'],
                    'password'  => Hash::make($s['password']),
                    'is_active' => true,
                ]
            );
        }
    }

    /* ── give each freelancer distinct performance data so matching has signal ── */
    private function creatorStats(): void
    {
        $profiles = [
            '@priyaedits'   => ['response_minutes' => 6,  'on_time_rate' => 98, 'orders_count' => 318, 'reviews_count' => 312, 'rating' => 4.9, 'price_from' => 2499, 'is_available' => true],
            '@rahulcuts'    => ['response_minutes' => 21, 'on_time_rate' => 94, 'orders_count' => 176, 'reviews_count' => 168, 'rating' => 4.8, 'price_from' => 2299, 'is_available' => true],
            '@amanmotion'   => ['response_minutes' => 44, 'on_time_rate' => 91, 'orders_count' => 98,  'reviews_count' => 86,  'rating' => 4.7, 'price_from' => 2799, 'is_available' => false],
            '@nehacreates'  => ['response_minutes' => 11, 'on_time_rate' => 97, 'orders_count' => 241, 'reviews_count' => 233, 'rating' => 4.9, 'price_from' => 1299, 'is_available' => true],
            '@sahilai'      => ['response_minutes' => 33, 'on_time_rate' => 89, 'orders_count' => 64,  'reviews_count' => 51,  'rating' => 4.6, 'price_from' => 6499, 'is_available' => true],
            '@ugc_riya'     => ['response_minutes' => 15, 'on_time_rate' => 96, 'orders_count' => 132, 'reviews_count' => 121, 'rating' => 4.8, 'price_from' => 3999, 'is_available' => true],
            '@barter_aman'  => ['response_minutes' => 58, 'on_time_rate' => 86, 'orders_count' => 27,  'reviews_count' => 19,  'rating' => 4.5, 'price_from' => 1999, 'is_available' => false],
        ];

        foreach ($profiles as $handle => $data) {
            Creator::where('handle', $handle)->update($data);
        }
    }

    /* ── landing FAQs (also power the FAQPage JSON-LD) ── */
    private function faqs(): void
    {
        $faqs = [
            ['How is Quick GIGS different from a normal freelance site?', 'You never post a job and wait for proposals. Pick a fixed-price gig or post a brief, and the matching engine assigns a verified freelancer in minutes. Payment stays in escrow until you approve.', 'General', 1, true],
            ['How fast is delivery, really?', 'Express gigs start within minutes and land in about three hours. Standard reels and thumbnails are next-day. Team packs and AI ads take up to two days.', 'Delivery', 2, true],
            ['What if I do not like the work?', 'Every gig includes two free revisions. If the delivery still misses the brief, raise a dispute before approving and the escrow is refunded in full.', 'Guarantee', 3, true],
            ['How are freelancers verified?', 'Freelancers submit ID, portfolio and client references. Our team reviews each profile manually, then tracks on-time delivery, rating and response time on every gig.', 'Freelancers', 4, true],
            ['What does it cost?', 'Gigs start at ₹1,299. The platform fee is a flat 10%, so freelancers keep 90%. Posting briefs, browsing gigs and getting matched is free.', 'Pricing', 5, true],
            ['How do freelancers get paid?', 'The moment you approve a delivery, escrow is released and paid out to the freelancer via UPI or bank transfer, usually within minutes.', 'Payouts', 6, true],
            ['Do you provide GST invoices?', 'Yes. Add your GSTIN in workspace settings and every order generates a GST-compliant invoice. Freelancers get matching payout statements.', 'Pricing', 7, false],
        ];

        // Replace any legacy seeded copy so the public site stays on-brand.
        Faq::query()->delete();

        foreach ($faqs as [$q, $a, $cat, $order, $featured]) {
            Faq::create([
                'question'     => $q,
                'answer'       => $a,
                'category'     => $cat,
                'sort_order'   => $order,
                'is_published' => true,
                'is_featured'  => $featured,
                'slug'         => Str::slug(Str::limit($q, 60, '')),
            ]);
        }
    }

    /* ── demo freelancer login, wired to an existing freelancer profile ── */
    private function creatorAccount(): Creator
    {
        $creator = Creator::where('handle', '@priyaedits')->first() ?? Creator::create([
            'name'         => 'Priya Sharma',
            'handle'       => '@priyaedits',
            'email'        => 'creator@quickgigs.in',
            'headline'     => 'Talking-head & retention editing',
            'bio'          => 'I cut founder-led videos that hold attention past 60%. 300+ reels delivered for D2C and SaaS teams.',
            'profile_type' => 'video_editor',
            'skills'       => ['Reels', 'Retention editing', 'Captions', 'Short-form'],
            'price_from'   => 2499,
            'rating'       => 4.9,
            'reviews_count'=> 312,
            'orders_count' => 318,
            'is_available' => true,
            'is_verified'  => true,
            'is_featured'  => true,
            'upi_id'       => 'priya@upi',
        ]);

        $user = User::updateOrCreate(
            ['email' => 'creator@quickgigs.in'],
            [
                'name'       => $creator->name,
                'role'       => 'creator',
                'password'   => Hash::make('Creator@123'),
                'creator_id' => $creator->id,
                'is_active'  => true,
            ]
        );

        $creator->update(['user_id' => $user->id, 'email' => 'creator@quickgigs.in']);

        return $creator;
    }

    /* ── demo business login + workspace ── */
    private function businessAccount(): Company
    {
        $company = Company::updateOrCreate(
            ['slug' => 'avante-studio'],
            [
                'name'        => 'Avante Studio',
                'person_name' => 'Rohan Sharma',
                'email'       => 'business@quickgigs.in',
                'phone'       => '+91 98765 43210',
                'plan'        => 'Pro',
                'plan_tier'       => 'growth',
                'monthly_credits' => 20,
                'credits_used'    => 7,
                'seats'           => 8,
                'renews_on'       => now()->addDays(12)->toDateString(),
                'industry'    => 'D2C brand',
                'team_size'   => 18,
                'bio'         => 'Direct-to-consumer brand shipping 40+ short videos a month.',
                'is_verified' => true,
                'is_active'   => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'business@quickgigs.in'],
            [
                'name'       => 'Rohan Sharma',
                'role'       => 'business',
                'password'   => Hash::make('Business@123'),
                'company_id' => $company->id,
                'is_active'  => true,
            ]
        );

        return $company;
    }

    /* ── freelancers beyond video, so the marketplace reads as a full platform ── */
    private function extraFreelancers(): void
    {
        $people = [
            ['Ananya Rao',   '@ananyawrites',  'Conversion copy & long-form content', 'hybrid',       ['Copywriting','SEO','Landing pages','Email'],        1499, 4.9, 16, 96, 142],
            ['Vikram Shah',  '@vikrambuilds',  'Laravel & React product development', 'hybrid',       ['Laravel','React','APIs','Shopify'],                 7999, 4.8, 38, 94, 61],
            ['Meera Nair',   '@meeradesigns',  'Brand systems and product UI',        'designer',     ['UI design','Branding','Figma','Design systems'],    3499, 4.9, 22, 97, 118],
            ['Arjun Kapoor', '@arjunvoice',    'Voice over in Hindi and English',     'hybrid',       ['Voice over','Narration','Dubbing'],                 999,  4.8, 12, 98, 205],
            ['Sana Sheikh',  '@sanagrowth',    'Performance marketing and ad ops',    'hybrid',       ['Meta ads','Google ads','Analytics','CRO'],          4999, 4.7, 41, 92, 73],
        ];

        foreach ($people as [$name, $handle, $headline, $type, $skills, $from, $rating, $mins, $onTime, $orders]) {
            Creator::updateOrCreate(
                ['handle' => $handle],
                [
                    'name' => $name, 'email' => ltrim($handle, '@') . '@quickgigs.in',
                    'headline' => $headline, 'profile_type' => $type, 'skills' => $skills,
                    'price_from' => $from, 'rating' => $rating, 'response_minutes' => $mins,
                    'on_time_rate' => $onTime, 'orders_count' => $orders, 'reviews_count' => (int) ($orders * 0.9),
                    'is_available' => true, 'is_verified' => true,
                ]
            );
        }
    }

    /* ── a few extra catalogue gigs so the marketplace never looks empty ── */
    private function extraGigs(): void
    {
        $creators = Creator::where('is_verified', true)->pluck('id')->all();
        if (empty($creators)) return;

        $byHandle = fn (string $h) => Creator::where('handle', $h)->value('id') ?? $creators[0];

        $wider = [
            ['Landing page copy that converts',        'Writing',     2999,  8999,  2, 'https://images.unsplash.com/photo-1455390582262-044cdead277a?w=800&q=80',  'Copywriting', 340, 4.9, '@ananyawrites', 'Full page: hero, three proof blocks, objections, FAQ and CTA — written from your positioning, not templates.'],
            ['SEO blog article, 1500 words',           'Writing',     1799,  2499,  2, 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=800&q=80',  'SEO',         520, 4.8, '@ananyawrites', 'Keyword-mapped article with internal links, meta title and description, ready to publish.'],
            ['Landing page in Laravel or React',       'Development', 14999, 19999, 3, 'https://images.unsplash.com/photo-1547658719-da2b51169166?w=800&q=80',    'Dev',         96,  4.8, '@vikrambuilds', 'Responsive build from your design, form handling, analytics and deploy — code handed over.'],
            ['Shopify store setup and theme polish',   'Development', 9999,  13999, 3, 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=800&q=80',    'Shopify',     140, 4.7, '@vikrambuilds', 'Theme setup, product templates, checkout tidy-up and speed pass.'],
            ['Product UI screens (5 screens)',         'Design',      8999,  11999, 3, 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=800&q=80',    'UI',          210, 4.9, '@meeradesigns', 'Figma file with components, states and a handoff-ready spec.'],
            ['Hindi + English voice over, 60 seconds', 'Voice Over',  1299,  1899,  1, 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?w=800&q=80',  'VO',          430, 4.8, '@arjunvoice',   'Studio-clean read, two takes, WAV and MP3 delivered with sync markers.'],
            ['Meta ads creative + campaign setup',     'Marketing',   6999,  9999,  2, 'https://images.unsplash.com/photo-1611926653458-09294b3142bf?w=800&q=80',  'Ads',         180, 4.7, '@sanagrowth',   'Three ad variants, audiences, pixel check and a first-week optimisation note.'],
        ];

        foreach ($wider as [$title, $category, $price, $mrp, $days, $cover, $badge, $sold, $rating, $handle, $desc]) {
            Service::firstOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'creator_id' => $byHandle($handle), 'title' => $title, 'description' => $desc,
                    'cover' => $cover, 'price' => $price, 'compare_price' => $mrp, 'mrp' => $mrp,
                    'delivery_days' => $days, 'category' => $category, 'badge' => $badge,
                    'sold_count' => $sold, 'rating' => $rating, 'is_active' => true, 'price_type' => 'paid',
                    'deliverables' => ['Source files', 'One round of notes', '2 free revisions'],
                ]
            );
        }

        $gigs = [
            ['UGC unboxing video by a real freelancer', 'UGC Video', 3999, 1, 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?w=800&q=80', 'UGC', 860, 4.9, 'A real freelancer films, speaks and edits an authentic 30-second unboxing for your product. Shot vertically with captions and a clear call to action.'],
            ['Podcast clips — 5 shorts from one episode', 'Reel', 4499, 2, 'https://images.unsplash.com/photo-1478737270239-2f02b77fc618?w=800&q=80', 'Bundle', 540, 4.8, 'Send one long episode and get five publish-ready vertical clips with captions, hooks and end cards.'],
            ['Brand kit — logo, colours and social templates', 'Bundle', 7499, 2, 'https://images.unsplash.com/photo-1626785774573-4b799315345d?w=800&q=80', 'Design', 210, 4.8, 'A compact brand system: primary logo, colour palette, type scale and ten editable social templates.'],
            ['Product photo retouching — 10 images', 'Thumbnail', 1999, 1, 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&q=80', 'Design', 430, 4.7, 'Clean cut-outs, colour correction and marketplace-ready exports for ten product photos.'],
        ];

        foreach ($gigs as $i => [$title, $category, $price, $days, $cover, $badge, $sold, $rating, $desc]) {
            Service::firstOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'creator_id'    => $creators[$i % count($creators)],
                    'title'         => $title,
                    'description'   => $desc,
                    'cover'         => $cover,
                    'price'         => $price,
                    'delivery_days' => $days,
                    'category'      => $category,
                    'badge'         => $badge,
                    'sold_count'    => $sold,
                    'rating'        => $rating,
                    'is_active'     => true,
                    'price_type'    => 'paid',
                    'deliverables'  => ['Source-quality export', 'Captions where relevant', '2 free revisions'],
                ]
            );
        }

        // Quick-commerce style MRP so the discount badge has something to show.
        Service::whereNull('compare_price')->orWhere('compare_price', 0)->get()
            ->each(fn ($s) => $s->update(['compare_price' => (int) round($s->price * 1.28 / 10) * 10]));

        // Make sure everything in the catalogue is orderable.
        Service::whereNull('is_active')->update(['is_active' => true]);
        Service::whereNull('price_type')->update(['price_type' => 'paid']);
    }

    /* ── a populated task board for the demo workspace ── */
    private function sampleBoard(Company $company, Creator $creator): void
    {
        if (Task::where('company_id', $company->id)->exists()) return;

        $second = Creator::where('handle', '@nehacreates')->first() ?? $creator;
        $third  = Creator::where('handle', '@rahulcuts')->first() ?? $creator;

        $rows = [
            ['Diwali campaign — hero reel',        'Reel',       'urgent', 'queued',     null,     2,  'Festive hero film for the homepage and Meta ads. Founder voiceover, product macro shots, 30 seconds.'],
            ['Amazon A+ banner set',               'Thumbnail',  'normal', 'queued',     null,     4,  'Six A+ content banners in brand colours, mobile-first crops included.'],
            ['Protein bar — UGC testimonial',      'UGC Video',  'high',   'assigned',   $creator, 1,  'Real customer on camera, unscripted, 30 seconds with burned-in captions.'],
            ['YouTube thumbnail A/B set',          'Thumbnail',  'normal', 'production', $second,  1,  'Three variants for the launch video: face-led, text-led and contrast-led.'],
            ['Founder podcast — 5 shorts',         'Reel',       'high',   'production', $third,   2,  'Five vertical clips from episode 14, captions and hook cards.'],
            ['Festive offer — static carousel',    'Bundle',     'low',    'review',     $second,  0,  'Five-slide Instagram carousel announcing the festive bundle pricing.'],
            ['AI product ad — skincare serum',     'AI Video',   'normal', 'done',       $creator, -2, 'Thirty-second AI-generated ad with voiceover and 1:1 crop for Meta.'],
            ['Brand kit refresh',                  'Bundle',     'normal', 'done',       $third,   -5, 'Logo lockups, palette and ten editable social templates.'],
        ];

        foreach ($rows as $i => [$title, $category, $priority, $status, $assignee, $dueInDays, $brief]) {
            Task::create([
                'company_id'     => $company->id,
                'creator_id'     => $assignee?->id,
                'title'          => $title,
                'brief'          => $brief,
                'category'       => $category,
                'priority'       => $priority,
                'status'         => $status,
                'start_on'       => now()->subDays(max(0, 3 - $i))->toDateString(),
                'due_on'         => now()->addDays($dueInDays)->toDateString(),
                'links'          => $i % 3 === 0 ? ['https://drive.google.com/brand-assets'] : [],
                'comments_count' => [4, 0, 7, 2, 11, 3, 6, 1][$i] ?? 0,
                'sort_order'     => $i,
                'delivered_at'   => $status === 'done' ? now()->subDays(abs($dueInDays)) : null,
            ]);
        }
    }

    /* ── two example orders so dashboards show real state ── */
    private function sampleOrders(Company $company, Creator $creator): void
    {
        if (Order::where('company_id', $company->id)->exists()) return;

        $services = Service::where('is_active', true)->limit(2)->get();
        if ($services->isEmpty()) return;

        $first = $services->first();
        Order::create([
            'company_id'    => $company->id,
            'creator_id'    => $creator->id,
            'service_id'    => $first->id,
            'brief'         => 'Founder-led reel about our new protein bar launch. Punchy hook, captions, 30-40 seconds, upbeat tone.',
            'turnaround'    => 'Standard · 24 hours',
            'subtotal'      => $first->price,
            'fee'           => (int) round($first->price * 0.10),
            'discount'      => 0,
            'total'         => $first->price,
            'status'        => 'working',
            'escrow_status' => 'held',
            'progress'      => 45,
            'due_at'        => now()->addDay(),
        ]);

        if ($services->count() > 1) {
            $second = $services->last();
            Order::create([
                'company_id'    => $company->id,
                'creator_id'    => $creator->id,
                'service_id'    => $second->id,
                'brief'         => 'Three thumbnail variants for the launch video — bold text, founder face, high contrast.',
                'turnaround'    => 'Express · 3 hours',
                'subtotal'      => $second->price,
                'fee'           => (int) round($second->price * 0.10),
                'discount'      => 0,
                'total'         => $second->price,
                'status'        => 'delivered',
                'escrow_status' => 'released',
                'progress'      => 100,
                'due_at'        => now()->subDay(),
            ]);
        }
    }
}
