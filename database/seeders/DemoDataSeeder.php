<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/** All the marketplace demo data, in dependency order. Never run automatically in production. */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            CreatorSeeder::class,
            ServiceSeeder::class,
            OrderSeeder::class,
            ContentSeeder::class,
        ]);
    }
}
