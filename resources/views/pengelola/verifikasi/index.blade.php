@extends('pengelola.layouts.admin')
@section('title', __('messages.verification_title'))
@section('page_title', __('messages.verification_title'))
@section('content')
<header class="admin-page-head"><div><h1>{{ __('messages.verification_title') }}</h1><p>Periksa bukti pembayaran sebelum menerbitkan tiket pengunjung.</p></div><span class="admin-status admin-status--pending"><span class="material-symbols-outlined" aria-hidden="true">pending_actions</span>Antrean pending ditampilkan lebih dulu</span></header>
<section class="admin-panel">
    <div class="admin-panel-head"><div><h2>Daftar pembayaran</h2><p>Status pembayaran dan pemesanan ditampilkan terpisah.</p></div><span class="admin-count">{{ method_exists($pemesanan, 'total') ? $pemesanan->total() : $pemesanan->count() }} pesanan</span></div>
    <div class="admin-table-wrap">
    <table class="admin-table admin-table--responsive">
        <caption class="sr-only">Pemesanan dan pembayaran untuk ditinjau</caption>
        <thead><tr><th scope="col">Pemesanan</th><th scope="col">Tiket & kunjungan</th><th scope="col">Total</th><th scope="col">Pemesanan</th><th scope="col">Pembayaran</th><th scope="col">Aksi</th></tr></thead>
        <tbody>
        @forelse($pemesanan as $p)
        @php
            $paymentStatus = $p->pembayaran->status ?? null;
            $paymentClass = match($paymentStatus) { 'valid' => '', 'pending' => 'admin-status--pending', 'ditolak' => 'admin-status--danger', default => 'admin-status--neutral' };
            $orderClass = match($p->status) { 'selesai' => '', 'pending' => 'admin-status--pending', 'dibatalkan' => 'admin-status--danger', default => 'admin-status--neutral' };
            $ticketSummary = $p->detailPemesanan->groupBy('nama_tiket')->map(fn($group) => $group->count() . '× ' . $group->first()->nama_tiket)->implode(', ');
        @endphp
        <tr>
            <td data-label="Pemesanan"><code>{{ $p->kode_booking }}</code><small>{{ $p->user->name ?? 'Pengunjung offline' }}</small></td>
            <td data-label="Tiket & kunjungan">{{ $ticketSummary ?: ($p->tiket->nama_tiket ?? '-') }}<small>{{ $p->tgl_kunjungan ? \Carbon\Carbon::parse($p->tgl_kunjungan)->format('d M Y') : '-' }}</small></td>
            <td data-label="Total" class="admin-money">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
            <td data-label="Status pesanan"><span class="admin-status {{ $orderClass }}">{{ ucfirst($p->status) }}</span></td>
            <td data-label="Pembayaran"><span class="admin-status {{ $paymentClass }}">{{ $paymentStatus ? ucfirst($paymentStatus) : 'Belum dibayar' }}</span></td>
            <td data-label="Aksi" class="admin-cell-wide"><a class="admin-text-action" href="{{ route('pengelola.verifikasi.show', $p->id_pemesanan) }}"><span class="material-symbols-outlined" aria-hidden="true">{{ $paymentStatus === 'pending' ? 'fact_check' : 'visibility' }}</span>{{ $paymentStatus === 'pending' ? 'Tinjau bukti' : __('messages.detail') }}<span class="sr-only"> {{ $p->kode_booking }}</span></a></td>
        </tr>
        @empty
        <tr><td colspan="6"><div class="admin-empty"><span class="material-symbols-outlined" aria-hidden="true">verified_user</span><h3>{{ __('messages.no_verification_data') }}</h3><p>Pesanan dengan pembayaran akan muncul di sini untuk diperiksa.</p></div></td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    @if(method_exists($pemesanan, 'links') && $pemesanan->hasPages())<div class="admin-pagination">{{ $pemesanan->links() }}</div>@endif
</section>
@endsection
