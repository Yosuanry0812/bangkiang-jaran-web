@extends('pengelola.layouts.admin')

@section('title', 'Pemesanan Offline — Bangkiang Jaran')
@section('page_title', 'Pemesanan Offline')

@push('styles')
<style>
    .ticket-card { transition: box-shadow .2s ease, transform .2s ease; }
    .ticket-card:hover { box-shadow: 0 4px 20px rgba(15,26,23,.06); transform: translateY(-1px); }
    .qty-btn { transition: all .15s ease; }
    .qty-btn:hover { border-color: #0f172a; color: #0f172a; }
</style>
@endpush

@section('content')
@php
    $pricesJson = json_encode($tiketList->pluck('harga', 'id_tiket')->toArray());
    $namesJson  = json_encode($tiketList->pluck('nama_tiket', 'id_tiket')->toArray());
@endphp

<div x-data="offlineOrder({{ $pricesJson }}, {{ $namesJson }})">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="font-serif text-[22px] text-slate-900 leading-tight">Buat Pemesanan Offline</h2>
            <p class="font-sans text-[13px] text-slate-400 mt-0.5">Input manual untuk wisatawan yang datang langsung</p>
        </div>
        <a href="{{ route('pengelola.pemesanan-offline.riwayat') }}"
           class="inline-flex items-center gap-1.5 font-sans text-[12px] font-semibold text-slate-500 hover:text-slate-900 bg-white border border-slate-200 px-4 py-2 rounded-xl hover:bg-slate-50 transition-all self-start">
            <span class="material-symbols-outlined" style="font-size:14px">history</span>
            Riwayat Offline
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('error') || (isset($errors) && $errors->any()))
    <div class="mb-6 border border-red-200 bg-red-50 text-red-700 px-5 py-4 rounded-xl font-sans text-sm">
        @if(session('error')){{ session('error') }}@endif
        @if(isset($errors) && $errors->any())
        <ul class="list-disc list-inside space-y-1 mt-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
        @endif
    </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('pengelola.pemesanan-offline.store') }}">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- LEFT: Ticket selection --}}
            <div class="lg:col-span-7 space-y-5">

                {{-- Date --}}
                <div class="bg-white border border-slate-100/80 rounded-2xl p-5">
                    <label class="block font-sans text-[11px] font-semibold uppercase tracking-[.12em] text-slate-400 mb-2">
                        Tanggal Kunjungan
                    </label>
                    <input type="date"
                           name="tgl_kunjungan"
                           value="{{ $tanggal }}"
                           class="w-full bg-transparent border-0 p-0 font-serif text-xl text-slate-900 focus:ring-0 outline-none cursor-pointer">
                    <p class="font-sans text-[11px] text-slate-400 mt-1.5">Tanggal kunjungan wisatawan</p>
                </div>

                {{-- Ticket list --}}
                <div class="bg-white border border-slate-100/80 rounded-2xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-sans text-[13px] font-semibold text-slate-800">Pilih Tiket</h3>
                            <p class="font-sans text-[11px] text-slate-400 mt-0.5">Tentukan jumlah tiket per jenis</p>
                        </div>
                        <span class="font-sans text-[10px] text-slate-400">Maks 10/tiket</span>
                    </div>

                    <div class="divide-y divide-slate-50">
                        @forelse($tiketList as $t)
                        <div class="ticket-card flex items-center justify-between px-5 py-4 hover:bg-slate-50/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                    @if($t->kategori === 'kendaraan')
                                        <span class="material-symbols-outlined text-slate-500" style="font-size:16px">directions_car</span>
                                    @else
                                        <span class="material-symbols-outlined text-slate-500" style="font-size:16px">person</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-sans text-[13px] font-medium text-slate-800">{{ $t->nama_tiket }}</p>
                                    <p class="font-sans text-[11px] text-slate-400">Rp{{ number_format($t->harga, 0, ',', '.') }}/orang</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <button type="button"
                                        class="qty-btn w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400"
                                        @click="decrement({{ $t->id_tiket }})">
                                    <span class="material-symbols-outlined" style="font-size:14px">remove</span>
                                </button>
                                <span class="font-sans text-sm font-medium w-6 text-center tabular-nums text-slate-800"
                                      x-text="qty({{ $t->id_tiket }})">0</span>
                                <button type="button"
                                        class="qty-btn w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400"
                                        @click="increment({{ $t->id_tiket }})">
                                    <span class="material-symbols-outlined" style="font-size:14px">add</span>
                                </button>
                                <input type="hidden" name="tickets[{{ $t->id_tiket }}]" :value="qty({{ $t->id_tiket }})">
                            </div>
                        </div>
                        @empty
                        <div class="px-5 py-10 text-center">
                            <p class="font-sans text-[13px] text-slate-400">Tidak ada tiket aktif</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <div class="flex flex-wrap gap-x-6 gap-y-2">
                    <span class="font-sans text-[11px] text-slate-400 flex items-center gap-1.5">
                        <span class="material-symbols-outlined" style="font-size:13px">check</span>
                        Harga sudah termasuk pajak
                    </span>
                    <span class="font-sans text-[11px] text-slate-400 flex items-center gap-1.5">
                        <span class="material-symbols-outlined" style="font-size:13px">schedule</span>
                        Tiket berlaku sesuai tanggal
                    </span>
                </div>
            </div>

            {{-- RIGHT: Summary --}}
            <div class="lg:col-span-5">
                <div class="sticky top-24 space-y-4">
                    <div class="bg-white border border-slate-100/80 rounded-2xl overflow-hidden">

                        <div class="px-5 py-4 border-b border-slate-100">
                            <h3 class="font-sans text-[13px] font-semibold text-slate-800">Ringkasan</h3>
                        </div>

                        <div class="p-5">
                            {{-- Empty --}}
                            <div class="py-6 text-center" x-show="selectedTickets().length === 0">
                                <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-2">
                                    <span class="material-symbols-outlined text-slate-300" style="font-size:18px">shopping_cart</span>
                                </div>
                                <p class="font-sans text-[12px] text-slate-400">Belum pilih tiket</p>
                                <p class="font-sans text-[10px] text-slate-300 mt-0.5">Pilih tiket di sebelah kiri</p>
                            </div>

                            {{-- Selected items --}}
                            <div class="space-y-3 mb-5" x-show="selectedTickets().length > 0">
                                <template x-for="item in selectedTickets()" :key="item.id">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-sans text-[12px] font-medium text-slate-800" x-text="item.nama"></p>
                                            <p class="font-sans text-[10px] text-slate-400 mt-0.5"
                                               x-text="item.qty + ' × Rp' + item.harga.toLocaleString('id-ID')"></p>
                                        </div>
                                        <span class="font-sans text-[12px] text-slate-700 tabular-nums"
                                              x-text="'Rp' + (item.qty * item.harga).toLocaleString('id-ID')"></span>
                                    </div>
                                </template>
                            </div>

                            {{-- Totals --}}
                            <div class="border-t border-slate-100 pt-4 space-y-2">
                                <div class="flex justify-between">
                                    <span class="font-sans text-[11px] text-slate-400">Subtotal</span>
                                    <span class="font-sans text-[11px] text-slate-600 tabular-nums"
                                          x-text="'Rp' + subtotal().toLocaleString('id-ID')">Rp0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-sans text-[11px] text-slate-400">Pajak</span>
                                    <span class="font-sans text-[11px] text-slate-600 tabular-nums"
                                          x-text="'Rp' + pajak().toLocaleString('id-ID')">Rp0</span>
                                </div>
                                <div class="flex justify-between items-baseline pt-2 border-t border-slate-100">
                                    <span class="font-sans text-[13px] font-semibold text-slate-800">Total</span>
                                    <span class="font-serif text-[22px] text-slate-900 tabular-nums"
                                          x-text="'Rp' + total().toLocaleString('id-ID')">Rp0</span>
                                </div>
                            </div>

                            {{-- Submit --}}
                            <button type="submit"
                                    class="mt-5 w-full py-3 rounded-xl font-sans text-[13px] font-semibold transition-all"
                                    :class="selectedTickets().length > 0
                                        ? 'bg-slate-900 text-white hover:bg-slate-800 cursor-pointer active:scale-[.98]'
                                        : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                                    :disabled="selectedTickets().length === 0">
                                <span x-show="selectedTickets().length > 0">Lanjut ke Data Pengunjung</span>
                                <span x-show="selectedTickets().length === 0">Pilih Tiket Terlebih Dahulu</span>
                            </button>
                        </div>
                    </div>

                    <div class="bg-amber-50/60 border border-amber-200/50 rounded-xl px-4 py-3.5 flex items-start gap-2.5">
                        <span class="material-symbols-outlined text-amber-500" style="font-size:15px; flex-shrink:0; margin-top:1px;">info</span>
                        <p class="font-sans text-[11px] text-slate-600 leading-relaxed">
                            Pemesanan offline langsung aktif. Pembayaran dicatat otomatis sebagai <strong>Lunas</strong>.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function offlineOrder(prices, names) {
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
