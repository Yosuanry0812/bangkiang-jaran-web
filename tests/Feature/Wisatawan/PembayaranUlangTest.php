<?php

namespace Tests\Feature\Wisatawan;

use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\Tiket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PembayaranUlangTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejected_payment_can_be_resubmitted(): void
    {
        config()->set('payment.methods', [
            ['id' => 'BCA', 'name' => 'Bank BCA', 'type' => 'bank', 'logo' => 'bca.jpg', 'number' => '123456789', 'account_name' => 'Test Operator'],
        ]);
        Storage::fake('public');
        $user = User::factory()->create();
        $tiket = Tiket::create(['nama_tiket' => 'Tiket Masuk', 'harga' => 20000, 'kategori' => 'perorangan', 'status' => 'aktif']);
        $booking = Pemesanan::create([
            'id_user' => $user->id,
            'id_tiket' => $tiket->id_tiket,
            'tgl_kunjungan' => now()->addDay()->toDateString(),
            'jumlah' => 1,
            'total_harga' => 20000,
            'status' => 'pending',
            'kode_booking' => 'BJ-RETRY-001',
        ]);
        Storage::disk('public')->put('bukti/lama.jpg', 'old proof');
        $payment = Pembayaran::create([
            'id_pemesanan' => $booking->id_pemesanan,
            'total' => 20000,
            'metode' => 'Transfer BCA',
            'bukti_bayar' => 'bukti/lama.jpg',
            'status' => 'ditolak',
            'tgl_bayar' => now()->toDateString(),
        ]);

        $this->actingAs($user)
            ->get(route('wisatawan.pembayaran.create', $booking->id_pemesanan))
            ->assertOk();

        $this->actingAs($user)
            ->post(route('wisatawan.pembayaran.store', $booking->id_pemesanan), [
                'metode' => 'BCA',
                'bukti_bayar' => UploadedFile::fake()->image('bukti-baru.jpg'),
            ])
            ->assertRedirect(route('wisatawan.pemesanan.detail', $booking->id_pemesanan));

        $payment->refresh();
        $this->assertSame('pending', $payment->status);
        $this->assertNotSame('bukti/lama.jpg', $payment->bukti_bayar);
        Storage::disk('public')->assertExists($payment->bukti_bayar);
        Storage::disk('public')->assertMissing('bukti/lama.jpg');
        $this->assertSame(1, Pembayaran::count());
    }

    public function test_payment_cannot_be_submitted_without_an_official_method(): void
    {
        config()->set('payment.methods', []);
        Storage::fake('public');
        $user = User::factory()->create();
        $tiket = Tiket::create(['nama_tiket' => 'Tiket Masuk', 'harga' => 20000, 'kategori' => 'perorangan', 'status' => 'aktif']);
        $booking = Pemesanan::create([
            'id_user' => $user->id,
            'id_tiket' => $tiket->id_tiket,
            'tgl_kunjungan' => now()->addDay()->toDateString(),
            'jumlah' => 1,
            'total_harga' => 20000,
            'status' => 'pending',
            'kode_booking' => 'BJ-NO-METHOD',
        ]);

        $this->actingAs($user)
            ->get(route('wisatawan.pembayaran.create', $booking->id_pemesanan))
            ->assertOk()
            ->assertSee('Belum ada rekening resmi yang dipublikasikan');

        $this->actingAs($user)
            ->post(route('wisatawan.pembayaran.store', $booking->id_pemesanan), [
                'metode' => 'BCA',
                'bukti_bayar' => UploadedFile::fake()->image('bukti.jpg'),
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame(0, Pembayaran::count());
    }
}
