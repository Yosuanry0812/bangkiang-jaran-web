@extends('layouts.app')

@section('content')
<div class="bg-background min-h-screen pb-xl">
    {{-- ═══════════════════════════════════════════════════════════════
         HERO
    ════════════════════════════════════════════════════════════════ --}}
    <section class="relative h-[40vh] min-h-[300px] flex items-center justify-center mb-xl">
        <div class="absolute inset-0 bg-cover bg-center"
             style="background-image: url('https://images.unsplash.com/photo-1503785640985-62d183d5cfe7?auto=format&fit=crop&w=1920&q=80')">
        </div>
        <div class="absolute inset-0 bg-gradient-to-b from-surface/40 to-background"></div>
        <div class="relative z-10 text-center px-gutter max-w-container-max mx-auto w-full mt-lg">
            <h1 class="font-display text-display-mobile md:text-display-lg text-on-background mb-sm">Plan Your Journey</h1>
            <p class="font-body text-body-lg text-on-surface-variant max-w-2xl mx-auto">Select your date and secure your entry to experience the untamed elegance of Bangkiang Jaran.</p>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════
         FILTER + TICKET GRID
    ════════════════════════════════════════════════════════════════ --}}
    <div class="max-w-container-max mx-auto px-gutter w-full -mt-16 relative z-20">
        {{-- Date Filter --}}
        <form method="GET" action="{{ route('tiket.index') }}"
              class="bg-surface-container-lowest/90 backdrop-blur-md card-shadow rounded-2xl p-md flex flex-col md:flex-row items-center justify-between gap-md mb-xl border border-white/50">
            <div class="flex items-center gap-sm">
                <span class="material-symbols-outlined text-primary text-2xl">calendar_month</span>
                <span class="font-display text-headline-sm text-on-background">Pilih Tanggal</span>
            </div>
            <div class="flex gap-sm w-full md:w-auto">
                <input type="date" name="tanggal" value="{{ $tanggal ?? date('Y-m-d') }}"
                       class="w-full md:w-64 bg-surface-container-lowest border border-outline-variant text-on-background rounded-xl px-4 py-3 font-body text-body-md focus:border-primary focus:ring-4 focus:ring-primary/20 transition-all outline-none">
                <button type="submit"
                        class="bg-primary-container text-white font-body text-label-md px-6 py-3 rounded-xl hover:-translate-y-0.5 transition-transform shadow-sm whitespace-nowrap">
                    Cari
                </button>
            </div>
        </form>

        {{-- Ticket Grid --}}
        @if(isset($tiketList) && $tiketList->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-lg">
            @foreach($tiketList as $item)
            <article class="bg-surface-container-lowest rounded-2xl overflow-hidden card-shadow group hover:-translate-y-1 transition-all duration-500 flex flex-col">
                <div class="h-48 relative overflow-hidden">
                    <div class="w-full h-full bg-gradient-to-br from-primary/10 to-primary-container/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-6xl text-primary/30">confirmation_number</span>
                    </div>
                    <div class="absolute top-4 left-4 bg-surface-container-lowest/90 backdrop-blur-sm px-3 py-1 rounded-full border border-white/50 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                        <span class="font-body text-caption text-primary">Tersedia</span>
                    </div>
                </div>
                <div class="p-md flex flex-col flex-grow">
                    <div class="mb-4 flex-grow">
                        <h2 class="font-display text-headline-sm text-on-background mb-2">{{ $item->nama_tiket }}</h2>
                        <p class="font-body text-body-md text-on-surface-variant">Akses penuh ke area wisata.</p>
                    </div>
                    <div class="mb-6">
                        <div class="font-display text-display-mobile text-primary mb-1">Rp{{ number_format($item->harga, 0, ',', '.') }}</div>
                        <div class="font-body text-caption text-outline">Sisa kuota: 150 tiket hari ini</div>
                    </div>
                    <a href="{{ route('wisatawan.pemesanan.create', ['id_tiket' => $item->id_tiket, 'tanggal' => $tanggal ?? date('Y-m-d')]) }}"
                       class="w-full py-3 bg-primary-container text-white rounded-xl font-body text-label-md hover:opacity-90 transition-opacity flex justify-center items-center gap-2">
                        Pesan Sekarang
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
        @else
        {{-- Empty state --}}
        <div class="text-center py-xl">
            <div class="w-20 h-20 mx-auto mb-md rounded-2xl bg-background flex items-center justify-center">
                <span class="material-symbols-outlined text-4xl text-outline">confirmation_number</span>
            </div>
            <p class="font-body text-body-lg text-on-surface-variant">Belum ada tiket tersedia untuk tanggal ini</p>
            <p class="font-body text-body-md text-outline mt-1">Silakan pilih tanggal lain atau hubungi pengelola</p>
        </div>
        @endif
    </div>
</div>
@endsection
