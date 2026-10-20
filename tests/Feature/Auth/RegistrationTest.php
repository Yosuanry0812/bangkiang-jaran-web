<?php

namespace Tests\Feature\Auth;

use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'phone' => '081234567890',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $user = User::where('email', 'test@example.com')->firstOrFail();
        $this->assertSame('Test User', $user->name);
        $this->assertSame('testuser', $user->username);
        $this->assertTrue(Hash::check('Password123', $user->password));
        $this->assertNull($user->email_verified_at);
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
    public function test_registration_rejects_mismatched_confirmation_and_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);
        $this->post('/register', [
            'name' => 'Test User', 'username' => 'newuser', 'email' => 'existing@example.com',
            'password' => 'Password123', 'password_confirmation' => 'different',
        ])->assertSessionHasErrors(['email', 'password']);
        $this->assertDatabaseCount('users', 1);
        $this->assertGuest();
    }
}
