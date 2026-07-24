<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.transaction_report_title') }} - Bangkiang Jaran Waterfall</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background: #0d9488; color: white; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 18px; margin: 0 0 5px; }
        .header p { font-size: 12px; margin: 2px 0; color: #555; }
        .footer { margin-top: 20px; font-size: 11px; color: #777; text-align: right; }
        .grand-total { font-weight: bold; font-size: 14px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('messages.transaction_report_title') }}</h1>
        <p>Bangkiang Jaran Waterfall</p>
        <p>{{ __('messages.period') }} {{ request('periode_awal') ?? __('messages.all') }} - {{ request('periode_akhir') ?? __('messages.all') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>{{ __('messages.th_booking_code') }}</th>
                <th>{{ __('messages.th_user') }}</th>
                <th>{{ __('messages.th_ticket') }}</th>
                <th>{{ __('messages.payment_date') }}</th>
                <th>{{ __('messages.method_label') }}</th>
                <th>{{ __('messages.th_total') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data ?? [] as $i => $t)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $t->pemesanan->kode_booking ?? '-' }}</td>
                <td>{{ $t->pemesanan->user->name ?? '-' }}</td>
                <td>
                    @php
                        $dt = $t->pemesanan->detailPemesanan;
                        $tSum = $dt ? $dt->groupBy('nama_tiket')->map(fn($g) => $g->count() . '× ' . $g->first()->nama_tiket)->implode(', ') : '';
                    @endphp
                    {{ $tSum ?: ($t->pemesanan->tiket->nama_tiket ?? '-') }}
                </td>
                <td>{{ $t->created_at ? \Carbon\Carbon::parse($t->created_at)->format('d/m/Y') : '-' }}</td>
                <td>{{ $t->metode ?? '-' }}</td>
                <td>Rp {{ number_format($t->total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center">{{ __('messages.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="grand-total">{{ __('messages.grand_total_label') }}: Rp {{ number_format($total ?? 0, 0, ',', '.') }}</div>

    <div class="footer">
        {{ __('messages.printed_on') }} {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
