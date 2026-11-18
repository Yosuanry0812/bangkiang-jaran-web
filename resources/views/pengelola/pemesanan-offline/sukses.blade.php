@extends('pengelola.layouts.admin')

@section('title', __('ui.t_booking_done'))
@section('page_title', __('ui.t_booking_done_short'))

@section('content')
<div class="max-w-3xl mx-auto text-center">

    {{-- Success icon --}}
    <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-5">
        <span class="material-symbols-outlined text-emerald-600" style="font-size:32px">check</span>
    </div>

    <h1 class="font-serif text-[26px] text-slate-900 mb-2">{{ __('panel.pemesanan_offline_berhasil_e21e') }}</h1>
    <p class="font-sans text-[13px] text-slate-400 mb-8">{{ __('panel.tiket_sudah_aktif_dan_7e38') }}</p>

    {{-- Booking info card --}}
    <div class="bg-white border border-slate-100/80 rounded-2xl overflow-hidden text-left max-w-lg mx-auto mb-8">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <span class="font-sans text-[11px] font-semibold text-slate-400 uppercase tracking-wider">{{ __('panel.detail_pemesanan_a856') }}</span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border border-emerald-200 bg-emerald-50 font-sans text-[10px] font-semibold text-emerald-700">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                {{ __('panel.lunas_3928') }}
            </span>
        </div>
        <div class="px-6 py-5 space-y-3">
            <div class="flex justify-between">
                <span class="font-sans text-[12px] text-slate-400">{{ __('panel.kode_booking_6179') }}</span>
                <span class="font-sans text-[12px] font-semibold text-slate-900 font-mono">{{ $pemesanan->kode_booking }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-sans text-[12px] text-slate-400">{{ __('panel.tanggal_kunjungan_3ddb') }}</span>
                <span class="font-sans text-[12px] text-slate-700">{{ \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->isoFormat('D MMMM YYYY') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-sans text-[12px] text-slate-400">{{ __('panel.jumlah_tiket_e45f') }}</span>
                <span class="font-sans text-[12px] text-slate-700">{{ trans_choice('ui.tickets_n', $pemesanan->jumlah) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-sans text-[12px] text-slate-400">{{ __('panel.total_bayar_8d61') }}</span>
                <span class="font-sans text-[13px] font-semibold text-slate-900">Rp{{ number_format($pemesanan->total_harga, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-sans text-[12px] text-slate-400">{{ __('panel.metode_bayar_dcb3') }}</span>
                <span class="font-sans text-[12px] font-medium text-slate-700">{{ \App\Support\Label::method($pemesanan->pembayaran->metode ?? null) ?: '—' }}</span>
            </div>
        </div>
    </div>

    {{-- Tickets list --}}
    <div class="bg-white border border-slate-100/80 rounded-2xl overflow-hidden text-left max-w-lg mx-auto mb-8">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-sans text-[13px] font-semibold text-slate-800">{{ __('panel.kode_tiket_1e81') }}</h3>
            <p class="font-sans text-[11px] text-slate-400 mt-0.5">{{ __('panel.gunakan_qr_code_ini_603f') }}</p>
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
                    {{ __('panel.aktif_81c3') }}
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
            {{ __('panel.buat_pemesanan_lagi_a6bf') }}
        </a>
        <a href="{{ route('pengelola.pemesanan-offline.riwayat') }}"
           class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-600 font-sans text-[12px] font-semibold px-6 py-3 rounded-xl hover:bg-slate-50 active:scale-[.98] transition-all">
            <span class="material-symbols-outlined" style="font-size:15px">history</span>
            {{ __('panel.lihat_riwayat_4b83') }}
        </a>
        <a href="{{ route('pengelola.dashboard') }}"
           class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-600 font-sans text-[12px] font-semibold px-6 py-3 rounded-xl hover:bg-slate-50 active:scale-[.98] transition-all">
            <span class="material-symbols-outlined" style="font-size:15px">space_dashboard</span>
            Dashboard
        </a>
    </div>

</div>
@include('partials.booking-lifecycle')
@endsection
