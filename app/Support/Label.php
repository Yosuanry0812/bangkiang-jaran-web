<?php

namespace App\Support;

use Illuminate\Support\Facades\Lang;

class Label
{
    /** Label status (pemesanan, pembayaran, tiket) sesuai bahasa aktif. */
    public static function status(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $key = 'statuses.'.strtolower($value);

        return Lang::has($key) ? __($key) : ucfirst($value);
    }

    /** Label metode pembayaran; nama bank/dompet digital tidak diterjemahkan. */
    public static function method(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return strtolower($value) === 'tunai' ? __('ui.pay_cash') : $value;
    }
}
