<?php

namespace App\Services;

use App\Models\Pemesanan;
use App\Models\VisitCapacity;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VisitQuota
{
    // Every booking/payment/expiry path locks date before booking, including unlimited dates.
    public function lock(string $date): VisitCapacity
    {
        DB::table('visit_capacities')->insertOrIgnore([
            'visit_date' => $date, 'capacity' => null, 'created_at' => now(), 'updated_at' => now(),
        ]);

        return VisitCapacity::whereKey($date)->lockForUpdate()->firstOrFail();
    }

    public function used(string $date): int
    {
        return (int) Pemesanan::where('tgl_kunjungan', $date)->holdingCapacity()->sum('jumlah');
    }

    public function availability(string $date): array
    {
        $capacity = VisitCapacity::find($date)?->capacity;
        $used = $this->used($date);

        return ['date' => $date, 'capacity' => $capacity, 'used' => $used,
            'remaining' => $capacity === null ? null : max(0, $capacity - $used)];
    }

    public function assertAvailable(VisitCapacity $quota, int $count): void
    {
        if ($quota->capacity !== null && $this->used($quota->visit_date) + $count > $quota->capacity) {
            throw ValidationException::withMessages(['tickets' => __('operations.quota_full')]);
        }
    }
}
