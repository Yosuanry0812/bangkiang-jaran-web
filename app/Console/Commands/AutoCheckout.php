<?php

namespace App\Console\Commands;

use App\Models\DetailPemesanan;
use Illuminate\Console\Command;

class AutoCheckout extends Command
{
    protected $signature = 'tiket:auto-checkout';
    protected $description = 'Auto check-out tiket yg sudah >24 jam sejak check-in';

    public function handle()
    {
        $batas = now()->subHours(24);

        $updated = DetailPemesanan::whereNotNull('check_in_at')
            ->whereNull('check_out_at')
            ->where('check_in_at', '<', $batas)
            ->update(['check_out_at' => now()]);

        $this->info("Auto check-out: {$updated} tiket diperbarui.");

        return Command::SUCCESS;
    }
}
