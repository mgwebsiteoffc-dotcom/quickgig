<?php

namespace Database\Seeders;

use App\Models\Creator;
use App\Models\PortfolioItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/** Demo creators, each with a portfolio item and a login bound to it (role: creator). */
class CreatorSeeder extends Seeder
{
    public const CREATORS = [
        ['name' => 'Priya Sharma',  'handle' => '@priyaedits',  'email' => 'priya@creators.test', 'profile_type' => 'video_editor', 'rating' => 4.9, 'price_from' => 1999, 'upi_id' => 'priya@upi', 'is_featured' => true],
        ['name' => 'Rahul Verma',   'handle' => '@rahulcuts',   'email' => 'rahul@creators.test', 'profile_type' => 'video_editor', 'rating' => 4.8, 'price_from' => 2499, 'upi_id' => 'rahul@upi', 'is_featured' => false],
        ['name' => 'Neha Jain',     'handle' => '@nehacreates', 'email' => 'neha@creators.test',  'profile_type' => 'designer',     'rating' => 4.7, 'price_from' => 999,  'upi_id' => 'neha@upi',  'is_featured' => true],
        ['name' => 'Riya Malhotra', 'handle' => '@ugc_riya',    'email' => 'riya@creators.test',  'profile_type' => 'ugc_creator',  'rating' => 4.8, 'price_from' => 3499, 'upi_id' => 'riya@upi',  'is_featured' => false, 'barter_available' => true],
    ];

    public function run(): void
    {
        foreach (self::CREATORS as $row) {
            $creator = Creator::firstOrCreate(
                ['handle' => $row['handle']],
                $row + [
                    'is_verified'      => true,
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
            );

            $user = User::firstOrCreate(
                ['email' => $row['email']],
                [
                    'name'       => $row['name'],
                    'password'   => Hash::make('Password123'),
                    'role'       => 'creator',
                    'creator_id' => $creator->id,
                    'is_active'  => true,
                    'email_verified_at' => now(),
                ]
            );

            if (! $creator->user_id) {
                $creator->update(['user_id' => $user->id]);
            }

            if ($creator->portfolio()->count() === 0) {
                PortfolioItem::create([
                    'creator_id'   => $creator->id,
                    'title'        => 'Sample work — '.$creator->name,
                    'slug'         => Str::slug($creator->name).'-sample-'.$creator->id,
                    'category'     => 'Reel',
                    'description'  => 'A representative piece from this creator.',
                    'is_published' => true,
                ]);
            }
        }

        $this->command?->info(count(self::CREATORS).' creators + creator logins seeded (password: Password123).');
    }
}
