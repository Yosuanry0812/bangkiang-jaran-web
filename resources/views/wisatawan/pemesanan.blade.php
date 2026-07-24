@extends('layouts.app')

@section('content')
@php
    $pricesJson = json_encode($tiketList->pluck('harga', 'id_tiket')->toArray());
    $namesJson  = json_encode($tiketList->pluck('nama_tiket', 'id_tiket')->toArray());
@endphp

<div x-data="ticketOrder({{ $pricesJson }}, {{ $namesJson }})">

    {{-- ── PAGE HEADER ──────────────────────────────── --}}
    <div class="pt-32 pb-10 px-gutter bg-white border-b border-gray-100">
        <div class="max-w-6xl mx-auto">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-xs text-gray-400 mb-6 font-sans">
                <a href="{{ route('landing') }}" class="hover:text-gray-700 transition-colors">Beranda</a>
                <span>/</span>
                <a href="{{ route('tiket.index') }}" class="hover:text-gray-700 transition-colors">Tiket</a>
                <span>/</span>
                <span class="text-gray-700">Pemesanan</span>
            </nav>
            <h1 class="font-serif text-4xl md:text-5xl text-gray-900">Pesan Tiket</h1>
            <p class="font-sans text-sm text-gray-500 mt-2">Bangkiang Jaran · Desa Bakbakan, Gianyar, Bali</p>
        </div>
    </div>

    {{-- ── ALERTS ───────────────────────────────────── --}}
    @if(session('error') || (isset($errors) && $errors->any()))
    <div class="max-w-6xl mx-auto px-gutter mt-6">
        <div class="border border-red-200 bg-red-50 text-red-700 px-5 py-4 rounded-xl font-sans text-sm">
            @if(session('error')){{ session('error') }}@endif
            @if(isset($errors) && $errors->any())
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
            @endif
        </div>
    </div>
    @endif

    {{-- ── FORM ─────────────────────────────────────── --}}
    <section class="py-12 px-gutter bg-white">
        <form method="POST" action="{{ route('wisatawan.pemesanan.store') }}"
              class="max-w-6xl mx-auto">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 xl:gap-12">

                {{-- ─ LEFT COLUMN ──────────────────── --}}
                <div class="lg:col-span-7 space-y-5">

                    {{-- Date picker --}}
                    <div class="border border-gray-200 rounded-2xl p-6 bg-white" data-aos="fade-up">
                        <label class="block font-sans text-xs tracking-[0.12em] uppercase text-gray-400 mb-3">
                            Tanggal Kunjungan
                        </label>
                        <input type="date"
                               name="tgl_kunjungan"
                               value="{{ $tanggal ?? date('Y-m-d') }}"
                               class="w-full bg-transparent border-0 p-0 font-serif text-2xl text-gray-900 focus:ring-0 outline-none cursor-pointer">
                    </div>

                    {{-- Ticket list --}}
                    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white" data-aos="fade-up" data-aos-delay="40">
                        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                            <div>
                                <h3 class="font-serif text-xl text-gray-900">Pilih Tiket</h3>
                                <p class="font-sans text-xs text-gray-400 mt-0.5">Tentukan jumlah tiket yang diinginkan</p>
                            </div>
                            <span class="font-sans text-xs text-gray-400">Maks. 10 per jenis</span>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @foreach($tiketList as $t)
                            <div class="flex items-center justify-between px-6 py-5 hover:bg-gray-50 transition-colors">
                                <div>
                                    <p class="font-sans text-sm font-medium text-gray-900">{{ $t->nama_tiket }}</p>
                                    <p class="font-sans text-xs text-gray-400 mt-0.5">Rp{{ number_format($t->harga, 0, ',', '.') }} / orang</p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <button type="button"
                                            class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:border-gray-900 hover:text-gray-900 transition-colors"
                                            @click="decrement({{ $t->id_tiket }})">
                                        <span class="material-symbols-outlined text-base">remove</span>
                                    </button>
                                    <span class="font-sans text-sm font-medium w-6 text-center tabular-nums text-gray-900"
                                          x-text="qty({{ $t->id_tiket }})">0</span>
                                    <button type="button"
                                            class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:border-gray-900 hover:text-gray-900 transition-colors"
                                            @click="increment({{ $t->id_tiket }})">
                                        <span class="material-symbols-outlined text-base">add</span>
                                    </button>
                                    <input type="hidden" name="tickets[{{ $t->id_tiket }}]" :value="qty({{ $t->id_tiket }})">
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Info notes --}}
                    <div class="flex flex-wrap gap-x-6 gap-y-2" data-aos="fade-up" data-aos-delay="60">
                        <span class="font-sans text-xs text-gray-400 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">check</span>
                            Harga sudah termasuk pajak
                        </span>
                        <span class="font-sans text-xs text-gray-400 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">schedule</span>
                            Tiket berlaku sesuai tanggal
                        </span>
                        <a href="mailto:info@bangkiangjaran.com" class="font-sans text-xs text-gray-400 hover:text-gray-700 transition-colors flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">mail</span>
                            Pertanyaan? Hubungi pengelola
                        </a>
                    </div>
                </div>

                {{-- ─ RIGHT COLUMN (sticky summary) ── --}}
                <div class="lg:col-span-5" data-aos="fade-left" data-aos-delay="60">
                    <div class="sticky top-24 space-y-4">

                        {{-- Summary card --}}
                        <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white">

                            {{-- Photo --}}
                            <div class="relative h-44 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80"
                                     class="w-full h-full object-cover" alt="Bangkiang Jaran">
                                <div class="absolute inset-0 bg-black/30"></div>
                                <div class="absolute bottom-4 left-5">
                                    <p class="font-serif text-lg text-white">Bangkiang Jaran</p>
                                    <p class="font-sans text-xs text-white/70 mt-0.5">Gianyar, Bali</p>
                                </div>
                            </div>

                            <div class="p-6">
                                <h3 class="font-serif text-lg text-gray-900 mb-5">Ringkasan Pesanan</h3>

                                {{-- Empty state --}}
                                <div class="py-8 text-center" x-show="selectedTickets().length === 0">
                                    <p class="font-sans text-sm text-gray-400">Belum ada tiket dipilih</p>
                                    <p class="font-sans text-xs text-gray-300 mt-1">Pilih tiket di sebelah kiri</p>
                                </div>

                                {{-- Selected items --}}
                                <div class="space-y-3 mb-5" x-show="selectedTickets().length > 0">
                                    <template x-for="item in selectedTickets()" :key="item.id">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-sans text-sm text-gray-900" x-text="item.nama"></p>
                                                <p class="font-sans text-xs text-gray-400 mt-0.5"
                                                   x-text="item.qty + ' × Rp' + item.harga.toLocaleString('id-ID')"></p>
                                            </div>
                                            <span class="font-sans text-sm text-gray-900 tabular-nums"
                                                  x-text="'Rp' + (item.qty * item.harga).toLocaleString('id-ID')"></span>
                                        </div>
                                    </template>
                                </div>

                                {{-- Totals --}}
                                <div class="border-t border-gray-100 pt-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="font-sans text-xs text-gray-400">Subtotal</span>
                                        <span class="font-sans text-xs text-gray-700 tabular-nums"
                                              x-text="'Rp' + subtotal().toLocaleString('id-ID')">Rp0</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-sans text-xs text-gray-400">Pajak (10%)</span>
                                        <span class="font-sans text-xs text-gray-700 tabular-nums"
                                              x-text="'Rp' + pajak().toLocaleString('id-ID')">Rp0</span>
                                    </div>
                                    <div class="flex justify-between items-baseline pt-2 border-t border-gray-100">
                                        <span class="font-sans text-sm font-medium text-gray-900">Total</span>
                                        <span class="font-serif text-2xl text-gray-900 tabular-nums"
                                              x-text="'Rp' + total().toLocaleString('id-ID')">Rp0</span>
                                    </div>
                                </div>

                                {{-- CTA --}}
                                <button type="submit"
                                        class="mt-5 w-full py-3.5 rounded-xl font-sans text-sm font-medium transition-colors"
                                        :class="selectedTickets().length > 0
                                            ? 'bg-gray-900 text-white hover:bg-gray-700 cursor-pointer'
                                            : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                                        :disabled="selectedTickets().length === 0">
                                    <span x-show="selectedTickets().length > 0">Lanjut ke Pembayaran</span>
                                    <span x-show="selectedTickets().length === 0">Pilih tiket terlebih dahulu</span>
                                </button>

                                <p class="font-sans text-xs text-center text-gray-400 mt-3">
                                    Anda belum akan dikenakan biaya sekarang
                                </p>
                            </div>
                        </div>

                        {{-- Help note --}}
                        <div class="px-5 py-4 rounded-xl border border-gray-200 bg-white">
                            <p class="font-sans text-sm font-medium text-gray-900">Butuh bantuan?</p>
                            <p class="font-sans text-xs text-gray-400 mt-0.5">
                                Hubungi kami di
                                <a href="mailto:info@bangkiangjaran.com" class="text-gray-700 hover:underline">
                                    info@bangkiangjaran.com
                                </a>
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </form>
    </section>

</div>

@push('scripts')
<script>
function ticketOrder(prices, names) {
    const tickets = {};
    Object.keys(prices).forEach(id => { tickets[id] = 0; });
    return {
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
        pajak()    { return Math.round(this.subtotal() * 0.1); },
        total()    { return this.subtotal() + this.pajak(); },
    };
}
</script>
@endpush
@endsection
