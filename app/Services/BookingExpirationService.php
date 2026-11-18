<?php

namespace App\Services;

use App\Helpers\ActivityLogger;
use App\Models\DetailPemesanan;
use App\Models\Pemesanan;
use Illuminate\Support\Facades\DB;

class BookingExpirationService
{
    /**
     * Membatalkan otomatis pesanan pending yang belum dibayar dan telah melewati batas waktu atau tanggal kunjungan.
     *
     * @param int|null $userId Batasi pemeriksaan hanya untuk user tertentu (opsional)
     * @return int Jumlah pesanan yang dibatalkan karena kedaluwarsa
     */
    public function expire(?int $userId = null): int
    {
        $expiryHours = (int) config('payment.booking_expiry_hours', 2);
        $threshold = now()->subHours($expiryHours);
        $today = now()->toDateString();

        $query = Pemesanan::with('pembayaran')
            ->where('status', 'pending')
            ->where(function ($q) use ($threshold, $today) {
                // 1. Belum unggah bukti bayar sama sekali dan melewati batas waktu jam pembuatan
                $q->where(function ($sub) use ($threshold) {
                    $sub->doesntHave('pembayaran')
                        ->where('created_at', '<', $threshold);
                })
                // 2. Bukti bayar ditolak dan tidak diunggah ulang sampai melewati batas waktu jam sejak ditolak
                ->orWhere(function ($sub) use ($threshold) {
                    $sub->whereHas('pembayaran', function ($p) use ($threshold) {
                        $p->where('status', 'ditolak')
                          ->where('updated_at', '<', $threshold);
                    });
                })
                // 3. Tanggal kunjungan sudah lewat hari ini dan pesanan belum lunas
                ->orWhere(function ($sub) use ($today) {
                    $sub->where('tgl_kunjungan', '<', $today);
                });
            });

        if ($userId) {
            $query->where('id_user', $userId);
        }

        $expiredBookings = $query->get();
        $count = 0;

        foreach ($expiredBookings as $booking) {
            DB::transaction(function () use ($booking, &$count) {
                $booking->update(['status' => 'dibatalkan']);

                DetailPemesanan::where('id_pemesanan', $booking->id_pemesanan)
                    ->update(['status_tiket' => 'kadaluarsa']);

                ActivityLogger::log(
                    'Pesanan kedaluwarsa',
                    'Pesanan ' . $booking->kode_booking . ' otomatis dibatalkan karena melewati batas waktu pembayaran.',
                    $booking->id_user
                );

                $count++;
            });
        }

        return $count;
    }

    /**
     * Menghitung batas waktu (deadline) pembayaran untuk pesanan tertentu.
     *
     * @param Pemesanan $pemesanan
     * @return \Carbon\Carbon
     */
    public function getDeadline(Pemesanan $pemesanan): \Carbon\Carbon
    {
        $expiryHours = (int) config('payment.booking_expiry_hours', 2);

        if ($pemesanan->expires_at) {
            return $pemesanan->expires_at->copy();
        }

        // Jika pembayaran pernah ditolak, deadline dihitung dari saat penolakan
        if ($pemesanan->pembayaran && $pemesanan->pembayaran->status === 'ditolak') {
            return $pemesanan->pembayaran->updated_at->copy()->addHours($expiryHours);
        }

        return $pemesanan->created_at->copy()->addHours($expiryHours);
    }
}
