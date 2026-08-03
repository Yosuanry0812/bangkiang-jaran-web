@extends('layouts.app')
@section('nav-mode', 'light')

@section('title', 'Riwayat Pemesanan — Bangkiang Jaran')

@section('content')
<section class="pt-20 md:pt-32 pb-24 px-gutter min-h-screen" style="background:#F8F7F5;">
    <div class="max-w-6xl mx-auto">

        <header class="mb-12 max-w-xl" data-aos="fade-up">
            <p class="font-sans text-[11px] tracking-[0.15em] uppercase text-gray-400 mb-3">Akun Saya</p>
            <h1 class="font-serif text-4xl md:text-5xl text-gray-900 leading-[1.1]">{{ __('messages.my_bookings') }}</h1>
            <p class="font-sans text-sm text-gray-500 mt-3 leading-relaxed">{{ __('messages.my_bookings_desc') }}</p>
        </header>

        @if(isset($pemesanan) && $pemesanan->count() > 0)
        @php
            $total      = $pemesanan->count();
            $pending    = $pemesanan->where('status', 'pending')->count();
            $selesai    = $pemesanan->where('status', 'selesai')->count();
            $dibatalkan = $pemesanan->where('status', 'dibatalkan')->count();
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- Sidebar --}}
            <aside class="lg:col-span-3" data-aos="fade-right">
                <div class="sticky top-28 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                    <h3 class="font-serif text-lg text-gray-900 mb-5">{{ __('messages.status_summary') }}</h3>
                    <ul class="space-y-1">
                        <li class="flex justify-between items-center py-3 border-b border-gray-100">
                             <span class="font-sans text-sm text-gray-600 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> {{ __('messages.completed') }}
                            </span>
                            <span class="font-sans text-sm font-semibold text-gray-900">{{ $selesai }}</span>
                        </li>
                        <li class="flex justify-between items-center py-3 border-b border-gray-100">
                             <span class="font-sans text-sm text-gray-600 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> {{ __('messages.pending') }}
                            </span>
                            <span class="font-sans text-sm font-semibold text-gray-900">{{ $pending }}</span>
                        </li>
                        <li class="flex justify-between items-center py-3">
                             <span class="font-sans text-sm text-gray-600 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span> {{ __('messages.cancelled') }}
                            </span>
                            <span class="font-sans text-sm font-semibold text-gray-900">{{ $dibatalkan }}</span>
                        </li>
                    </ul>
                </div>
            </aside>

            {{-- Feed --}}
            <div class="lg:col-span-9 space-y-4">
                @foreach($pemesanan as $i => $item)
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300"
                     data-aos="fade-up" data-aos-delay="{{ $i * 40 }}">

                    <div class="p-5 cursor-pointer flex flex-col md:flex-row gap-4 justify-between items-start md:items-center"
                         onclick="toggleDetails({{ $item->id_pemesanan }})">
                        <div class="flex-grow">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="font-sans text-xs text-gray-400">{{ $item->kode_booking }}</span>
                                @if($item->status == 'pending')
                                <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-0.5 rounded-full font-sans text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                    {{ $item->pembayaran ? __('messages.waiting_verification') : __('messages.pending') }}
                                </span>
                                @elseif($item->status == 'selesai')
                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 rounded-full font-sans text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    {{ __('messages.completed') }}
                                </span>
                                @elseif($item->status == 'dibatalkan')
                                <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-500 border border-gray-200 px-2.5 py-0.5 rounded-full font-sans text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                    {{ __('messages.cancelled') }}
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-0.5 rounded-full font-sans text-xs">
                                    {{ ucfirst($item->status) }}
                                </span>
                                @endif
                            </div>
                            @php
                                $ticketSummary = $item->detailPemesanan->groupBy('nama_tiket')->map(fn($g) => $g->count() . '× ' . $g->first()->nama_tiket)->implode(', ');
                            @endphp
                            <h3 class="font-serif text-xl text-gray-900 mb-1">{{ $ticketSummary ?: ($item->tiket->nama_tiket ?? '-') }}</h3>
                            <p class="font-sans text-xs text-gray-400 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-xs">calendar_today</span>
                                {{ \Carbon\Carbon::parse($item->tgl_kunjungan)->format('d M Y') }}
                                <span class="text-gray-300 mx-1">·</span>
                                <span>{{ __('messages.ticket_count', ['count' => $item->detailPemesanan->count()]) }}</span>
                            </p>
                        </div>
                        <div class="text-left md:text-right w-full md:w-auto flex justify-between md:flex-col items-center md:items-end">
                            <span class="font-serif text-2xl {{ $item->status == 'dibatalkan' ? 'text-gray-300 line-through' : 'text-gray-900' }}">
                                Rp{{ number_format($item->total_harga, 0, ',', '.') }}
                            </span>
                            <span class="font-sans text-xs text-gray-400 flex items-center gap-1 mt-1">
                                {{ __('messages.detail') }} <span class="material-symbols-outlined text-sm">expand_more</span>
                            </span>
                        </div>
                    </div>

                    <div id="detail-{{ $item->id_pemesanan }}" class="hidden border-t border-gray-100 bg-gray-50 p-5">
                        @if($item->status == 'dibatalkan')
                        <div class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-gray-400 text-sm mt-0.5">info</span>
                            <div>
                                <p class="font-sans text-sm font-medium text-gray-900">{{ __('messages.reason_rejection') }}</p>
                                <p class="font-sans text-xs text-gray-500 mt-0.5">{{ __('messages.booking_cancelled') }}</p>
                            </div>
                        </div>
                        @else
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <p class="font-sans text-xs text-gray-400 mb-2">{{ __('messages.ticket_list') }}</p>
                                <div class="space-y-1.5">
                                    @foreach($item->detailPemesanan as $dt)
                                    <div class="flex items-center justify-between bg-white rounded-lg border border-gray-150 px-3.5 py-2.5">
                                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                            @if($dt->plat_kendaraan)
                                                <span class="material-symbols-outlined text-gray-400 text-sm flex-shrink-0">directions_car</span>
                                            @else
                                                <span class="material-symbols-outlined text-gray-400 text-sm flex-shrink-0">person</span>
                                            @endif
                                            <div class="min-w-0">
                                                <p class="font-sans text-xs text-gray-700 truncate">{{ $dt->plat_kendaraan ?? $dt->nama_pengunjung ?? $dt->nama_tiket }}</p>
                                                <p class="font-sans text-[10px] text-gray-400">{{ $dt->nama_tiket }}</p>
                                            </div>
                                        </div>
                                        <span class="font-mono text-xs font-bold text-emerald-600 tracking-wider flex-shrink-0 ml-2">{{ $dt->kode_tiket }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <a href="{{ route('wisatawan.pemesanan.detail', $item->id_pemesanan) }}"
                                   class="flex-1 text-center py-2.5 rounded-xl border border-gray-200 text-gray-700 font-sans text-sm hover:border-gray-400 hover:text-gray-900 transition-colors">
                                   {{ __('messages.view_eticket_btn') }}
                                </a>
                                @if(!$item->pembayaran || $item->pembayaran->status == 'ditolak')
                                <a href="{{ route('wisatawan.pembayaran.create', $item->id_pemesanan) }}"
                                   class="flex-1 text-center py-2.5 rounded-xl bg-gray-900 text-white font-sans text-sm hover:bg-gray-700 transition-colors">
                                   {{ __('messages.pay_btn') }}
                                </a>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        @else
        <div class="text-center max-w-sm mx-auto py-24" data-aos="fade-up">
            <span class="material-symbols-outlined text-5xl text-gray-200 block mb-5">receipt_long</span>
            <p class="font-serif text-xl text-gray-600 mb-2">{{ __('messages.no_bookings') }}</p>
            <p class="font-sans text-sm text-gray-400 mb-8">{{ __('messages.no_bookings_desc') }}</p>
            <a href="{{ route('tiket.index') }}"
               class="inline-flex items-center gap-2 bg-gray-900 text-white font-sans text-sm font-medium px-6 py-3 rounded-full hover:bg-gray-700 transition-colors">
                <span class="material-symbols-outlined text-sm">confirmation_number</span>
                {{ __('messages.book_now_from_history') }}
            </a>
        </div>
        @endif

    </div>
</section>

<script>
    function toggleDetails(id) {
        const el = document.getElementById('detail-' + id);
        if (el) el.classList.toggle('hidden');
    }
</script>
@endsection
