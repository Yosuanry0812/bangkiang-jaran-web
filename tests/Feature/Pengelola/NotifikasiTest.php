<?php

namespace Tests\Feature\Pengelola;

use App\Models\Pemesanan;
use App\Models\Pembayaran;
use App\Models\Tiket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotifikasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifikasi_mengembalikan_pending_dan_pesanan_baru(): void
    {
        $pengelola = User::create([
            'name' => 'Pengelola Test',
            'username' => 'pengelola_test',
            'email' => 'pengelola_test@example.com',
            'password' => bcrypt('Password123'),
            'role' => 'pengelola',
            'email_verified_at' => now(),
        ]);
        $wisatawan = User::create([
            'name' => 'Wisatawan Test',
            'username' => 'wisatawan_test',
            'email' => 'wisatawan_test@example.com',
            'password' => bcrypt('Password123'),
            'role' => 'wisatawan',
            'email_verified_at' => now(),
        ]);
        $tiket = Tiket::create([
            'nama_tiket' => 'Tiket Masuk Dewasa',
            'harga' => 20000,
            'kategori' => 'perorangan',
            'status' => 'aktif',
        ]);

        // Pesanan baru, belum bayar
        Pemesanan::create([
            'id_user' => $wisatawan->id,
            'id_tiket' => $tiket->id_tiket,
            'tgl_kunjungan' => now()->addDay()->toDateString(),
            'jumlah' => 2,
            'total_harga' => 40000,
            'status' => 'pending',
            'kode_booking' => 'BJ-TEST001',
        ]);

        // Pesanan sudah upload bukti = pembayaran pending (butuh verifikasi)
        $p2 = Pemesanan::create([
            'id_user' => $wisatawan->id,
            'id_tiket' => $tiket->id_tiket,
            'tgl_kunjungan' => now()->addDay()->toDateString(),
            'jumlah' => 1,
            'total_harga' => 20000,
            'status' => 'pending',
            'kode_booking' => 'BJ-TEST002',
        ]);
        Pembayaran::create([
            'id_pemesanan' => $p2->id_pemesanan,
            'total' => 20000,
            'metode' => 'Transfer BRI',
            'status' => 'pending',
            'tgl_bayar' => now()->toDateString(),
        ]);

        $response = $this->actingAs($pengelola)->getJson('/pengelola/notifikasi');

        $response->assertOk()
            ->assertJson(['count' => 2])
            ->assertJsonPath('verifikasi.0.kode_booking', 'BJ-TEST002')
            ->assertJsonPath('pesanan_baru.0.kode_booking', 'BJ-TEST001');
    }

    public function test_verifikasi_index_prioritas_pending_di_atas(): void
    {
        $pengelola = User::create([
            'name' => 'Pengelola Test',
            'username' => 'pengelola_test',
            'email' => 'pengelola_test@example.com',
            'password' => bcrypt('Password123'),
            'role' => 'pengelola',
            'email_verified_at' => now(),
        ]);
        $wisatawan = User::create([
            'name' => 'Wisatawan Test',
            'username' => 'wisatawan_test',
            'email' => 'wisatawan_test@example.com',
            'password' => bcrypt('Password123'),
            'role' => 'wisatawan',
            'email_verified_at' => now(),
        ]);
        $tiket = Tiket::create([
            'nama_tiket' => 'Tiket Masuk Dewasa',
            'harga' => 20000,
            'kategori' => 'perorangan',
            'status' => 'aktif',
        ]);

        // Lebih BARU tapi sudah valid → harus di BAWAH yang pending
        $baruValid = Pemesanan::create([
            'id_user' => $wisatawan->id,
            'id_tiket' => $tiket->id_tiket,
            'tgl_kunjungan' => now()->addDay()->toDateString(),
            'jumlah' => 1,
            'total_harga' => 20000,
            'status' => 'selesai',
            'kode_booking' => 'BJ-VALID001',
            'created_at' => now(),
        ]);
        Pembayaran::create([
            'id_pemesanan' => $baruValid->id_pemesanan,
            'total' => 20000,
            'metode' => 'Transfer BRI',
            'status' => 'valid',
            'tgl_bayar' => now()->toDateString(),
            'created_at' => now(),
        ]);

        // Lebih LAMA tapi belum diverifikasi → harus di ATAS
        $lamaPending = Pemesanan::create([
            'id_user' => $wisatawan->id,
            'id_tiket' => $tiket->id_tiket,
            'tgl_kunjungan' => now()->addDay()->toDateString(),
            'jumlah' => 1,
            'total_harga' => 20000,
            'status' => 'pending',
            'kode_booking' => 'BJ-PENDING1',
            'created_at' => now()->subHour(),
        ]);
        Pembayaran::create([
            'id_pemesanan' => $lamaPending->id_pemesanan,
            'total' => 20000,
            'metode' => 'Dana',
            'status' => 'pending',
            'tgl_bayar' => now()->toDateString(),
            'created_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($pengelola)->get('/pengelola/verifikasi');

        $response->assertOk();
        // Pending (lebih lama) muncul sebelum valid (lebih baru)
        $response->assertSeeInOrder(['BJ-PENDING1', 'BJ-VALID001']);
    }
}
