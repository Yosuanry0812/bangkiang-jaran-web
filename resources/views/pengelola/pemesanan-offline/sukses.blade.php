@extends('pengelola.layouts.admin')

@section('title', 'Pemesanan Berhasil — Bangkiang Jaran')
@section('page_title', 'Pemesanan Berhasil')

@section('content')
<div class="max-w-3xl mx-auto text-center">

    {{-- Success icon --}}
    <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-5">
        <span class="material-symbols-outlined text-emerald-600" style="font-size:32px">check</span>
    </div>

    <h2 class="font-serif text-[26px] text-slate-900 mb-2">Pemesanan Offline Berhasil</h2>
    <p class="font-sans text-[13px] text-slate-400 mb-8">Tiket sudah aktif dan siap digunakan</p>

    {{-- Booking info card --}}
    <div class="bg-white border border-slate-100/80 rounded-2xl overflow-hidden text-left max-w-lg mx-auto mb-8">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <span class="font-sans text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Detail Pemesanan</span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border border-emerald-200 bg-emerald-50 font-sans text-[10px] font-semibold text-emerald-700">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Lunas
            </span>
        </div>
        <div class="px-6 py-5 space-y-3">
            <div class="flex justify-between">
                <span class="font-sans text-[12px] text-slate-400">Kode Booking</span>
                <span class="font-sans text-[12px] font-semibold text-slate-900 font-mono">{{ $pemesanan->kode_booking }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-sans text-[12px] text-slate-400">Tanggal Kunjungan</span>
                <span class="font-sans text-[12px] text-slate-700">{{ \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->isoFormat('D MMMM YYYY') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-sans text-[12px] text-slate-400">Jumlah Tiket</span>
                <span class="font-sans text-[12px] text-slate-700">{{ $pemesanan->jumlah }} tiket</span>
            </div>
            <div class="flex justify-between">
                <span class="font-sans text-[12px] text-slate-400">Total Bayar</span>
                <span class="font-sans text-[13px] font-semibold text-slate-900">Rp{{ number_format($pemesanan->total_harga, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-sans text-[12px] text-slate-400">Metode Bayar</span>
                <span class="font-sans text-[12px] font-medium text-slate-700">{{ $pemesanan->pembayaran->metode ?? '—' }}</span>
            </div>
        </div>
    </div>

    {{-- Tickets list --}}
    <div class="bg-white border border-slate-100/80 rounded-2xl overflow-hidden text-left max-w-lg mx-auto mb-8">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-sans text-[13px] font-semibold text-slate-800">Kode Tiket</h3>
            <p class="font-sans text-[11px] text-slate-400 mt-0.5">Gunakan QR code ini untuk check-in di pintu masuk</p>
        </div>
        <div class="divide-y divide-slate-50">
            @foreach($pemesanan->detailPemesanan as $d)
            <div class="px-6 py-3.5 flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-slate-500" style="font-size:14px">qr_code</span>
                    </div>
                    <div class="min-w-0">
                        <p class="font-sans text-[12px] font-medium text-slate-800">{{ $d->nama_pengunjung ?? $d->nama_tiket }}</p>
                        <p class="font-sans text-[10px] text-slate-400 font-mono truncate">{{ $d->kode_tiket }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 font-sans text-[9px] font-semibold text-emerald-700 flex-shrink-0 ml-2">
                    <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                    Aktif
                </span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="{{ route('pengelola.pemesanan-offline.create') }}"
           class="inline-flex items-center gap-2 bg-slate-900 text-white font-sans text-[12px] font-semibold px-6 py-3 rounded-xl hover:bg-slate-800 active:scale-[.98] transition-all">
            <span class="material-symbols-outlined" style="font-size:15px">add</span>
            Buat Pemesanan Lagi
        </a>
        <a href="{{ route('pengelola.pemesanan-offline.riwayat') }}"
           class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-600 font-sans text-[12px] font-semibold px-6 py-3 rounded-xl hover:bg-slate-50 active:scale-[.98] transition-all">
            <span class="material-symbols-outlined" style="font-size:15px">history</span>
            Lihat Riwayat
        </a>
        <a href="{{ route('pengelola.dashboard') }}"
           class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-600 font-sans text-[12px] font-semibold px-6 py-3 rounded-xl hover:bg-slate-50 active:scale-[.98] transition-all">
            <span class="material-symbols-outlined" style="font-size:15px">space_dashboard</span>
            Dashboard
        </a>
    </div>

</div>
@endsection
