<?php

namespace Tests\Feature;

use App\Mail\PembayaranDitolak;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\Tiket;
use App\Models\User;
use App\Support\WindowsProcessDriver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Process\Factory;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TransactionalMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_remains_saved_when_queue_submission_fails(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $ticket = Tiket::create(['nama_tiket' => 'Dewasa', 'harga' => 15000, 'kategori' => 'perorangan', 'status' => 'aktif']);
        $this->actingAs($user)->post(route('wisatawan.pemesanan.store'), [
            'tgl_kunjungan' => now()->toDateString(), 'tickets' => [$ticket->id_tiket => 1],
        ])->assertSessionHasNoErrors();
        Mail::shouldReceive('to')->once()->with($user->email)->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new \RuntimeException('Queue unavailable'));

        $response = $this->post(route('wisatawan.pemesanan.store-data-diri'), ['nama_0' => 'Pengunjung Uji', 'gender_0' => 'L']);
        $booking = Pemesanan::sole();
        $response->assertRedirect(route('wisatawan.pembayaran.create', $booking->id_pemesanan))->assertSessionHas('success');
        $this->assertSame('pending', $booking->status);
        $this->assertDatabaseCount('detail_pemesanan', 1);
        $this->assertNull(session('pemesanan_flow'));
    }

    public function test_payment_rejection_queues_email_without_sending_it_in_the_request(): void
    {
        Mail::fake();
        $user = User::factory()->create(['role' => 'wisatawan']);
        $manager = User::factory()->create(['role' => 'pengelola']);
        $ticket = Tiket::create(['nama_tiket' => 'Dewasa', 'harga' => 15000, 'kategori' => 'perorangan', 'status' => 'aktif']);
        $booking = Pemesanan::create([
            'id_user' => $user->id, 'id_tiket' => $ticket->id_tiket, 'tgl_kunjungan' => now()->toDateString(),
            'jumlah' => 1, 'total_harga' => 15000, 'status' => 'pending', 'kode_booking' => 'BJ-MAIL-TEST',
        ]);
        Pembayaran::create(['id_pemesanan' => $booking->id_pemesanan, 'total' => 15000, 'metode' => 'BCA', 'status' => 'pending']);
        $this->actingAs($manager)->post(route('pengelola.verifikasi.validasi', $booking->id_pemesanan), ['action' => 'ditolak'])
            ->assertSessionHas('success');
        $this->assertSame('ditolak', $booking->fresh()->pembayaran->status);
        Mail::assertQueued(PembayaranDitolak::class, fn ($mail) => $mail->connection === 'background' && $mail->hasTo($user->email));
        Mail::assertNothingSent();
    }

    public function test_windows_background_launch_is_deferred_hidden_and_reports_launch_failure(): void
    {
        $factory = new Factory;
        $factory->fake(['*' => $factory->result(exitCode: 1)]);
        $driver = new WindowsProcessDriver($factory);
        $callback = $driver->defer(static fn () => null);
        $factory->assertNothingRan();
        try {
            $callback();
            $this->fail('A failed launcher must be reported.');
        } catch (\Illuminate\Process\Exceptions\ProcessFailedException) {
            $factory->assertRan(function ($process) {
                $script = mb_convert_encoding(base64_decode(end($process->command)), 'UTF-8', 'UTF-16LE');
                return $process->command[0] === 'powershell.exe'
                    && str_contains($script, 'Start-Process -WindowStyle Hidden')
                    && !str_contains($script, '-Wait')
                    && isset($process->environment['LARAVEL_INVOKABLE_CLOSURE']);
            });
        }
    }
}
