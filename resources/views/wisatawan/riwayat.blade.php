@extends('layouts.app')

@section('content')
<div class="bg-background min-h-screen">
    <main class="flex-grow w-full max-w-container-max mx-auto px-gutter py-xl">
        {{-- Header --}}
        <header class="mb-xl text-center md:text-left">
            <h1 class="font-display text-display-mobile md:text-display-lg text-primary mb-sm">My Bookings</h1>
            <p class="font-body text-body-lg text-on-surface-variant max-w-2xl">Review your upcoming adventures and past visits to the lush tranquility of Bangkiang Jaran.</p>
        </header>

        @if(isset($pemesanan) && $pemesanan->count() > 0)
        @php
            $total = $pemesanan->count();
            $pending = $pemesanan->where('status', 'pending')->count();
            $selesai = $pemesanan->where('status', 'selesai')->count();
            $dibatalkan = $pemesanan->where('status', 'dibatalkan')->count();
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-md">
            {{-- Filters / Summary Sidebar --}}
            <aside class="lg:col-span-3 space-y-md">
                <div class="bg-surface-container-low rounded-2xl p-lg sticky top-[100px]">
                    <h3 class="font-display text-headline-sm text-primary mb-md">Status Summary</h3>
                    <ul class="space-y-sm">
                        <li class="flex justify-between items-center py-2 border-b border-outline-variant/30">
                            <span class="font-body text-body-md text-on-surface flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-green-600"></span> Selesai
                            </span>
                            <span class="font-body text-label-md font-bold">{{ $selesai }}</span>
                        </li>
                        <li class="flex justify-between items-center py-2 border-b border-outline-variant/30">
                            <span class="font-body text-body-md text-on-surface flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-tertiary-container"></span> Pending
                            </span>
                            <span class="font-body text-label-md font-bold">{{ $pending }}</span>
                        </li>
                        <li class="flex justify-between items-center py-2">
                            <span class="font-body text-body-md text-on-surface flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-error"></span> Dibatalkan
                            </span>
                            <span class="font-body text-label-md font-bold">{{ $dibatalkan }}</span>
                        </li>
                    </ul>
                </div>
            </aside>

            {{-- Bookings Feed --}}
            <div class="lg:col-span-9 space-y-md">
                @foreach($pemesanan as $item)
                <article class="bg-surface-container-lowest rounded-2xl p-0 overflow-hidden border border-outline-variant/30 card-shadow transition-all duration-300 {{ $item->status == 'dibatalkan' ? 'opacity-80' : '' }}">
                    <div class="p-lg cursor-pointer flex flex-col md:flex-row gap-lg justify-between items-start md:items-center"
                         onclick="toggleDetails({{ $item->id_pemesanan }})">
                        <div class="flex-grow">
                            <div class="flex items-center gap-sm mb-xs">
                                <span class="font-body text-label-md text-outline">{{ $item->kode_booking }}</span>
                                @if($item->status == 'pending')
                                <span class="bg-tertiary-container/10 text-tertiary-container px-3 py-1 rounded-full font-body text-caption flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">schedule</span> Pending
                                </span>
                                @elseif($item->status == 'diproses')
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-body text-caption flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">hourglass_empty</span> Diproses
                                </span>
                                @elseif($item->status == 'selesai')
                                <span class="bg-secondary/10 text-secondary px-3 py-1 rounded-full font-body text-caption flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">check_circle</span> Selesai
                                </span>
                                @elseif($item->status == 'dibatalkan')
                                <span class="bg-error/10 text-error px-3 py-1 rounded-full font-body text-caption flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">cancel</span> Dibatalkan
                                </span>
                                @endif
                            </div>
                            <h2 class="font-display text-headline-sm text-on-background mb-xs">{{ $item->tiket->nama_tiket ?? '-' }}</h2>
                            <p class="font-body text-body-md text-on-surface-variant flex items-center gap-2">
                                <span class="material-symbols-outlined text-[20px]">calendar_today</span> {{ \Carbon\Carbon::parse($item->tgl_kunjungan)->format('d M Y') }}
                            </p>
                        </div>
                        <div class="text-left md:text-right w-full md:w-auto flex justify-between md:flex-col items-center md:items-end">
                            <span class="font-display text-headline-sm {{ $item->status == 'dibatalkan' ? 'text-outline line-through' : 'text-primary' }}">
                                Rp{{ number_format($item->total_harga, 0, ',', '.') }}
                            </span>
                            <span class="font-body text-label-md text-primary flex items-center gap-1 mt-2 hover:underline">
                                View Details <span class="material-symbols-outlined text-[18px]">expand_more</span>
                            </span>
                        </div>
                    </div>

                    {{-- Expandable Details --}}
                    <div id="detail-{{ $item->id_pemesanan }}" class="hidden border-t border-outline-variant/30 bg-surface-container-low p-lg">
                        @if($item->status == 'dibatalkan')
                        <div>
                            <h4 class="font-body text-label-md text-on-error-container mb-2 flex items-center gap-2">
                                <span class="material-symbols-outlined">info</span> Reason for Rejection
                            </h4>
                            <p class="font-body text-body-md text-on-surface">Pemesanan ini telah dibatalkan.</p>
                        </div>
                        @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-lg">
                            <div>
                                <h4 class="font-body text-label-md text-outline mb-2">Booking Details</h4>
                                <p class="font-body text-body-md text-on-background">{{ $item->jumlah }} tiket</p>
                                <p class="font-body text-body-md text-on-background">{{ $item->tiket->nama_tiket ?? '-' }}</p>
                            </div>
                            <div class="flex gap-md">
                                <a href="{{ route('wisatawan.pemesanan.detail', $item->id_pemesanan) }}"
                                   class="flex-1 py-sm rounded-xl border border-primary text-primary font-body text-label-md hover:bg-primary/5 transition-colors flex justify-center items-center gap-2">
                                    <span class="material-symbols-outlined">visibility</span> Lihat E-Ticket
                                </a>
                                @if(in_array($item->status, ['pending', 'diproses']))
                                <a href="{{ route('wisatawan.pembayaran.create', $item->id_pemesanan) }}"
                                   class="flex-1 py-sm rounded-xl bg-primary text-white font-body text-label-md hover:opacity-90 transition-opacity flex justify-center items-center gap-2">
                                    <span class="material-symbols-outlined">payments</span> Bayar
                                </a>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </article>
                @endforeach
            </div>
        </div>
        @else
        {{-- Empty State --}}
        <div class="bg-surface-container-lowest rounded-2xl card-shadow p-xl text-center max-w-lg mx-auto">
            <div class="w-20 h-20 mx-auto mb-md rounded-2xl bg-background flex items-center justify-center">
                <span class="material-symbols-outlined text-4xl text-outline">receipt_long</span>
            </div>
            <p class="font-display text-headline-sm text-on-background mb-sm">Belum Ada Pemesanan</p>
            <p class="font-body text-body-md text-on-surface-variant mb-lg">Mulai pesan tiket sekarang untuk menikmati keindahan Bangkiang Jaran Waterfall</p>
            <a href="{{ route('tiket.index') }}"
               class="inline-flex items-center gap-2 bg-primary-container text-white font-body text-label-md px-6 py-3 rounded-xl hover:-translate-y-0.5 transition-all duration-300 shadow-sm">
                <span class="material-symbols-outlined text-sm">confirmation_number</span>
                Pesan Tiket Sekarang
            </a>
        </div>
        @endif
    </main>
</div>

<script>
    function toggleDetails(id) {
        const el = document.getElementById('detail-' + id);
        if (el) el.classList.toggle('hidden');
    }
</script>
@endsection