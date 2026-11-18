<?php

namespace Tests\Feature\Pengelola;

use App\Models\DetailPemesanan;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\Tiket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanTiketTest extends TestCase
{
    use RefreshDatabase;

    private function setupPengelola(): User
    {
        return User::create([
            'name' => 'Pengelola Test',
            'username' => 'pengelola_test',
            'email' => 'pengelola_test@example.com',
            'password' => bcrypt('Password123'),
            'role' => 'pengelola',
            'email_verified_at' => now(),
        ]);
    }

    private function setupPemesanan(int $idUser, string $kodeBooking, string $kodeTiket, string $tglKunjungan, string $statusPemesanan): void
    {
        $tiket = Tiket::create([
            'nama_tiket' => 'Tiket Masuk Dewasa',
            'harga' => 20000,
            'kategori' => 'perorangan',
            'status' => 'aktif',
        ]);

        $pemesanan = Pemesanan::create([
            'id_user' => $idUser,
            'id_tiket' => $tiket->id_tiket,
            'tgl_kunjungan' => $tglKunjungan,
            'jumlah' => 1,
            'total_harga' => 20000,
            'status' => $statusPemesanan,
            'kode_booking' => $kodeBooking,
        ]);

        if ($statusPemesanan === 'selesai') {
            Pembayaran::create(['id_pemesanan' => $pemesanan->id_pemesanan, 'total' => 20000, 'metode' => 'Tunai', 'status' => 'valid']);
        }
        DetailPemesanan::create([
            'id_pemesanan' => $pemesanan->id_pemesanan,
            'id_tiket' => $tiket->id_tiket,
            'kode_tiket' => $kodeTiket,
            'nama_tiket' => 'Tiket Masuk Dewasa',
            'nama_pengunjung' => 'Pengunjung Test',
            'jenis_kelamin' => 'L',
            'harga' => 20000,
            'status_tiket' => 'aktif',
        ]);
    }

    public function test_check_in_dan_check_out_tiket_hari_ini(): void
    {
        $pengelola = $this->setupPengelola();
        $this->setupPemesanan($pengelola->id, 'BJ-SCAN001', 'BJ-TODAY01', now()->toDateString(), 'selesai');

        // Check-in
        $this->actingAs($pengelola)
            ->post('/pengelola/scan/gunakan', ['kode' => 'BJ-TODAY01'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $detail = DetailPemesanan::where('kode_tiket', 'BJ-TODAY01')->first();
        $this->assertSame('digunakan', $detail->status_tiket);
        $this->assertNotNull($detail->check_in_at);

        // Check-out
        $this->actingAs($pengelola)
            ->post('/pengelola/scan/checkout', ['kode' => 'BJ-TODAY01'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $detail->refresh();
        $this->assertNotNull($detail->check_out_at);

        // Check-out ganda ditolak
        $this->actingAs($pengelola)
            ->post('/pengelola/scan/checkout', ['kode' => 'BJ-TODAY01'])
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_check_out_tetap_bisa_setelah_tanggal_kunjungan_lewat(): void
    {
        $pengelola = $this->setupPengelola();
        $this->setupPemesanan($pengelola->id, 'BJ-SCAN002', 'BJ-NIGHT01', now()->subDay()->toDateString(), 'selesai');
        DetailPemesanan::where('kode_tiket', 'BJ-NIGHT01')->update(['status_tiket' => 'digunakan', 'check_in_at' => now()->subHours(2)]);

        $this->actingAs($pengelola)
            ->post('/pengelola/scan/checkout', ['kode' => 'BJ-NIGHT01'])
            ->assertSessionHas('success');

        $this->assertNotNull(DetailPemesanan::where('kode_tiket', 'BJ-NIGHT01')->first()->check_out_at);
    }

    public function test_halaman_scan_memakai_pustaka_kamera_lokal(): void
    {
        $this->actingAs($this->setupPengelola())->get('/pengelola/scan')
            ->assertOk()
            ->assertSee('js/vendor/html5-qrcode-2.3.8.min.js', false)
            ->assertDontSee('unpkg.com', false);
    }

    public function test_check_in_tiket_tanggal_lain_ditolak(): void
    {
        $pengelola = $this->setupPengelola();
        $this->setupPemesanan($pengelola->id, 'BJ-SCAN002', 'BJ-TOMORROW', now()->addDay()->toDateString(), 'selesai');

        $this->actingAs($pengelola)
            ->post('/pengelola/scan/gunakan', ['kode' => 'BJ-TOMORROW'])
            ->assertRedirect()
            ->assertSessionHas('error', function ($msg) {
                return str_contains($msg, 'Hanya tiket hari ini');
            });

        $this->assertSame('aktif', DetailPemesanan::where('kode_tiket', 'BJ-TOMORROW')->first()->status_tiket);
    }

    public function test_check_in_tiket_belum_lunas_ditolak(): void
    {
        $pengelola = $this->setupPengelola();
        $this->setupPemesanan($pengelola->id, 'BJ-SCAN003', 'BJ-UNPAID1', now()->toDateString(), 'pending');

        $this->actingAs($pengelola)
            ->post('/pengelola/scan/gunakan', ['kode' => 'BJ-UNPAID1'])
            ->assertRedirect()
            ->assertSessionHas('error', function ($msg) {
                return str_contains($msg, 'belum lunas');
            });

        $this->assertSame('aktif', DetailPemesanan::where('kode_tiket', 'BJ-UNPAID1')->first()->status_tiket);
    }

    public function test_check_in_kode_tidak_ditemukan(): void
    {
        $pengelola = $this->setupPengelola();

        $this->actingAs($pengelola)
            ->post('/pengelola/scan/gunakan', ['kode' => 'BJ-NOPE123'])
            ->assertRedirect()
            ->assertSessionHas('error', function ($msg) {
                return str_contains($msg, 'tidak ditemukan');
            });
    }

    public function test_cari_tiket_menampilkan_status_bukan_hari_ini(): void
    {
        $pengelola = $this->setupPengelola();
        $this->setupPemesanan($pengelola->id, 'BJ-SCAN004', 'BJ-FUTURE1', now()->addDays(3)->toDateString(), 'selesai');

        $this->actingAs($pengelola)
            ->post('/pengelola/scan/cari', ['kode' => 'BJ-FUTURE1'])
            ->assertOk()
            ->assertSee('Tiket Bukan Untuk Hari Ini');
    }

    public function test_cari_dan_check_in_dengan_url_lengkap_qr_code(): void
    {
        $pengelola = $this->setupPengelola();
        $this->setupPemesanan($pengelola->id, 'BJ-SCANURL1', 'BJ-URL01', now()->toDateString(), 'selesai');

        // Cari dengan full URL dari hasil pemindaian QR Code kamera
        $this->actingAs($pengelola)
            ->post('/pengelola/scan/cari', ['kode' => 'http://127.0.0.1:8000/tiket/verifikasi/BJ-URL01'])
            ->assertOk()
            ->assertSee('BJ-URL01');

        // Check-in dengan full URL
        $this->actingAs($pengelola)
            ->post('/pengelola/scan/gunakan', ['kode' => 'http://127.0.0.1:8000/tiket/verifikasi/BJ-URL01'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $detail = DetailPemesanan::where('kode_tiket', 'BJ-URL01')->first();
        $this->assertSame('digunakan', $detail->status_tiket);
        $this->assertNotNull($detail->check_in_at);

        // Check-out dengan full URL
        $this->actingAs($pengelola)
            ->post('/pengelola/scan/checkout', ['kode' => 'http://127.0.0.1:8000/tiket/verifikasi/BJ-URL01'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $detail->refresh();
        $this->assertNotNull($detail->check_out_at);
    }
}
