<?php

namespace Tests\Feature;

use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\Tiket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TransactionIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_unavailable_ticket_cannot_start_booking(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $this->actingAs($user)->from('/wisatawan/pemesanan')->post('/wisatawan/pemesanan', [
            'tgl_kunjungan' => now()->addDay()->toDateString(), 'tickets' => [999999 => 1],
        ])->assertRedirect()->assertSessionHasErrors('tickets');
        $this->assertNull(session('pemesanan_flow'));
    }

    public function test_verified_payment_cannot_be_rejected_or_verified_again(): void
    {
        Mail::fake();
        $admin = User::factory()->create(['role' => 'pengelola']);
        $user = User::factory()->create(['role' => 'wisatawan']);
        $tiket = Tiket::create(['nama_tiket' => 'Tiket', 'harga' => 20000, 'kategori' => 'perorangan', 'status' => 'aktif']);
        $booking = Pemesanan::create([
            'id_user' => $user->id, 'id_tiket' => $tiket->id_tiket,
            'tgl_kunjungan' => now()->toDateString(), 'jumlah' => 1,
            'total_harga' => 20000, 'status' => 'pending', 'kode_booking' => 'BJ-INTEGRITY',
        ]);
        $payment = Pembayaran::create([
            'id_pemesanan' => $booking->id_pemesanan, 'total' => 20000,
            'metode' => 'BCA', 'status' => 'pending',
        ]);
        $url = route('pengelola.verifikasi.validasi', $booking->id_pemesanan);
        $this->actingAs($admin)->post($url, ['action' => 'valid'])->assertSessionHas('success');
        $this->post($url, ['action' => 'ditolak'])->assertSessionHas('error');
        $this->assertSame('valid', $payment->fresh()->status);
        $this->assertSame('selesai', $booking->fresh()->status);
    }
    public function test_google_id_must_be_unique_but_null_is_allowed(): void
    {
        User::factory()->create(['google_id' => null]);
        User::factory()->create(['google_id' => null]);
        User::factory()->create(['google_id' => 'google-id']);
        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['google_id' => 'google-id']);
    }

    public function test_booking_cannot_have_duplicate_payment_records(): void
    {
        $user = User::factory()->create();
        $ticket = Tiket::create(['nama_tiket' => 'Tiket', 'harga' => 20000, 'kategori' => 'perorangan', 'status' => 'aktif']);
        $booking = Pemesanan::create([
            'id_user' => $user->id, 'id_tiket' => $ticket->id_tiket,
            'tgl_kunjungan' => now()->toDateString(), 'jumlah' => 1,
            'total_harga' => 20000, 'status' => 'pending', 'kode_booking' => 'BJ-UNIQUE',
        ]);
        $data = ['id_pemesanan' => $booking->id_pemesanan, 'total' => 20000, 'metode' => 'BCA', 'status' => 'pending'];
        Pembayaran::create($data);
        $this->expectException(\Illuminate\Database\QueryException::class);
        Pembayaran::create($data);
    }
}
