<?php

namespace Tests\Feature\Auth;

use App\Http\Middleware\EnsureExplicitLogin;
use App\Mail\RegistrationOtpMail;
use App\Models\User;
use App\Services\RegistrationOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    private function registration(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test User', 'username' => 'testuser', 'email' => 'test@example.com',
            'password' => 'Password123', 'password_confirmation' => 'Password123',
        ], $overrides);
    }

    private function code(): string
    {
        return Mail::sent(RegistrationOtpMail::class)->last()->code;
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get('/register')->assertOk()->assertSee('Kirim Kode Verifikasi');
    }

    public function test_registration_sends_otp_without_creating_or_logging_in_user(): void
    {
        $this->post('/register', $this->registration())->assertRedirect(route('registration.otp'));
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
        Mail::assertSent(RegistrationOtpMail::class, fn ($mail) => $mail->hasTo('test@example.com') && preg_match('/^[0-9]{6}$/', $mail->code));
        $pending = app(RegistrationOtp::class)->pending(request());
        $this->assertTrue(Hash::check('Password123', $pending['password']));
        $this->assertTrue(Hash::check($this->code(), $pending['code_hash']));
        $cached = Cache::get('registration:'.session(RegistrationOtp::SESSION_KEY));
        $this->assertStringNotContainsString('test@example.com', $cached);
        $this->assertStringNotContainsString('Password123', $cached);
        $this->get('/register/otp')->assertOk()->assertSee('test@example.com')->assertDontSee($this->code());
        $this->get('/wisatawan/pemesanan')->assertRedirect('/login');
    }

    public function test_correct_otp_creates_verified_email_account_once_and_ignores_spoofed_methods(): void
    {
        $this->post('/register', $this->registration([
            'role' => 'pengelola', 'registration_method' => 'google', 'email_verification_method' => 'google',
        ]));
        $code = $this->code();
        $id = session(RegistrationOtp::SESSION_KEY);
        $this->post('/register/otp', ['otp' => $code])->assertRedirect(route('redirect.after.login'));
        $user = User::firstOrFail();
        $this->assertTrue(Hash::check('Password123', $user->password));
        $this->assertSame('testuser', $user->username);
        $this->assertSame('wisatawan', $user->role);
        $this->assertSame('email', $user->registration_method);
        $this->assertSame('otp', $user->email_verification_method);
        $this->assertNotNull($user->email_verified_at);
        $this->assertAuthenticatedAs($user);
        $this->assertSame((string) $user->id, session(EnsureExplicitLogin::SESSION_KEY));
        $this->assertNull(Cache::get('registration:'.$id));
        $this->post('/logout');
        Auth::forgetGuards();
        $this->withSession([RegistrationOtp::SESSION_KEY => $id])->from('/register/otp')
            ->post('/register/otp', ['otp' => $code])->assertSessionHasErrors('otp');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_wrong_or_malformed_codes_are_rejected_and_never_flashed(): void
    {
        $this->post('/register', $this->registration());
        $wrong = $this->code() === '111111' ? '222222' : '111111';
        foreach (['12345', 'abcdef', '1234567', $wrong] as $code) {
            $this->from('/register/otp')->post('/register/otp', ['otp' => $code])
                ->assertSessionHasErrors('otp')->assertSessionMissing('_old_input.otp');
        }
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_expired_code_is_rejected_and_new_code_can_be_requested(): void
    {
        $this->post('/register', $this->registration());
        $old = $this->code();
        $this->travel(10)->minutes();
        $this->from('/register/otp')->post('/register/otp', ['otp' => $old])->assertSessionHasErrors('otp');
        $this->post('/register/otp/resend')->assertSessionHas('status', 'otp-sent');
        $this->post('/register/otp', ['otp' => $this->code()])->assertSessionHasNoErrors();
        $this->assertAuthenticated();
    }

    public function test_resend_is_throttled_and_replaces_old_code(): void
    {
        $this->post('/register', $this->registration());
        $old = $this->code();
        $this->from('/register/otp')->post('/register/otp/resend')->assertSessionHasErrors('otp');
        Mail::assertSentCount(1);
        $this->travel(61)->seconds();
        $this->post('/register/otp/resend')->assertSessionHas('status', 'otp-sent');
        Mail::assertSentCount(2);
        if ($old !== $this->code()) {
            $this->from('/register/otp')->post('/register/otp', ['otp' => $old])->assertSessionHasErrors('otp');
        }
        $this->post('/register/otp', ['otp' => $this->code()])->assertSessionHasNoErrors();
    }

    public function test_five_wrong_attempts_block_correct_code_even_after_resend_and_restart(): void
    {
        $this->post('/register', $this->registration());
        $wrong = $this->code() === '111111' ? '222222' : '111111';
        for ($i = 0; $i < 5; $i++) {
            $this->from('/register/otp')->post('/register/otp', ['otp' => $wrong])->assertSessionHasErrors('otp');
        }
        $this->travel(61)->seconds();
        $this->post('/register/otp/resend');
        $this->post('/register/otp', ['otp' => $this->code()])->assertSessionHasErrors('otp');
        $this->travel(61)->seconds();
        $this->post('/register', $this->registration());
        $this->post('/register/otp', ['otp' => $this->code()])->assertSessionHasErrors('otp');
        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_challenge_is_bound_to_session_and_expires_after_thirty_minutes(): void
    {
        $this->get('/register/otp')->assertRedirect('/register');
        $this->post('/register', $this->registration());
        $code = $this->code();
        $id = session(RegistrationOtp::SESSION_KEY);
        $this->withSession([RegistrationOtp::SESSION_KEY => null])->from('/register/otp')
            ->post('/register/otp', ['otp' => $code])->assertSessionHasErrors('otp');
        $this->travel(31)->minutes();
        $this->withSession([RegistrationOtp::SESSION_KEY => $id])->get('/register/otp')->assertRedirect('/register');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_cancel_discards_pending_data_and_preserves_booking(): void
    {
        $this->withSession(['url.intended' => '/wisatawan/pemesanan?id_tiket=1'])->post('/register', $this->registration());
        $id = session(RegistrationOtp::SESSION_KEY);
        $this->post('/register/otp/cancel')->assertRedirect('/register')
            ->assertSessionHas('url.intended', '/wisatawan/pemesanan?id_tiket=1');
        $this->assertNull(Cache::get('registration:'.$id));
        $this->assertDatabaseCount('users', 0);
    }

    public function test_uniqueness_is_rechecked_before_creating_verified_account(): void
    {
        $this->post('/register', $this->registration());
        User::factory()->create(['email' => 'test@example.com']);
        $this->from('/register/otp')->post('/register/otp', ['otp' => $this->code()])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_log_mailer_is_rejected_without_sending_or_creating_user(): void
    {
        config(['mail.default' => 'log']);
        $this->post('/register', $this->registration())->assertRedirect(route('registration.otp'))->assertSessionHasErrors('otp');
        Mail::assertNothingSent();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_delivery_failure_leaves_registration_retryable_without_creating_user(): void
    {
        Mail::shouldReceive('to')->once()->with('test@example.com')->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new \RuntimeException('Transport unavailable'));
        $this->post('/register', $this->registration())->assertRedirect(route('registration.otp'))->assertSessionHasErrors('otp');
        $this->assertNull(app(RegistrationOtp::class)->pending(request())['code_hash']);
        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_registration_rejects_mismatched_confirmation_and_duplicate_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);
        $this->post('/register', $this->registration([
            'email' => 'existing@example.com', 'password_confirmation' => 'different',
        ]))->assertSessionHasErrors(['email', 'password']);
        Mail::assertNothingSent();
        $this->assertDatabaseCount('users', 1);
        $this->assertGuest();
    }

    public function test_failed_resend_keeps_previous_code_valid(): void
    {
        $this->post('/register', $this->registration());
        $old = $this->code();
        $this->travel(61)->seconds();
        Mail::shouldReceive('to')->once()->with('test@example.com')->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new \RuntimeException('SMTP unavailable'));
        $this->from('/register/otp')->post('/register/otp/resend')->assertSessionHasErrors('otp');
        $this->post('/register/otp', ['otp' => $old])->assertSessionHasNoErrors();
        $this->assertAuthenticated();
    }

    public function test_hourly_email_send_limit_survives_restarting_registration(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/register', $this->registration())->assertSessionHas('status', 'otp-sent');
            $this->travel(61)->seconds();
        }
        $this->post('/register', $this->registration())->assertSessionHasErrors('otp');
        Mail::assertSentCount(5);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_resend_near_pending_expiry_still_grants_full_ten_minute_code_lifetime(): void
    {
        $this->post('/register', $this->registration());
        $this->travel(29)->minutes();
        $this->post('/register/otp/resend')->assertSessionHas('status', 'otp-sent');
        $this->travel(9)->minutes();
        $this->post('/register/otp', ['otp' => $this->code()])->assertSessionHasNoErrors();
        $this->assertAuthenticated();
    }

    public function test_registration_returns_to_booking_after_verification(): void
    {
        $bookingUrl = route('wisatawan.pemesanan.create', ['id_tiket' => 1]);
        $this->withSession(['url.intended' => $bookingUrl])->post('/register', $this->registration())
            ->assertRedirect(route('registration.otp'));
        $this->post('/register/otp', ['otp' => $this->code()])->assertRedirect(route('redirect.after.login'));
        Auth::forgetGuards();
        $this->get('/redirect-after-login')->assertRedirect($bookingUrl);
        $this->assertAuthenticated();
    }
}
