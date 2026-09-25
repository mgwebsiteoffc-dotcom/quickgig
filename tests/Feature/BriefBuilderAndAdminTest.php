<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Blog;
use App\Models\Creator;
use App\Models\Faq;
use App\Models\Service;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\RefreshesDatabase;
use Tests\TestCase;

class BriefBuilderAndAdminTest extends TestCase
{
    use RefreshesDatabase;

    private function staff(string $role = 'super_admin'): User
    {
        return User::create([
            'name' => ucfirst($role), 'email' => $role . uniqid() . '@example.com',
            'password' => Hash::make('secret123'), 'role' => $role, 'is_active' => true,
        ]);
    }

    private function gig(): Service
    {
        $creator = Creator::create([
            'name' => 'Priya', 'handle' => '@priya' . uniqid(), 'email' => 'priya' . uniqid() . '@example.com',
            'profile_type' => 'video_editor', 'is_available' => true, 'is_verified' => true,
        ]);

        return Service::create([
            'creator_id' => $creator->id, 'title' => 'Launch reel', 'slug' => 'reel-' . uniqid(),
            'price' => 2000, 'delivery_days' => 1, 'category' => 'Reel', 'is_active' => true, 'price_type' => 'paid',
        ]);
    }

    /* ── brief builder (public, no signup) ── */

    public function test_the_builder_page_is_public(): void
    {
        $this->get('/brief-builder')->assertOk();
    }

    public function test_a_brief_is_generated_from_one_line(): void
    {
        $this->gig();

        $this->post('/brief-builder', [
            'idea'     => 'Launch film for a protein bar with the founder on camera',
            'format'   => array_key_first(\App\Services\BriefComposer::FORMATS),
            'goal'     => array_key_first(\App\Services\BriefComposer::GOALS),
            'tone'     => array_key_first(\App\Services\BriefComposer::TONES),
            'urgency'  => 'standard',
        ])->assertRedirect();

        $this->assertNotEmpty(session('brief.state')['brief'] ?? null, 'a brief should be stored in the session');
    }

    public function test_the_builder_rejects_a_one_word_idea(): void
    {
        $this->post('/brief-builder', [
            'idea'    => 'reel',
            'format'  => array_key_first(\App\Services\BriefComposer::FORMATS),
            'goal'    => array_key_first(\App\Services\BriefComposer::GOALS),
            'tone'    => array_key_first(\App\Services\BriefComposer::TONES),
            'urgency' => 'standard',
        ])->assertSessionHasErrors('idea');
    }

    public function test_the_builder_rejects_an_unknown_format(): void
    {
        $this->post('/brief-builder', [
            'idea'    => 'Launch film for a protein bar with the founder on camera',
            'format'  => 'interpretive-dance',
            'goal'    => array_key_first(\App\Services\BriefComposer::GOALS),
            'tone'    => array_key_first(\App\Services\BriefComposer::TONES),
            'urgency' => 'standard',
        ])->assertSessionHasErrors('format');
    }

    public function test_the_builder_can_be_reset(): void
    {
        $this->gig();

        $this->post('/brief-builder', [
            'idea'    => 'Launch film for a protein bar with the founder on camera',
            'format'  => array_key_first(\App\Services\BriefComposer::FORMATS),
            'goal'    => array_key_first(\App\Services\BriefComposer::GOALS),
            'tone'    => array_key_first(\App\Services\BriefComposer::TONES),
            'urgency' => 'standard',
        ]);

        $this->post('/brief-builder/reset')->assertRedirect();

        $this->assertNull(session('brief.state'));
    }

    /* ── admin screens ── */

    public function test_the_admin_area_is_closed_to_guests_and_to_buyers(): void
    {
        $this->get('/admin')->assertRedirect('/login');

        $buyer = User::create([
            'name' => 'Buyer', 'email' => 'buyer' . uniqid() . '@example.com',
            'password' => Hash::make('secret123'), 'role' => 'business', 'is_active' => true,
        ]);

        $this->actingAs($buyer)->get('/admin')->assertForbidden();
    }

    public function test_every_admin_screen_renders_for_a_super_admin(): void
    {
        $this->gig();

        $this->actingAs($this->staff());

        foreach ([
            '/admin', '/admin/orders', '/admin/creators', '/admin/companies', '/admin/leads',
            '/admin/services', '/admin/blogs', '/admin/faqs', '/admin/skills',
            '/admin/users', '/admin/payouts', '/admin/settings',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_finance_cannot_reach_the_skills_screen_but_a_manager_can(): void
    {
        $this->actingAs($this->staff('finance'))->get('/admin/skills')->assertForbidden();
        $this->actingAs($this->staff('manager'))->get('/admin/skills')->assertOk();
    }

    public function test_only_a_super_admin_opens_settings(): void
    {
        $this->actingAs($this->staff('manager'))->get('/admin/settings')->assertForbidden();
        $this->actingAs($this->staff('super_admin'))->get('/admin/settings')->assertOk();
    }

    public function test_a_skill_can_be_created_and_toggled(): void
    {
        $admin = $this->staff();

        $this->actingAs($admin)->post('/admin/skills', [
            'name' => 'Colour grading', 'discipline' => 'Video',
        ])->assertRedirect();

        $skill = Skill::where('name', 'Colour grading')->firstOrFail();
        $this->assertTrue((bool) $skill->is_active);

        $this->actingAs($admin)->post("/admin/skills/{$skill->id}/toggle");
        $this->assertFalse((bool) $skill->fresh()->is_active);
    }

    public function test_an_faq_can_be_created_and_deleted(): void
    {
        $admin = $this->staff();

        $this->actingAs($admin)->post('/admin/faqs', [
            'question' => 'How fast is delivery?', 'answer' => 'Most gigs land within 24 hours.',
        ])->assertRedirect();

        $faq = Faq::where('question', 'How fast is delivery?')->firstOrFail();

        $this->actingAs($admin)->delete("/admin/faqs/{$faq->id}");

        $this->assertNull(Faq::find($faq->id));
    }

    public function test_a_gig_can_be_toggled_off_from_admin(): void
    {
        $gig   = $this->gig();
        $admin = $this->staff();

        $this->actingAs($admin)->post("/admin/services/{$gig->id}/toggle");

        $this->assertFalse((bool) $gig->fresh()->is_active);
    }

    public function test_a_support_user_cannot_delete_a_gig(): void
    {
        $gig = $this->gig();

        $this->actingAs($this->staff('support'))->delete("/admin/services/{$gig->id}")->assertForbidden();

        $this->assertNotNull(Service::find($gig->id));
    }

    /* ── audit trail ── */

    public function test_admin_writes_are_recorded_in_the_audit_log(): void
    {
        $admin = $this->staff();

        $this->actingAs($admin)->post('/admin/skills', ['name' => 'Sound design', 'discipline' => 'Audio']);

        $entry = AuditLog::latest('id')->first();

        $this->assertNotNull($entry);
        $this->assertSame($admin->id, $entry->user_id);
        $this->assertSame('admin.skills.store', $entry->route);
        $this->assertSame('Sound design', $entry->payload['name']);
    }

    public function test_reads_are_not_audited(): void
    {
        $this->actingAs($this->staff())->get('/admin/skills');

        $this->assertSame(0, AuditLog::count());
    }

    public function test_secrets_are_masked_before_they_reach_the_audit_table(): void
    {
        $this->actingAs($this->staff())->post('/admin/settings', [
            'platform_fee_percent'       => 10,
            'payments_razorpay_key_secret' => 'rzp_secret_value',
        ]);

        $entry = AuditLog::latest('id')->first();

        $this->assertNotNull($entry);
        $this->assertNotContains('rzp_secret_value', array_values($entry->payload));
    }
}
