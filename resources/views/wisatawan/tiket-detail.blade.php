@extends('layouts.app')

@section('content')
<div class="bg-background min-h-screen py-xl px-gutter">
    {{-- Header --}}
    <section class="max-w-container-max mx-auto mb-md">
        <nav class="font-body text-caption text-on-surface-variant flex items-center gap-2">
            <a href="{{ url('/') }}" class="hover:text-primary transition-colors">Beranda</a>
            <span class="material-symbols-outlined text-sm text-outline">chevron_right</span>
            <a href="{{ route('tiket.index') }}" class="hover:text-primary transition-colors">Tiket</a>
            <span class="material-symbols-outlined text-sm text-outline">chevron_right</span>
            <span class="text-primary">Detail Tiket</span>
        </nav>
    </section>

    <div class="max-w-4xl mx-auto">
        <div class="bg-surface-container-lowest rounded-2xl card-shadow p-lg md:p-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-lg">
                {{-- Left: Info --}}
                <div class="space-y-md">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-3xl text-primary">confirmation_number</span>
                    </div>
                    <h1 class="font-display text-headline-md text-on-background">{{ $tiket->nama_tiket }}</h1>
                    <p class="font-display text-headline-md text-primary">
                        Rp{{ number_format($tiket->harga, 0, ',', '.') }}
                    </p>
                    <div class="flex items-center gap-2">
                        <span class="font-body text-body-md text-on-surface-variant">Status:</span>
                        @if($tiket->status == 'aktif')
                        <span class="font-body text-label-md text-green-600 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-green-600"></span>
                            Tersedia
                        </span>
                        @else
                        <span class="font-body text-label-md text-red-600 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-red-600"></span>
                            Tidak Tersedia
                        </span>
                        @endif
                    </div>
                    @if($tiket->status == 'aktif')
                    <a href="{{ route('wisatawan.pemesanan.create', ['id_tiket' => $tiket->id_tiket]) }}"
                       class="inline-flex items-center gap-2 bg-primary-container text-white font-body text-label-md px-6 py-3 rounded-xl hover:-translate-y-0.5 transition-all duration-300 shadow-sm">
                        <span class="material-symbols-outlined text-sm">shopping_cart</span>
                        Pesan Sekarang
                    </a>
                    @endif
                </div>

                {{-- Right: Fasilitas --}}
                <div class="bg-background rounded-2xl p-lg">
                    <h3 class="font-display text-headline-sm text-on-background mb-md flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-sm">checklist</span>
                        Fasilitas Tiket Ini
                    </h3>
                    <ul class="space-y-sm">
                        <li class="flex items-start gap-2 font-body text-body-md text-on-surface-variant">
                            <span class="material-symbols-outlined text-sm text-primary shrink-0 mt-0.5">check_circle</span>
                            Akses masuk ke area wisata
                        </li>
                        <li class="flex items-start gap-2 font-body text-body-md text-on-surface-variant">
                            <span class="material-symbols-outlined text-sm text-primary shrink-0 mt-0.5">check_circle</span>
                            Tiket berlaku sesuai tanggal kunjungan
                        </li>
                        <li class="flex items-start gap-2 font-body text-body-md text-on-surface-variant">
                            <span class="material-symbols-outlined text-sm text-primary shrink-0 mt-0.5">check_circle</span>
                            Free akses ke fasilitas umum
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-md text-center">
            <a href="{{ route('tiket.index') }}" class="font-body text-label-md text-primary hover:underline inline-flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Kembali ke Daftar Tiket
            </a>
        </div>
    </div>
</div>
@endsection