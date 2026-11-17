<?php

namespace Tests\Feature\Auth;

use App\Http\Middleware\EnsureExplicitLogin;
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

    public function test_new_google_user_is_redirected_to_username_creation_and_sets_username_manually(): void
    {
        $this->googleUser('baru@example.com', 'google-123');

        // Pendaftaran pertama kali via Google diarahkan ke halaman pembuatan username sendiri
        $response = $this->get('/auth/google/callback');
        $response->assertRedirect(route('auth.google.username'));

        // Akun belum disimpan di DB sampai username ditentukan oleh pengguna
        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();

        // Buka halaman form pembuatan username
        $this->get(route('auth.google.username'))
            ->assertOk()
            ->assertSee('Buat Username Anda')
            ->assertSee('baru@example.com')
            ->assertSee('Wisatawan');

        // Pengguna membuat username sendiri secara mandiri
        $storeResponse = $this->post(route('auth.google.username.store'), [
            'username' => 'petualang_bali',
        ]);

        $storeResponse->assertRedirect(route('landing'));

        $user = User::where('google_id', 'google-123')->firstOrFail();
        $this->assertSame('baru@example.com', $user->email);
        $this->assertSame('petualang_bali', $user->username);
        $this->assertNull($user->password);
        $this->assertNotNull($user->email_verified_at);
        $this->assertSame('wisatawan', $user->role);
        $this->assertSame('google', $user->registration_method);
        $this->assertSame('google', $user->email_verification_method);
        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, $user->login_count);
        $this->assertSame((string) $user->id, session(EnsureExplicitLogin::SESSION_KEY));
    }

    public function test_existing_google_user_logs_in_directly_to_landing_without_username_or_profile_page(): void
    {
        $existing = User::factory()->create([
            'google_id' => 'google-999',
            'email' => 'lama@example.com',
            'username' => 'wisatawan_setia',
            'role' => 'wisatawan',
            'login_count' => 1,
        ]);

        $this->googleUser('lama@example.com', 'google-999');

        // Akun lama langsung masuk ke beranda, tidak ke halaman setting profil atau halaman username
        $response = $this->get('/auth/google/callback');
        $response->assertRedirect(route('landing'));

        $this->assertAuthenticatedAs($existing);
        $this->assertSame(2, $existing->fresh()->login_count);
        $this->assertSame('wisatawan_setia', $existing->fresh()->username);
    }

    public function test_unverified_google_email_is_rejected(): void
    {
        $this->googleUser('google@example.com', 'google-123', false);
        $this->get('/auth/google/callback')->assertRedirect('/login')->assertSessionHas('error');
        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_new_google_user_returns_to_booking_after_submitting_username(): void
    {
        $bookingUrl = route('wisatawan.pemesanan.create', ['id_tiket' => 1]);
        $this->googleUser('booking@example.com', 'google-book');

        $this->withSession(['url.intended' => $bookingUrl])
            ->get('/auth/google/callback')
            ->assertRedirect(route('auth.google.username'));

        $this->post(route('auth.google.username.store'), [
            'username' => 'liburan_seru',
        ])->assertRedirect($bookingUrl);

        $user = User::where('google_id', 'google-book')->firstOrFail();
        $this->assertSame('liburan_seru', $user->username);
        $this->assertSame('wisatawan', $user->role);
        $this->assertAuthenticated();
    }

    public function test_username_validation_rejects_duplicate_and_invalid_formats(): void
    {
        User::factory()->create(['username' => 'sudah_ada']);

        $this->googleUser('validasi@example.com', 'google-val');
        $this->get('/auth/google/callback');

        // Duplikat username
        $this->post(route('auth.google.username.store'), ['username' => 'sudah_ada'])
            ->assertSessionHasErrors('username');

        // Terlalu pendek (< 3 karakter)
        $this->post(route('auth.google.username.store'), ['username' => 'ab'])
            ->assertSessionHasErrors('username');

        // Karakter tidak diizinkan
        $this->post(route('auth.google.username.store'), ['username' => 'user name!#'])
            ->assertSessionHasErrors('username');

        $this->assertDatabaseCount('users', 1);
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
