<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Creates the staff accounts. Safe to run on production and re-runnable.
 *
 * The super admin's credentials come from the environment:
 *   ADMIN_EMAIL=you@yourdomain.com
 *   ADMIN_PASSWORD=aStrongPassword
 *
 * If ADMIN_PASSWORD is not set, a random one is generated and printed ONCE —
 * copy it immediately, it is not stored anywhere in plain text.
 *
 * The extra role accounts (manager/support/finance) are only created when
 * --class is run with seeding of demo data, i.e. in non-production envs.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $host  = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost';
        $email = env('ADMIN_EMAIL') ?: 'admin@'.$host;

        $existing = User::where('email', $email)->first();

        if ($existing) {
            $existing->update(['role' => 'super_admin', 'is_active' => true]);
            $this->command?->info("Super admin already exists: {$email} (left untouched)");
        } else {
            $plain    = env('ADMIN_PASSWORD') ?: Str::password(16);
            $generated = ! env('ADMIN_PASSWORD');

            User::create([
                'name'      => env('ADMIN_NAME', 'Super Admin'),
                'email'     => $email,
                'password'  => Hash::make($plain),
                'role'      => 'super_admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            $this->command?->info("Super admin created: {$email}");

            if ($generated) {
                $this->command?->warn("Generated password: {$plain}");
                $this->command?->warn('Copy it now — it is not stored anywhere and will not be shown again.');
            }
        }

        if (app()->environment('production')) {
            $this->command?->info('Production environment — skipping the demo role accounts.');

            return;
        }

        $this->demoStaff();
    }

    /** Non-production helper logins, one per role. Password: Password123 */
    private function demoStaff(): void
    {
        foreach ([
            ['Ops Manager',  'manager@example.test', 'manager'],
            ['Support Desk', 'support@example.test', 'support'],
            ['Finance Team', 'finance@example.test', 'finance'],
            ['Admin',        'admin2@example.test',  'admin'],
        ] as [$name, $email, $role]) {
            User::firstOrCreate(
                ['email' => $email],
                [
                    'name'      => $name,
                    'password'  => Hash::make('Password123'),
                    'role'      => $role,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command?->info('Demo staff logins ready (manager|support|finance|admin2 @example.test / Password123).');
    }
}
