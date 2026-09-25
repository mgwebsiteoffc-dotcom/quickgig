<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/** Demo businesses, each with a login bound to it (role: business). */
class CompanySeeder extends Seeder
{
    public const COMPANIES = [
        ['name' => 'Avante Studio',    'person_name' => 'Rohan Sharma', 'email' => 'rohan@avante.test',     'phone' => '9876543210', 'plan' => 'Pro',     'city' => 'Gurugram',  'industry' => 'Creative agency'],
        ['name' => 'BrandScale Media', 'person_name' => 'Priya Kapoor', 'email' => 'priya@brandscale.test', 'phone' => '9876543211', 'plan' => 'Team',    'city' => 'Mumbai',    'industry' => 'D2C'],
        ['name' => 'GrowthX Labs',     'person_name' => 'Aman Verma',   'email' => 'aman@growthx.test',     'phone' => '9876543212', 'plan' => 'Starter', 'city' => 'Bengaluru', 'industry' => 'SaaS'],
    ];

    public function run(): void
    {
        foreach (self::COMPANIES as $row) {
            $company = Company::firstOrCreate(
                ['email' => $row['email']],
                $row + ['is_active' => true, 'is_verified' => true, 'team_size' => 12]
            );

            User::firstOrCreate(
                ['email' => $row['email']],
                [
                    'name'       => $row['person_name'],
                    'password'   => Hash::make('Password123'),
                    'role'       => 'business',
                    'company_id' => $company->id,
                    'is_active'  => true,
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command?->info(count(self::COMPANIES).' companies + business logins seeded (password: Password123).');
    }
}
