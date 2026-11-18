<?php

namespace App\Services;

use App\Models\Pemesanan;
use Carbon\Carbon;

/**
 * Pembungkus tipis di atas BookingLifecycle: satu-satunya sumber aturan
 * kedaluwarsa adalah kolom expires_at / tgl_kunjungan (diatur pengelola).
 */
class BookingExpirationService
{
    /**
     * Menutup pesanan pending yang lewat tenggat dan tidak sedang menunggu verifikasi bukti.
     *
     * @return int Jumlah pesanan yang ditutup karena kedaluwarsa
     */
    public function expire(?int $userId = null): int
    {
        $count = 0;

        Pemesanan::where('status', 'pending')
            ->when($userId, fn ($q) => $q->where('id_user', $userId))
            ->where(fn ($q) => $q->where('expires_at', '<=', now())
                ->orWhere('tgl_kunjungan', '<', now()->toDateString()))
            ->chunkById(100, function ($bookings) use (&$count) {
                foreach ($bookings as $booking) {
                    $count += app(BookingLifecycle::class)->refresh($booking)->closed_reason === 'expired' ? 1 : 0;
                }
            }, 'id_pemesanan');

        return $count;
    }

    /** Batas waktu bayar untuk ditampilkan ke wisatawan. */
    public function getDeadline(Pemesanan $pemesanan): Carbon
    {
        if ($pemesanan->expires_at) {
            return $pemesanan->expires_at->copy();
        }

        $minutes = (int) app(DestinationSettings::class)->values()['payment_minutes'];

        return $pemesanan->created_at->copy()->addMinutes($minutes);
    }
}
