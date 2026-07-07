<?php

namespace Tests\Feature;

use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_admin_area(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_can_approve_a_pending_user_and_action_is_logged(): void
    {
        $admin = User::factory()->admin()->create();
        $pending = User::factory()->pending()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.approve', $pending))
            ->assertRedirect();

        $this->assertSame(User::STATUS_APPROVED, $pending->refresh()->status);
        $this->assertNotNull($pending->approved_at);
        $this->assertDatabaseHas('admin_activity_logs', [
            'admin_id' => $admin->id,
            'target_user_id' => $pending->id,
            'action' => 'approved',
        ]);
    }

    public function test_admin_cannot_suspend_another_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $other = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.suspend', $other))
            ->assertForbidden();

        $this->assertSame(User::STATUS_APPROVED, $other->refresh()->status);
    }
}
