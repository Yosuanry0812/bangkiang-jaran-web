<?php

namespace App\Services;

use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentProofMigration
{
    public function move(Pembayaran $payment): bool
    {
        $old = null;
        $moved = DB::transaction(function () use ($payment, &$old) {
            $locked = Pembayaran::whereKey($payment->getKey())->lockForUpdate()->firstOrFail();
            if (! $locked->bukti_bayar) {
                return false;
            }
            $old = $locked->bukti_bayar;
            if (! str_starts_with($old, 'bukti/') || str_contains($old, '..')) {
                throw new \RuntimeException('Path bukti tidak diizinkan.');
            }
            if ($locked->proof_disk === 'private') {
                if (! Storage::disk('public')->exists($old)) {
                    $old = null;

                    return false;
                }
                if (! Storage::disk('private')->exists($old)
                    || hash_file('sha256', Storage::disk('public')->path($old)) !== hash_file('sha256', Storage::disk('private')->path($old))) {
                    throw new \RuntimeException('Salinan lama tidak cocok dengan bukti privat; berkas tidak dihapus.');
                }

                return false;
            }
            if (! Storage::disk('public')->exists($old)) {
                throw new \RuntimeException('Berkas bukti lama tidak ditemukan; metadata tidak diubah.');
            }
            // Keep the same relative path so an interrupted cleanup can be resumed safely.
            $stream = Storage::disk('public')->readStream($old);
            try {
                Storage::disk('private')->put($old, $stream);
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
            if (hash_file('sha256', Storage::disk('public')->path($old)) !== hash_file('sha256', Storage::disk('private')->path($old))) {
                throw new \RuntimeException('Checksum bukti berbeda; berkas publik tidak dihapus.');
            }
            $locked->update(['proof_disk' => 'private']);

            return true;
        });
        if ($old) {
            Storage::disk('public')->delete($old);
        }

        return $moved;
    }
}
