@extends('layouts.app')
@section('nav-mode', 'light')

@section('title', 'E-Ticket — Bangkiang Jaran')

@section('content')
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .page-break { page-break-before: always; }
    }
    .ticket-card {
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
        transition: box-shadow 0.2s;
    }
    .ticket-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
</style>

<div class="min-h-screen pt-28 pb-16 px-gutter bg-[#F8F7F5]" data-booking-watch="{{ route('wisatawan.pemesanan.status', $pemesanan->id_pemesanan) }}" data-status-token="{{ $pemesanan->statusToken() }}">
    <div class="max-w-2xl mx-auto">

        @if(session('success'))
        <div class="mb-5 flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-800 text-sm no-print">
            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
        @endif
        @if(session('error'))
        <div class="mb-5 flex items-center gap-3 p-4 rounded-2xl bg-red-50 border border-red-100 text-red-800 text-sm no-print">
            <span class="material-symbols-outlined text-red-600">error</span>
            <p>{{ session('error') }}</p>
        </div>
        @endif

        {{-- Expiration Alert / Notice --}}
        @if($pemesanan->status === 'dibatalkan')
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50/90 p-4 text-sm text-red-900 flex items-start gap-3 no-print" data-aos="fade-down">
            <span class="material-symbols-outlined text-red-600 mt-0.5">cancel</span>
            <div>
                <strong class="font-semibold">Pesanan Dibatalkan / Kedaluwarsa</strong>
                <p class="text-xs text-red-700 mt-1 leading-relaxed">
                    Pesanan ini sudah tidak aktif karena telah dibatalkan atau melewati batas waktu pembayaran tanpa adanya bukti transfer yang sah. E-ticket tidak dapat digunakan.
                </p>
            </div>
        </div>
        @elseif($pemesanan->status === 'pending' && (!$pemesanan->pembayaran || $pemesanan->pembayaran->status === 'ditolak'))
        @php
            $deadlineTime = isset($deadline) ? $deadline : app(\App\Services\BookingExpirationService::class)->getDeadline($pemesanan);
            $isPastDeadline = now()->isAfter($deadlineTime);
        @endphp
        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50/90 p-4 text-sm text-amber-900 flex items-start justify-between gap-3 no-print" data-aos="fade-down">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-amber-600 mt-0.5">hourglass_top</span>
                <div>
                    <strong class="font-semibold">Batas Waktu Pembayaran</strong>
                    <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                        Selesaikan pembayaran sebelum <strong class="text-amber-950 font-bold">{{ $deadlineTime->format('H:i') }} WITA</strong> ({{ $deadlineTime->translatedFormat('d M Y') }}). Pesanan yang belum dibayar dalam batas waktu akan otomatis hangus.
                    </p>
                </div>
            </div>
        </div>
        @endif


        <h1 class="font-sans text-2xl font-semibold mb-6">{{ __('operations.booking_details') }}</h1>
        {{-- Order info header --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6" data-aos="fade-up">
            <div class="bj-booking-summarybar bg-forest px-5 py-4 flex items-center justify-between">
                <div>
                    <p class="font-sans text-[10px] uppercase tracking-[0.15em] text-white/40">{{ __('messages.booking_code') }}</p>
                    <p class="font-mono text-lg font-bold text-white tracking-wider">{{ $pemesanan->kode_booking }}</p>
                </div>
                <div class="text-right">
                    <p class="font-sans text-[10px] uppercase tracking-[0.15em] text-white/40">{{ __('messages.th_status') }}</p>
                    @include('partials.booking-status', ['booking' => $pemesanan])
                </div>
            </div>
            <div class="px-5 py-4 flex flex-wrap gap-x-6 gap-y-2 text-xs text-stone font-sans">
                <div>
                    <span class="text-pebble">{{ __('messages.visit_date') }}:</span>
                    <span class="font-medium text-ink ml-1">{{ \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->isoFormat('D MMMM YYYY') }}</span>
                </div>
                <div>
                    <span class="text-pebble">{{ __('messages.jumlah_tiket') }}:</span>
                    <span class="font-medium text-ink ml-1">{{ $pemesanan->detailPemesanan->count() }} {{ __('messages.ticket_unit') }}</span>
                </div>
                <div>
                    <span class="text-pebble">{{ __('messages.total') }}:</span>
                    <span class="font-medium text-ink ml-1">Rp{{ number_format($pemesanan->total_harga, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Individual ticket cards --}}
        <div class="space-y-4">
            @foreach($pemesanan->detailPemesanan as $i => $item)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden {{ $i > 0 ? 'page-break' : '' }}" data-aos="fade-up" @if($i > 0) data-aos-delay="{{ min($i * 30, 150) }}" @endif>
                {{-- Ticket type badge --}}
                <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-forest text-sm">person</span>
                        <span class="font-sans text-xs font-medium text-ink">{{ $item->nama_tiket }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full {{ $item->canCheckIn() ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                        <span class="font-sans text-[10px] uppercase tracking-wider {{ $item->canCheckIn() ? 'text-green-600' : 'text-gray-400' }}">
                            {{ __('operations.badge_'.$item->verificationState()) }}
                        </span>
                    </div>
                </div>

                {{-- Content --}}
                <div class="bj-ticket-row p-5 flex items-start gap-5">
                    {{-- QR code --}}
                    <div class="bj-ticket-qr w-32 h-32 bg-white rounded-xl border border-gray-200 flex items-center justify-center flex-shrink-0 p-1">
                        @if($pemesanan->isApproved())
                        <img src="{{ app(\App\Services\TicketQr::class)->dataUri($item->kode_tiket) }}"
                             alt="QR {{ $item->kode_tiket }}"
                             class="w-full h-full object-contain"
                             loading="lazy"
                             onerror="this.parentElement.innerHTML='<span class=\'material-symbols-outlined text-gray-300\' style=\'font-size:40px;\'>qr_code</span>'">
                        @else<span class="text-sm px-3 text-center">{{ __('operations.qr_pending') }}</span>@endif
                    </div>

                    {{-- Info --}}
                    <div class="bj-ticket-info min-w-0 flex-1">
                        <div class="bj-ticket-meta grid grid-cols-2 gap-y-2.5 gap-x-4">
                            <div class="bj-ticket-code">
                                <p class="font-sans text-[10px] uppercase tracking-wider text-pebble">{{ __('messages.ticket_code') }}</p>
                                <p class="font-mono text-sm font-bold text-forest tracking-wider mt-0.5">{{ $item->kode_tiket }}</p>
                            </div>
                            <div>
                                <p class="font-sans text-[10px] uppercase tracking-wider text-pebble">{{ __('messages.visitor_name') }}</p>
                                <p class="font-sans text-sm font-medium text-ink mt-0.5">{{ $item->nama_pengunjung ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="font-sans text-[10px] uppercase tracking-wider text-pebble">{{ __('messages.price') }}</p>
                                <p class="font-sans text-sm text-ink mt-0.5">Rp{{ number_format($item->harga, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="font-sans text-[10px] uppercase tracking-wider text-pebble">{{ __('messages.date') }}</p>
                                <p class="font-sans text-sm text-ink mt-0.5">{{ \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Actions --}}
        <div class="mt-8 flex flex-col sm:flex-row gap-3 no-print" data-aos="fade-up">
            @if($pemesanan->status !== 'dibatalkan' && (!$pemesanan->pembayaran || $pemesanan->pembayaran->status === 'ditolak'))
            <a href="{{ route('wisatawan.pembayaran.create', $pemesanan->id_pemesanan) }}"
               class="flex-1 flex items-center justify-center gap-2 bg-forest text-white font-sans text-sm font-semibold py-3.5 rounded-xl hover:bg-leaf transition-colors">
                <span class="material-symbols-outlined text-sm">payments</span>
                {{ $pemesanan->pembayaran?->status === 'ditolak' ? 'Unggah Ulang Bukti Bayar' : __('messages.pay_btn') }}
            </a>
            <form method="POST" action="{{ route('wisatawan.pemesanan.cancel', $pemesanan->id_pemesanan) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')" class="flex-none">
                @csrf
                <button type="submit"
                        class="w-full sm:w-auto px-4 flex items-center justify-center gap-1.5 border border-red-200 text-red-600 font-sans text-sm font-medium py-3.5 rounded-xl hover:bg-red-50 hover:border-red-300 transition-colors cursor-pointer"
                        title="Batalkan pesanan ini">
                    <span class="material-symbols-outlined text-sm">close</span>
                    Batalkan
                </button>
            </form>
            @endif
            <button onclick="window.print()"
                    class="flex-1 flex items-center justify-center gap-2 bg-forest text-white font-sans text-sm font-semibold py-3.5 rounded-xl hover:bg-leaf transition-colors">
                <span class="material-symbols-outlined text-sm">download</span>
                {{ __('messages.print_download') }}
            </button>
            <a href="{{ route('wisatawan.pemesanan.riwayat') }}"
               class="flex-1 flex items-center justify-center gap-2 border border-gray-200 text-stone font-sans text-sm font-medium py-3.5 rounded-xl hover:border-gray-300 hover:text-ink transition-colors">
                <span class="material-symbols-outlined text-sm">history</span>
                {{ __('messages.back_to_history') }}
            </a>
        </div>

        {{-- Info --}}
        <div class="mt-6 flex items-start gap-2.5 bg-amber-50 border border-amber-200/60 rounded-xl px-4 py-3.5 no-print" data-aos="fade-up">
            <span class="material-symbols-outlined text-amber-500 text-sm mt-0.5 flex-shrink-0">info</span>
            <p class="font-sans text-xs text-stone leading-relaxed">
                {{ __('messages.ticket_unique_note') }}
            </p>
        </div>
    </div>
</div>
@include('partials.booking-lifecycle')
@endsection
