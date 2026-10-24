@extends('pengelola.layouts.admin')
@section('title', 'Dashboard — Bangkiang Jaran')
@section('page_title', 'Dashboard')
@section('content')
<section class="admin-welcome">
    <div>
        <time datetime="{{ now()->toDateString() }}"><span class="material-symbols-outlined" aria-hidden="true">calendar_today</span>{{ now()->locale(app()->getLocale())->translatedFormat('l, d F Y') }}</time>
        <h1>Selamat datang kembali.</h1>
        <p>Pantau kunjungan dan kelola operasional Bangkiang Jaran.</p>
    </div>
    <div class="admin-welcome-actions">
        <a href="{{ route('pengelola.pemesanan-offline.create') }}" class="admin-btn admin-btn--gold"><span class="material-symbols-outlined" aria-hidden="true">add</span>Pemesanan offline</a>
        <small>{{ $scanHariIni ?? 0 }} tiket telah dipindai hari ini</small>
    </div>
</section>
<dl class="admin-metrics" aria-label="Ringkasan operasional">
    <div><dt><span class="material-symbols-outlined" aria-hidden="true">groups</span>Pengunjung hari ini</dt><dd>{{ $pengunjungHariIni ?? 0 }}</dd><small>Orang terkonfirmasi</small></div>
    <div><dt><span class="material-symbols-outlined" aria-hidden="true">payments</span>Pendapatan bulan ini</dt><dd>Rp{{ number_format($pendapatanBulanIni ?? 0, 0, ',', '.') }}</dd><small>Pembayaran yang sudah valid</small></div>
    <div><dt><span class="material-symbols-outlined" aria-hidden="true">pending_actions</span>Perlu verifikasi</dt><dd>{{ $pemesananPending ?? 0 }}</dd><small><a href="{{ route('pengelola.verifikasi.index') }}" class="underline underline-offset-4">Tinjau pembayaran</a></small></div>
    <div><dt><span class="material-symbols-outlined" aria-hidden="true">person</span>Wisatawan terdaftar</dt><dd>{{ $totalWisatawan ?? 0 }}</dd><small>Total akun wisatawan</small></div>
</dl>
<div class="admin-dashboard-grid">
    <section class="admin-panel">
        <div class="admin-panel-head"><div><h2>Pemesanan terbaru</h2><p>Lima transaksi terakhir yang masuk</p></div><a class="admin-text-action" href="{{ route('pengelola.verifikasi.index') }}">Lihat pemesanan<span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a></div>
        <div class="admin-table-wrap">
            <table class="admin-table admin-table--responsive">
                <caption class="sr-only">Pemesanan terbaru</caption>
                <thead><tr><th scope="col">Pemesanan</th><th scope="col">Tiket</th><th scope="col">Status</th><th scope="col">Tanggal</th></tr></thead>
                <tbody>
                @forelse($pemesananTerbaru ?? [] as $p)
                    @php
                        $statusClass = match($p->status) { 'selesai' => '', 'pending' => 'admin-status--pending', 'dibatalkan' => 'admin-status--danger', default => 'admin-status--neutral' };
                        $summary = $p->detailPemesanan->groupBy('nama_tiket')->map(fn($g) => $g->count() . '× ' . $g->first()->nama_tiket)->implode(', ');
                    @endphp
                    <tr>
                        <td data-label="Pemesanan"><code>{{ $p->kode_booking }}</code><small>{{ $p->user->name ?? 'Pengunjung offline' }}</small></td>
                        <td data-label="Tiket">{{ $summary ?: ($p->tiket->nama_tiket ?? '—') }}</td>
                        <td data-label="Status"><span class="admin-status {{ $statusClass }}">{{ ucfirst($p->status) }}</span></td>
                        <td data-label="Tanggal">{{ $p->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4"><div class="admin-empty"><span class="material-symbols-outlined" aria-hidden="true">receipt_long</span><h3>Belum ada pemesanan</h3><p>Transaksi terbaru akan tampil di sini saat pemesanan masuk.</p></div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <section class="admin-panel">
        <div class="admin-panel-head"><div><h2>Komposisi tiket</h2><p>Dari pemesanan yang selesai</p></div><span class="material-symbols-outlined" aria-hidden="true">donut_large</span></div>
        @php
            $chartTotal = array_sum($grafikData ?? []);
            $chartPalette = ['#005344', '#bd965f', '#74916e', '#a9b8a0', '#576e60', '#d5c4a6'];
            $chartOffset = 0;
        @endphp
        @if($chartTotal > 0)
            <div class="admin-donut" role="img" aria-label="Komposisi {{ $chartTotal }} tiket; rincian tersedia di bawah">
                <svg viewBox="0 0 120 120" aria-hidden="true">
                    @foreach($grafikData as $value)
                        @php $percent = $value / $chartTotal * 100; @endphp
                        <circle cx="60" cy="60" r="49" fill="none" pathLength="100" stroke="{{ $chartPalette[$loop->index % count($chartPalette)] }}" stroke-width="14" stroke-dasharray="{{ $percent }} {{ 100 - $percent }}" stroke-dashoffset="{{ -$chartOffset }}" />
                        @php $chartOffset += $percent; @endphp
                    @endforeach
                </svg>
                <div class="admin-donut-label">{{ $chartTotal }}<small>Tiket terjual</small></div>
            </div>
            <ul class="admin-chart-legend">
                @foreach($grafikLabels as $label)
                <li><i style="background:{{ $chartPalette[$loop->index % count($chartPalette)] }}" aria-hidden="true"></i><span>{{ $label ?: 'Tiket' }}</span><strong>{{ $grafikData[$loop->index] }}</strong></li>
                @endforeach
            </ul>
        @else
            <div class="admin-empty"><span class="material-symbols-outlined" aria-hidden="true">donut_large</span><h3>Belum ada data tiket</h3><p>Komposisi terisi setelah ada pemesanan yang selesai.</p></div>
        @endif
    </section>
</div>
<nav class="admin-shortcuts" aria-label="Akses operasional">
    <a href="{{ route('pengelola.scan.index') }}"><span class="material-symbols-outlined" aria-hidden="true">qr_code_scanner</span><span><strong>Scan tiket</strong><small>Check-in dan check-out pengunjung</small></span><span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
    <a href="{{ route('pengelola.verifikasi.index') }}"><span class="material-symbols-outlined" aria-hidden="true">verified_user</span><span><strong>Verifikasi pembayaran</strong><small>{{ $pemesananPending ?? 0 }} pesanan menunggu tinjauan</small></span><span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
    <a href="{{ route('pengelola.laporan.index') }}"><span class="material-symbols-outlined" aria-hidden="true">monitoring</span><span><strong>Laporan operasional</strong><small>Kunjungan dan transaksi wisata</small></span><span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
</nav>
@endsection
