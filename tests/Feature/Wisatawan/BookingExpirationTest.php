<?php

namespace Tests\Feature\Wisatawan;

use App\Models\DetailPemesanan;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\Tiket;
use App\Models\User;
use App\Services\BookingExpirationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingExpirationTest extends TestCase
{
    use RefreshDatabase;

    private function createTicket(): Tiket
    {
        return Tiket::create([
            'nama_tiket' => 'Tiket Masuk',
            'harga' => 20000,
            'kategori' => 'perorangan',
            'status' => 'aktif',
        ]);
    }

    private function createBooking(User $user, array $overrides = []): Pemesanan
    {
        $ticket = $this->createTicket();
        $createdAt = $overrides['created_at'] ?? null;
        unset($overrides['created_at']);

        $booking = Pemesanan::create(array_merge([
            'id_user' => $user->id,
            'id_tiket' => $ticket->id_tiket,
            'tgl_kunjungan' => now()->addDays(2)->toDateString(),
            'jumlah' => 1,
            'total_harga' => 20000,
            'status' => 'pending',
            'kode_booking' => 'BJ-' . strtoupper(uniqid()),
        ], $overrides));

        if ($createdAt) {
            $booking->created_at = $createdAt;
            $booking->save();
        }

        DetailPemesanan::create([
            'id_pemesanan' => $booking->id_pemesanan,
            'id_tiket' => $ticket->id_tiket,
            'kode_tiket' => 'TK-' . strtoupper(uniqid()),
            'nama_tiket' => $ticket->nama_tiket,
            'nama_pengunjung' => $user->name,
            'harga' => 20000,
            'status_tiket' => 'aktif',
        ]);

        return $booking;
    }

    public function test_unpaid_booking_past_expiry_hours_is_automatically_cancelled(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $booking = $this->createBooking($user, [
            'created_at' => now()->subHours(3), // Melewati batas 2 jam
        ]);

        $service = app(BookingExpirationService::class);
        $count = $service->expire();

        $this->assertSame(1, $count);
        $this->assertSame('dibatalkan', $booking->fresh()->status);
        $this->assertSame('kadaluarsa', $booking->fresh()->detailPemesanan->first()->status_tiket);
    }

    public function test_visiting_riwayat_triggers_on_the_fly_expiration(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $booking = $this->createBooking($user, [
            'created_at' => now()->subHours(3),
        ]);

        $this->actingAs($user)
            ->get(route('wisatawan.pemesanan.riwayat'))
            ->assertOk();

        $this->assertSame('dibatalkan', $booking->fresh()->status);
        $this->assertSame('kadaluarsa', $booking->fresh()->detailPemesanan->first()->status_tiket);
    }

    public function test_booking_with_uploaded_pending_proof_is_not_expired_prematurely(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $booking = $this->createBooking($user, [
            'created_at' => now()->subHours(5), // Dibuat 5 jam lalu tapi bukti sudah diupload
        ]);

        Pembayaran::create([
            'id_pemesanan' => $booking->id_pemesanan,
            'total' => $booking->total_harga,
            'metode' => 'BCA',
            'bukti_bayar' => 'bukti/sample.jpg',
            'status' => 'pending', // Sedang menunggu verifikasi admin
            'tgl_bayar' => now()->toDateString(),
        ]);

        $service = app(BookingExpirationService::class);
        $count = $service->expire();

        $this->assertSame(0, $count);
        $this->assertSame('pending', $booking->fresh()->status);
        $this->assertSame('aktif', $booking->fresh()->detailPemesanan->first()->status_tiket);
    }

    public function test_rejected_payment_past_expiry_hours_is_automatically_cancelled(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $booking = $this->createBooking($user);

        $payment = Pembayaran::create([
            'id_pemesanan' => $booking->id_pemesanan,
            'total' => $booking->total_harga,
            'metode' => 'BCA',
            'bukti_bayar' => 'bukti/sample.jpg',
            'status' => 'ditolak',
            'tgl_bayar' => now()->toDateString(),
        ]);
        $payment->updated_at = now()->subHours(3);
        $payment->save();

        $service = app(BookingExpirationService::class);
        $count = $service->expire();

        $this->assertSame(1, $count);
        $this->assertSame('dibatalkan', $booking->fresh()->status);
    }

    public function test_tourist_can_manually_cancel_their_own_pending_booking(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $booking = $this->createBooking($user);

        $this->actingAs($user)
            ->post(route('wisatawan.pemesanan.cancel', $booking->id_pemesanan))
            ->assertRedirect(route('wisatawan.pemesanan.riwayat'))
            ->assertSessionHas('success');

        $this->assertSame('dibatalkan', $booking->fresh()->status);
        $this->assertSame('kadaluarsa', $booking->fresh()->detailPemesanan->first()->status_tiket);
    }

    public function test_tourist_cannot_cancel_already_approved_booking(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $booking = $this->createBooking($user, ['status' => 'selesai']);

        Pembayaran::create([
            'id_pemesanan' => $booking->id_pemesanan,
            'total' => $booking->total_harga,
            'metode' => 'BCA',
            'status' => 'valid',
            'tgl_bayar' => now()->toDateString(),
        ]);

        $this->actingAs($user)
            ->post(route('wisatawan.pemesanan.cancel', $booking->id_pemesanan))
            ->assertSessionHas('error');

        $this->assertSame('selesai', $booking->fresh()->status);
    }

    public function test_tourist_cannot_pay_for_cancelled_booking(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $booking = $this->createBooking($user, ['status' => 'dibatalkan']);

        $this->actingAs($user)
            ->get(route('wisatawan.pembayaran.create', $booking->id_pemesanan))
            ->assertRedirect(route('wisatawan.pemesanan.detail', $booking->id_pemesanan))
            ->assertSessionHas('error');
    }

    public function test_artisan_expire_pending_command(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $this->createBooking($user, [
            'created_at' => now()->subHours(4),
        ]);

        $this->artisan('tiket:expire-pending')
            ->expectsOutputToContain('1 pesanan diperbarui menjadi dibatalkan')
            ->assertSuccessful();
    }
}
