@extends('pengelola.layouts.admin')
@section('title', __('messages.verification_title'))
@section('page_title', __('messages.verification_title'))
@section('content')
<header class="admin-page-head"><div><h1>{{ __('messages.verification_title') }}</h1><p>{{ __('panel.periksa_bukti_pembayaran_seb_79da') }}</p></div><span class="admin-status admin-status--pending"><span class="material-symbols-outlined" aria-hidden="true">pending_actions</span>{{ __('panel.antrean_pending_ditampilkan__2e1e') }}</span></header>
<section class="admin-panel">
    <div class="admin-panel-head"><div><h2>{{ __('panel.daftar_pembayaran_4ad7') }}</h2><p>{{ __('panel.status_pembayaran_dan_pemesa_9f63') }}</p></div><span class="admin-count">{{ trans_choice('ui.bookings_count', method_exists($pemesanan, 'total') ? $pemesanan->total() : $pemesanan->count()) }}</span></div>
    <div class="admin-table-wrap">
    <table class="admin-table admin-table--responsive">
        <caption class="sr-only">{{ __('panel.pemesanan_dan_pembayaran_unt_7a3b') }}</caption>
        <thead><tr><th scope="col">{{ __('panel.pemesanan_f431') }}</th><th scope="col">{{ __('panel.tiket_kunjungan_5e3f') }}</th><th scope="col">{{ __('panel.total_96b0') }}</th><th scope="col">{{ __('panel.pemesanan_f431') }}</th><th scope="col">{{ __('panel.pembayaran_71e2') }}</th><th scope="col">{{ __('panel.aksi_ae3b') }}</th></tr></thead>
        <tbody>
        @forelse($pemesanan as $p)
        @php
            $paymentStatus = $p->pembayaran->status ?? null;
            $paymentClass = match($paymentStatus) { 'valid' => '', 'pending' => 'admin-status--pending', 'ditolak' => 'admin-status--danger', default => 'admin-status--neutral' };
            $orderClass = match($p->status) { 'selesai' => '', 'pending' => 'admin-status--pending', 'dibatalkan' => 'admin-status--danger', default => 'admin-status--neutral' };
            $ticketSummary = $p->detailPemesanan->groupBy('nama_tiket')->map(fn($group) => $group->count() . '× ' . $group->first()->nama_tiket)->implode(', ');
        @endphp
        <tr>
            <td data-label="{{ __('panel.pemesanan_f431') }}"><code>{{ $p->kode_booking }}</code><small>{{ $p->user->name ?? __('ui.walk_in') }}</small></td>
            <td data-label="{{ __('panel.tiket_kunjungan_5e3f') }}">{{ $ticketSummary ?: ($p->tiket->nama_tiket ?? '-') }}<small>{{ $p->tgl_kunjungan ? \Carbon\Carbon::parse($p->tgl_kunjungan)->format('d M Y') : '-' }}</small></td>
            <td data-label="{{ __('panel.total_96b0') }}" class="admin-money">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
            <td data-label="{{ __('panel.status_pesanan_1b8e') }}"><span class="admin-status {{ $orderClass }}">{{ \App\Support\Label::status($p->status) }}</span></td>
            <td data-label="{{ __('panel.pembayaran_71e2') }}"><span class="admin-status {{ $paymentClass }}">{{ $paymentStatus ? \App\Support\Label::status($paymentStatus) : __('ui.unpaid') }}</span></td>
            <td data-label="{{ __('panel.aksi_ae3b') }}" class="admin-cell-wide"><a class="admin-text-action" href="{{ route('pengelola.verifikasi.show', $p->id_pemesanan) }}"><span class="material-symbols-outlined" aria-hidden="true">{{ $paymentStatus === 'pending' ? 'fact_check' : 'visibility' }}</span>{{ $paymentStatus === 'pending' ? __('ui.review_proof') : __('messages.detail') }}<span class="sr-only"> {{ $p->kode_booking }}</span></a></td>
        </tr>
        @empty
        <tr><td colspan="6"><div class="admin-empty"><span class="material-symbols-outlined" aria-hidden="true">verified_user</span><h3>{{ __('messages.no_verification_data') }}</h3><p>{{ __('panel.pesanan_dengan_pembayaran_ak_2f19') }}</p></div></td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    @if(method_exists($pemesanan, 'links') && $pemesanan->hasPages())<div class="admin-pagination">{{ $pemesanan->links() }}</div>@endif
</section>
@endsection
