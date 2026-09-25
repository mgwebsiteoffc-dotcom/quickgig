<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payout;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminScreensTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_area_requires_authentication(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_every_admin_screen_renders_for_a_super_admin(): void
    {
        $this->makeOrder();

        $this->actingAs($this->admin('super_admin'));

        foreach ([
            '/admin',
            '/admin/orders',
            '/admin/creators',
            '/admin/companies',
            '/admin/services',
            '/admin/blogs',
            '/admin/faqs',
            '/admin/users',
            '/admin/payouts',
            '/admin/settings',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_order_detail_renders_real_data(): void
    {
        $order = $this->makeOrder();

        $this->actingAs($this->admin())
            ->get('/admin/orders/'.$order->uid)
            ->assertOk()
            ->assertSee($order->uid)
            ->assertSee('Test brief for the order.');
    }

    public function test_dashboard_counts_come_from_the_database(): void
    {
        $this->makeOrder();
        $this->makeOrder(['status' => 'review']);

        $this->actingAs($this->admin())
            ->get('/admin')
            ->assertOk()
            ->assertSee('Total Orders')
            ->assertDontSee('1,247'); // the old hardcoded figure
    }

    public function test_support_role_cannot_change_order_status(): void
    {
        $order = $this->makeOrder();

        $this->actingAs($this->admin('support'))
            ->post('/admin/orders/'.$order->uid.'/status', ['status' => 'approved'])
            ->assertForbidden();

        $this->assertSame('working', $order->fresh()->status);
    }

    public function test_manager_can_change_order_status(): void
    {
        $order = $this->makeOrder();

        $this->actingAs($this->admin('manager'))
            ->post('/admin/orders/'.$order->uid.'/status', ['status' => 'review'])
            ->assertRedirect();

        $this->assertSame('review', $order->fresh()->status);
    }

    public function test_finance_can_release_escrow_and_a_payout_is_created_once(): void
    {
        $order = $this->makeOrder();

        $finance = $this->admin('finance');

        $this->actingAs($finance)->post('/admin/orders/'.$order->uid.'/release')->assertRedirect();
        $this->actingAs($finance)->post('/admin/orders/'.$order->uid.'/release')->assertRedirect();

        $this->assertSame('released', $order->fresh()->escrow_status);
        $this->assertSame(1, Payout::where('order_id', $order->id)->count());
        $this->assertSame(945, (int) Payout::where('order_id', $order->id)->value('amount')); // 1050 minus 10%
    }

    public function test_manager_cannot_manage_users(): void
    {
        $this->actingAs($this->admin('manager'))->get('/admin/users')->assertForbidden();
    }

    public function test_admin_can_create_a_team_member(): void
    {
        $this->actingAs($this->admin('admin'))
            ->post('/admin/users', [
                'name' => 'New Support', 'email' => 'new.support@test.local',
                'role' => 'support', 'password' => 'Password123',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'new.support@test.local', 'role' => 'support']);
    }

    public function test_non_super_admin_cannot_promote_to_super_admin(): void
    {
        $target = $this->admin('support');

        $this->actingAs($this->admin('admin'))
            ->post('/admin/users/'.$target->id.'/role', ['role' => 'super_admin'])
            ->assertRedirect();

        $this->assertSame('support', $target->fresh()->role);
    }

    public function test_the_last_super_admin_cannot_be_deleted(): void
    {
        $super = $this->admin('super_admin');
        $other = $this->admin('admin');

        $this->actingAs($super)->delete('/admin/users/'.$other->id)->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $other->id]);

        $second = $this->admin('super_admin');
        $this->actingAs($second)->delete('/admin/users/'.$super->id)->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $super->id]);
    }

    public function test_settings_persist_to_the_database(): void
    {
        $this->actingAs($this->admin('super_admin'))
            ->post('/admin/settings', [
                'platform_fee' => 7, 'creator_fee' => 12, 'escrow_hours' => 72,
                'support_email' => 'help@test.local',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('settings', ['key' => 'creator_fee', 'value' => '12']);
        $this->assertSame('72', (string) Setting::get('escrow_hours'));
    }

    public function test_only_super_admin_reaches_settings(): void
    {
        $this->actingAs($this->admin('finance'))->get('/admin/settings')->assertForbidden();
    }
}
