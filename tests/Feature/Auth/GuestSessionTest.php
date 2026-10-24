<?php

namespace Tests\Feature\Auth;

use App\Http\Middleware\EnsureExplicitLogin;
use App\Models\Tiket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class GuestSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_visit_remains_guest_on_public_pages(): void
    {
        User::factory()->create(['role' => 'pengelola']);
        $ticket = $this->ticket();

        foreach (['/', '/restoran', '/tiket', '/tiket/'.$ticket->id_tiket] as $url) {
            $this->get($url)->assertOk()
                ->assertSee(route('login'), false)
                ->assertDontSee('id="nav-logout"', false);
            $this->assertGuest();
        }
    }

    public function test_login_offers_guest_mode_without_persistent_login(): void
    {
        $response = $this->get('/login')->assertOk()
            ->assertSee('Lanjutkan sebagai tamu')
            ->assertSee(route('landing'), false)
            ->assertDontSee('name="remember"', false);

        $this->assertTrue(config('session.expire_on_close'));
        $this->assertSame(0, $response->getCookie(config('session.cookie'))->getExpiresTime());
        $this->assertTrue($response->headers->hasCacheControlDirective('no-store'));
        $this->assertGuest();
    }

    public function test_legacy_sessions_for_both_roles_become_guest_without_clearing_browser_data(): void
    {
        foreach (['pengelola', 'wisatawan'] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->flushSession();
            Auth::forgetGuards();
            $this->withSession([Auth::guard('web')->getName() => $user->id, 'locale' => 'id']);

            $this->get('/tiket')->assertOk()
                ->assertSessionMissing(EnsureExplicitLogin::SESSION_KEY)
                ->assertSessionHas('locale', 'id')
                ->assertDontSee('id="nav-logout"', false);
            $this->assertGuest();
        }
    }

    public function test_session_marker_for_another_user_cannot_authorize_a_stored_session(): void
    {
        $user = User::factory()->create(['role' => 'pengelola']);
        $this->withSession([
            Auth::guard('web')->getName() => $user->id,
            EnsureExplicitLogin::SESSION_KEY => (string) ($user->id + 1),
        ])->get('/pengelola/dashboard')->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_legacy_remember_cookie_cannot_automatically_log_in(): void
    {
        $user = User::factory()->create(['role' => 'pengelola', 'remember_token' => Str::random(60)]);
        $cookieName = Auth::guard('web')->getRecallerName();
        $cookie = implode('|', [$user->id, $user->getRememberToken(), $user->getAuthPassword()]);

        $this->withCookie($cookieName, $cookie)->get('/tiket')
            ->assertOk()->assertCookieExpired($cookieName)
            ->assertSessionMissing(EnsureExplicitLogin::SESSION_KEY);
        $this->assertGuest();
    }

    public function test_explicit_login_survives_subsequent_requests_but_ignores_remember_input(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);

        $this->post('/login', ['login' => $user->email, 'password' => 'password', 'remember' => 'on'])
            ->assertRedirect()
            ->assertSessionHas(EnsureExplicitLogin::SESSION_KEY, (string) $user->id)
            ->assertCookieMissing(Auth::guard('web')->getRecallerName());

        Auth::forgetGuards();
        $this->get('/tiket')->assertOk()->assertSee('id="nav-logout"', false);
        $this->assertAuthenticatedAs($user);
    }

    public function test_explicit_manager_login_and_public_or_guest_urls_lead_to_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'pengelola']);
        $ticket = $this->ticket();

        $this->post('/login', ['login' => $user->username, 'password' => 'password'])
            ->assertRedirect(route('pengelola.dashboard'));
        Auth::forgetGuards();

        foreach (['/', '/restoran', '/informasi', '/tiket', '/tiket/'.$ticket->id_tiket, '/login', '/register'] as $url) {
            $this->get($url)->assertRedirect(route('pengelola.dashboard'));
        }

        $this->get('/pengelola/dashboard')->assertOk();
        $this->get('/wisatawan/pemesanan')->assertForbidden();
        $this->assertAuthenticatedAs($user);
    }

    public function test_guest_booking_requires_login_and_keeps_selected_ticket(): void
    {
        $ticket = $this->ticket();
        $bookingUrl = route('wisatawan.pemesanan.create', ['id_tiket' => $ticket->id_tiket]);
        $user = User::factory()->create(['role' => 'wisatawan']);

        $this->get('/tiket')->assertOk()->assertSee($bookingUrl, false);
        $this->get('/tiket/'.$ticket->id_tiket)->assertOk()->assertSee($bookingUrl, false);
        $this->get($bookingUrl)->assertRedirect(route('login'))->assertSessionHas('url.intended', $bookingUrl);
        $this->assertGuest();

        $this->post('/login', ['login' => $user->email, 'password' => 'password'])
            ->assertRedirect($bookingUrl);
        Auth::forgetGuards();
        $this->get($bookingUrl)->assertOk()->assertViewHas('selectedId', (string) $ticket->id_tiket);
        $this->get('/pengelola/dashboard')->assertForbidden();
    }

    public function test_guest_cannot_submit_booking_or_access_visitor_and_manager_private_pages(): void
    {
        foreach (['/wisatawan/pemesanan', '/wisatawan/pemesanan/data-diri', '/wisatawan/pemesanan/riwayat', '/wisatawan/pembayaran/1', '/pengelola/dashboard'] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }

        $this->post('/wisatawan/pemesanan', [])->assertRedirect(route('login'));
        $this->postJson('/wisatawan/pemesanan', [])->assertUnauthorized();
        $this->assertDatabaseCount('pemesanan', 0);
        $this->assertGuest();
    }

    public function test_logout_clears_explicit_login_and_booking_becomes_private_again(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $this->post('/login', ['login' => $user->email, 'password' => 'password'])->assertRedirect();

        $this->post('/logout')->assertRedirect('/')
            ->assertSessionMissing(EnsureExplicitLogin::SESSION_KEY);
        Auth::forgetGuards();
        $this->get('/tiket')->assertOk()->assertDontSee('id="nav-logout"', false);
        $this->get('/wisatawan/pemesanan')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    private function ticket(): Tiket
    {
        return Tiket::create(['nama_tiket' => 'Tiket Dewasa', 'harga' => 20000, 'kategori' => 'perorangan', 'status' => 'aktif']);
    }
}
