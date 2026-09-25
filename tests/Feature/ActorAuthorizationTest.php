<?php

namespace Tests\Feature;

use App\Models\Creator;
use App\Models\Order;
use App\Models\PortfolioItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Guards the hole where /creator/* and /business/* were unauthenticated and
 * identity came from session('creator_id', 1) — i.e. every anonymous visitor
 * was creator #1.
 */
class ActorAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function creatorUser(Creator $creator): User
    {
        return User::create([
            'name' => 'Creator User', 'email' => 'cu'.uniqid().'@test.local',
            'password' => Hash::make('Password123'), 'role' => 'creator',
            'creator_id' => $creator->id, 'is_active' => true,
        ]);
    }

    private function businessUser(int $companyId): User
    {
        return User::create([
            'name' => 'Buyer', 'email' => 'bu'.uniqid().'@test.local',
            'password' => Hash::make('Password123'), 'role' => 'business',
            'company_id' => $companyId, 'is_active' => true,
        ]);
    }

    public function test_guests_are_redirected_from_every_app_route(): void
    {
        $order = $this->makeOrder();

        foreach ([
            '/business', '/app', '/business/profile',
            '/creator', '/creator/profile',
            '/orders/'.$order->uid,
            '/creator/orders/'.$order->uid,
        ] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
    }

    public function test_a_guest_cannot_write_to_a_creator_profile(): void
    {
        $order = $this->makeOrder();

        $this->post('/creator/profile', ['name' => 'Hijacked', 'handle' => '@hijack'])
            ->assertRedirect('/login');

        $this->assertSame('Test Creator', Creator::find($order->creator_id)->name);
    }

    public function test_a_guest_cannot_upload_a_delivery(): void
    {
        Storage::fake('public');

        $order = $this->makeOrder();

        $this->post('/creator/orders/'.$order->uid.'/deliver', [
            'file' => UploadedFile::fake()->create('x.mp4', 10, 'video/mp4'),
        ])->assertRedirect('/login');

        $this->assertSame(0, \App\Models\OrderDelivery::count());
    }

    public function test_a_stale_session_value_no_longer_grants_creator_access(): void
    {
        $order = $this->makeOrder();

        // The old exploit: just set the session key (or rely on the default of 1).
        $this->withSession(['creator_id' => $order->creator_id])
            ->get('/creator')
            ->assertRedirect('/login');
    }

    public function test_a_creator_without_a_linked_profile_is_forbidden(): void
    {
        $user = User::create([
            'name' => 'Orphan', 'email' => 'orphan@test.local',
            'password' => Hash::make('Password123'), 'role' => 'creator', 'is_active' => true,
        ]);

        $this->actingAs($user)->get('/creator')->assertForbidden();
    }

    public function test_a_creator_only_sees_their_own_profile_and_orders(): void
    {
        $mine     = $this->makeOrder();
        $theirs   = $this->makeOrder();
        $creator  = Creator::find($mine->creator_id);

        $this->actingAs($this->creatorUser($creator))
            ->get('/creator/orders/'.$mine->uid)->assertOk();

        $this->actingAs($this->creatorUser($creator))
            ->get('/creator/orders/'.$theirs->uid)->assertNotFound();
    }

    public function test_a_creator_cannot_delete_another_creators_portfolio_item(): void
    {
        $mine   = $this->makeOrder();
        $theirs = $this->makeOrder();

        $victim = PortfolioItem::create([
            'creator_id' => $theirs->creator_id, 'title' => 'Their work',
            'slug' => 'their-work', 'is_published' => true,
        ]);

        $this->actingAs($this->creatorUser(Creator::find($mine->creator_id)))
            ->delete('/creator/portfolio/'.$victim->id)
            ->assertRedirect();

        $this->assertDatabaseHas('portfolio_items', ['id' => $victim->id]);
    }

    public function test_a_buyer_cannot_open_another_companys_order(): void
    {
        $mine   = $this->makeOrder();
        $theirs = $this->makeOrder();

        $buyer = $this->businessUser($mine->company_id);

        $this->actingAs($buyer)->get('/orders/'.$mine->uid)->assertOk();
        $this->actingAs($buyer)->get('/orders/'.$theirs->uid)->assertForbidden();
    }

    public function test_a_buyer_cannot_approve_another_companys_order(): void
    {
        $mine   = $this->makeOrder();
        $theirs = $this->makeOrder();

        $this->actingAs($this->businessUser($mine->company_id))
            ->post('/orders/'.$theirs->uid.'/approve')
            ->assertForbidden();

        $this->assertSame('held', $theirs->fresh()->escrow_status);
    }

    public function test_a_creator_cannot_approve_their_own_delivery(): void
    {
        $order = $this->makeOrder();

        $this->actingAs($this->creatorUser(Creator::find($order->creator_id)))
            ->post('/orders/'.$order->uid.'/approve')
            ->assertForbidden();

        $this->assertSame('held', $order->fresh()->escrow_status);
    }

    public function test_ordering_on_behalf_of_someone_elses_business_is_blocked(): void
    {
        $mine   = $this->makeOrder();
        $theirs = $this->makeOrder();

        $this->actingAs($this->businessUser($mine->company_id))
            ->post('/orders', [
                'service_id' => $theirs->service_id,
                'company_id' => $theirs->company_id,
                'brief'      => 'Trying to bill another business.',
            ])
            ->assertForbidden();

        $this->assertSame(2, Order::count());
    }

    public function test_switching_to_a_company_you_do_not_own_is_blocked(): void
    {
        $mine   = $this->makeOrder();
        $theirs = $this->makeOrder();

        $this->actingAs($this->businessUser($mine->company_id))
            ->post('/business/switch', ['company_id' => $theirs->company_id])
            ->assertForbidden();
    }

    public function test_the_public_creator_profile_stays_public(): void
    {
        $order = $this->makeOrder();

        $this->get('/creator/'.$order->creator_id)->assertOk();
    }

    public function test_staff_can_act_on_behalf_of_a_creator_for_support(): void
    {
        $order = $this->makeOrder();

        $this->actingAs($this->admin('support'))
            ->withSession(['creator_id' => $order->creator_id])
            ->get('/creator')
            ->assertOk();
    }
}
