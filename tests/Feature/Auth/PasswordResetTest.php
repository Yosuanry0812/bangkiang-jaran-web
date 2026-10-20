<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertSessionHasNoErrors()->assertRedirect('/login');
            $this->assertTrue(Hash::check('password', $user->fresh()->password));
            $this->assertDatabaseMissing('password_resets', ['email' => $user->email]);
            $this->post('/reset-password', [
                'token' => $notification->token, 'email' => $user->email,
                'password' => 'newpassword', 'password_confirmation' => 'newpassword',
            ])->assertSessionHasErrors('email');
            $this->assertTrue(Hash::check('password', $user->fresh()->password));

            return true;
        });
    }
    public function test_unknown_email_gets_the_same_response_as_a_registered_email(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $this->post('/forgot-password', ['email' => $user->email])->assertSessionHas('status', 'Jika email terdaftar, link reset password akan dikirim. Periksa juga folder spam.');
        $this->post('/forgot-password', ['email' => 'unknown@example.com'])->assertSessionHas('status', 'Jika email terdaftar, link reset password akan dikirim. Periksa juga folder spam.')->assertSessionHasNoErrors();
    }

    public function test_expired_token_cannot_reset_password(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $oldHash = $user->password;
        $this->post('/forgot-password', ['email' => $user->email]);
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user, $oldHash) {
            $this->travel(61)->minutes();
            $this->post('/reset-password', [
                'token' => $notification->token, 'email' => $user->email,
                'password' => 'newpassword', 'password_confirmation' => 'newpassword',
            ])->assertSessionHasErrors('email');
            $this->assertSame($oldHash, $user->fresh()->password);
            return true;
        });
    }

    public function test_mail_failure_shows_a_usable_error(): void
    {
        Password::shouldReceive('sendResetLink')->once()->andThrow(new \RuntimeException('transport failed'));
        $this->from('/forgot-password')->post('/forgot-password', ['email' => 'user@example.com'])
            ->assertRedirect('/forgot-password')->assertSessionHasErrors('email');
    }
    public function test_reset_requires_matching_password_confirmation(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $oldHash = $user->password;
        $this->post('/forgot-password', ['email' => $user->email]);
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user, $oldHash) {
            $this->post('/reset-password', [
                'token' => $notification->token, 'email' => $user->email,
                'password' => 'newpassword', 'password_confirmation' => 'different',
            ])->assertSessionHasErrors('password');
            $this->assertSame($oldHash, $user->fresh()->password);
            return true;
        });
    }

    public function test_reset_link_requests_are_rate_limited(): void
    {
        Notification::fake();
        for ($i = 0; $i < 5; $i++) {
            $this->post('/forgot-password', ['email' => 'unknown@example.com'])->assertRedirect();
        }
        $this->post('/forgot-password', ['email' => 'unknown@example.com'])->assertStatus(429);
    }
}
