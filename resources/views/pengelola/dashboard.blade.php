@extends('pengelola.layouts.admin')
@section('title', __('ui.t_dashboard'))
@section('page_title', 'Dashboard')
@section('content')
<section class="admin-welcome">
    <div>
        <time datetime="{{ now()->toDateString() }}"><span class="material-symbols-outlined" aria-hidden="true">calendar_today</span>{{ now()->locale(app()->getLocale())->translatedFormat('l, d F Y') }}</time>
        <h1>{{ __('panel.selamat_datang_kembali_20ea') }}</h1>
        <p>{{ __('panel.pantau_kunjungan_dan_kelola_e93b') }}</p>
    </div>
    <div class="admin-welcome-actions">
        <a href="{{ route('pengelola.pemesanan-offline.create') }}" class="admin-btn admin-btn--gold"><span class="material-symbols-outlined" aria-hidden="true">add</span>{{ __('panel.pemesanan_offline_13e7') }}</a>
        <small>{{ trans_choice('ui.scanned_today', $scanHariIni ?? 0) }}</small>
    </div>
</section>
<dl class="admin-metrics" aria-label="{{ __('panel.ringkasan_operasional_34db') }}">
    <div><dt><span class="material-symbols-outlined" aria-hidden="true">groups</span>{{ __('operations.paid_tickets') }}</dt><dd>{{ $pengunjungHariIni ?? 0 }}</dd><small>{{ __('panel.tanggal_kunjungan_hari_ini_5aeb') }}</small></div>
    <div><dt><span class="material-symbols-outlined" aria-hidden="true">payments</span>{{ __('panel.pendapatan_bulan_ini_82a9') }}</dt><dd>Rp{{ number_format($pendapatanBulanIni ?? 0, 0, ',', '.') }}</dd><small>{{ __('panel.pembayaran_yang_sudah_valid_097d') }}</small></div>
    <div><dt><span class="material-symbols-outlined" aria-hidden="true">pending_actions</span>{{ __('panel.perlu_verifikasi_a762') }}</dt><dd>{{ $pemesananPending ?? 0 }}</dd><small><a href="{{ route('pengelola.verifikasi.index') }}" class="underline underline-offset-4">{{ __('panel.tinjau_pembayaran_0151') }}</a></small></div>
    <div><dt><span class="material-symbols-outlined" aria-hidden="true">person</span>{{ __('panel.wisatawan_terdaftar_b82b') }}</dt><dd>{{ $totalWisatawan ?? 0 }}</dd><small>{{ __('panel.total_akun_wisatawan_c5c1') }}</small></div>
</dl>
<dl class="bj-operational-metrics" aria-label="{{ __('operations.today_visits') }}">
<div><dt>{{ __('operations.ordered_tickets') }}</dt><dd>{{ $terpesanHariIni }}</dd></div>
<div><dt>{{ __('operations.arrivals') }}</dt><dd>{{ $scanHariIni }}</dd></div>
<div><dt>{{ __('operations.departures') }}</dt><dd>{{ $keluarHariIni }}</dd></div>
<div><dt><a href="{{ route('pengelola.onsite.index') }}">{{ __('operations.onsite') }}</a></dt><dd>{{ $diLokasi }}</dd></div>
</dl>
<div class="admin-dashboard-grid">
    <section class="admin-panel">
        <div class="admin-panel-head"><div><h2 id="recent-bookings-title">{{ __('panel.pemesanan_terbaru_e922') }}</h2><p>{{ __('panel.lima_transaksi_terakhir_yang_4d32') }}</p></div><a class="admin-text-action" href="{{ route('pengelola.pesanan.index') }}">{{ __('panel.lihat_pemesanan_bf7e') }}<span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a></div>
        <div class="admin-table-wrap">
            <table class="admin-table admin-table--responsive" aria-labelledby="recent-bookings-title">
                <thead><tr><th scope="col">{{ __('panel.pemesanan_f431') }}</th><th scope="col">{{ __('panel.tiket_f17c') }}</th><th scope="col">{{ __('panel.status_ec53') }}</th><th scope="col">{{ __('panel.tanggal_6b26') }}</th></tr></thead>
                <tbody>
                @forelse($pemesananTerbaru ?? [] as $p)
                    @php
                        $statusClass = match($p->status) { 'selesai' => '', 'pending' => 'admin-status--pending', 'dibatalkan' => 'admin-status--danger', default => 'admin-status--neutral' };
                        $summary = $p->detailPemesanan->groupBy('nama_tiket')->map(fn($g) => $g->count() . '× ' . $g->first()->nama_tiket)->implode(', ');
                    @endphp
                    <tr>
                        <td data-label="{{ __('panel.pemesanan_f431') }}"><code>{{ $p->kode_booking }}</code><small>{{ $p->user->name ?? __('ui.walk_in') }}</small></td>
                        <td data-label="{{ __('panel.tiket_f17c') }}">{{ $summary ?: ($p->tiket->nama_tiket ?? '—') }}</td>
                        <td data-label="{{ __('panel.status_ec53') }}"><span class="admin-status {{ $statusClass }}">{{ \App\Support\Label::status($p->status) }}</span></td>
                        <td data-label="{{ __('panel.tanggal_6b26') }}">{{ $p->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4"><div class="admin-empty"><span class="material-symbols-outlined" aria-hidden="true">receipt_long</span><h3>{{ __('panel.belum_ada_pemesanan_1ad1') }}</h3><p>{{ __('panel.transaksi_terbaru_akan_tampi_e36c') }}</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <section class="admin-panel">
        <div class="admin-panel-head"><div><h2>{{ __('panel.komposisi_tiket_c000') }}</h2><p>{{ __('panel.dari_pemesanan_yang_selesai_829b') }}</p></div><span class="material-symbols-outlined" aria-hidden="true">donut_large</span></div>
        @php
            $chartTotal = array_sum($grafikData ?? []);
            $chartPalette = ['#005344', '#bd965f', '#74916e', '#a9b8a0', '#576e60', '#d5c4a6'];
            $chartOffset = 0;
        @endphp
        @if($chartTotal > 0)
            <div class="admin-donut" role="img" aria-label="{{ __('ui.chart_label', ['total' => $chartTotal]) }}">
                <svg viewBox="0 0 120 120" aria-hidden="true">
                    @foreach($grafikData as $value)
                        @php $percent = $value / $chartTotal * 100; @endphp
                        <circle cx="60" cy="60" r="49" fill="none" pathLength="100" stroke="{{ $chartPalette[$loop->index % count($chartPalette)] }}" stroke-width="14" stroke-dasharray="{{ $percent }} {{ 100 - $percent }}" stroke-dashoffset="{{ -$chartOffset }}" />
                        @php $chartOffset += $percent; @endphp
                    @endforeach
                </svg>
                <div class="admin-donut-label">{{ $chartTotal }}<small>{{ __('panel.tiket_terjual_014f') }}</small></div>
            </div>
            <ul class="admin-chart-legend">
                @foreach($grafikLabels as $label)
                <li><i style="background:{{ $chartPalette[$loop->index % count($chartPalette)] }}" aria-hidden="true"></i><span>{{ $label ?: __('panel.tiket_f17c') }}</span><strong>{{ $grafikData[$loop->index] }}</strong></li>
                @endforeach
            </ul>
        @else
            <div class="admin-empty"><span class="material-symbols-outlined" aria-hidden="true">donut_large</span><h3>{{ __('panel.belum_ada_data_tiket_ea90') }}</h3><p>{{ __('panel.komposisi_terisi_setelah_ada_4230') }}</p></div>
        @endif
    </section>
</div>
<nav class="admin-shortcuts" aria-label="{{ __('panel.akses_operasional_f71c') }}">
    <a href="{{ route('pengelola.scan.index') }}"><span class="material-symbols-outlined" aria-hidden="true">qr_code_scanner</span><span><strong>{{ __('panel.scan_tiket_4ee9') }}</strong><small>{{ __('panel.check_in_dan_check_01a8') }}</small></span><span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
    <a href="{{ route('pengelola.verifikasi.index') }}"><span class="material-symbols-outlined" aria-hidden="true">verified_user</span><span><strong>{{ __('panel.verifikasi_pembayaran_3d3d') }}</strong><small>{{ trans_choice('ui.bookings_pending', $pemesananPending ?? 0) }}</small></span><span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
    <a href="{{ route('pengelola.laporan.index') }}"><span class="material-symbols-outlined" aria-hidden="true">monitoring</span><span><strong>{{ __('panel.laporan_operasional_8322') }}</strong><small>{{ __('panel.kunjungan_dan_transaksi_wisa_672b') }}</small></span><span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
</nav>
@endsection
