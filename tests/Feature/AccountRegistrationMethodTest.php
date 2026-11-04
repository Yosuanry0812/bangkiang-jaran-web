<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountRegistrationMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_migration_classifies_old_accounts_without_inventing_otp_verification(): void
    {
        $migration = require database_path('migrations/2026_10_28_000000_add_registration_methods_to_users_table.php');
        $migration->down();
        $google = User::factory()->create(['google_id' => 'legacy-google']);
        $googleUnverified = User::factory()->unverified()->create(['google_id' => 'legacy-unverified']);
        $email = User::factory()->unverified()->create();
        $emailVerified = User::factory()->create();
        $migration->up();

        $this->assertSame('google', $google->fresh()->registration_method);
        $this->assertSame('google', $google->fresh()->email_verification_method);
        $this->assertSame('google', $googleUnverified->fresh()->registration_method);
        $this->assertNull($googleUnverified->fresh()->email_verification_method);
        $this->assertNull($googleUnverified->fresh()->email_verified_at);
        $this->assertSame('email', $email->fresh()->registration_method);
        $this->assertNull($email->fresh()->email_verification_method);
        $this->assertNull($email->fresh()->email_verified_at);
        $this->assertNull($emailVerified->fresh()->email_verification_method);
        $this->assertNotNull($emailVerified->fresh()->email_verified_at);
    }

    public function test_management_lists_registration_origin_and_real_verification_status(): void
    {
        $manager = User::factory()->create(['role' => 'pengelola']);
        User::factory()->create(['registration_method' => 'google', 'email_verification_method' => 'google']);
        User::factory()->create(['registration_method' => 'email', 'email_verification_method' => 'otp']);
        User::factory()->unverified()->create();
        $this->actingAs($manager)->get(route('pengelola.user.index'))->assertOk()
            ->assertSee('Pendaftaran')->assertSee('Verifikasi email')->assertSee('Google')
            ->assertSee('OTP email')->assertSee('Belum diverifikasi');
    }
}
