@extends('layouts.app')

@section('title', ($tiket->nama_tiket ?? 'Detail Tiket') . ' — Bangkiang Jaran')

@section('content')
<div class="min-h-screen" style="background:#F8F7F5;">

    {{-- ── Breadcrumb ──────────────────────────────────────── --}}
    <div class="pt-28 pb-0 px-gutter">
        <div class="max-w-5xl mx-auto">
            <nav class="flex items-center gap-2 font-sans text-xs text-stone" aria-label="Breadcrumb">
                <a href="{{ url('/') }}" class="hover:text-forest transition-colors">Beranda</a>
                <span class="text-gray-300">/</span>
                <a href="{{ route('tiket.index') }}" class="hover:text-forest transition-colors">Tiket</a>
                <span class="text-gray-300">/</span>
                <span class="text-forest">Detail Tiket</span>
            </nav>
        </div>
    </div>

    {{-- ── Main Content ────────────────────────────────────── --}}
    <section class="py-12 px-gutter">
        <div class="max-w-5xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                {{-- ── Left: Ticket Card ─────────────────── --}}
                <div class="lg:col-span-7" data-aos="fade-right">
                    {{-- Ticket UI --}}
                    <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100">
                        {{-- Header band --}}
                        <div class="bg-forest px-8 py-8 relative overflow-hidden">
                            <div class="absolute right-0 top-0 bottom-0 w-40 opacity-5">
                                <span class="material-symbols-outlined text-white" style="font-size:200px; line-height:1; transform:rotate(-15deg); display:block; margin-top:-30px; margin-right:-30px;">confirmation_number</span>
                            </div>
                            <p class="font-sans text-[10px] tracking-[0.2em] uppercase text-white/40 mb-3">Bangkiang Jaran Waterfall</p>
                            <h1 class="font-serif text-3xl text-white mb-1">{{ $tiket->nama_tiket }}</h1>
                            <p class="font-sans text-sm text-white/50">Tiket Masuk · 1 Hari Kunjungan</p>
                        </div>

                        {{-- Perforation --}}
                        <div class="relative flex items-center px-0 bg-white">
                            <div class="w-6 h-6 rounded-full -ml-3 flex-shrink-0" style="background:#F8F7F5;"></div>
                            <div class="flex-1 border-t-2 border-dashed border-gray-150 mx-2"></div>
                            <div class="w-6 h-6 rounded-full -mr-3 flex-shrink-0" style="background:#F8F7F5;"></div>
                        </div>

                        {{-- Body --}}
                        <div class="px-8 py-7 bg-white">
                            {{-- Price & Status --}}
                            <div class="flex items-center justify-between mb-8">
                                <div>
                                    <p class="font-sans text-xs text-stone uppercase tracking-widest mb-1">Harga</p>
                                    <div class="flex items-baseline gap-1">
                                        <span class="font-sans text-sm text-stone">Rp</span>
                                        <span class="font-serif text-4xl text-forest">{{ number_format($tiket->harga, 0, ',', '.') }}</span>
                                    </div>
                                    <p class="font-sans text-xs text-pebble mt-0.5">per orang</p>
                                </div>
                                @if($tiket->status == 'aktif')
                                <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 font-sans text-xs font-medium px-3.5 py-1.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    {{ __('messages.ticket_available') }}
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 bg-red-50 text-red-700 border border-red-200 font-sans text-xs font-medium px-3.5 py-1.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    {{ __('messages.ticket_unavailable') }}
                                </span>
                                @endif
                            </div>

                            {{-- Divider --}}
                            <div class="h-px bg-gray-100 mb-7"></div>

                            {{-- Facilities --}}
                            <div class="mb-8">
                                <p class="font-sans text-xs text-stone uppercase tracking-widest mb-4">{{ __('messages.facilities_title') }}</p>
                                <ul class="space-y-3">
                                    <li class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-lg bg-forest/8 flex items-center justify-center flex-shrink-0">
                                            <span class="material-symbols-outlined text-forest text-base icon-fill">check_circle</span>
                                        </div>
                                        <span class="font-sans text-sm text-ink">{{ __('messages.facility_access_entry') }}</span>
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-lg bg-forest/8 flex items-center justify-center flex-shrink-0">
                                            <span class="material-symbols-outlined text-forest text-base icon-fill">check_circle</span>
                                        </div>
                                        <span class="font-sans text-sm text-ink">{{ __('messages.facility_valid_date') }}</span>
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-lg bg-forest/8 flex items-center justify-center flex-shrink-0">
                                            <span class="material-symbols-outlined text-forest text-base icon-fill">check_circle</span>
                                        </div>
                                        <span class="font-sans text-sm text-ink">{{ __('messages.facility_free_access') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Back link --}}
                    <div class="mt-5">
                        <a href="{{ route('tiket.index') }}"
                           class="inline-flex items-center gap-1.5 font-sans text-sm text-stone hover:text-forest transition-colors">
                            <span class="material-symbols-outlined text-sm">arrow_back</span>
                            {{ __('messages.back_to_tickets') }}
                        </a>
                    </div>
                </div>

                {{-- ── Right: Order Panel ─────────────────── --}}
                <div class="lg:col-span-5 lg:sticky lg:top-28" data-aos="fade-left" data-aos-delay="80">
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-7 pt-7 pb-6 border-b border-gray-100">
                            <h3 class="font-serif text-xl text-forest mb-0.5">Pesan Tiket Ini</h3>
                            <p class="font-sans text-xs text-stone">Konfirmasi langsung via email</p>
                        </div>

                        <div class="px-7 py-6">
                            {{-- Summary row --}}
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="font-sans text-sm text-stone">{{ $tiket->nama_tiket }}</span>
                                <span class="font-sans text-sm text-forest font-semibold">Rp{{ number_format($tiket->harga, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="font-sans text-sm text-stone">Jam Buka</span>
                                <span class="font-sans text-sm text-ink">07:00 – 18:00 WITA</span>
                            </div>
                            <div class="flex items-center justify-between py-3">
                                <span class="font-sans text-sm text-stone">Lokasi</span>
                                <span class="font-sans text-sm text-ink">Desa Bakbakan, Gianyar</span>
                            </div>

                            {{-- CTA --}}
                            <div class="mt-6 space-y-2.5">
                                @if($tiket->status == 'aktif')
                                @auth
                                <a href="{{ route('wisatawan.pemesanan.create', ['id_tiket' => $tiket->id_tiket]) }}"
                                   class="flex items-center justify-center gap-2 w-full bg-forest text-white font-sans text-sm font-semibold py-3.5 rounded-xl hover:bg-leaf transition-colors">
                                    <span class="material-symbols-outlined text-sm">shopping_cart</span>
                                    {{ __('messages.book_now_btn') }}
                                </a>
                                @else
                                <a href="{{ route('login') }}"
                                   class="flex items-center justify-center gap-2 w-full bg-forest text-white font-sans text-sm font-semibold py-3.5 rounded-xl hover:bg-leaf transition-colors">
                                    <span class="material-symbols-outlined text-sm">login</span>
                                    Masuk untuk Memesan
                                </a>
                                @endauth
                                @else
                                <button disabled
                                        class="flex items-center justify-center gap-2 w-full bg-gray-100 text-gray-400 font-sans text-sm font-semibold py-3.5 rounded-xl cursor-not-allowed">
                                    Tiket Tidak Tersedia
                                </button>
                                @endif
                                <a href="{{ route('tiket.index') }}"
                                   class="flex items-center justify-center w-full border border-gray-200 text-stone font-sans text-sm py-3.5 rounded-xl hover:border-gray-400 hover:text-ink transition-colors">
                                    Lihat Tiket Lainnya
                                </a>
                            </div>

                            {{-- Note --}}
                            <p class="font-sans text-xs text-center text-pebble mt-4">
                                <span class="material-symbols-outlined text-xs align-middle mr-0.5">lock</span>
                                Transaksi aman & terenkripsi
                            </p>
                        </div>
                    </div>

                    {{-- Help card --}}
                    <div class="mt-4 bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-forest/8 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-forest text-sm">support_agent</span>
                            </div>
                            <div>
                                <p class="font-sans text-sm font-medium text-ink mb-0.5">Butuh bantuan?</p>
                                <p class="font-sans text-xs text-stone leading-relaxed">
                                    Hubungi kami di
                                    <a href="mailto:info@bangkiangjaran.com" class="text-forest hover:underline">info@bangkiangjaran.com</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>
@endsection
