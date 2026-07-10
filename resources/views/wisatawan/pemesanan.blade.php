@extends('layouts.app')

@section('content')
<div class="bg-background min-h-screen" x-data="{ harga: {{ $selectedTiket->harga ?? 0 }}, jumlah: 1 }">
    {{-- Header --}}
    <section class="relative h-48 flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-background/40 to-background"></div>
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=2070&q=80')] bg-cover bg-center opacity-30"></div>
        <div class="relative z-10 text-center px-gutter max-w-container-max mx-auto w-full">
            <nav class="font-body text-caption text-on-surface-variant mb-2 flex items-center justify-center gap-2">
                <a href="{{ url('/') }}" class="hover:text-primary transition-colors">Beranda</a>
                <span class="material-symbols-outlined text-sm text-outline">chevron_right</span>
                <a href="{{ route('tiket.index') }}" class="hover:text-primary transition-colors">Tiket</a>
                <span class="material-symbols-outlined text-sm text-outline">chevron_right</span>
                <span class="text-primary">Pemesanan</span>
            </nav>
            <h1 class="font-display text-headline-md text-on-background">Secure Your Visit</h1>
            <p class="font-body text-body-md text-on-surface-variant">Complete your booking for a serene experience at Bangkiang Jaran Waterfall.</p>
        </div>
    </section>

    {{-- Error & Success --}}
    <div class="max-w-container-max mx-auto px-gutter -mt-8 relative z-10">
        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-6 font-body text-body-md">{{ session('error') }}</div>
        @endif
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-6 font-body text-body-md">
            <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
        @endif
    </div>

    {{-- Form --}}
    <section class="max-w-container-max mx-auto px-gutter pb-xl">
        <form method="POST" action="{{ route('wisatawan.pemesanan.store') }}">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-lg">
                {{-- Left: Main Form (8 cols) --}}
                <div class="lg:col-span-8 flex flex-col gap-lg">
                    {{-- Selected Ticket Banner --}}
                    @if(isset($selectedTiket))
                    <div class="bg-primary-container/10 border border-primary-container/20 rounded-2xl p-md flex items-center gap-md">
                        <span class="material-symbols-outlined text-primary text-2xl">confirmation_number</span>
                        <div>
                            <p class="font-body text-label-md text-primary">Tiket Dipilih</p>
                            <p class="font-display text-headline-sm text-on-background">{{ $selectedTiket->nama_tiket }}</p>
                            <p class="font-body text-body-md text-primary">Rp{{ number_format($selectedTiket->harga, 0, ',', '.') }} / orang</p>
                        </div>
                    </div>
                    @endif

                    {{-- Ticket Selection --}}
                    <div class="bg-surface-container-lowest rounded-2xl card-shadow p-lg">
                        <h2 class="font-display text-headline-sm text-on-background mb-md">Pilih Tiket</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
                            @if(isset($selectedTiket))
                            @php
                                $tiketList = $tiketList ?? collect([$selectedTiket]);
                            @endphp
                            @endif
                            @if(isset($tiketList))
                            @foreach($tiketList as $t)
                            <label class="relative flex flex-col p-md rounded-xl border border-outline-variant cursor-pointer hover:bg-surface-container-low transition-colors group">
                                <input type="radio" name="id_tiket" value="{{ $t->id_tiket }}" data-harga="{{ $t->harga }}"
                                       @change="harga = {{ $t->harga }}"
                                       {{ isset($selectedTiket) && $selectedTiket->id_tiket == $t->id_tiket ? 'checked' : ($loop->first ? 'checked' : '') }}
                                       class="peer sr-only">
                                <div class="absolute inset-0 rounded-xl border-2 border-transparent peer-checked:border-primary peer-checked:bg-primary/5 pointer-events-none"></div>
                                <div class="flex justify-between items-center mb-sm z-10">
                                    <span class="font-body text-label-md text-on-surface">{{ $t->nama_tiket }}</span>
                                    <span class="material-symbols-outlined text-primary opacity-0 peer-checked:opacity-100 transition-opacity">check_circle</span>
                                </div>
                                <p class="font-body text-body-md text-on-surface-variant mb-sm flex-grow z-10">Akses penuh ke area wisata.</p>
                                <span class="font-display text-headline-sm text-primary z-10">Rp{{ number_format($t->harga, 0, ',', '.') }}</span>
                            </label>
                            @endforeach
                            @endif
                        </div>
                    </div>

                    {{-- Date & Quantity --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                        <div class="bg-surface-container-lowest rounded-2xl card-shadow p-lg">
                            <label for="tgl_kunjungan" class="font-body text-label-md text-on-surface-variant mb-2 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-sm">calendar_month</span>
                                Tanggal Kunjungan
                            </label>
                            <input type="date" name="tgl_kunjungan" id="tgl_kunjungan" value="{{ $tanggal ?? date('Y-m-d') }}"
                                   class="w-full bg-background border border-outline-variant text-on-background rounded-xl px-4 py-3 font-body text-body-md focus:border-primary focus:ring-4 focus:ring-primary/20 transition-all outline-none">
                        </div>
                        <div class="bg-surface-container-lowest rounded-2xl card-shadow p-lg">
                            <label class="font-body text-label-md text-on-surface-variant mb-2 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-sm">group</span>
                                Jumlah Tiket
                            </label>
                            <div class="flex items-center justify-between py-sm border-b border-outline-variant last:border-0">
                                <div>
                                    <p class="font-body text-label-md text-on-surface">Adults</p>
                                    <p class="font-body text-caption text-on-surface-variant">Age 12+</p>
                                </div>
                                <div class="flex items-center gap-md">
                                    <button type="button" class="w-10 h-10 rounded-full border border-outline-variant flex items-center justify-center text-on-surface hover:bg-surface-container transition-colors"
                                            @click="jumlah = Math.max(1, jumlah - 1)">
                                        <span class="material-symbols-outlined">remove</span>
                                    </button>
                                    <span class="font-body text-body-lg w-8 text-center" x-text="jumlah">1</span>
                                    <button type="button" class="w-10 h-10 rounded-full border border-outline-variant flex items-center justify-center text-on-surface hover:bg-surface-container transition-colors"
                                            @click="jumlah = Math.min(10, jumlah + 1)">
                                        <span class="material-symbols-outlined">add</span>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="jumlah" x-model="jumlah">
                        </div>
                    </div>
                </div>

                {{-- Right: Summary Sidebar (4 cols) --}}
                <div class="lg:col-span-4 relative">
                    <div class="sticky top-24 bg-surface-container-lowest rounded-2xl card-shadow overflow-hidden flex flex-col">
                        {{-- Image Header --}}
                        <div class="h-48 w-full overflow-hidden">
                            <img class="w-full h-full object-cover"
                                 src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=400&q=80"
                                 alt="Bangkiang Jaran Waterfall">
                        </div>
                        <div class="p-lg space-y-md">
                            <h3 class="font-display text-headline-sm text-on-background">Booking Summary</h3>
                            <div class="space-y-sm">
                                <div class="flex justify-between items-start">
                                    <div class="flex flex-col">
                                        <span class="font-body text-label-md text-on-surface" x-text="selectedTicketName || '{{ $selectedTiket->nama_tiket ?? 'Tiket' }}'">{{ $selectedTiket->nama_tiket ?? 'Tiket' }}</span>
                                        <span class="font-body text-caption text-on-surface-variant" x-text="selectedDate || '{{ isset($tanggal) ? \Carbon\Carbon::parse($tanggal)->format('M d, Y') : date('M d, Y') }}'">{{ isset($tanggal) ? \Carbon\Carbon::parse($tanggal)->format('M d, Y') : date('M d, Y') }}</span>
                                    </div>
                                    <span class="font-body text-body-md text-on-surface" x-text="'Rp' + harga.toLocaleString('id-ID')">Rp{{ number_format($selectedTiket->harga ?? 0, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="border-t border-outline-variant pt-md space-y-sm">
                                <div class="flex justify-between items-center">
                                    <span class="font-body text-body-md text-on-surface-variant">Subtotal</span>
                                    <span class="font-body text-body-md text-on-surface" x-text="'Rp' + (harga * jumlah).toLocaleString('id-ID')">Rp{{ number_format(($selectedTiket->harga ?? 0) * 1, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-body text-body-md text-on-surface-variant">Taxes &amp; Fees</span>
                                    <span class="font-body text-body-md text-on-surface">Rp{{ number_format(round(($selectedTiket->harga ?? 0) * 0.1), 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-md">
                                    <span class="font-display text-headline-sm text-on-background">Total</span>
                                    <span class="font-display text-headline-sm text-primary" x-text="'Rp' + ((harga * jumlah) + Math.round((harga * jumlah) * 0.1)).toLocaleString('id-ID')">
                                        Rp{{ number_format(round(($selectedTiket->harga ?? 0) * 1 * 1.1), 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            <button type="submit"
                                    class="w-full bg-primary-container text-white font-body text-label-md px-6 py-4 rounded-xl hover:-translate-y-0.5 transition-all duration-300 shadow-sm flex items-center justify-center gap-2">
                                Lanjut ke Pembayaran
                            </button>
                            <p class="font-body text-caption text-center text-on-surface-variant">You won't be charged yet.</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
@endsection
