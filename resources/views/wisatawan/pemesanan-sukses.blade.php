@extends('layouts.app')

@section('content')
<div class="bg-background min-h-screen flex items-center justify-center px-gutter py-xl">
    <div class="bg-surface-container-lowest rounded-2xl card-shadow p-lg md:p-xl max-w-lg w-full text-center">
        {{-- Check Icon --}}
        <div class="w-20 h-20 mx-auto mb-md rounded-2xl bg-primary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-4xl text-primary">check_circle</span>
        </div>

        <h1 class="font-display text-headline-md text-on-background mb-sm">Pemesanan Berhasil!</h1>
        <p class="font-body text-body-md text-on-surface-variant mb-lg">Simpan kode booking Anda untuk referensi</p>

        @if(isset($pemesanan))
        {{-- Booking Code --}}
        <div class="bg-background rounded-xl p-md mb-lg">
            <p class="font-body text-caption text-on-surface-variant mb-1">Kode Booking</p>
            <p class="font-mono text-headline-md text-primary tracking-widest">{{ $pemesanan->kode_booking }}</p>
        </div>

        {{-- Summary --}}
        <div class="grid grid-cols-2 gap-sm mb-lg text-left">
            <div class="bg-background rounded-xl p-md">
                <p class="font-body text-caption text-on-surface-variant">Tiket</p>
                <p class="font-body text-label-md text-on-background">{{ $pemesanan->tiket->nama_tiket ?? '-' }}</p>
            </div>
            <div class="bg-background rounded-xl p-md">
                <p class="font-body text-caption text-on-surface-variant">Tanggal</p>
                <p class="font-body text-label-md text-on-background">{{ \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->format('d M Y') }}</p>
            </div>
            <div class="bg-background rounded-xl p-md">
                <p class="font-body text-caption text-on-surface-variant">Jumlah</p>
                <p class="font-body text-label-md text-on-background">{{ $pemesanan->jumlah }} tiket</p>
            </div>
            <div class="bg-background rounded-xl p-md">
                <p class="font-body text-caption text-on-surface-variant">Total</p>
                <p class="font-body text-label-md text-primary">Rp{{ number_format($pemesanan->total_harga, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Status --}}
        <div class="mb-lg">
            <span class="inline-flex items-center gap-1 font-body text-label-md px-4 py-2 rounded-full
                {{ $pemesanan->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                {{ $pemesanan->status == 'diproses' ? 'bg-blue-100 text-blue-700' : '' }}
                {{ $pemesanan->status == 'selesai' ? 'bg-green-100 text-green-700' : '' }}
                {{ $pemesanan->status == 'dibatalkan' ? 'bg-red-100 text-red-700' : '' }}">
                <span class="material-symbols-outlined text-sm">info</span>
                {{ ucfirst($pemesanan->status) }}
            </span>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-md justify-center">
            @if(in_array($pemesanan->status, ['pending', 'diproses']))
            <a href="{{ route('wisatawan.pembayaran.create', $pemesanan->id_pemesanan) }}"
               class="bg-primary-container text-white font-body text-label-md px-6 py-3 rounded-xl hover:-translate-y-0.5 transition-all duration-300 shadow-sm flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-sm">payments</span>
                Upload Bukti Pembayaran
            </a>
            @endif
            <a href="{{ route('wisatawan.pemesanan.detail', $pemesanan->id_pemesanan) }}"
               class="bg-surface-container-low text-on-background font-body text-label-md px-6 py-3 rounded-xl hover:-translate-y-0.5 transition-all duration-300 shadow-sm flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-sm">confirmation_number</span>
                Lihat E-Ticket
            </a>
        </div>
        @endif

        <div class="mt-lg">
            <a href="{{ route('wisatawan.pemesanan.riwayat') }}" class="font-body text-label-md text-primary hover:underline inline-flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">history</span>
                Lihat Riwayat Pemesanan
            </a>
        </div>
    </div>
</div>
@endsection