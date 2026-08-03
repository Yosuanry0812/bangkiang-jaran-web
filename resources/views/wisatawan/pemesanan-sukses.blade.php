@extends('layouts.app')
@section('nav-mode', 'light')

@section('title', 'Pemesanan Berhasil — Bangkiang Jaran')

@section('content')
<div class="min-h-screen flex items-center justify-center px-gutter py-20" style="background:#F8F7F5;">
    <div class="bg-white rounded-3xl card-shadow border border-gray-100 shadow-sm p-6 md:p-10 max-w-lg w-full text-center">

        {{-- Check Icon --}}
        <div class="w-20 h-20 mx-auto mb-6 rounded-2xl bg-forest/8 flex items-center justify-center">
            <span class="material-symbols-outlined text-4xl text-forest">check_circle</span>
        </div>

        <h1 class="font-serif text-3xl md:text-4xl text-forest mb-3">{{ __('messages.booking_success') }}</h1>
        <p class="font-sans text-sm text-stone leading-relaxed mb-6">{{ __('messages.booking_success_desc') }}</p>

        @if(isset($pemesanan))
        {{-- Booking Code --}}
        <div class="bg-gray-50 rounded-xl p-4 mb-6">
            <p class="font-sans text-xs text-stone mb-1">{{ __('messages.booking_code') }}</p>
            <p class="font-mono text-2xl text-forest tracking-widest">{{ $pemesanan->kode_booking }}</p>
        </div>

        {{-- Summary --}}
        <div class="grid grid-cols-2 gap-3 mb-6 text-left">
            <div class="bg-gray-50 rounded-xl p-4 col-span-2 sm:col-span-1">
                <p class="font-sans text-[10px] uppercase tracking-wider text-pebble mb-0.5">{{ __('messages.ticket_label') }}</p>
                @php
                    $tSum = $pemesanan->detailPemesanan->groupBy('nama_tiket')->map(fn($g) => $g->count() . ' ' . $g->first()->nama_tiket)->implode(', ');
                @endphp
                <p class="font-sans text-sm font-medium text-ink">{{ $tSum ?: ($pemesanan->tiket->nama_tiket ?? '-') }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="font-sans text-[10px] uppercase tracking-wider text-pebble mb-0.5">{{ __('messages.date_label') }}</p>
                <p class="font-sans text-sm font-medium text-ink">{{ \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->format('d M Y') }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="font-sans text-[10px] uppercase tracking-wider text-pebble mb-0.5">{{ __('messages.quantity_label') }}</p>
                <p class="font-sans text-sm font-medium text-ink">{{ $pemesanan->detailPemesanan->count() }} tiket</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="font-sans text-[10px] uppercase tracking-wider text-pebble mb-0.5">{{ __('messages.total_label') }}</p>
                <p class="font-sans text-sm font-semibold text-forest">Rp{{ number_format($pemesanan->total_harga, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Status --}}
        <div class="mb-6">
            <span class="inline-flex items-center gap-1 font-sans text-sm px-4 py-2 rounded-full
                {{ $pemesanan->status == 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                {{ $pemesanan->status == 'diproses' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                {{ $pemesanan->status == 'selesai' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                {{ $pemesanan->status == 'dibatalkan' ? 'bg-red-50 text-red-700 border border-red-200' : '' }}">
                <span class="material-symbols-outlined text-sm">info</span>
                {{ ucfirst($pemesanan->status) }}
            </span>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            @if(in_array($pemesanan->status, ['pending', 'diproses']))
            <a href="{{ route('wisatawan.pembayaran.create', $pemesanan->id_pemesanan) }}"
               class="inline-flex items-center justify-center gap-2 bg-forest text-white font-sans text-sm font-semibold px-6 py-3 rounded-xl hover:bg-leaf transition-all duration-300 shadow-sm">
                <span class="material-symbols-outlined text-sm">payments</span>
                {{ __('messages.upload_payment') }}
            </a>
            @endif
            <a href="{{ route('wisatawan.pemesanan.detail', $pemesanan->id_pemesanan) }}"
               class="inline-flex items-center justify-center gap-2 border border-gray-200 text-stone font-sans text-sm font-medium px-6 py-3 rounded-xl hover:border-gray-400 hover:text-ink transition-all duration-300 shadow-sm">
                <span class="material-symbols-outlined text-sm">confirmation_number</span>
                {{ __('messages.view_eticket') }}
            </a>
        </div>
        @endif

        <div class="mt-8">
            <a href="{{ route('wisatawan.pemesanan.riwayat') }}" class="font-sans text-sm text-forest hover:underline inline-flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">history</span>
                {{ __('messages.view_booking_history') }}
            </a>
        </div>
    </div>
</div>
@endsection
