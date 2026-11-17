<?php

namespace App\Console\Commands;

use App\Models\Pembayaran;
use App\Services\PaymentProofMigration;
use Illuminate\Console\Command;

class PrivatizePaymentProofs extends Command
{
    protected $signature = 'pembayaran:privatize-proofs {--apply : Pindahkan berkas dan perbarui metadata setelah cadangan diperiksa}';

    protected $description = 'Inventarisasi atau migrasikan bukti lama dari disk publik ke privat';

    public function handle(PaymentProofMigration $migration): int
    {
        $query = Pembayaran::whereNotNull('bukti_bayar');
        $this->info('Bukti publik tersisa: '.(clone $query)->where('proof_disk', 'public')->count());
        if (! $this->option('apply')) {
            return self::SUCCESS;
        }
        $errors = 0;
        $query->chunkById(100, function ($payments) use ($migration, &$errors) {
            foreach ($payments as $payment) {
                try {
                    $migration->move($payment);
                } catch (\Throwable) {
                    $errors++;
                    $this->error('Gagal migrasi ID pembayaran '.$payment->getKey().'; periksa berkas dan izin storage.');
                }
            }
        }, 'id_bayar');

        return $errors ? self::FAILURE : self::SUCCESS;
    }
}
