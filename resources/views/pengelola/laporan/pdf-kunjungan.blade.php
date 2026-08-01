<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.visit_report_title') }} - Bangkiang Jaran Waterfall</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background: #0d9488; color: white; }
        .header { text-align: center; margin-bottom: 10px; }
        .header h1 { font-size: 18px; margin: 0 0 5px; }
        .header p { font-size: 12px; margin: 2px 0; color: #555; }
        .info-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .info-table td { border: 1px solid #999; padding: 5px 8px; font-size: 11px; }
        .info-table td.label { background: #f0fdfa; font-weight: bold; width: 20%; }
        .footer { margin-top: 20px; font-size: 11px; color: #777; text-align: right; }
        .total { font-weight: bold; font-size: 14px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('messages.visit_report_title') }}</h1>
        <p>Bangkiang Jaran Waterfall</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">{{ __('messages.report_type') }}</td>
            <td>{{ $tipe === 'offline' ? __('messages.offline') : ($tipe === 'online' ? __('messages.online') : __('messages.all')) }}</td>
            <td class="label">{{ __('messages.period') }}</td>
            <td>{{ $periode_awal }} - {{ $periode_akhir }}</td>
            <td class="label">{{ __('messages.printed_on') }}</td>
            <td>{{ now()->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>{{ __('messages.th_booking_code') }}</th>
                <th>{{ __('messages.th_type') }}</th>
                <th>{{ __('messages.th_user') }}</th>
                <th>{{ __('messages.th_ticket') }}</th>
                <th>{{ __('messages.th_date') }}</th>
                <th>{{ __('messages.quantity_label') }}</th>
                <th>{{ __('messages.th_total') }}</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>{{ __('messages.th_status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data ?? [] as $i => $k)
            @php
                $checkIns = $k->detailPemesanan->whereNotNull('check_in_at');
                $checkOuts = $k->detailPemesanan->whereNotNull('check_out_at');
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $k->kode_booking }}</td>
                <td>{{ str_starts_with($k->kode_booking, 'BJ-OFF-') ? __('messages.offline') : __('messages.online') }}</td>
                <td>{{ $k->user->name ?? '-' }}</td>
                <td>
                    @php
                        $tSum = $k->detailPemesanan->groupBy('nama_tiket')->map(fn($g) => $g->count() . '× ' . $g->first()->nama_tiket)->implode(', ');
                    @endphp
                    {{ $tSum ?: ($k->tiket->nama_tiket ?? '-') }}
                </td>
                <td>{{ $k->tgl_kunjungan ? \Carbon\Carbon::parse($k->tgl_kunjungan)->format('d/m/Y') : '-' }}</td>
                <td>{{ $k->jumlah }}</td>
                <td>Rp {{ number_format($k->total_harga, 0, ',', '.') }}</td>
                <td>{{ $checkIns->count() > 0 ? $checkIns->count() . ' org ' . ($checkIns->first()->check_in_at ? \Carbon\Carbon::parse($checkIns->first()->check_in_at)->format('H:i') : '-') : '-' }}</td>
                <td>{{ $checkOuts->count() > 0 ? $checkOuts->count() . ' org ' . ($checkOuts->first()->check_out_at ? \Carbon\Carbon::parse($checkOuts->first()->check_out_at)->format('H:i') : '-') : '-' }}</td>
                <td>{{ ucfirst($k->status) }}</td>
            </tr>
            @empty
            <tr><td colspan="11" style="text-align:center">{{ __('messages.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>

    @php
        $totalCheckIn = 0;
        $totalCheckOut = 0;
        foreach ($data ?? [] as $k) {
            $totalCheckIn += $k->detailPemesanan->whereNotNull('check_in_at')->count();
            $totalCheckOut += $k->detailPemesanan->whereNotNull('check_out_at')->count();
        }
    @endphp
    <div class="total">{{ __('messages.total_visitors_label') }}: {{ $total ?? 0 }}</div>
    <div style="margin-top:5px;font-size:12px;color:#555;">
        <span>Jumlah Masuk: {{ $totalCheckIn }}</span> &nbsp;|&nbsp;
        <span>Jumlah Keluar: {{ $totalCheckOut }}</span>
    </div>

    <div class="footer">
        {{ __('messages.printed_on') }} {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
