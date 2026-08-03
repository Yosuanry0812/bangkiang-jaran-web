<?php

namespace Tests\Feature\Pengelola;

use App\Models\DetailPemesanan;
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
}
