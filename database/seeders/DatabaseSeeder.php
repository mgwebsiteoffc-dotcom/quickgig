<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Creator;
use App\Models\Faq;
use App\Models\Order;
use App\Models\Service;
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
        $this->faqs();
        $creator = $this->creatorAccount();
        $company = $this->businessAccount();
        $this->extraGigs();
        $this->sampleOrders($company, $creator);

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

    /* ── landing FAQs (also power the FAQPage JSON-LD) ── */
    private function faqs(): void
    {
        $faqs = [
            ['How is Quick GIGS different from a normal freelance site?', 'You never post a job and wait for proposals. Pick a fixed-price gig or post a brief, and the matching engine assigns a verified creator in minutes. Payment stays in escrow until you approve.', 'General', 1, true],
            ['How fast is delivery, really?', 'Express gigs start within minutes and land in about three hours. Standard reels and thumbnails are next-day. Team packs and AI ads take up to two days.', 'Delivery', 2, true],
            ['What if I do not like the work?', 'Every gig includes two free revisions. If the delivery still misses the brief, raise a dispute before approving and the escrow is refunded in full.', 'Guarantee', 3, true],
            ['How are creators verified?', 'Creators submit ID, portfolio and client references. Our team reviews each profile manually, then tracks on-time delivery, rating and response time on every gig.', 'Creators', 4, true],
            ['What does it cost?', 'Gigs start at ₹1,299. The platform fee is a flat 10%, so creators keep 90%. Posting briefs, browsing gigs and getting matched is free.', 'Pricing', 5, true],
            ['How do creators get paid?', 'The moment you approve a delivery, escrow is released and paid out to the creator via UPI or bank transfer, usually within minutes.', 'Payouts', 6, true],
            ['Do you provide GST invoices?', 'Yes. Add your GSTIN in workspace settings and every order generates a GST-compliant invoice. Creators get matching payout statements.', 'Pricing', 7, false],
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

    /* ── demo creator login, wired to an existing creator profile ── */
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

    /* ── a few extra catalogue gigs so the marketplace never looks empty ── */
    private function extraGigs(): void
    {
        $creators = Creator::where('is_verified', true)->pluck('id')->all();
        if (empty($creators)) return;

        $gigs = [
            ['UGC unboxing video by a real creator', 'UGC Video', 3999, 1, 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?w=800&q=80', 'UGC', 860, 4.9, 'A real creator films, speaks and edits an authentic 30-second unboxing for your product. Shot vertically with captions and a clear call to action.'],
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

        // Make sure everything in the catalogue is orderable.
        Service::whereNull('is_active')->update(['is_active' => true]);
        Service::whereNull('price_type')->update(['price_type' => 'paid']);
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
