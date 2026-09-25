<?php

namespace Tests\Feature;

use App\Models\OrderDelivery;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BoardScreensTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render(): void
    {
        $this->get('/')->assertOk();
        $this->get('/business')->assertOk();
        $this->get('/blog')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/health')->assertOk()->assertJsonPath('status', 'ok');
        $this->get('/sitemap.xml')->assertOk();
    }

    public function test_service_page_renders_and_shows_the_order_form(): void
    {
        $order   = $this->makeOrder();
        $service = Service::first();

        $this->get('/services/'.$service->id)
            ->assertOk()
            ->assertSee('Test Reel')
            ->assertSee('Place your order');
    }

    public function test_service_page_404s_for_unknown_service(): void
    {
        $this->get('/services/does-not-exist')->assertNotFound();
    }

    public function test_creator_order_screen_renders(): void
    {
        $order = $this->makeOrder();

        $this->withSession(['creator_id' => $order->creator_id])
            ->get('/creator/orders/'.$order->uid)
            ->assertOk()
            ->assertSee($order->uid)
            ->assertSee('Deliver this order');
    }

    public function test_creator_can_deliver_with_a_file_upload(): void
    {
        Storage::fake('public');

        $order = $this->makeOrder();

        $this->withSession(['creator_id' => $order->creator_id])
            ->post('/creator/orders/'.$order->uid.'/deliver', [
                'file' => UploadedFile::fake()->create('final-cut.mp4', 512, 'video/mp4'),
                'note' => 'Hook added at 0:02.',
            ])
            ->assertRedirect();

        $delivery = OrderDelivery::where('order_id', $order->id)->firstOrFail();

        $this->assertSame('final-cut.mp4', $delivery->file_name);
        Storage::disk('public')->assertExists($delivery->file_path);
        $this->assertSame('review', $order->fresh()->status);
    }

    public function test_delivery_rejects_an_executable_upload(): void
    {
        Storage::fake('public');

        $order = $this->makeOrder();

        $this->withSession(['creator_id' => $order->creator_id])
            ->post('/creator/orders/'.$order->uid.'/deliver', [
                'file' => UploadedFile::fake()->create('payload.php', 10, 'application/x-php'),
            ])
            ->assertSessionHasErrors('file');

        $this->assertSame(0, OrderDelivery::count());
    }

    public function test_delivery_requires_a_file_or_a_link(): void
    {
        $order = $this->makeOrder();

        $this->withSession(['creator_id' => $order->creator_id])
            ->post('/creator/orders/'.$order->uid.'/deliver', ['note' => 'nothing attached'])
            ->assertSessionHasErrors('delivery_url');
    }

    public function test_an_order_can_be_placed_from_the_service_page(): void
    {
        $order   = $this->makeOrder();
        $service = Service::first();

        $this->post('/orders', [
            'service_id' => $service->id,
            'company_id' => $order->company_id,
            'brief'      => 'A brief that is definitely long enough.',
        ])->assertRedirect();

        $this->assertSame(2, \App\Models\Order::count());
    }
}
