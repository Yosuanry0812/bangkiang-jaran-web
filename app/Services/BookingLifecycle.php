<?php

namespace App\Services;

use App\Models\Pemesanan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingLifecycle
{
    public function expire(Pemesanan $booking): bool
    {
        if (! $booking->isOverdue() || $booking->pembayaran?->status === 'pending') {
            return false;
        }
        $booking->update(['status' => 'dibatalkan', 'closed_reason' => 'expired']);
        $booking->recordEvent('expired');

        return true;
    }

    public function refresh(Pemesanan $booking): Pemesanan
    {
        DB::transaction(function () use ($booking) {
            $locked = Pemesanan::whereKey($booking->getKey())->lockForUpdate()->firstOrFail();
            $this->expire($locked);
        });

        return $booking->refresh();
    }

    public function cancel(Pemesanan $booking): void
    {
        DB::transaction(function () use ($booking) {
            $locked = Pemesanan::whereKey($booking->getKey())->lockForUpdate()->firstOrFail();
            if ($this->expire($locked)) {
                return;
            }
            if (! $locked->canCancel()) {
                throw ValidationException::withMessages(['booking' => __('operations.cannot_cancel')]);
            }
            $locked->update(['status' => 'dibatalkan', 'closed_reason' => 'visitor_cancelled']);
            $locked->recordEvent('cancelled');
        });
    }
}
