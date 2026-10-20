@extends('layouts.app')
@section('nav-mode', 'light')
@section('title', __('messages.my_bookings') . ' — Bangkiang Jaran')

@section('content')
@php
    $isEnglish = app()->getLocale() === 'en';
@endphp
<div class="bj-history">
    @php
        $total = isset($pemesanan) ? $pemesanan->count() : 0;
        $pending = isset($pemesanan) ? $pemesanan->where('status', 'pending')->count() : 0;
        $selesai = isset($pemesanan) ? $pemesanan->where('status', 'selesai')->count() : 0;
        $dibatalkan = isset($pemesanan) ? $pemesanan->where('status', 'dibatalkan')->count() : 0;
    @endphp

    <header class="bj-history-intro" aria-labelledby="history-title">
        <div class="bj-history-wrap bj-history-intro__inner">
            <div class="bj-history-intro__copy">
                <p class="bj-history-kicker bj-reveal"><span aria-hidden="true"></span>{{ $isEnglish ? 'Your visits' : 'Kunjungan Anda' }}</p>
                <h1 id="history-title" class="bj-reveal" data-bj-split>{{ $isEnglish ? 'A record of days by the waterfall.' : 'Arsip hari-hari di tepi air terjun.' }}</h1>
                <p class="bj-history-intro__lead bj-reveal">{{ __('messages.my_bookings_desc') }}</p>
            </div>
            <div class="bj-history-intro__count bj-reveal"><span>{{ $isEnglish ? 'Total reservations' : 'Total reservasi' }}</span><strong>{{ str_pad($total, 2, '0', STR_PAD_LEFT) }}</strong><small>{{ $isEnglish ? 'kept in your account' : 'tersimpan di akun Anda' }}</small></div>
        </div>
    </header>

    <main class="bj-history-main">
        <div class="bj-history-wrap">
            @if($total > 0)
                <div class="bj-history-overview bj-reveal" aria-label="{{ __('messages.status_summary') }}">
                    <div><span class="material-symbols-outlined" aria-hidden="true">calendar_month</span><p><small>{{ $isEnglish ? 'All reservations' : 'Semua reservasi' }}</small><strong>{{ $total }}</strong></p></div>
                    <div class="is-complete"><span class="material-symbols-outlined" aria-hidden="true">verified</span><p><small>{{ __('messages.completed') }}</small><strong>{{ $selesai }}</strong></p></div>
                    <div class="is-pending"><span class="material-symbols-outlined" aria-hidden="true">hourglass_top</span><p><small>{{ __('messages.pending') }}</small><strong>{{ $pending }}</strong></p></div>
                    <div class="is-cancelled"><span class="material-symbols-outlined" aria-hidden="true">do_not_disturb_on</span><p><small>{{ __('messages.cancelled') }}</small><strong>{{ $dibatalkan }}</strong></p></div>
                </div>

                <section class="bj-history-list" aria-labelledby="history-list-title">
                    <header class="bj-history-list__head bj-reveal"><div><p>{{ $isEnglish ? 'Reservation archive' : 'Arsip reservasi' }}</p><h2 id="history-list-title">{{ $isEnglish ? 'Your bookings' : 'Daftar pemesanan' }}</h2></div><span>{{ $total }} {{ $isEnglish ? 'records' : 'catatan' }}</span></header>

                    @foreach($pemesanan as $i => $item)
                        @php
                            $isPending = $item->status === 'pending';
                            $isCompleted = $item->status === 'selesai';
                            $isCancelled = $item->status === 'dibatalkan';
                            $isRejected = $item->pembayaran?->status === 'ditolak';
                            $ticketSummary = $item->detailPemesanan->groupBy('nama_tiket')->map(fn($group) => $group->count() . '× ' . $group->first()->nama_tiket)->implode(', ');
                            $statusClass = $isCompleted ? 'is-complete' : ($isCancelled ? 'is-cancelled' : 'is-pending');
                            $statusLabel = $isCompleted ? __('messages.completed') : ($isCancelled ? __('messages.cancelled') : ($isRejected ? ($isEnglish ? 'Payment proof declined' : 'Bukti bayar ditolak') : ($item->pembayaran ? __('messages.waiting_verification') : __('messages.pending'))));
                        @endphp
                        <article class="bj-history-card bj-reveal {{ $statusClass }}" style="--history-order: {{ min($i, 6) }};">
                            <button type="button" class="bj-history-card__toggle" aria-expanded="false" aria-controls="history-detail-{{ $item->id_pemesanan }}" onclick="toggleHistoryDetails({{ $item->id_pemesanan }}, this)">
                                <div class="bj-history-card__date"><span>{{ \Carbon\Carbon::parse($item->tgl_kunjungan)->format('d') }}</span><small>{{ \Carbon\Carbon::parse($item->tgl_kunjungan)->isoFormat('MMM') }}</small></div>
                                <div class="bj-history-card__main">
                                    <div class="bj-history-card__meta"><span>{{ $item->kode_booking }}</span><b class="{{ $statusClass }}"><i aria-hidden="true"></i>{{ $statusLabel }}</b></div>
                                    <h3>{{ $ticketSummary ?: ($item->tiket->nama_tiket ?? '-') }}</h3>
                                    <p><span class="material-symbols-outlined" aria-hidden="true">group</span>{{ __('messages.ticket_count', ['count' => $item->detailPemesanan->count()]) }}<span aria-hidden="true">·</span>{{ \Carbon\Carbon::parse($item->tgl_kunjungan)->isoFormat('D MMMM YYYY') }}</p>
                                </div>
                                <div class="bj-history-card__total"><strong class="{{ $isCancelled ? 'is-muted' : '' }}">Rp{{ number_format($item->total_harga, 0, ',', '.') }}</strong><span>{{ __('messages.detail') }}<i class="material-symbols-outlined" aria-hidden="true">add</i></span></div>
                            </button>
                            <div id="history-detail-{{ $item->id_pemesanan }}" class="bj-history-card__detail" hidden>
                                @if($isCancelled)
                                    <div class="bj-history-card__cancelled"><span class="material-symbols-outlined" aria-hidden="true">info</span><div><strong>{{ __('messages.reason_rejection') }}</strong><p>{{ __('messages.booking_cancelled') }}</p></div></div>
                                @else
                                    <div class="bj-history-ticket-list"><p>{{ __('messages.ticket_list') }}</p><ul>@foreach($item->detailPemesanan as $detail)<li><span class="material-symbols-outlined" aria-hidden="true">{{ $detail->plat_kendaraan ? 'directions_car' : 'person' }}</span><p><strong>{{ $detail->plat_kendaraan ?? $detail->nama_pengunjung ?? $detail->nama_tiket }}</strong><small>{{ $detail->nama_tiket }}</small></p><code>{{ $detail->kode_tiket }}</code></li>@endforeach</ul></div>
                                    <div class="bj-history-card__actions"><a href="{{ route('wisatawan.pemesanan.detail', $item->id_pemesanan) }}">{{ __('messages.view_eticket_btn') }}<span class="material-symbols-outlined" aria-hidden="true">arrow_outward</span></a>@if(!$item->pembayaran || $isRejected)<a class="is-primary" href="{{ route('wisatawan.pembayaran.create', $item->id_pemesanan) }}">{{ $isRejected ? ($isEnglish ? 'Upload payment proof again' : 'Unggah ulang bukti bayar') : __('messages.pay_btn') }}<span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>@endif</div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </section>
            @else
                <section class="bj-history-empty bj-reveal" aria-labelledby="history-empty-title"><span class="material-symbols-outlined" aria-hidden="true">water_drop</span><p>{{ $isEnglish ? 'Plan your next visit.' : 'Rencanakan kunjungan Anda.' }}</p><h2 id="history-empty-title">{{ __('messages.no_bookings') }}</h2><small>{{ __('messages.no_bookings_desc') }}</small><a href="{{ route('tiket.index') }}">{{ __('messages.book_now_from_history') }}<span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a></section>
            @endif
        </div>
    </main>
</div>

@push('scripts')
<script>
function toggleHistoryDetails(id, trigger) {
    const detail = document.getElementById('history-detail-' + id);
    if (!detail) return;
    const open = trigger.getAttribute('aria-expanded') === 'true';
    detail.hidden = open;
    trigger.setAttribute('aria-expanded', String(!open));
    trigger.closest('.bj-history-card')?.classList.toggle('is-open', !open);
}
</script>
@endpush
@endsection

