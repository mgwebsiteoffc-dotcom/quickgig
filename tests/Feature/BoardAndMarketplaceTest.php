<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Creator;
use App\Models\Order;
use App\Models\Service;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\RefreshesDatabase;
use Tests\TestCase;

class BoardAndMarketplaceTest extends TestCase
{
    use RefreshesDatabase;

    private function buyer(): User
    {
        $company = Company::create([
            'name' => 'Avante Studio', 'slug' => 'avante-' . uniqid(),
            'person_name' => 'Rohan', 'email' => 'rohan' . uniqid() . '@example.com', 'is_active' => true,
        ]);

        return User::create([
            'name' => 'Rohan', 'email' => 'rohan' . uniqid() . '@example.com',
            'password' => Hash::make('secret123'), 'role' => 'business',
            'company_id' => $company->id, 'is_active' => true,
        ]);
    }

    private function gig(array $overrides = []): Service
    {
        $creator = Creator::create([
            'name' => 'Priya', 'handle' => '@priya' . uniqid(), 'email' => 'priya' . uniqid() . '@example.com',
            'profile_type' => 'video_editor', 'is_available' => true, 'is_verified' => true, 'upi_id' => 'priya@upi',
        ]);

        return Service::create(array_merge([
            'creator_id' => $creator->id, 'title' => 'Launch reel', 'slug' => 'launch-reel-' . uniqid(),
            'price' => 2000, 'delivery_days' => 1, 'category' => 'Reel',
            'is_active' => true, 'price_type' => 'paid', 'rating' => 4.9, 'sold_count' => 50,
        ], $overrides));
    }

    /* ── task board ── */

    public function test_the_board_is_private_to_signed_in_buyers(): void
    {
        $this->get('/business/board')->assertRedirect('/login');
    }

    public function test_a_task_can_be_created_from_plain_fields(): void
    {
        $user = $this->buyer();

        $this->actingAs($user)->post('/business/board/tasks', [
            'title'    => 'Three reels for the launch',
            'brief'    => 'Founder on camera, vertical, captions burned in.',
            'priority' => 'high',
        ])->assertStatus(302);

        $task = Task::latest('id')->first();

        $this->assertSame('Three reels for the launch', $task->title);
        $this->assertSame('high', $task->priority);
        $this->assertSame('queued', $task->status);
        $this->assertSame($user->company_id, $task->company_id);
    }

    public function test_a_task_can_be_created_from_one_sentence(): void
    {
        $user = $this->buyer();

        $this->actingAs($user)->post('/business/board/tasks', [
            'prompt' => 'Need 3 instagram reels edited by friday for the protein bar launch',
        ])->assertStatus(302);

        $this->assertSame(1, Task::count());
        $this->assertNotEmpty(Task::first()->title);
    }

    public function test_a_task_requires_either_a_title_or_a_prompt(): void
    {
        $this->actingAs($this->buyer())
            ->post('/business/board/tasks', ['brief' => 'no title, no prompt'])
            ->assertSessionHasErrors('title');

        $this->assertSame(0, Task::count());
    }

    public function test_status_and_priority_move_and_persist(): void
    {
        $user = $this->buyer();
        $task = Task::create([
            'company_id' => $user->company_id, 'title' => 'Thumbnail set',
            'status' => 'queued', 'priority' => 'normal',
        ]);

        $this->actingAs($user)->post("/business/board/{$task->id}/status", ['status' => 'in_progress']);
        $this->assertSame('in_progress', $task->fresh()->status);

        $this->actingAs($user)->post("/business/board/{$task->id}/priority", ['priority' => 'urgent']);
        $this->assertSame('urgent', $task->fresh()->priority);
    }

    public function test_an_invalid_status_is_rejected(): void
    {
        $user = $this->buyer();
        $task = Task::create(['company_id' => $user->company_id, 'title' => 'X', 'status' => 'queued']);

        $this->actingAs($user)
            ->post("/business/board/{$task->id}/status", ['status' => 'teleported'])
            ->assertSessionHasErrors('status');

        $this->assertSame('queued', $task->fresh()->status);
    }

    public function test_a_buyer_cannot_touch_another_companys_task(): void
    {
        $mine   = $this->buyer();
        $theirs = $this->buyer();

        $task = Task::create(['company_id' => $theirs->company_id, 'title' => 'Not yours', 'status' => 'queued']);

        $this->actingAs($mine)->post("/business/board/{$task->id}/status", ['status' => 'done'])
            ->assertForbidden();

        $this->assertSame('queued', $task->fresh()->status);
    }

    public function test_a_task_can_be_deleted_by_its_owner(): void
    {
        $user = $this->buyer();
        $task = Task::create(['company_id' => $user->company_id, 'title' => 'Bin me', 'status' => 'queued']);

        $this->actingAs($user)->delete("/business/board/{$task->id}");

        $this->assertSame(0, Task::count());
    }

    public function test_converting_a_task_raises_an_escrow_order(): void
    {
        $user = $this->buyer();
        $gig  = $this->gig();

        $task = Task::create([
            'company_id' => $user->company_id, 'title' => 'Launch reel',
            'brief' => 'Founder on camera for the launch film.', 'status' => 'queued',
        ]);

        $this->actingAs($user)->post("/business/board/{$task->id}/convert");

        $order = Order::latest('id')->first();

        $this->assertNotNull($order, 'converting a task should create an order');
        $this->assertSame($user->company_id, $order->company_id);
        $this->assertSame($order->id, $task->fresh()->order_id);
    }

    /* ── marketplace filters ── */

    public function test_search_matches_the_title(): void
    {
        $this->gig(['title' => 'Podcast teaser edit']);
        $this->gig(['title' => 'Packaging design']);

        $this->get('/marketplace?q=podcast')
            ->assertOk()
            ->assertSee('Podcast teaser edit')
            ->assertDontSee('Packaging design');
    }

    public function test_the_category_filter_narrows_the_list(): void
    {
        $this->gig(['title' => 'Reel edit A', 'category' => 'Reel']);
        $this->gig(['title' => 'Logo pack B', 'category' => 'Design']);

        $this->get('/marketplace?category=Design')
            ->assertOk()
            ->assertSee('Logo pack B')
            ->assertDontSee('Reel edit A');
    }

    public function test_the_max_price_filter_excludes_dearer_gigs(): void
    {
        $this->gig(['title' => 'Cheap cut', 'price' => 900]);
        $this->gig(['title' => 'Premium film', 'price' => 45000]);

        $this->get('/marketplace?max=1000')
            ->assertOk()
            ->assertSee('Cheap cut')
            ->assertDontSee('Premium film');
    }

    public function test_the_24_hour_filter_only_keeps_one_day_delivery(): void
    {
        $this->gig(['title' => 'Same day cut', 'delivery_days' => 1]);
        $this->gig(['title' => 'Week long film', 'delivery_days' => 7]);

        $this->get('/marketplace?fast=1')
            ->assertOk()
            ->assertSee('Same day cut')
            ->assertDontSee('Week long film');
    }

    public function test_price_sorting_orders_the_results(): void
    {
        $this->gig(['title' => 'Dearest gig', 'price' => 9000]);
        $this->gig(['title' => 'Cheapest gig', 'price' => 500]);

        $body = $this->get('/marketplace?sort=price_low')->assertOk()->getContent();

        $this->assertLessThan(
            strpos($body, 'Dearest gig'),
            strpos($body, 'Cheapest gig'),
            'low-to-high sorting should put the cheapest gig first'
        );
    }

    public function test_an_unknown_sort_falls_back_instead_of_erroring(): void
    {
        $this->gig();

        $this->get('/marketplace?sort=; DROP TABLE services;--')->assertOk();
    }

    public function test_an_inactive_gig_never_shows(): void
    {
        $this->gig(['title' => 'Retired gig', 'is_active' => false]);

        $this->get('/marketplace')->assertOk()->assertDontSee('Retired gig');
    }

    public function test_the_gig_page_renders_and_unknown_ids_404(): void
    {
        $gig = $this->gig(['title' => 'Detail page gig']);

        $this->get('/gigs/' . $gig->id)->assertOk()->assertSee('Detail page gig');
        $this->get('/gigs/999999')->assertNotFound();
    }
}
