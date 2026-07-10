@extends('layouts.app')

@section('content')
<style>
    .ticket-perforation {
        position: relative;
    }
    .ticket-perforation::before, .ticket-perforation::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 32px;
        height: 32px;
        background-color: theme('colors.surface-container-lowest');
        border-radius: 50%;
        transform: translateY(-50%);
        z-index: 10;
    }
    .ticket-perforation::before { left: -16px; }
    .ticket-perforation::after { right: -16px; }
    .dashed-line {
        border-top: 2px dashed theme('colors.outline-variant');
        position: absolute;
        top: 50%;
        left: 20px;
        right: 20px;
        z-index: 5;
    }
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
    }
</style>

<div class="bg-background min-h-screen py-xl px-gutter">
    {{-- Breadcrumb (no-print) --}}
    <div class="max-w-md mx-auto mb-md no-print">
        <nav class="font-body text-caption text-on-surface-variant flex items-center gap-2">
            <a href="{{ route('wisatawan.pemesanan.riwayat') }}" class="hover:text-primary transition-colors">{{ __('messages.history') }}</a>
            <span class="material-symbols-outlined text-sm text-outline">chevron_right</span>
            <span class="text-primary">{{ __('messages.view_eticket') }}</span>
        </nav>
    </div>

    @if(isset($pemesanan))
    {{-- E-Ticket Container --}}
    <div class="w-full max-w-md mx-auto relative z-10 flex flex-col items-center">
        {{-- The Ticket --}}
        <div class="w-full bg-surface-container-lowest rounded-2xl card-shadow overflow-hidden flex flex-col">
            {{-- Ticket Header/Image area --}}
            <div class="relative h-48 w-full bg-surface-container">
                <div class="absolute inset-0 bg-gradient-to-br from-primary/20 to-primary-container/40"></div>
                <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1555400038-63f5ba517a47?auto=format&fit=crop&w=800&q=80')] bg-cover bg-center opacity-30"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                <div class="absolute bottom-md left-md right-md text-white flex justify-between items-end">
                    <div>
                        <p class="font-body text-label-md opacity-80 uppercase tracking-widest mb-xs">{{ __('messages.eticket_label') }}</p>
                        <h2 class="font-display text-headline-sm">Bangkiang Jaran</h2>
                    </div>
                    <div class="bg-primary-container text-white px-3 py-1 rounded-full backdrop-blur-md">
                        <span class="material-symbols-outlined text-[20px] align-middle mr-1">forest</span>
                        <span class="font-body text-label-md align-middle">{{ $pemesanan->tiket->nama_tiket ?? __('messages.standard_ticket') }}</span>
                    </div>
                </div>
            </div>

            {{-- Ticket Details Area --}}
            <div class="p-lg bg-surface-container-lowest">
                <div class="grid grid-cols-2 gap-y-md gap-x-sm mb-lg">
                    <div>
                        <p class="font-body text-caption text-outline mb-xs uppercase tracking-wider">{{ __('messages.visitor_name') }}</p>
                        <p class="font-body text-body-md font-semibold text-on-surface">{{ $pemesanan->user->name ?? Auth::user()->name ?? '-' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-body text-caption text-outline mb-xs uppercase tracking-wider">{{ __('messages.booking_id') }}</p>
                        <p class="font-body text-body-md font-semibold text-primary">{{ $pemesanan->kode_booking }}</p>
                    </div>
                    <div>
                        <p class="font-body text-caption text-outline mb-xs uppercase tracking-wider">{{ __('messages.visit_date_label') }}</p>
                        <p class="font-body text-body-md font-semibold text-on-surface">{{ \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->format('d M Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-body text-caption text-outline mb-xs uppercase tracking-wider">{{ __('messages.party_size') }}</p>
                        <p class="font-body text-body-md font-semibold text-on-surface">{{ __('messages.ticket_count', ['count' => $pemesanan->jumlah]) }}</p>
                    </div>
                </div>
            </div>

            {{-- Perforation Line --}}
            <div class="ticket-perforation h-8 relative bg-surface-container-lowest">
                <div class="dashed-line"></div>
            </div>

            {{-- QR Code Area --}}
            <div class="p-lg bg-surface-container-lowest flex flex-col items-center justify-center">
                <div class="bg-white p-4 rounded-xl card-shadow border border-outline-variant/30 mb-md">
                    <div class="w-40 h-40 bg-surface-container flex items-center justify-center rounded-lg border border-dashed border-outline-variant">
                        <span class="material-symbols-outlined text-[64px] text-outline opacity-50">qr_code</span>
                    </div>
                </div>
                <p class="font-body text-caption text-center text-outline max-w-[200px]">{{ __('messages.scan_code_desc') }}</p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-xl w-full flex flex-col gap-md no-print">
            <button onclick="window.print()"
                    class="w-full bg-primary-container text-white font-body text-label-md py-4 rounded-xl flex items-center justify-center gap-sm hover:-translate-y-0.5 transition-transform card-shadow">
                <span class="material-symbols-outlined">download</span>
                {{ __('messages.download_pdf') }}
            </button>
            <a href="{{ route('wisatawan.pemesanan.riwayat') }}"
               class="w-full border border-primary-container text-primary font-body text-label-md py-4 rounded-xl flex items-center justify-center gap-sm hover:bg-primary/5 transition-colors">
                <span class="material-symbols-outlined">history</span>
                {{ __('messages.back_to_history') }}
            </a>
        </div>
    </div>
    @endif
</div>
@endsection