<?php

namespace Tests\Feature\Pengelola;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_page_lists_logs_ordered_by_primary_key(): void
    {
        $manager = User::factory()->create(['role' => 'pengelola']);
        ActivityLog::create(['id_user' => $manager->id, 'aktivitas' => 'login_pertama']);
        ActivityLog::create(['id_user' => $manager->id, 'aktivitas' => 'login_kedua']);

        $this->actingAs($manager)->get('/pengelola/aktivitas')
            ->assertOk()
            ->assertSeeInOrder(['login_kedua', 'login_pertama']);
    }
}
