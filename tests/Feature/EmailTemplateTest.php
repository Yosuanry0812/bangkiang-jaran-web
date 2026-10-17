<?php

namespace Tests\Feature;

use App\Models\DetailPemesanan;
use App\Models\Pemesanan;
use App\Models\User;
use Tests\TestCase;

class EmailTemplateTest extends TestCase
{
    public function test_all_customer_email_templates_render(): void
    {
        $user = User::factory()->make(['name' => 'Ayu']);
        $booking = new Pemesanan([
            'kode_booking' => 'BJ-EMAIL-001',
            'tgl_kunjungan' => '2026-10-20',
            'total_harga' => 20000,
        ]);
        $booking->id_pemesanan = 1;
        $booking->setRelation('user', $user);
        $detail = new DetailPemesanan();
        $detail->nama_tiket = 'Tiket Masuk';
        $detail->nama_pengunjung = 'Ayu';
        $detail->kode_tiket = 'BJ-TK-001';
        $booking->setRelation('detailPemesanan', collect([$detail]));

        $this->assertStringContainsString('Selamat datang, Ayu', view('emails.welcome', ['user' => $user])->render());

        foreach (['pemesanan-berhasil', 'pembayaran-valid', 'pembayaran-ditolak'] as $template) {
            $html = view('emails.'.$template, ['pemesanan' => $booking])->render();
            $this->assertStringContainsString('BJ-EMAIL-001', $html);
            $this->assertStringContainsString('Bangkiang Jaran', $html);
        }
    }
}
