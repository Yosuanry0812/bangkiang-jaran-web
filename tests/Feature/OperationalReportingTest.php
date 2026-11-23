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

    private function reportUrl(string $route = 'index', array $extra = []): string
    {
        return route('pengelola.laporan.'.$route, ['bulan' => now()->format('Y-m')] + $extra);
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
            ->assertViewHas('scanHariIni', 0)->assertViewHas('keluarHariIni', 1);
        $chart = array_combine($response->viewData('grafikLabels'), $response->viewData('grafikData'));
        $this->assertSame(['Anak' => 1, 'Dewasa' => 1], $chart);
    }

    public function test_revenue_report_counts_only_valid_payments_and_breaks_down_by_ticket_type(): void
    {
        $this->fixture();
        $this->fixture('pending', 'BJ-PENDING');
        $manager = User::factory()->create(['role' => 'pengelola']);
        $response = $this->actingAs($manager)->get($this->reportUrl())->assertOk()->assertSee('BJ-PENDING');
        $chart = $response->viewData('chart');
        $this->assertSame(25000, $chart['revenue']);
        $this->assertSame(1, $chart['transactions']);
        $this->assertSame(2, $response->viewData('visitorTotal'));
        $breakdown = $response->viewData('breakdown');
        $this->assertSame(['count' => 1, 'revenue' => 15000], $breakdown['dewasa']);
        $this->assertSame(['count' => 1, 'revenue' => 10000], $breakdown['anak']);
        $this->assertSame(['count' => 0, 'revenue' => 0], $breakdown['wisman']);
    }

    public function test_period_uses_payment_date_and_unknown_tickets_are_grouped_as_other(): void
    {
        $booking = $this->fixture();
        $booking->pembayaran->update(['tgl_bayar' => now()->subMonths(2)->toDateString()]);
        $manager = User::factory()->create(['role' => 'pengelola']);
        $this->actingAs($manager)->get($this->reportUrl())->assertViewHas('chart', fn ($c) => $c['revenue'] === 0);
        $this->assertSame('lainnya', \App\Services\RevenueReport::category('Tiket Parkir'));
        $this->assertSame('wisman', \App\Services\RevenueReport::category('Tiket Wisatawan Mancanegara'));
        $this->assertSame('anak', \App\Services\RevenueReport::category('Tiket Anak'));
        $this->assertSame('dewasa', \App\Services\RevenueReport::category('Tiket Dewasa'));
    }

    public function test_realtime_data_endpoint_returns_every_day_of_the_month_with_daily_values(): void
    {
        $this->fixture();
        $manager = User::factory()->create(['role' => 'pengelola']);
        $json = $this->actingAs($manager)->getJson($this->reportUrl('data'))->assertOk()->json();
        $this->assertCount(now()->daysInMonth, $json['days']);
        $today = collect($json['days'])->firstWhere('tgl', now()->toDateString());
        $this->assertSame(25000, $today['pendapatan']);
        $this->assertSame(['dewasa' => 1, 'anak' => 1, 'wisman' => 0, 'lainnya' => 0], $today['tiket']);
        $this->assertSame(25000, $json['revenue']);
        $future = collect($json['days'])->first(fn ($d) => $d['tgl'] > now()->toDateString());
        $this->assertTrue($future === null || $future['pendapatan'] === null);
    }

    public function test_xlsx_columns_follow_paid_ticket_names_and_show_payment_and_checkin_dates(): void
    {
        for ($i = 0; $i < 27; $i++) {
            $this->fixture(code: 'BJ-R-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT));
        }
        $special = $this->fixture(code: 'BJ-OFF-001');
        $extra = Tiket::firstOrCreate(['nama_tiket' => 'Paket Rombongan'], ['harga' => 5000, 'kategori' => 'perorangan', 'status' => 'aktif']);
        $special->detailPemesanan->first()->update(['id_tiket' => $extra->id_tiket, 'nama_tiket' => 'Paket Rombongan', 'harga' => 5000, 'check_in_at' => now()]);
        $manager = User::factory()->create(['role' => 'pengelola']);
        $response = $this->actingAs($manager)->get($this->reportUrl('xlsx'));
        $response->assertOk();
        $this->assertStringContainsString('laporan-pendapatan-', $response->headers->get('Content-Disposition'));

        $zip = new \ZipArchive;
        $this->assertTrue($zip->open($response->baseResponse->getFile()->getPathname()));
        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        $this->assertNotFalse($sheet);
        $rows = simplexml_load_string($sheet)->sheetData->row;
        $this->assertCount(30, $rows); // header + 28 transaksi + baris TOTAL
        $header = [];
        foreach ($rows[0]->c as $c) {
            $header[] = (string) $c->is->t;
        }
        $this->assertContains('Paket Rombongan (orang)', $header);
        $this->assertContains('Paket Rombongan (Rp)', $header);
        $this->assertNotContains('Wisatawan Mancanegara (orang)', $header);
        $this->assertNotContains('Pengunjung Lainnya', $header);
        $this->assertContains('Tanggal Bayar', $header);
        $this->assertContains('Tanggal Kunjungan (Check-in)', $header);
        $this->assertStringContainsString('Belum check-in', $sheet);
        $last = $rows[29]->c;
        $this->assertSame('TOTAL', (string) $last[0]->is->t);
        $this->assertEquals(28 * 25000, (int) $last[count($last) - 1]->v);
    }

    public function test_deleted_account_remains_in_reports_and_booking_list_search_is_part_of_the_report_page(): void
    {
        $booking = $this->fixture();
        $offline = $this->fixture(code: 'BJ-OFF-001');
        $manager = User::factory()->create(['role' => 'pengelola']);
        $this->actingAs($booking->user)->delete('/profile', ['password' => 'password'])->assertRedirect('/');
        $this->actingAs($manager)->get($this->reportUrl())->assertViewHas('chart', fn ($c) => $c['revenue'] === 50000)->assertSee('Akun dihapus');
        $this->get($this->reportUrl('index', ['q' => 'BJ-OFF-', 'channel' => 'offline']))->assertOk()->assertSee($offline->kode_booking)->assertDontSee('BJ-REPORT');
        $this->get(route('pengelola.pesanan.index'))->assertRedirect('/pengelola/laporan');
    }
}
