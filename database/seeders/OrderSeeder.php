<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Order;
use App\Models\OrderDelivery;
use App\Models\Payout;
use App\Models\Service;
use Illuminate\Database\Seeder;

/** Orders across every status, with payouts and a delivery record where relevant. */
class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::orderBy('id')->get();
        $services  = Service::with('creator')->orderBy('id')->get();

        if ($companies->isEmpty() || $services->isEmpty()) {
            $this->command?->warn('Companies or services missing — run CompanySeeder and ServiceSeeder first.');

            return;
        }

        if (Order::count() > 0) {
            $this->command?->info('Orders already present — skipping.');

            return;
        }

        $statuses = ['pending', 'working', 'review', 'delivered', 'approved'];

        foreach (range(0, 9) as $i) {
            $service = $services[$i % $services->count()];
            $company = $companies[$i % $companies->count()];
            $status  = $statuses[$i % count($statuses)];

            $isBarter = $service->isBarter();
            $subtotal = $isBarter ? 0 : (int) $service->price;
            $fee      = $isBarter ? 0 : (int) round($subtotal * 0.05);

            $order = Order::create([
                'company_id'    => $company->id,
                'creator_id'    => $service->creator_id,
                'service_id'    => $service->id,
                'brief'         => 'Hook in the first two seconds, 9:16, burned-in captions, brand colours.',
                'turnaround'    => $service->delivery_days.' Day',
                'subtotal'      => $subtotal,
                'fee'           => $fee,
                'discount'      => 0,
                'total'         => $subtotal + $fee,
                'status'        => $status,
                'escrow_status' => $isBarter ? 'barter' : ($status === 'approved' ? 'released' : 'held'),
                'progress'      => match ($status) { 'pending' => 5, 'working' => 40, 'review' => 90, default => 100 },
                'due_at'        => now()->addDays($service->delivery_days),
            ]);

            $order->forceFill(['created_at' => now()->subDays(10 - $i)])->save();

            if (in_array($status, ['review', 'delivered', 'approved'], true)) {
                OrderDelivery::create([
                    'order_id'     => $order->id,
                    'creator_id'   => $order->creator_id,
                    'delivery_url' => 'https://example.test/deliveries/'.$order->uid.'.mp4',
                    'note'         => 'First cut — hook added at 0:02.',
                ]);
            }

            if ($status === 'approved' && ! $isBarter) {
                Payout::firstOrCreate(['order_id' => $order->id], [
                    'creator_id' => $order->creator_id,
                    'amount'     => (int) round($order->total * 0.9),
                    'upi_id'     => $order->creator->upi_id ?? null,
                    'status'     => $i % 2 ? Payout::STATUS_READY : Payout::STATUS_HOLD,
                    'hold_until' => $i % 2 ? now()->subHour() : now()->addHours(48),
                ]);
            }
        }

        $this->command?->info(Order::count().' orders, '.OrderDelivery::count().' deliveries and '.Payout::count().' payouts seeded.');
    }
}
