<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_invalidates_session_and_rotates_remember_token(): void
    {
        $user = User::factory()->create([
            'email' => 'logout@example.com',
            'password' => 'password',
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
            'remember' => true,
        ])->assertRedirect();

        $user->refresh();
        $rememberTokenBeforeLogout = $user->remember_token;

        $this->post(route('logout'))
            ->assertRedirect(route('welcome'));

        $user->refresh();

        $this->assertNotSame($rememberTokenBeforeLogout, $user->remember_token);
        $this->assertGuest();
    }

    public function test_admin_logout_redirects_to_login(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->actingAs($admin)
            ->post(route('logout'))
            ->assertRedirect(route('login'));
    }

    public function test_inactive_user_is_logged_out_with_invalidated_session(): void
    {
        $user = User::factory()->create([
            'aktif' => false,
        ]);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
