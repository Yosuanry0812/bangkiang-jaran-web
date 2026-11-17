<?php

namespace Tests\Feature;

use App\Models\DetailPemesanan;
use App\Models\Pemesanan;
use App\Models\Tiket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationalReportingTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(string $state = 'selesai', string $code = 'BJ-REPORT'): Pemesanan
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $adult = Tiket::firstOrCreate(['nama_tiket' => 'Dewasa'], ['harga' => 15000, 'kategori' => 'perorangan', 'status' => 'aktif']);
        $child = Tiket::firstOrCreate(['nama_tiket' => 'Anak'], ['harga' => 10000, 'kategori' => 'perorangan', 'status' => 'aktif']);
        $booking = Pemesanan::create(['id_user' => $user->id, 'id_tiket' => $adult->id_tiket, 'tgl_kunjungan' => now()->toDateString(),
            'jumlah' => 2, 'total_harga' => 25000, 'kode_booking' => $code, 'status' => $state, 'expires_at' => now()->addHour()]);
        foreach ([$adult, $child] as $index => $ticket) {
            DetailPemesanan::create(['id_pemesanan' => $booking->id_pemesanan, 'id_tiket' => $ticket->id_tiket,
                'kode_tiket' => $code.'-'.$index, 'nama_tiket' => $ticket->nama_tiket, 'nama_pengunjung' => '=SUM(1,2)',
                'harga' => $ticket->harga, 'status_tiket' => 'aktif']);
        }
        if ($state === 'selesai') {
            $booking->pembayaran()->create(['total' => 25000, 'metode' => 'Tunai', 'status' => 'valid', 'tgl_bayar' => now()->toDateString()]);
        }

        return $booking;
    }

    private function reportUrl(string $report, array $extra = []): string
    {
        return route('pengelola.laporan.'.$report, [...['periode_awal' => now()->toDateString(), 'periode_akhir' => now()->toDateString()], ...$extra]);
    }

    public function test_mixed_ticket_chart_counts_details_and_scan_metrics_use_checkin_time(): void
    {
        $booking = $this->fixture();
        $this->fixture('pending', 'BJ-UNPAID');
        $detail = $booking->detailPemesanan->first();
        $detail->update(['status_tiket' => 'digunakan', 'check_in_at' => now()->subDay(), 'check_out_at' => now()]);
        $manager = User::factory()->create(['role' => 'pengelola']);
        $response = $this->actingAs($manager)->get(route('pengelola.dashboard'));
        $response->assertOk()->assertViewHas('pengunjungHariIni', 2)->assertViewHas('terpesanHariIni', 4)
            ->assertViewHas('scanHariIni', 0)->assertViewHas('keluarHariIni', 1)->assertViewHas('diLokasi', 0);
        $chart = array_combine($response->viewData('grafikLabels'), $response->viewData('grafikData'));
        $this->assertSame(['Anak' => 1, 'Dewasa' => 1], $chart);
    }

    public function test_visits_separate_paid_tickets_from_actual_arrivals_and_exclude_pending(): void
    {
        $booking = $this->fixture();
        $this->fixture('pending', 'BJ-PENDING');
        $booking->detailPemesanan->first()->update(['check_in_at' => now(), 'status_tiket' => 'digunakan']);
        $manager = User::factory()->create(['role' => 'pengelola']);
        $this->actingAs($manager)->get($this->reportUrl('kunjungan'))->assertOk()->assertViewHas('total', 1)->assertViewHas('terbayar', 2)->assertViewHas('keluar', 0)->assertDontSee('BJ-PENDING');
        $this->get($this->reportUrl('transaksi'))->assertViewHas('total', fn ($sum) => (int) $sum === 25000);
    }

    public function test_csv_exports_all_filtered_records_and_neutralizes_spreadsheet_formulas(): void
    {
        for ($i = 0; $i < 27; $i++) {
            $this->fixture(code: 'BJ-R-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT));
        }
        $this->fixture(code: 'BJ-OFF-001');
        $manager = User::factory()->create(['role' => 'pengelola']);
        $response = $this->actingAs($manager)->get($this->reportUrl('kunjungan', ['tipe' => 'online', 'export' => 'csv']));
        $response->assertOk()->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $csv = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);
        $this->assertStringContainsString('BJ-R-026', $csv);
        $this->assertStringNotContainsString('BJ-OFF-001', $csv);
        $this->assertStringContainsString("'=SUM(1,2)", $csv);
        $this->assertSame(55, count(array_filter(explode("\n", trim($csv)))));
        $transactions = $this->get($this->reportUrl('transaksi', ['tipe' => 'offline', 'export' => 'csv']))->streamedContent();
        $this->assertStringContainsString('BJ-OFF-001', $transactions);
        $this->assertStringNotContainsString('BJ-R-', $transactions);
    }

    public function test_deleted_account_remains_in_reports_and_management_search_covers_all_channels(): void
    {
        $booking = $this->fixture();
        $offline = $this->fixture(code: 'BJ-OFF-001');
        $manager = User::factory()->create(['role' => 'pengelola']);
        $this->actingAs($booking->user)->delete('/profile', ['password' => 'password'])->assertRedirect('/');
        $this->actingAs($manager)->get($this->reportUrl('transaksi'))->assertViewHas('total', fn ($n) => (int) $n === 50000)->assertSee('Akun dihapus');
        $this->get(route('pengelola.pesanan.index', ['q' => 'BJ-OFF-', 'channel' => 'offline']))->assertOk()->assertSee($offline->kode_booking)->assertDontSee('BJ-REPORT');
    }
}
