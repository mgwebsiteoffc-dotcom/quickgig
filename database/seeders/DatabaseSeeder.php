<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Entry point: `php artisan db:seed` (or `migrate --seed`).
 *
 * Production-safe by default — only staff accounts and settings are created.
 * Demo marketplace data (companies, creators, services, orders) is seeded in
 * every other environment, or on demand:
 *
 *   php artisan db:seed --class=AdminUserSeeder
 *   php artisan db:seed --class=DemoDataSeeder
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            AdminUserSeeder::class,
        ]);

        if (app()->environment('production')) {
            $this->command?->info('Production environment — demo marketplace data skipped.');
            $this->command?->info('Run `php artisan db:seed --class=DemoDataSeeder` explicitly if you really want it.');

            return;
        }

        $this->call(DemoDataSeeder::class);
    }
}
