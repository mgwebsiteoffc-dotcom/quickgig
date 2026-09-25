<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Company;
use App\Models\Creator;
use App\Models\Faq;
use App\Models\Order;
use App\Models\Payout;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_install_migration_no_longer_creates_accounts_with_published_passwords(): void
    {
        $this->assertSame(0, User::count());
        $this->assertDatabaseMissing('users', ['email' => 'admin@quickcontent.in']);
    }

    public function test_admin_seeder_honours_the_environment_credentials(): void
    {
        config(['app.url' => 'https://example.test']);
        putenv('ADMIN_EMAIL=boss@example.test');
        putenv('ADMIN_PASSWORD=SuperStrong123');

        $this->seed(AdminUserSeeder::class);

        $admin = User::where('email', 'boss@example.test')->first();

        $this->assertNotNull($admin);
        $this->assertSame('super_admin', $admin->role);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('SuperStrong123', $admin->password));

        putenv('ADMIN_EMAIL');
        putenv('ADMIN_PASSWORD');
    }

    public function test_admin_seeder_is_idempotent(): void
    {
        putenv('ADMIN_EMAIL=boss@example.test');
        putenv('ADMIN_PASSWORD=SuperStrong123');

        $this->seed(AdminUserSeeder::class);
        $this->seed(AdminUserSeeder::class);

        $this->assertSame(1, User::where('email', 'boss@example.test')->count());

        putenv('ADMIN_EMAIL');
        putenv('ADMIN_PASSWORD');
    }

    public function test_demo_seeder_builds_a_usable_marketplace(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->assertSame(3, Company::count());
        $this->assertSame(4, Creator::count());
        $this->assertSame(5, Service::count());
        $this->assertSame(10, Order::count());
        $this->assertGreaterThan(0, Payout::count());
        $this->assertSame(5, Faq::count());
        $this->assertSame(2, Blog::count());
    }

    public function test_demo_seeder_is_idempotent(): void
    {
        $this->seed(DemoDataSeeder::class);
        $this->seed(DemoDataSeeder::class);

        $this->assertSame(3, Company::count());
        $this->assertSame(4, Creator::count());
        $this->assertSame(10, Order::count());
    }

    public function test_every_seeded_creator_and_company_has_a_bound_login(): void
    {
        $this->seed(DemoDataSeeder::class);

        foreach (Creator::all() as $creator) {
            $this->assertDatabaseHas('users', ['creator_id' => $creator->id, 'role' => 'creator']);
        }

        foreach (Company::all() as $company) {
            $this->assertDatabaseHas('users', ['company_id' => $company->id, 'role' => 'business']);
        }
    }

    public function test_a_seeded_creator_can_sign_in_and_reach_their_dashboard(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->post('/login', ['email' => 'priya@creators.test', 'password' => 'Password123'])
            ->assertRedirect(route('creator.dashboard'));

        $this->get('/creator')->assertOk();
    }

    public function test_a_seeded_buyer_can_sign_in_and_reach_the_marketplace(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->post('/login', ['email' => 'rohan@avante.test', 'password' => 'Password123'])
            ->assertRedirect(route('business.home'));

        $this->get('/business')->assertOk();
    }

    public function test_setting_seeder_fills_the_defaults(): void
    {
        $this->seed(\Database\Seeders\SettingSeeder::class);

        foreach (array_keys(Setting::DEFAULTS) as $key) {
            $this->assertDatabaseHas('settings', ['key' => $key]);
        }
    }
}
