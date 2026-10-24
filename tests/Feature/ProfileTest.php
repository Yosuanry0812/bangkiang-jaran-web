<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_manager_profile_uses_management_navigation_and_keeps_update_fields(): void
    {
        $manager = User::factory()->create(['role' => 'pengelola']);

        $this->actingAs($manager)->get('/profile')
            ->assertOk()
            ->assertSee('admin-profile-form', false)
            ->assertSee('Navigasi pengelola')
            ->assertSee('name="name"', false)
            ->assertSee('name="username"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="phone"', false)
            ->assertDontSee('id="main-nav"', false);

        $this->actingAs(User::factory()->create(['role' => 'wisatawan']))->get('/profile')
            ->assertOk()
            ->assertSee('id="main-nav"', false)
            ->assertDontSee('admin-profile-form', false);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_manager_can_save_profile_without_optional_phone(): void
    {
        $manager = User::factory()->create(['role' => 'pengelola', 'phone' => null]);

        $this->actingAs($manager)->patch('/profile', [
            'name' => 'Pengelola QA',
            'username' => $manager->username,
            'email' => $manager->email,
            'phone' => '',
        ])->assertSessionHasNoErrors()->assertRedirect('/profile');

        $this->assertSame('Pengelola QA', $manager->fresh()->name);
        $this->assertNull($manager->fresh()->phone);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
