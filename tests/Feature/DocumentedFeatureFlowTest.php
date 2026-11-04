<?php

namespace Tests\Feature;

use App\Mail\PembayaranValid;
use App\Mail\PemesananBerhasil;
use App\Models\DetailPemesanan;
use App\Models\Galeri;
use App\Models\Pemesanan;
use App\Models\Tiket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentedFeatureFlowTest extends TestCase
{
    use RefreshDatabase;

    private function ticket(string $category = 'perorangan', int $price = 15000): Tiket
    {
        return Tiket::create([
            'nama_tiket' => $category === 'kendaraan' ? 'Motor Uji' : 'Pengunjung Uji',
            'nama_tiket_en' => $category === 'kendaraan' ? 'Test Motorbike' : 'Test Visitor',
            'harga' => $price, 'kategori' => $category, 'status' => 'aktif',
        ]);
    }

    public function test_online_booking_payment_approval_ticket_and_gate_form_one_complete_flow(): void
    {
        Mail::fake();
        Storage::fake('public');
        config(['payment.methods' => [['id' => 'BCA']]]);
        $visitor = User::factory()->create(['role' => 'wisatawan']);
        $manager = User::factory()->create(['role' => 'pengelola']);
        $person = $this->ticket();
        $vehicle = $this->ticket('kendaraan', 5000);

        $this->actingAs($visitor)->post(route('wisatawan.pemesanan.store'), [
            'tgl_kunjungan' => now()->toDateString(),
            'tickets' => [$person->id_tiket => 2, $vehicle->id_tiket => 1],
            'total_harga' => 1,
        ])->assertRedirect(route('wisatawan.pemesanan.data-diri'))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('pemesanan', 0);
        $this->assertEquals(35000, session('pemesanan_flow.total_harga'));
        $this->get(route('wisatawan.pemesanan.data-diri'))->assertOk();
        $this->post(route('wisatawan.pemesanan.store-data-diri'), [
            'nama_0' => 'Pengunjung Satu', 'gender_0' => 'L',
            'nama_1' => 'Pengunjung Dua', 'gender_1' => 'P', 'plat_2' => 'DK 1234 QA',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $booking = Pemesanan::with('detailPemesanan')->sole();
        $this->assertSame('pending', $booking->status);
        $this->assertEquals(35000, $booking->total_harga);
        $this->assertCount(3, $booking->detailPemesanan);
        $this->assertCount(3, $booking->detailPemesanan->pluck('kode_tiket')->unique());
        $this->assertSame('DK 1234 QA', $booking->detailPemesanan->last()->plat_kendaraan);
        Mail::assertSent(PemesananBerhasil::class);
        $this->get(route('wisatawan.pemesanan.riwayat'))->assertOk()->assertSee($booking->kode_booking);
        $this->post(route('wisatawan.pembayaran.store', $booking->id_pemesanan), [
            'metode' => 'BCA', 'bukti_bayar' => UploadedFile::fake()->image('bukti-uji.jpg'),
        ])->assertRedirect(route('wisatawan.pemesanan.detail', $booking->id_pemesanan))
            ->assertSessionHasNoErrors();
        $this->assertSame('pending', $booking->fresh()->pembayaran->status);
        $this->getJson(route('wisatawan.pemesanan.status', $booking->id_pemesanan))
            ->assertJsonPath('approved', false);

        $this->actingAs($manager)->post(route('pengelola.verifikasi.validasi', $booking->id_pemesanan), [
            'action' => 'valid',
        ])->assertSessionHas('success');
        $this->assertTrue($booking->fresh()->isApproved());
        Mail::assertSent(PembayaranValid::class);
        $code = $booking->detailPemesanan->first()->kode_tiket;
        $this->get(route('tiket.verifikasi', $code))->assertOk()->assertViewHas('valid', true);
        $this->post(route('pengelola.scan.gunakan'), ['kode' => $code])->assertSessionHas('success');
        $this->post(route('pengelola.scan.gunakan'), ['kode' => $code])->assertSessionHas('error');
        $this->post(route('pengelola.scan.checkout'), ['kode' => $code])->assertSessionHas('success');
        $detail = DetailPemesanan::where('kode_tiket', $code)->sole();
        $this->assertSame('digunakan', $detail->status_tiket);
        $this->assertNotNull($detail->check_in_at);
        $this->assertNotNull($detail->check_out_at);
        $this->actingAs($visitor)->get(route('wisatawan.pemesanan.detail', $booking->id_pemesanan))
            ->assertOk()->assertSee($code)->assertSee('window.print()', false);
    }

    public function test_online_flow_requires_current_date_nonzero_tickets_and_visitor_identity(): void
    {
        $visitor = User::factory()->create(['role' => 'wisatawan']);
        $ticket = $this->ticket();
        $this->actingAs($visitor)->post(route('wisatawan.pemesanan.store'), [
            'tgl_kunjungan' => now()->subDay()->toDateString(), 'tickets' => [$ticket->id_tiket => 1],
        ])->assertSessionHasErrors('tgl_kunjungan');
        $this->post(route('wisatawan.pemesanan.store'), [
            'tgl_kunjungan' => now()->toDateString(), 'tickets' => [$ticket->id_tiket => 0],
        ])->assertSessionHasErrors('tickets');
        $this->post(route('wisatawan.pemesanan.store'), [
            'tgl_kunjungan' => now()->toDateString(), 'tickets' => [$ticket->id_tiket => 1],
        ])->assertSessionHasNoErrors();
        $this->post(route('wisatawan.pemesanan.store-data-diri'), [])
            ->assertSessionHasErrors(['nama_0', 'gender_0']);
        $this->assertDatabaseCount('pemesanan', 0);
    }

    public function test_offline_counter_creates_paid_booking_and_filters_reports(): void
    {
        $manager = User::factory()->create(['role' => 'pengelola']);
        $ticket = $this->ticket();
        $this->actingAs($manager)->post(route('pengelola.pemesanan-offline.store'), [
            'tgl_kunjungan' => now()->toDateString(), 'tickets' => [$ticket->id_tiket => 1],
        ])->assertRedirect(route('pengelola.pemesanan-offline.data-diri'));
        $this->get(route('pengelola.pemesanan-offline.data-diri'))->assertOk();
        $this->post(route('pengelola.pemesanan-offline.store-data-diri'), [
            'nama_0' => 'Pengunjung Loket', 'gender_0' => 'P', 'metode_bayar' => 'Tunai',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $booking = Pemesanan::sole();
        $this->assertStringStartsWith('BJ-OFF-', $booking->kode_booking);
        $this->assertSame($manager->id, $booking->id_user);
        $this->assertTrue($booking->isApproved());
        $this->assertNull($booking->pembayaran->bukti_bayar);
        $this->get(route('pengelola.pemesanan-offline.sukses', $booking->id_pemesanan))->assertOk();
        $this->get(route('pengelola.pemesanan-offline.riwayat'))->assertOk()->assertSee($booking->kode_booking);
        $period = ['periode_awal' => now()->toDateString(), 'periode_akhir' => now()->toDateString()];
        foreach (['pengelola.laporan.kunjungan', 'pengelola.laporan.transaksi'] as $report) {
            $this->get(route($report, $period + ['tipe' => 'offline']))->assertOk()
                ->assertSee($booking->kode_booking);
            $this->get(route($report, $period + ['tipe' => 'online']))->assertOk()
                ->assertDontSee($booking->kode_booking);
        }
    }

    public function test_manager_can_create_update_disable_and_soft_delete_a_ticket(): void
    {
        $manager = User::factory()->create(['role' => 'pengelola']);
        $data = ['nama_tiket' => 'Tiket Dokumentasi', 'nama_tiket_en' => 'Documentation Ticket',
            'harga' => 15000, 'kategori' => 'perorangan', 'status' => 'aktif'];
        $this->actingAs($manager)->post(route('pengelola.tiket.store'), $data)
            ->assertSessionHasNoErrors()->assertRedirect(route('pengelola.tiket.index'));
        $ticket = Tiket::sole();
        $this->get(route('pengelola.tiket.edit', $ticket->id_tiket))->assertOk();
        $this->put(route('pengelola.tiket.update', $ticket->id_tiket), array_replace($data, [
            'harga' => 20000, 'status' => 'nonaktif',
        ]))->assertSessionHasNoErrors();
        $this->assertEquals(20000, $ticket->fresh()->harga);
        $visitor = User::factory()->create(['role' => 'wisatawan']);
        $this->actingAs($visitor)->get(route('tiket.detail', $ticket->id_tiket))->assertNotFound();
        $this->actingAs($manager)->delete(route('pengelola.tiket.destroy', $ticket->id_tiket))->assertRedirect();
        $this->assertSoftDeleted('tiket', ['id_tiket' => $ticket->id_tiket]);
    }

    public function test_manager_can_upload_filter_and_delete_gallery_photo(): void
    {
        Storage::fake('local');
        $manager = User::factory()->create(['role' => 'pengelola']);
        $this->actingAs($manager)->post(route('pengelola.galeri.store'), [
            'file' => UploadedFile::fake()->image('restoran-uji.jpg'), 'tipe' => 'restoran',
            'keterangan' => 'Foto restoran pengujian', 'keterangan_en' => 'Test restaurant photo',
        ])->assertSessionHasNoErrors()->assertRedirect(route('pengelola.galeri.index'));
        $photo = Galeri::sole();
        Storage::assertExists('public/'.$photo->file);
        $this->get(route('pengelola.galeri.index', ['tipe' => 'restoran']))->assertOk()
            ->assertSee('Foto restoran pengujian');
        $this->get(route('pengelola.galeri.index', ['tipe' => 'wisata']))->assertOk()
            ->assertDontSee('Foto restoran pengujian');
        $path = 'public/'.$photo->file;
        $this->delete(route('pengelola.galeri.destroy', $photo->id_galeri))->assertRedirect();
        $this->assertSoftDeleted('galeri', ['id_galeri' => $photo->id_galeri]);
        Storage::assertMissing($path);
    }

    public function test_visitor_cannot_read_or_upload_to_another_visitors_booking(): void
    {
        $owner = User::factory()->create(['role' => 'wisatawan']);
        $other = User::factory()->create(['role' => 'wisatawan']);
        $ticket = $this->ticket();
        $booking = Pemesanan::create([
            'id_user' => $owner->id, 'id_tiket' => $ticket->id_tiket,
            'tgl_kunjungan' => now()->toDateString(), 'jumlah' => 1, 'total_harga' => 15000,
            'status' => 'pending', 'kode_booking' => 'BJ-OWNER-TEST',
        ]);
        $this->actingAs($other);
        foreach (['wisatawan.pemesanan.detail', 'wisatawan.pemesanan.sukses',
            'wisatawan.pembayaran.create', 'wisatawan.pemesanan.status'] as $route) {
            $this->get(route($route, $booking->id_pemesanan))->assertNotFound();
        }
        $this->post(route('wisatawan.pembayaran.store', $booking->id_pemesanan), [])->assertNotFound();
        $this->assertDatabaseCount('pembayaran', 0);
    }

    public function test_language_switch_and_public_verification_have_no_checkin_side_effects(): void
    {
        $this->from('/')->get(route('lang.switch', 'en'))->assertRedirect('/')->assertSessionHas('locale', 'en');
        $this->get('/')->assertOk()->assertSee('Choose your starting point.');
        $this->get(route('lang.switch', 'id'))->assertSessionHas('locale', 'id');
        $this->get(route('lang.switch', 'invalid'))->assertSessionHas('locale', 'id');
        $this->get(route('tiket.verifikasi', 'BJ-NOT-FOUND'))->assertOk()->assertViewHas('found', false);
        $this->assertDatabaseCount('detail_pemesanan', 0);
    }

    public function test_user_search_lists_visitors_and_does_not_offer_manager_accounts(): void
    {
        $manager = User::factory()->create(['role' => 'pengelola']);
        $visitor = User::factory()->create(['role' => 'wisatawan', 'username' => 'dokumentasi_uji']);
        $unrelated = User::factory()->create(['role' => 'wisatawan']);
        $this->actingAs($manager)->get(route('pengelola.user.index', ['search' => 'dokumentasi_uji']))
            ->assertOk()->assertViewHas('users', fn ($users) => $users->count() === 1 && $users->first()->id === $visitor->id);
        $this->get(route('pengelola.user.index'))->assertViewHas('users', fn ($users) => $users->count() === 2 && ! $users->contains('id', $manager->id));
    }

    public function test_auto_checkout_command_only_marks_visits_older_than_twenty_four_hours(): void
    {
        $manager = User::factory()->create(['role' => 'pengelola']);
        $ticket = $this->ticket();
        $booking = Pemesanan::create([
            'id_user' => $manager->id, 'id_tiket' => $ticket->id_tiket,
            'tgl_kunjungan' => now()->toDateString(), 'jumlah' => 2, 'total_harga' => 30000,
            'status' => 'selesai', 'kode_booking' => 'BJ-AUTO-QA',
        ]);
        foreach (['BJ-OLD-QA' => 25, 'BJ-NEW-QA' => 1] as $code => $hours) {
            DetailPemesanan::create([
                'id_pemesanan' => $booking->id_pemesanan, 'id_tiket' => $ticket->id_tiket,
                'kode_tiket' => $code, 'nama_tiket' => $ticket->nama_tiket, 'harga' => 15000,
                'status_tiket' => 'digunakan', 'check_in_at' => now()->subHours($hours),
            ]);
        }
        $this->artisan('tiket:auto-checkout')->expectsOutput('Auto check-out: 1 tiket diperbarui.')->assertSuccessful();
        $this->assertNotNull(DetailPemesanan::where('kode_tiket', 'BJ-OLD-QA')->sole()->check_out_at);
        $this->assertNull(DetailPemesanan::where('kode_tiket', 'BJ-NEW-QA')->sole()->check_out_at);
    }
}
