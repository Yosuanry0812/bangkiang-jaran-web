@extends('layouts.app')
@section('nav-mode', 'light')
@section('title', __('messages.my_bookings') . ' | Bangkiang Jaran')

@section('content')
@php
    $isEnglish = app()->getLocale() === 'en';
@endphp
<div class="bj-history" data-bookings-watch="{{ route('wisatawan.pemesanan.statuses') }}" data-status-tokens='@json($pemesanan->mapWithKeys(fn ($booking) => [$booking->id_pemesanan => $booking->statusToken()])->all())'>
    @php
        $total = isset($pemesanan) ? $pemesanan->count() : 0;
        $pending = $pemesanan->filter(fn ($booking) => !$booking->isApproved() && $booking->status !== 'dibatalkan')->count();
        $selesai = $pemesanan->filter(fn ($booking) => $booking->isApproved())->count();
        $dibatalkan = isset($pemesanan) ? $pemesanan->where('status', 'dibatalkan')->count() : 0;
    @endphp

    <header class="bj-history-intro" aria-labelledby="history-title">
        <div class="bj-history-wrap bj-history-intro__inner">
            <div class="bj-history-intro__copy">
                <p class="bj-history-kicker bj-reveal"><span aria-hidden="true"></span>{{ $isEnglish ? 'Your visits' : 'Kunjungan Anda' }}</p>
                <h1 id="history-title" class="bj-reveal">{{ $isEnglish ? 'Your visits, all in one place.' : 'Kunjunganmu, dalam satu tempat.' }}</h1>
                <p class="bj-history-intro__lead bj-reveal">{{ $isEnglish ? 'Check payment status, visitor details, and e-tickets for each booking.' : 'Lihat status pembayaran, detail pengunjung, dan e-ticket untuk setiap pesanan.' }}</p>
            </div>
            <div class="bj-history-intro__count bj-reveal"><span>{{ $isEnglish ? 'Total reservations' : 'Total reservasi' }}</span><strong>{{ str_pad($total, 2, '0', STR_PAD_LEFT) }}</strong><small>{{ $isEnglish ? 'kept in your account' : 'tersimpan di akun Anda' }}</small></div>
        </div>
    </header>

    <div class="bj-history-main">
        <div class="bj-history-wrap">
            @if($total > 0)
                <div class="bj-history-filters" role="group" aria-label="{{ $isEnglish ? 'Filter bookings' : 'Filter pemesanan' }}">
                    <button type="button" data-history-filter="all" aria-pressed="true">{{ $isEnglish ? 'All' : 'Semua' }}<span>{{ $total }}</span></button>
                    <button type="button" data-history-filter="pending" aria-pressed="false">{{ __('messages.pending') }}<span>{{ $pending }}</span></button>
                    <button type="button" data-history-filter="selesai" aria-pressed="false">{{ __('messages.completed') }}<span>{{ $selesai }}</span></button>
                    <button type="button" data-history-filter="dibatalkan" aria-pressed="false">{{ __('messages.cancelled') }}<span>{{ $dibatalkan }}</span></button>
                </div>

                <section class="bj-history-list" aria-labelledby="history-list-title">
                    <header class="bj-history-list__head bj-reveal"><div><h2 id="history-list-title">{{ $isEnglish ? 'Your bookings' : 'Daftar pemesanan' }}</h2></div><span role="status"><span data-history-count>{{ $total }}</span> {{ $isEnglish ? 'records' : 'catatan' }}</span></header>
                    <p class="bj-history-filter-empty" data-history-filter-empty hidden>{{ $isEnglish ? 'No bookings with this status yet.' : 'Belum ada pemesanan dengan status ini.' }}</p>

                    @foreach($pemesanan as $i => $item)
                        @php
                            $isCompleted = $item->isApproved();
                            $isCancelled = $item->status === 'dibatalkan';
                            $isRejected = $item->pembayaran?->status === 'ditolak';
                            $ticketSummary = $item->detailPemesanan->groupBy('nama_tiket')->map(fn($group) => $group->count() . '× ' . $group->first()->nama_tiket)->implode(', ');
                            $statusClass = $isCompleted ? 'is-complete' : ($isCancelled ? 'is-cancelled' : 'is-pending');
                            $statusLabel = $isCompleted ? __('messages.completed') : ($isCancelled ? __('messages.cancelled') : ($isRejected ? ($isEnglish ? 'Payment proof declined' : 'Bukti bayar ditolak') : ($item->pembayaran ? __('messages.waiting_verification') : __('messages.pending'))));
                        @endphp
                        <article class="bj-history-card bj-reveal {{ $statusClass }}" data-history-status="{{ $isCompleted ? 'selesai' : ($isCancelled ? 'dibatalkan' : 'pending') }}" style="--history-order: {{ min($i, 6) }};">
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
    </div>
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

