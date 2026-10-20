<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;
use Mockery;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    private function googleUser(string $email, string $id = 'google-123', bool $verified = true): void
    {
        $google = (new GoogleUser)->setRaw(['email_verified' => $verified])->map([
            'id' => $id, 'email' => $email, 'name' => 'Google User',
        ]);
        $provider = Mockery::mock();
        $provider->shouldReceive('user')->once()->andReturn($google);
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);
    }

    public function test_verified_google_user_is_saved_and_can_return(): void
    {
        $this->googleUser('google@example.com');
        $this->get('/auth/google/callback')->assertRedirect();
        $user = User::where('google_id', 'google-123')->firstOrFail();
        $this->assertSame('google@example.com', $user->email);
        $this->assertNull($user->password);
        $this->assertNotNull($user->email_verified_at);
        $this->assertSame('wisatawan', $user->role);
        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, $user->login_count);

        $this->googleUser('google@example.com');
        $this->get('/auth/google/callback')->assertRedirect();
        $this->assertDatabaseCount('users', 1);
        $this->assertSame(2, $user->fresh()->login_count);
    }

    public function test_unverified_google_email_is_rejected(): void
    {
        $this->googleUser('google@example.com', 'google-123', false);
        $this->get('/auth/google/callback')->assertRedirect('/login')->assertSessionHas('error');
        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_existing_manual_account_is_not_automatically_linked(): void
    {
        $user = User::factory()->create(['email' => 'existing@example.com']);
        $this->googleUser($user->email);
        $this->get('/auth/google/callback')->assertRedirect('/login')->assertSessionHas('error');
        $this->assertNull($user->fresh()->google_id);
        $this->assertGuest();
    }

    public function test_callback_without_oauth_state_is_rejected(): void
    {
        config(['services.google.client_id' => 'test', 'services.google.client_secret' => 'test']);
        $this->get('/auth/google/callback')->assertRedirect('/login')->assertSessionHas('error');
        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }
}
