<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_no_longer_leaks_demo_credentials(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertDontSee('Admin@12345')
            ->assertDontSee('Demo logins');
    }

    public function test_a_valid_admin_can_sign_in(): void
    {
        $user = $this->admin('admin');

        $this->post('/login', ['email' => $user->email, 'password' => 'Password123'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_a_disabled_account_cannot_stay_signed_in(): void
    {
        $user = $this->admin('admin');
        $user->update(['is_active' => false]);

        $this->post('/login', ['email' => $user->email, 'password' => 'Password123']);

        $this->assertGuest();
    }

    public function test_brute_force_is_rate_limited_after_five_attempts(): void
    {
        $user = $this->admin('admin');

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password']);
        }

        $this->post('/login', ['email' => $user->email, 'password' => 'Password123'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_password_reset_link_can_be_requested(): void
    {
        Notification::fake();

        $user = $this->admin('admin');

        $this->post('/forgot-password', ['email' => $user->email])->assertRedirect();

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_request_does_not_reveal_unknown_accounts(): void
    {
        Notification::fake();

        $this->post('/forgot-password', ['email' => 'nobody@test.local'])
            ->assertRedirect()
            ->assertSessionHas('toast');

        Notification::assertNothingSent();
    }

    public function test_a_password_can_actually_be_reset(): void
    {
        Notification::fake();

        $user  = $this->admin('admin');
        $token = app('auth.password.broker')->createToken($user);

        $this->post('/reset-password', [
            'token'    => $token,
            'email'    => $user->email,
            'password' => 'BrandNew123',
            'password_confirmation' => 'BrandNew123',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('BrandNew123', $user->fresh()->password));
    }

    public function test_weak_passwords_are_rejected(): void
    {
        $user  = $this->admin('admin');
        $token = app('auth.password.broker')->createToken($user);

        $this->post('/reset-password', [
            'token' => $token, 'email' => $user->email,
            'password' => 'short', 'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');
    }
}
