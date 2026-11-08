<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_protected_response_is_not_cacheable(): void
    {
        $user = $this->createUser(UserRole::Pelanggan);

        $response = $this->actingAs($user)->get(route('home'));

        $this->assertStringContainsString(
            'no-store',
            (string) $response->headers->get('Cache-Control')
        );
        $response->assertHeader('Pragma', 'no-cache');
    }

    public function test_authenticated_admin_is_redirected_from_login_to_admin(): void
    {
        $user = $this->createUser(UserRole::Admin);

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('admin'));
    }

    public function test_authenticated_customer_is_redirected_from_login_to_booking(): void
    {
        $user = $this->createUser(UserRole::Pelanggan);

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('booking-saya'));
    }

    public function test_logged_out_customer_is_redirected_to_welcome_from_dashboard(): void
    {
        $user = $this->createUser(UserRole::Pelanggan);

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('welcome'));

        $this->get(route('booking-saya'))
            ->assertRedirect(route('login'));
    }

    private function createUser(UserRole $role): User
    {
        return User::create([
            'name' => 'Session Test User',
            'no_hp' => '0812'.fake()->unique()->numerify('########'),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => $role,
            'aktif' => true,
        ]);
    }
}
