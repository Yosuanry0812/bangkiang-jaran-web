<?php

namespace Tests\Feature\Pengelola;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_visit_and_transaction_reports_export_as_pdf(): void
    {
        $manager = User::factory()->create(['role' => 'pengelola']);

        foreach (['kunjungan', 'transaksi'] as $report) {
            $response = $this->actingAs($manager)->get(route('pengelola.laporan.'.$report, [
                'periode_awal' => '2026-10-01',
                'periode_akhir' => '2026-10-31',
                'export' => 'pdf',
            ]));

            $response->assertOk();
            $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
            $this->assertStringStartsWith('%PDF-', $response->getContent());
        }
    }
}
