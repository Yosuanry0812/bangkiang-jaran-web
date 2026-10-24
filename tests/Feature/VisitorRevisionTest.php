<?php

namespace Tests\Feature;

use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\Tiket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VisitorRevisionTest extends TestCase
{
    use RefreshDatabase;

    private function booking(User $user): Pemesanan
    {
        $ticket = Tiket::create(['nama_tiket' => 'Tiket Masuk', 'harga' => 15000, 'kategori' => 'perorangan', 'status' => 'aktif']);

        return Pemesanan::create([
            'id_user' => $user->id, 'id_tiket' => $ticket->id_tiket, 'tgl_kunjungan' => now()->addDay()->toDateString(),
            'jumlah' => 1, 'total_harga' => 15000, 'status' => 'pending', 'kode_booking' => 'BJ-REV-' . $user->id,
        ]);
    }

    public function test_booking_accepts_quantities_above_the_previous_limits_for_people_and_vehicles(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $person = Tiket::create(['nama_tiket' => 'Dewasa', 'harga' => 15000, 'kategori' => 'perorangan', 'status' => 'aktif']);
        $vehicle = Tiket::create(['nama_tiket' => 'Motor', 'harga' => 5000, 'kategori' => 'kendaraan', 'status' => 'aktif']);
        $this->actingAs($user)->post(route('wisatawan.pemesanan.store'), [
            'tgl_kunjungan' => now()->addDay()->toDateString(), 'tickets' => [$person->id_tiket => 101, $vehicle->id_tiket => 123],
        ])->assertRedirect(route('wisatawan.pemesanan.data-diri'))->assertSessionHasNoErrors();
        $this->assertSame(224, count(session('pemesanan_flow.detailRows')));
        $this->assertSame(2130000, session('pemesanan_flow.total_harga'));
    }

    public function test_trial_payment_accepts_a_receipt_without_publishing_a_fake_account(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'wisatawan']);
        $booking = $this->booking($user);
        config(['payment.methods' => [[
            'id' => 'BCA', 'name' => 'Bank BCA', 'type' => 'bank', 'logo' => 'bca.jpg',
            'number' => null, 'account_name' => null, 'is_trial' => true,
        ]]]);
        $this->actingAs($user)->get(route('wisatawan.pembayaran.create', $booking->id_pemesanan))
            ->assertOk()->assertSee('Mode uji coba:')->assertDontSee('Detail pembayaran sedang disiapkan');
        $this->post(route('wisatawan.pembayaran.store', $booking->id_pemesanan), [
            'metode' => 'BCA', 'bukti_bayar' => UploadedFile::fake()->image('contoh.jpg'),
        ])->assertRedirect(route('wisatawan.pemesanan.detail', $booking->id_pemesanan))->assertSessionHasNoErrors();
        $payment = $booking->fresh()->pembayaran;
        $this->assertSame('pending', $payment->status);
        $this->assertSame('pending', $booking->fresh()->status);
        $this->assertFalse($booking->fresh()->isApproved());
        Storage::disk('public')->assertExists($payment->bukti_bayar);
    }

    public function test_only_admin_approval_changes_the_owned_status_endpoint_to_success(): void
    {
        Mail::fake();
        $user = User::factory()->create(['role' => 'wisatawan']);
        $admin = User::factory()->create(['role' => 'pengelola']);
        $booking = $this->booking($user);
        Pembayaran::create(['id_pemesanan' => $booking->id_pemesanan, 'total' => 15000, 'metode' => 'BCA', 'status' => 'pending']);
        $status = route('wisatawan.pemesanan.status', $booking->id_pemesanan);
        $approve = route('pengelola.verifikasi.validasi', $booking->id_pemesanan);
        $this->actingAs($user)->getJson($status)->assertOk()->assertJsonPath('approved', false)->assertJsonPath('token', 'pending|pending');
        $this->post($approve, ['action' => 'valid'])->assertForbidden();
        $this->actingAs($admin)->post($approve, ['action' => 'valid'])->assertSessionHas('success');
        $this->actingAs($user)->getJson($status)->assertOk()->assertJsonPath('approved', true)->assertJsonPath('token', 'selesai|valid');
        $this->getJson(route('wisatawan.pemesanan.statuses'))->assertJsonPath((string) $booking->id_pemesanan, 'selesai|valid');
        $this->get($this->app['url']->route('wisatawan.pemesanan.detail', $booking->id_pemesanan))->assertSee('Berhasil');
        $this->actingAs(User::factory()->create(['role' => 'wisatawan']))->getJson($status)->assertNotFound();
    }

    public function test_an_inconsistent_booking_status_does_not_claim_success(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $booking = $this->booking($user);
        $booking->update(['status' => 'selesai']);
        Pembayaran::create(['id_pemesanan' => $booking->id_pemesanan, 'total' => 15000, 'metode' => 'BCA', 'status' => 'pending']);
        $this->actingAs($user)->getJson(route('wisatawan.pemesanan.status', $booking->id_pemesanan))
            ->assertJsonPath('approved', false);
    }

    public function test_trial_mode_cannot_activate_accountless_methods_in_production(): void
    {
        $originalEnv = $_ENV['APP_ENV'] ?? null;
        $originalServer = $_SERVER['APP_ENV'] ?? null;
        $_ENV['APP_ENV'] = $_SERVER['APP_ENV'] = 'production';
        try {
            $configuration = require config_path('payment.php');
            $this->assertFalse($configuration['trial_mode']);
            foreach ($configuration['methods'] as $method) $this->assertFalse($method['is_trial']);
        } finally {
            if ($originalEnv === null) unset($_ENV['APP_ENV']); else $_ENV['APP_ENV'] = $originalEnv;
            if ($originalServer === null) unset($_SERVER['APP_ENV']); else $_SERVER['APP_ENV'] = $originalServer;
        }
    }
}
