<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_register_as_pending_and_see_the_waiting_screen(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('status.pending'));

        $user = User::where('email', 'test@example.com')->first();
        $this->assertSame(User::STATUS_PENDING, $user->status);
        $this->assertSame(User::ROLE_USER, $user->role);
        // Every new user gets a profile row up front.
        $this->assertNotNull($user->profile);
    }

    public function test_pending_users_cannot_reach_the_dashboard(): void
    {
        $user = User::factory()->pending()->create();

        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('status.pending'));
    }
}
