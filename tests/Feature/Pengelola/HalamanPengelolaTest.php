<?php

namespace Tests\Feature\Pengelola;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HalamanPengelolaTest extends TestCase
{
    use RefreshDatabase;

    public function test_main_management_pages_render_without_seed_data(): void
    {
        $manager = User::factory()->create(['role' => 'pengelola']);

        foreach ([
            'pengelola.dashboard',
            'pengelola.galeri.index',
            'pengelola.galeri.create',
            'pengelola.tiket.index',
            'pengelola.tiket.create',
            'pengelola.verifikasi.index',
            'pengelola.laporan.index',
            'pengelola.user.index',
            'pengelola.scan.index',
            'pengelola.pemesanan-offline.create',
            'pengelola.pemesanan-offline.riwayat',
        ] as $route) {
            $this->actingAs($manager)->get(route($route))->assertOk();
        }
    }
}
