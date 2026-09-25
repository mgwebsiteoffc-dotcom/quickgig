<?php

namespace Database\Seeders;

use App\Models\Creator;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /** [title, category, price, delivery_days, profile_type, price_type] */
    public const CATALOG = [
        ['Talking-Head Reel',    'Reel',          2499, 1, 'video_editor', 'paid'],
        ['Retention Reel Edit',  'Reel',          3999, 2, 'video_editor', 'paid'],
        ['High CTR Thumbnail',   'Thumbnail',     1299, 1, 'designer',     'paid'],
        ['UGC Unboxing Video',   'UGC Video',     3499, 2, 'ugc_creator',  'paid'],
        ['Barter Collab Reel',   'Barter Collab',    0, 3, 'ugc_creator',  'barter'],
    ];

    public function run(): void
    {
        $creators = Creator::orderBy('id')->get();

        if ($creators->isEmpty()) {
            $this->command?->warn('No creators found — run CreatorSeeder first.');

            return;
        }

        foreach (self::CATALOG as $i => [$title, $category, $price, $days, $type, $priceType]) {
            $creator = $creators->firstWhere('profile_type', $type) ?? $creators[$i % $creators->count()];

            Service::firstOrCreate(
                ['title' => $title, 'creator_id' => $creator->id],
                [
                    'description'    => $title.' delivered in '.$days.' day(s) with unlimited minor tweaks.',
                    'price'          => $price,
                    'mrp'            => $price ? (int) round($price * 1.25) : 0,
                    'delivery_days'  => $days,
                    'category'       => $category,
                    'profile_type'   => $type,
                    'price_type'     => $priceType,
                    'is_barter'      => $priceType === 'barter',
                    'barter_value'   => $priceType === 'barter' ? 4000 : null,
                    'is_active'      => true,
                    'deliverables'   => ['Source file', 'Burned-in captions', '1 revision'],
                    'revision_count' => 1,
                    'sold_count'     => random_int(5, 120),
                    'rating'         => 4.8,
                ]
            );
        }

        $this->command?->info(count(self::CATALOG).' services seeded.');
    }
}
