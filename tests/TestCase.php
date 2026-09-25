<?php

namespace Tests;

use App\Models\Company;
use App\Models\Creator;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function admin(string $role = 'super_admin'): User
    {
        return User::create([
            'name'      => ucfirst($role),
            'email'     => $role.'-'.uniqid().'@test.local',
            'password'  => Hash::make('Password123'),
            'role'      => $role,
            'is_active' => true,
        ]);
    }

    protected function makeOrder(array $overrides = []): Order
    {
        $company = Company::create([
            'name' => 'Test Co '.uniqid(), 'person_name' => 'Tester',
            'email' => 'co'.uniqid().'@test.local', 'is_active' => true,
        ]);

        $creator = Creator::create([
            'name' => 'Test Creator', 'handle' => '@tc'.uniqid(),
            'profile_type' => 'video_editor', 'upi_id' => 'tc@upi',
            'is_verified' => true, 'is_available' => true,
        ]);

        $service = Service::create([
            'creator_id' => $creator->id, 'title' => 'Test Reel', 'price' => 1000,
            'delivery_days' => 1, 'category' => 'Reel', 'price_type' => 'paid', 'is_active' => true,
        ]);

        return Order::create(array_merge([
            'company_id' => $company->id,
            'creator_id' => $creator->id,
            'service_id' => $service->id,
            'brief'      => 'Test brief for the order.',
            'subtotal'   => 1000,
            'fee'        => 50,
            'discount'   => 0,
            'total'      => 1050,
            'status'     => 'working',
            'escrow_status' => 'held',
        ], $overrides));
    }
}
