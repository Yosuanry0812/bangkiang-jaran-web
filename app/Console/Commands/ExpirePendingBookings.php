<?php

namespace App\Console\Commands;

use App\Services\BookingExpirationService;
use Illuminate\Console\Command;

class ExpirePendingBookings extends Command
{
    protected $signature = 'tiket:expire-pending';
    protected $description = 'Otomatis membatalkan pesanan pending yang belum dibayar dan melewati batas waktu';

    public function handle(BookingExpirationService $service): int
    {
        $count = $service->expire();
        $this->info("Otomatis kedaluwarsa: {$count} pesanan diperbarui menjadi dibatalkan.");

        return Command::SUCCESS;
    }
}
