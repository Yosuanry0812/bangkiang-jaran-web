<?php

namespace App\Console\Commands;

use App\Models\Pemesanan;
use App\Services\BookingLifecycle;
use Illuminate\Console\Command;

class ExpireBookings extends Command
{
    protected $signature = 'pemesanan:expire';

    protected $description = 'Tutup pesanan lewat tenggat yang tidak sedang menunggu pemeriksaan bukti';

    public function handle(BookingLifecycle $lifecycle): int
    {
        $count = 0;
        Pemesanan::where('status', 'pending')->where(fn ($q) => $q->where('expires_at', '<=', now())
            ->orWhere('tgl_kunjungan', '<', now()->toDateString()))->chunkById(100, function ($bookings) use ($lifecycle, &$count) {
                foreach ($bookings as $booking) {
                    $count += $lifecycle->refresh($booking)->closed_reason === 'expired' ? 1 : 0;
                }
            }, 'id_pemesanan');
        $this->info('Pesanan kedaluwarsa: '.$count);

        return self::SUCCESS;
    }
}
