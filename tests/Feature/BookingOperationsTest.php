<?php

namespace Tests\Feature;

use App\Mail\PemesananBerhasil;
use App\Models\Pemesanan;
use App\Models\Tiket;
use App\Models\User;
use App\Models\VisitCapacity;
use App\Services\DestinationSettings;
use App\Services\PaymentProofMigration;
use App\Services\VisitQuota;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookingOperationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Storage::fake('public');
        Storage::fake('private');
        config(['payment.methods' => [['id' => 'BCA', 'name' => 'BCA', 'number' => '', 'account_name' => '', 'type' => 'bank', 'logo' => 'bca.jpg']]]);
    }

    private function booking(string $state = 'pending', ?User $user = null, int $quantity = 1): Pemesanan
    {
        $user ??= User::factory()->create(['role' => 'wisatawan']);
        $ticket = Tiket::create(['nama_tiket' => 'Dewasa Uji', 'harga' => 15000, 'kategori' => 'perorangan', 'status' => 'aktif']);
        $booking = Pemesanan::create(['id_user' => $user->id, 'id_tiket' => $ticket->id_tiket,
            'tgl_kunjungan' => now()->toDateString(), 'jumlah' => $quantity, 'total_harga' => 15000 * $quantity,
            'status' => $state, 'kode_booking' => 'BJ-'.strtoupper(Str::random(15)), 'expires_at' => now()->addHour()]);
        for ($i = 0; $i < $quantity; $i++) {
            $booking->detailPemesanan()->create(['id_tiket' => $ticket->id_tiket,
                'kode_tiket' => 'BJ-'.strtoupper(Str::random(15)), 'nama_tiket' => 'Dewasa Uji',
                'nama_pengunjung' => 'Pengunjung Uji '.$i, 'jenis_kelamin' => 'L', 'harga' => 15000, 'status_tiket' => 'aktif']);
        }
        if ($state === 'selesai') {
            $booking->pembayaran()->create(['total' => $booking->total_harga, 'metode' => 'BCA', 'status' => 'valid', 'tgl_bayar' => now()->toDateString()]);
        }

        return $booking;
    }

    private function begin(User $user, Tiket $ticket, int $count = 1): array
    {
        $this->actingAs($user)->post(route('wisatawan.pemesanan.store'), ['tgl_kunjungan' => now()->toDateString(), 'tickets' => [$ticket->id_tiket => $count]])->assertSessionHasNoErrors();

        return session('pemesanan_flow');
    }

    public function test_deadline_timeline_and_duplicate_submit_form_one_booking(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $ticket = Tiket::create(['nama_tiket' => 'Masuk', 'harga' => 20000, 'kategori' => 'perorangan', 'status' => 'aktif']);
        $flow = $this->begin($user, $ticket);
        $payload = ['nama_0' => 'Pengunjung', 'gender_0' => 'L'];
        $this->post(route('wisatawan.pemesanan.store-data-diri'), $payload)->assertSessionHasNoErrors();
        $booking = Pemesanan::sole();
        $this->assertNotNull($booking->expires_at);
        $this->assertSame($flow['draft_token'], $booking->draft_token);
        $this->assertSame(['created'], $booking->events->pluck('event')->all());
        $this->post(route('wisatawan.pemesanan.store-data-diri'), $payload)->assertRedirect(route('wisatawan.pemesanan.detail', $booking->id_pemesanan));
        $this->assertDatabaseCount('pemesanan', 1);
        Mail::assertQueued(PemesananBerhasil::class, 1);
        $this->get(route('wisatawan.pemesanan.detail', $booking->id_pemesanan))->assertSee('data-booking-countdown', false)->assertSee(__('operations.timeline'));
    }

    public function test_checkout_rejects_ticket_disabled_since_selection(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $ticket = $this->booking()->tiket;
        $this->begin($user, $ticket);
        $ticket->update(['status' => 'nonaktif']);
        $this->post(route('wisatawan.pemesanan.store-data-diri'), ['nama_0' => 'Uji', 'gender_0' => 'L'])->assertSessionHasErrors('booking');
        $this->assertSame(0, Pemesanan::where('id_user', $user->id)->count());
    }

    public function test_checkout_rejects_changed_price_and_expired_draft(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $ticket = $this->booking()->tiket;
        $this->begin($user, $ticket);
        $ticket->update(['harga' => 25000]);
        $this->post(route('wisatawan.pemesanan.store-data-diri'), ['nama_0' => 'Uji', 'gender_0' => 'L'])->assertSessionHasErrors('booking');
        $this->begin($user, $ticket);
        $this->travel(61)->minutes();
        $this->post(route('wisatawan.pemesanan.store-data-diri'), ['nama_0' => 'Uji', 'gender_0' => 'L'])->assertSessionHasErrors();
        $this->assertSame(0, Pemesanan::where('id_user', $user->id)->count());
    }

    public function test_cancel_releases_capacity_and_cannot_cancel_another_owners_booking(): void
    {
        $booking = $this->booking();
        $other = User::factory()->create(['role' => 'wisatawan']);
        $this->actingAs($other)->post(route('wisatawan.pemesanan.cancel', $booking->id_pemesanan))->assertNotFound();
        $this->assertSame(1, app(VisitQuota::class)->used(now()->toDateString()));
        $this->actingAs($booking->user)->post(route('wisatawan.pemesanan.cancel', $booking->id_pemesanan))->assertSessionHas('success');
        $this->assertSame('visitor_cancelled', $booking->fresh()->closed_reason);
        $this->assertSame(0, app(VisitQuota::class)->used(now()->toDateString()));
        $this->assertSame('cancelled', $booking->fresh()->events->last()->event);
    }

    public function test_paid_or_reviewing_bookings_cannot_be_cancelled(): void
    {
        $paid = $this->booking('selesai');
        $this->actingAs($paid->user)->post(route('wisatawan.pemesanan.cancel', $paid->id_pemesanan))->assertSessionHasErrors('booking');
        $review = $this->booking();
        $review->pembayaran()->create(['total' => 15000, 'metode' => 'BCA', 'status' => 'pending']);
        $this->actingAs($review->user)->post(route('wisatawan.pemesanan.cancel', $review->id_pemesanan))->assertSessionHasErrors('booking');
        $this->assertSame('pending', $review->fresh()->status);
    }

    public function test_expiry_skips_receipts_under_review_and_is_idempotent(): void
    {
        $expired = $this->booking();
        $review = $this->booking();
        $expired->update(['expires_at' => now()->subMinute()]);
        $review->update(['expires_at' => now()->subMinute()]);
        $review->pembayaran()->create(['total' => 15000, 'metode' => 'BCA', 'status' => 'pending']);
        $this->artisan('pemesanan:expire')->assertSuccessful();
        $this->artisan('pemesanan:expire')->assertSuccessful();
        $this->assertSame('expired', $expired->fresh()->closed_reason);
        $this->assertSame('pending', $review->fresh()->status);
        $this->assertSame(1, $expired->events()->where('event', 'expired')->count());
        $this->assertSame(1, app(VisitQuota::class)->used(now()->toDateString()));
    }

    public function test_expired_booking_cannot_upload_receipt(): void
    {
        $booking = $this->booking();
        $booking->update(['expires_at' => now()->subMinute()]);
        $this->actingAs($booking->user)->post(route('wisatawan.pembayaran.store', $booking->id_pemesanan), ['metode' => 'BCA', 'bukti_bayar' => UploadedFile::fake()->image('receipt.jpg')])->assertSessionHas('error');
        $this->assertDatabaseCount('pembayaran', 0);
        $this->assertSame([], Storage::disk('private')->allFiles());
    }

    public function test_receipt_is_private_and_authorized_for_owner_or_manager_only(): void
    {
        $booking = $this->booking();
        $this->actingAs($booking->user)->post(route('wisatawan.pembayaran.store', $booking->id_pemesanan), ['metode' => 'BCA', 'bukti_bayar' => UploadedFile::fake()->image('receipt.jpg')])->assertSessionHasNoErrors();
        $payment = $booking->fresh()->pembayaran;
        $this->assertSame('private', $payment->proof_disk);
        Storage::disk('private')->assertExists($payment->bukti_bayar);
        Storage::disk('public')->assertMissing($payment->bukti_bayar);
        $url = route('payment.proof', $payment->id_bayar);
        $this->get($url)->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $other = User::factory()->create(['role' => 'wisatawan']);
        $this->actingAs($other)->get($url)->assertNotFound();
        $manager = User::factory()->create(['role' => 'pengelola']);
        $this->actingAs($manager)->get($url)->assertOk();
    }

    public function test_rejection_requires_reason_and_grants_a_new_payment_deadline(): void
    {
        $booking = $this->booking();
        $booking->update(['expires_at' => now()->subMinute()]);
        $booking->pembayaran()->create(['total' => 15000, 'metode' => 'BCA', 'status' => 'pending']);
        $manager = User::factory()->create(['role' => 'pengelola']);
        $url = route('pengelola.verifikasi.validasi', $booking->id_pemesanan);
        $this->actingAs($manager)->post($url, ['action' => 'ditolak'])->assertSessionHasErrors('rejection_reason');
        $this->post($url, ['action' => 'ditolak', 'rejection_reason' => 'Nominal transfer tidak sesuai.'])->assertSessionHas('success');
        $booking->refresh();
        $this->assertTrue($booking->expires_at->isFuture());
        $this->assertSame('Nominal transfer tidak sesuai.', $booking->pembayaran->rejection_reason);
        $this->assertSame('payment_rejected', $booking->events->last()->event);
        $this->actingAs($booking->user)->get(route('wisatawan.pemesanan.detail', $booking->id_pemesanan))->assertSee('Nominal transfer tidak sesuai.');
    }

    public function test_quota_is_checked_at_final_checkout_and_cannot_be_reduced_below_holds(): void
    {
        $existing = $this->booking();
        $user = User::factory()->create(['role' => 'wisatawan']);
        VisitCapacity::create(['visit_date' => now()->toDateString(), 'capacity' => 1]);
        $this->begin($user, $existing->tiket);
        $this->post(route('wisatawan.pemesanan.store-data-diri'), ['nama_0' => 'Uji', 'gender_0' => 'L'])->assertSessionHasErrors('tickets');
        $this->assertDatabaseCount('pemesanan', 1);
        $this->getJson(route('kunjungan.kuota', ['date' => now()->toDateString()]))->assertOk()->assertJsonPath('remaining', 0);
        $manager = User::factory()->create(['role' => 'pengelola']);
        $this->actingAs($manager)->put(route('pengelola.quotas.update'), ['visit_date' => now()->toDateString(), 'capacity' => 2])->assertSessionHasNoErrors();
        $this->put(route('pengelola.quotas.update'), ['visit_date' => now()->toDateString(), 'capacity' => 0])->assertSessionHasErrors('capacity');
        $this->put(route('pengelola.quotas.update'), ['visit_date' => now()->toDateString(), 'capacity' => null])->assertSessionHasNoErrors();
        $this->assertNull(VisitCapacity::find(now()->toDateString())->capacity);
    }

    public function test_quota_reduction_below_paid_tickets_is_rejected(): void
    {
        $this->booking('selesai', quantity: 2);
        $manager = User::factory()->create(['role' => 'pengelola']);
        $this->actingAs($manager)->put(route('pengelola.quotas.update'), ['visit_date' => now()->toDateString(), 'capacity' => 1])->assertSessionHasErrors('capacity');
        $this->assertDatabaseMissing('visit_capacities', ['visit_date' => now()->toDateString(), 'capacity' => 1]);
    }

    public function test_deleting_account_preserves_paid_financial_records_and_revokes_account(): void
    {
        $booking = $this->booking('selesai');
        $user = $booking->user;
        $originalEmail = $user->email;
        $this->actingAs($user)->delete('/profile', ['password' => 'password'])->assertRedirect('/');
        $this->assertGuest();
        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertDatabaseHas('pemesanan', ['id_pemesanan' => $booking->id_pemesanan, 'status' => 'selesai']);
        $this->assertDatabaseHas('pembayaran', ['id_pemesanan' => $booking->id_pemesanan, 'status' => 'valid']);
        $this->assertDatabaseCount('detail_pemesanan', 1);
        $this->assertSame('Akun dihapus', $booking->fresh()->user->name);
        $this->assertNotSame($originalEmail, $booking->fresh()->user->email);
    }

    public function test_public_verification_hides_identity_and_distinguishes_future_and_expired(): void
    {
        $booking = $this->booking('selesai');
        $detail = $booking->detailPemesanan->first();
        $url = route('tiket.verifikasi', $detail->kode_tiket);
        $this->get($url)->assertOk()->assertViewHas('valid', true)->assertDontSee($booking->user->name)->assertDontSee($detail->nama_pengunjung)->assertDontSee($booking->kode_booking)->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $booking->update(['tgl_kunjungan' => now()->subDay()->toDateString()]);
        $this->get($url)->assertViewHas('valid', false)->assertSee(__('operations.verification_expired'));
        $booking->update(['tgl_kunjungan' => now()->addDay()->toDateString()]);
        $this->get($url)->assertViewHas('valid', false)->assertSee(__('operations.verification_future_visit'));
    }

    public function test_gate_rejects_inconsistent_payment_and_onsite_tracks_entry_and_exit(): void
    {
        $booking = $this->booking('selesai');
        $detail = $booking->detailPemesanan->first();
        $manager = User::factory()->create(['role' => 'pengelola']);
        $booking->pembayaran->update(['status' => 'pending']);
        $this->actingAs($manager)->post(route('pengelola.scan.gunakan'), ['kode' => $detail->kode_tiket])->assertSessionHas('error');
        $this->assertNull($detail->fresh()->check_in_at);
        $booking->pembayaran->update(['status' => 'valid']);
        $this->post(route('pengelola.scan.gunakan'), ['kode' => $detail->kode_tiket])->assertSessionHas('success');
        $this->get(route('pengelola.onsite.index'))->assertOk()->assertViewHas('total', 1)->assertSee($detail->nama_pengunjung);
        $this->post(route('pengelola.scan.checkout'), ['kode' => $detail->kode_tiket])->assertSessionHas('success');
        $this->get(route('pengelola.onsite.index'))->assertViewHas('total', 0);
        $this->assertSame(['checked_in', 'checked_out'], $booking->events->pluck('event')->all());
    }

    public function test_pdf_and_qr_are_local_and_unpaid_or_other_owner_cannot_download(): void
    {
        $paid = $this->booking('selesai');
        $url = route('eticket.download', $paid->id_pemesanan);
        $this->actingAs($paid->user)->get($url)->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->get(route('wisatawan.pemesanan.detail', $paid->id_pemesanan))->assertSee('data:image/svg+xml;base64,', false)->assertDontSee('api.qrserver.com');
        $other = User::factory()->create(['role' => 'wisatawan']);
        $this->actingAs($other)->get($url)->assertNotFound();
        $pending = $this->booking(user: $other);
        $this->get(route('wisatawan.pemesanan.detail', $pending->id_pemesanan))->assertOk()->assertSee(__('operations.qr_pending'))->assertDontSee('data:image/svg+xml;base64,', false);
        $this->get(route('eticket.download', $pending->id_pemesanan))->assertForbidden();
    }

    public function test_history_search_is_owned_paginated_and_date_filtered(): void
    {
        $own = $this->booking();
        $foreign = $this->booking();
        $this->actingAs($own->user)->get(route('wisatawan.pemesanan.riwayat', ['q' => $foreign->kode_booking]))->assertViewHas('pemesanan', fn ($items) => $items->total() === 0);
        $this->get(route('wisatawan.pemesanan.riwayat', ['q' => $own->kode_booking]))->assertSee($own->kode_booking)->assertViewHas('pemesanan', fn ($items) => $items->perPage() === 15 && $items->total() === 1);
        $this->get(route('wisatawan.pemesanan.riwayat', ['from' => now()->addDay()->toDateString()]))->assertViewHas('pemesanan', fn ($items) => $items->total() === 0);
    }

    public function test_legacy_public_receipt_migration_preserves_bytes_and_removes_public_copy(): void
    {
        $booking = $this->booking();
        Storage::disk('public')->put('bukti/legacy.pdf', 'fixture bytes');
        $payment = $booking->pembayaran()->create(['total' => 15000, 'metode' => 'BCA', 'status' => 'pending', 'bukti_bayar' => 'bukti/legacy.pdf']);
        $service = app(PaymentProofMigration::class);
        $this->assertTrue($service->move($payment));
        $this->assertSame('private', $payment->fresh()->proof_disk);
        $this->assertSame('fixture bytes', Storage::disk('private')->get('bukti/legacy.pdf'));
        Storage::disk('public')->assertMissing('bukti/legacy.pdf');
        $this->assertFalse($service->move($payment->fresh()));
    }

    public function test_new_management_pages_are_for_managers_only(): void
    {
        $visitor = User::factory()->create(['role' => 'wisatawan']);
        $manager = User::factory()->create(['role' => 'pengelola']);
        foreach (['pesanan.index', 'onsite.index', 'quotas.index', 'audit.index', 'settings.edit'] as $name) {
            $url = route('pengelola.'.$name);
            $this->actingAs($visitor)->get($url)->assertForbidden();
            $this->actingAs($manager)->get($url)->assertOk();
        }
    }

    public function test_destination_settings_validate_render_safely_and_record_actor(): void
    {
        $manager = User::factory()->create(['role' => 'pengelola']);
        $settings = app(DestinationSettings::class)->values();
        $settings['operating_hours'] = '08.30 - 17.30';
        $settings['rules'] = '<script>alert(1)</script>';
        $settings['payment_minutes'] = 180;
        $this->actingAs($manager)->put(route('pengelola.settings.update'), $settings)->assertSessionHasNoErrors();
        $this->assertDatabaseHas('activity_logs', ['id_user' => $manager->id, 'aktivitas' => 'Ubah pengaturan destinasi']);
        $this->assertSame(180, app(DestinationSettings::class)->values()['payment_minutes']);
        $this->actingAs(User::factory()->create(['role' => 'wisatawan']))->get('/')->assertSee('08.30 - 17.30')->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->actingAs($manager)->put(route('pengelola.settings.update'), [...$settings, 'maps_embed' => 'javascript:alert(1)'])->assertSessionHasErrors('maps_embed');
    }

    public function test_existing_google_password_requires_current_password_and_get_logout_has_no_side_effect(): void
    {
        $user = User::factory()->create(['role' => 'wisatawan', 'google_id' => 'audit-google']);
        $this->actingAs($user)->post(route('profile.set-password'), ['password' => 'NewPass123!', 'password_confirmation' => 'NewPass123!'])->assertSessionHasErrors('current_password');
        $this->get('/logout')->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
        $this->post(route('logout'))->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_payment_page_uses_destination_contact_saved_by_manager(): void
    {
        $settings = app(DestinationSettings::class)->values();
        app(DestinationSettings::class)->save([...$settings, 'whatsapp' => '6281234567890']);
        $booking = $this->booking();
        $this->actingAs($booking->user)->get(route('wisatawan.pembayaran.create', $booking->id_pemesanan))
            ->assertOk()->assertSee('https://wa.me/6281234567890', false);
    }

    public function test_receipt_migration_resumes_cleanup_after_metadata_was_already_committed(): void
    {
        $booking = $this->booking();
        Storage::disk('public')->put('bukti/interrupted.pdf', 'fixture bytes');
        Storage::disk('private')->put('bukti/interrupted.pdf', 'fixture bytes');
        $payment = $booking->pembayaran()->create(['total' => 15000, 'metode' => 'BCA', 'status' => 'pending',
            'proof_disk' => 'private', 'bukti_bayar' => 'bukti/interrupted.pdf']);
        $this->assertFalse(app(PaymentProofMigration::class)->move($payment));
        Storage::disk('public')->assertMissing('bukti/interrupted.pdf');
        Storage::disk('private')->assertExists('bukti/interrupted.pdf');
    }
}
