<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.transaction_report_title') }} - Bangkiang Jaran Waterfall</title>
    <style>@include('pengelola.laporan.pdf-style')</style>
</head>
<body>
    <div class="header">
        <h1>{{ __('messages.transaction_report_title') }}</h1>
        <p>Bangkiang Jaran · Desa Bakbakan, Gianyar</p>
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
                <td>{{ !empty($t->pemesanan->kode_booking) && str_starts_with($t->pemesanan->kode_booking, 'BJ-OFF-') ? __('messages.offline') : __('messages.online') }}</td>
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
            <tr><td colspan="8" style="text-align:center">{{ __('messages.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="grand-total">{{ __('messages.grand_total_label') }}: Rp {{ number_format($total ?? 0, 0, ',', '.') }}</div>

    <div class="footer">
        {{ __('messages.printed_on') }} {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
