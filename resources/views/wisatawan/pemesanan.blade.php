@extends('layouts.app')
@section('title', 'Reservasi Tiket — Bangkiang Jaran')
@section('nav-mode', 'light')

@section('content')
@php
    $pricesJson = json_encode($tiketList->pluck('harga', 'id_tiket')->toArray());
    $namesJson  = json_encode($tiketList->pluck('nama_tiket', 'id_tiket')->toArray());
    $preSelectedId = $selectedId ?? null;
@endphp

<div class="min-h-screen bg-[#FAF8F5] text-ink pb-24" x-data="ticketBooking({{ $pricesJson }}, {{ $namesJson }}, '{{ $tanggal ?? date('Y-m-d') }}', {{ $preSelectedId ? (int)$preSelectedId : 'null' }})">

    {{-- ── 1. QUIET EDITORIAL HEADER ────────────────── --}}
    <header class="pt-28 sm:pt-36 pb-10 px-gutter border-b border-stone/15 bg-white">
        <div class="max-w-6xl mx-auto">
            {{-- Minimal Breadcrumb --}}
            <nav class="flex items-center gap-2 text-xs text-stone/60 mb-6 font-sans">
                <a href="{{ route('landing') }}" class="hover:text-forest transition-colors">{{ __('messages.breadcrumb_home') }}</a>
                <span>/</span>
                <a href="{{ route('tiket.index') }}" class="hover:text-forest transition-colors">{{ __('messages.breadcrumb_tickets') }}</a>
                <span>/</span>
                <span class="text-forest font-medium">{{ __('messages.breadcrumb_booking') }}</span>
            </nav>

            <div class="flex flex-col md:flex-row md:items-baseline justify-between gap-4">
                <div>
                    <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl text-forest tracking-tight">
                        {{ __('messages.book_ticket') }}
                    </h1>
                    <p class="font-sans text-sm text-stone font-light mt-2">
                        Air Terjun Bangkiang Jaran · Desa Bakbakan, Gianyar, Bali
                    </p>
                </div>
                <div class="font-sans text-xs text-stone/70 tracking-widest uppercase">
                    Langkah 1 dari 3 · Pemilihan Tiket
                </div>
            </div>
        </div>
    </header>

    {{-- ── 2. NOTIFICATIONS / ERRORS ────────────────── --}}
    @if(session('error') || (isset($errors) && $errors->any()))
    <div class="max-w-6xl mx-auto px-gutter mt-8">
        <div class="border border-red-200 bg-red-50 text-red-800 px-6 py-4 rounded-xl font-sans text-sm">
            @if(session('error'))<p>{{ session('error') }}</p>@endif
            @if(isset($errors) && $errors->any())
            <ul class="list-disc list-inside space-y-1 text-xs sm:text-sm mt-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
            @endif
        </div>
    </div>
    @endif

    {{-- ── 3. RESERVATION FORM ──────────────────────── --}}
    <main class="max-w-6xl mx-auto px-gutter mt-10">
        <form method="POST" action="{{ route('wisatawan.pemesanan.store') }}">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">

                {{-- ─ LEFT MAIN COLUMN ────────────────── --}}
                <div class="lg:col-span-7 space-y-12">

                    {{-- Section: Tanggal Kunjungan --}}
                    <div>
                        <div class="flex items-baseline justify-between pb-3 border-b border-stone/20 mb-6">
                            <h2 class="font-sans text-xs uppercase tracking-[0.2em] text-forest font-semibold">
                                {{ __('messages.visit_date') }}
                            </h2>
                            <span class="font-sans text-xs text-stone/60">07:00 – 18:00 WITA</span>
                        </div>

                        <div class="bg-white p-6 rounded-2xl border border-stone/15">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-11 h-11 rounded-xl bg-forest/8 text-forest flex items-center justify-center flex-shrink-0">
                                        <span class="material-symbols-outlined text-xl">calendar_today</span>
                                    </div>
                                    <div>
                                        <p class="font-serif text-xl text-forest" x-text="formattedDisplayDate()"></p>
                                        <p class="font-sans text-xs text-stone font-light">Tiket berlaku penuh pada tanggal terpilih</p>
                                    </div>
                                </div>

                                <div class="relative">
                                    <input type="date"
                                           name="tgl_kunjungan"
                                           x-model="tglKunjungan"
                                           min="{{ date('Y-m-d') }}"
                                           required
                                           class="bg-[#FAF8F5] border border-stone/20 hover:border-forest rounded-xl px-4 py-2.5 font-sans text-xs font-medium text-forest outline-none focus:border-forest transition-colors cursor-pointer">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Pilihan Tiket Masuk --}}
                    <div>
                        <div class="flex items-baseline justify-between pb-3 border-b border-stone/20 mb-6">
                            <h2 class="font-sans text-xs uppercase tracking-[0.2em] text-forest font-semibold">
                                {{ __('messages.select_ticket') }}
                            </h2>
                            <span class="font-sans text-xs text-stone/60">{{ __('messages.max_per_type') }}</span>
                        </div>

                        {{-- Line-Item Table Style (No Clunky Nested Cards) --}}
                        <div class="bg-white rounded-2xl border border-stone/15 divide-y divide-stone/10 overflow-hidden">
                            @foreach($tiketList as $t)
                            <div class="p-6 transition-colors duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-5"
                                 :class="qty({{ $t->id_tiket }}) > 0 ? 'bg-forest/[0.02]' : 'hover:bg-stone/[0.02]'">

                                {{-- Details --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="font-serif text-xl text-forest">{{ $t->nama_tiket }}</h3>
                                        <span class="text-[10px] font-sans uppercase tracking-wider text-stone/70 border border-stone/20 px-2 py-0.5 rounded">
                                            {{ $t->kategori ?? 'Perorangan' }}
                                        </span>
                                    </div>
                                    <p class="font-sans text-xs text-stone font-light">
                                        Termasuk akses air terjun, kolam alami & fasilitas umum
                                    </p>
                                    <p class="font-serif text-lg text-forest mt-2">
                                        Rp {{ number_format($t->harga, 0, ',', '.') }}
                                        <span class="font-sans text-xs text-stone/60 font-light">/ orang</span>
                                    </p>
                                </div>

                                {{-- Stepper --}}
                                <div class="flex items-center gap-3 self-end sm:self-center flex-shrink-0">
                                    <button type="button"
                                            @click="decrement({{ $t->id_tiket }})"
                                            :disabled="qty({{ $t->id_tiket }}) === 0"
                                            class="w-9 h-9 rounded-full border border-stone/25 flex items-center justify-center text-forest hover:bg-forest hover:text-white disabled:opacity-30 disabled:hover:bg-transparent disabled:hover:text-forest transition-all cursor-pointer">
                                        <span class="text-base leading-none">−</span>
                                    </button>

                                    <span class="font-sans text-base font-semibold w-8 text-center tabular-nums text-forest"
                                          x-text="qty({{ $t->id_tiket }})">0</span>

                                    <button type="button"
                                            @click="increment({{ $t->id_tiket }})"
                                            :disabled="qty({{ $t->id_tiket }}) >= 10"
                                            class="w-9 h-9 rounded-full border border-stone/25 flex items-center justify-center text-forest hover:bg-forest hover:text-white disabled:opacity-30 disabled:hover:bg-transparent disabled:hover:text-forest transition-all cursor-pointer">
                                        <span class="text-base leading-none">+</span>
                                    </button>

                                    <input type="hidden" name="tickets[{{ $t->id_tiket }}]" :value="qty({{ $t->id_tiket }})">
                                </div>

                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Simple Assurance Text --}}
                    <div class="flex flex-wrap items-center gap-6 pt-2 font-sans text-xs text-stone/70">
                        <span class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-forest"></span>
                            <span>Konfirmasi instan ke email & akun</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-forest"></span>
                            <span>Jalur cepat pemindaian QR di pintu masuk</span>
                        </span>
                    </div>

                </div>

                {{-- ─ RIGHT SIDEBAR: Minimalist Folio Summary ─ --}}
                <div class="lg:col-span-5">
                    <div class="sticky top-32">

                        <div class="bg-white rounded-2xl border border-stone/15 p-7 sm:p-8">
                            <h3 class="font-serif text-2xl text-forest pb-4 border-b border-stone/15">
                                {{ __('messages.booking_summary') }}
                            </h3>

                            {{-- Folio Metadata --}}
                            <div class="py-5 border-b border-stone/10 space-y-2 font-sans text-xs">
                                <div class="flex justify-between">
                                    <span class="text-stone font-light">Lokasi</span>
                                    <span class="text-forest font-medium text-right">Bangkiang Jaran, Gianyar</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-stone font-light">Tanggal Kunjungan</span>
                                    <span class="text-forest font-medium text-right" x-text="formattedDisplayDate()"></span>
                                </div>
                            </div>

                            {{-- Empty State --}}
                            <div class="py-10 text-center" x-show="selectedTickets().length === 0">
                                <p class="font-serif text-lg text-stone/60 mb-1">{{ __('messages.no_tickets_selected') }}</p>
                                <p class="font-sans text-xs text-stone/40">Tentukan jumlah tiket pada daftar di sebelah kiri.</p>
                            </div>

                            {{-- Itemized Breakdown --}}
                            <div class="py-5 space-y-3.5 border-b border-stone/10" x-show="selectedTickets().length > 0">
                                <template x-for="item in selectedTickets()" :key="item.id">
                                    <div class="flex justify-between items-start font-sans text-xs sm:text-sm">
                                        <div>
                                            <p class="text-forest font-medium" x-text="item.nama"></p>
                                            <p class="text-stone/60 text-xs mt-0.5"
                                               x-text="item.qty + ' × Rp ' + item.harga.toLocaleString('id-ID')"></p>
                                        </div>
                                        <span class="text-forest font-semibold tabular-nums"
                                              x-text="'Rp ' + (item.qty * item.harga).toLocaleString('id-ID')"></span>
                                    </div>
                                </template>
                            </div>

                            {{-- Total Row --}}
                            <div class="pt-5 pb-6">
                                <div class="flex justify-between items-baseline">
                                    <span class="font-sans text-xs uppercase tracking-widest text-stone font-medium">Total Pembayaran</span>
                                    <span class="font-serif text-3xl text-forest tabular-nums font-normal"
                                          x-text="'Rp ' + total().toLocaleString('id-ID')">Rp 0</span>
                                </div>
                                <p class="font-sans text-[11px] text-stone/50 font-light mt-1">Sudah termasuk seluruh akses & biaya operasional.</p>
                            </div>

                            {{-- Submit Button --}}
                            <button type="submit"
                                    class="w-full py-4 rounded-full font-sans text-xs uppercase tracking-widest font-semibold transition-all duration-300 flex items-center justify-center gap-2.5"
                                    :class="selectedTickets().length > 0
                                        ? 'bg-forest text-white hover:bg-leaf cursor-pointer shadow-md'
                                        : 'bg-stone/15 text-stone/40 cursor-not-allowed'"
                                    :disabled="selectedTickets().length === 0">
                                <span x-show="selectedTickets().length > 0">Lanjutkan ke Data Pengunjung</span>
                                <span x-show="selectedTickets().length === 0">{{ __('messages.select_tickets_first') }}</span>
                                <span class="material-symbols-outlined text-sm" x-show="selectedTickets().length > 0">arrow_forward</span>
                            </button>

                            <p class="font-sans text-[11px] text-center text-stone/60 font-light mt-4">
                                {{ __('messages.no_charge_yet') }}
                            </p>
                        </div>

                        {{-- Direct Assistance Link --}}
                        <p class="font-sans text-xs text-center text-stone/70 mt-4">
                            Butuh bantuan pemesanan?
                            <a href="https://wa.me/6281234567890" target="_blank" class="text-forest font-medium hover:underline ml-0.5">
                                Hubungi Pengelola
                            </a>
                        </p>

                    </div>
                </div>

            </div>

        </form>
    </main>

</div>

@push('scripts')
<script>
function ticketBooking(prices, names, initialDate, preSelectedId) {
    const tickets = {};
    Object.keys(prices).forEach(id => {
        tickets[id] = (preSelectedId && parseInt(id) === preSelectedId) ? 1 : 0;
    });

    return {
        tglKunjungan: initialDate,
        tickets,
        prices,
        names,
        qty(id)       { return this.tickets[id] || 0; },
        increment(id) { if (this.tickets[id] < 10) this.tickets[id]++; },
        decrement(id) { if (this.tickets[id] > 0)  this.tickets[id]--; },
        selectedTickets() {
            return Object.entries(this.tickets)
                .filter(([, qty]) => qty > 0)
                .map(([id, qty]) => ({
                    id:    parseInt(id),
                    nama:  this.names[id] || 'Tiket',
                    qty,
                    harga: this.prices[id] || 0,
                }));
        },
        subtotal() { return this.selectedTickets().reduce((s, t) => s + t.qty * t.harga, 0); },
        total()    { return this.subtotal(); },
        formattedDisplayDate() {
            if (!this.tglKunjungan) return '-';
            try {
                const parts = this.tglKunjungan.split('-');
                if (parts.length === 3) {
                    const d = new Date(parts[0], parts[1] - 1, parts[2]);
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    return d.toLocaleDateString('id-ID', options);
                }
            } catch (e) {}
            return this.tglKunjungan;
        }
    };
}
</script>
@endpush
@endsection
