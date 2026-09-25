<?php

namespace Tests\Feature;

use App\Models\Creator;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\User;
use App\Services\Ai\AiManager;
use App\Services\BriefComposer;
use App\Services\MatchEngine;
use Illuminate\Support\Facades\Hash;
use Tests\RefreshesDatabase;
use Tests\TestCase;

class PlatformTest extends TestCase
{
    use RefreshesDatabase;

    /* ── settings ── */

    public function test_secrets_are_encrypted_at_rest_and_masked_for_display(): void
    {
        Setting::put('ai.openai.key', 'sk-super-secret-value', true);

        $raw = Setting::query()->find('ai.openai.key')->value;

        $this->assertNotSame('sk-super-secret-value', $raw, 'the key must not sit in the table in clear text');
        $this->assertSame('sk-super-secret-value', setting('ai.openai.key'));
        $this->assertSame('sk-sup••••••••alue', Setting::masked('ai.openai.key'));
    }

    public function test_settings_fall_back_to_defaults_when_unset(): void
    {
        $this->assertSame(48, (int) setting('platform.escrow_hours', 48));
        $this->assertNull(setting('nothing.here'));
    }

    /* ── AI provider selection ── */

    public function test_ai_falls_back_to_the_deterministic_engine_with_no_keys(): void
    {
        $ai = app(AiManager::class);

        $this->assertFalse($ai->enabled());
        $this->assertSame('none', $ai->key());
    }

    public function test_admin_choice_decides_which_provider_is_used(): void
    {
        Setting::put('ai.openai.key', 'sk-test', true);
        Setting::put('ai.gemini.key', 'AIza-test', true);
        Setting::put('ai.default_provider', 'gemini');

        $this->assertSame('gemini', app(AiManager::class)->key());

        Setting::put('ai.default_provider', 'openai');
        $this->assertSame('openai', app(AiManager::class)->key());

        Setting::put('ai.default_provider', 'none');
        $this->assertFalse(app(AiManager::class)->enabled(), 'off means off, even with keys saved');
    }

    /* ── brief engine ── */

    public function test_brief_engine_returns_a_usable_brief_without_any_model(): void
    {
        $brief = app(BriefComposer::class)->compose([
            'idea'     => 'a launch reel for our ₹999 protein bar, founder on camera',
            'format'   => 'reel',
            'goal'     => 'launch',
            'tone'     => 'confident',
            'audience' => 'gym goers in metros',
            'urgency'  => 'express',
        ]);

        $this->assertCount(3, $brief['hooks']);
        $this->assertNotEmpty($brief['beats']);
        $this->assertNotEmpty($brief['qa_gate']);
        $this->assertSame('Reel', $brief['suggested']['category']);
        $this->assertSame(3998, $brief['suggested']['price'], 'express is 1.6x the ₹2,499 base');
        $this->assertStringContainsString('protein bar', $brief['title']);
    }

    /* ── matching ── */

    public function test_match_scores_rank_the_better_freelancer_first_and_explain_why(): void
    {
        $strong = Creator::create([
            'name' => 'Strong', 'handle' => '@strong', 'profile_type' => 'video_editor',
            'skills' => ['Reels', 'Captions'], 'price_from' => 2000, 'rating' => 4.9,
            'response_minutes' => 5, 'on_time_rate' => 98, 'orders_count' => 300,
            'is_available' => true, 'is_verified' => true,
        ]);

        $weak = Creator::create([
            'name' => 'Weak', 'handle' => '@weak', 'profile_type' => 'designer',
            'skills' => ['Packaging'], 'price_from' => 9000, 'rating' => 4.1,
            'response_minutes' => 90, 'on_time_rate' => 70, 'orders_count' => 3,
            'is_available' => false, 'is_verified' => true,
        ]);

        $ranked = app(MatchEngine::class)->rank(
            collect([$weak, $strong]),
            ['category' => 'Reel', 'skills' => ['reels', 'captions'], 'budget' => 2500, 'urgency' => 'express']
        );

        $this->assertSame('Strong', $ranked->first()['creator']->name);
        $this->assertGreaterThan($ranked->last()['score'], $ranked->first()['score']);
        $this->assertCount(5, $ranked->first()['breakdown'], 'every factor is published');
        $this->assertNotEmpty($ranked->first()['breakdown'][0]['reason']);
    }

    /* ── skills library ── */

    public function test_hidden_skills_disappear_from_the_picker_but_stay_on_profiles(): void
    {
        $skill = Skill::create(['name' => 'Colour grading', 'discipline' => 'Video']);

        $this->assertContains('Colour grading', Skill::allowedNames());

        $skill->update(['is_active' => false]);

        $this->assertNotContains('Colour grading', Skill::allowedNames());
        $this->assertArrayNotHasKey('Video', Skill::grouped());
    }

    public function test_signup_stores_picked_skills_as_an_array(): void
    {
        Skill::create(['name' => 'Reels', 'discipline' => 'Video']);

        $this->post('/register', [
            'account_type' => 'creator',
            'name'         => 'Picker Tester',
            'email'        => 'picker@example.com',
            'password'     => 'Password123',
            'password_confirmation' => 'Password123',
            'terms'        => '1',
            'skills'       => ['Reels', 'Captions & subtitles'],
        ])->assertRedirect();

        $creator = Creator::where('email', 'picker@example.com')->first();

        $this->assertNotNull($creator, 'signing up without a handle must still work');
        $this->assertSame(['Reels', 'Captions & subtitles'], $creator->skills);
        $this->assertSame('@pickertester', $creator->handle);
    }

    /* ── access control ── */

    public function test_admin_areas_are_closed_to_business_accounts(): void
    {
        $user = User::create([
            'name' => 'Buyer', 'email' => 'buyer@example.com', 'password' => Hash::make('secret123'),
            'role' => 'business', 'is_active' => true,
        ]);

        $this->actingAs($user)->get('/admin/settings')->assertForbidden();
        $this->actingAs($user)->get('/admin/payouts')->assertForbidden();
    }

    public function test_only_super_admin_can_write_settings(): void
    {
        $manager = User::create([
            'name' => 'Manager', 'email' => 'manager@example.com', 'password' => Hash::make('secret123'),
            'role' => 'manager', 'is_active' => true,
        ]);

        $this->actingAs($manager)->post('/admin/settings', ['platform' => ['fee_percent' => 30]])->assertForbidden();
    }

    public function test_public_pages_stay_up(): void
    {
        foreach (['/', '/marketplace', '/pricing', '/faq', '/brief-builder', '/for-business', '/compare'] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
