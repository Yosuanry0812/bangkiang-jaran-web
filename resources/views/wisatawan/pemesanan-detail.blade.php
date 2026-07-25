@extends('layouts.app')

@section('title', 'E-Ticket — Bangkiang Jaran')

@section('content')
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .page-break { page-break-before: always; }
    }
    .ticket-card {
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
        transition: box-shadow 0.2s;
    }
    .ticket-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
</style>

<div class="min-h-screen pt-28 pb-16 px-gutter bg-[#F8F7F5]">
    <div class="max-w-2xl mx-auto">

        {{-- Breadcrumb --}}
        <div class="mb-6 no-print" data-aos="fade-up">
            <nav class="flex items-center gap-2 text-xs text-stone font-sans">
                <a href="{{ route('wisatawan.pemesanan.riwayat') }}" class="hover:text-forest transition-colors">Riwayat</a>
                <span class="material-symbols-outlined text-sm text-pebble">chevron_right</span>
                <span class="text-forest font-medium">E-Ticket</span>
            </nav>
        </div>

        {{-- Order info header --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6" data-aos="fade-up">
            <div class="bg-forest px-5 py-4 flex items-center justify-between">
                <div>
                    <p class="font-sans text-[10px] uppercase tracking-[0.15em] text-white/40">Kode Booking</p>
                    <p class="font-mono text-lg font-bold text-white tracking-wider">{{ $pemesanan->kode_booking }}</p>
                </div>
                <div class="text-right">
                    <p class="font-sans text-[10px] uppercase tracking-[0.15em] text-white/40">Status</p>
                    @php
                        $statusClass = match($pemesanan->status) {
                            'selesai' => 'bg-green-500',
                            'pending' => 'bg-amber-500',
                            'dibatalkan' => 'bg-red-500',
                            default => 'bg-gray-400',
                        };
                        $statusLabel = match($pemesanan->status) {
                            'selesai' => 'LUNAS',
                            'pending' => 'Menunggu Bayar',
                            'diproses' => 'Diverifikasi',
                            'dibatalkan' => 'Dibatalkan',
                            default => ucfirst($pemesanan->status),
                        };
                    @endphp
                    <span class="inline-block {{ $statusClass }} text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full">{{ $statusLabel }}</span>
                </div>
            </div>
            <div class="px-5 py-4 flex flex-wrap gap-x-6 gap-y-2 text-xs text-stone font-sans">
                <div>
                    <span class="text-pebble">Tanggal Kunjungan:</span>
                    <span class="font-medium text-ink ml-1">{{ \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->isoFormat('D MMMM YYYY') }}</span>
                </div>
                <div>
                    <span class="text-pebble">Jumlah Tiket:</span>
                    <span class="font-medium text-ink ml-1">{{ $pemesanan->detailPemesanan->count() }} tiket</span>
                </div>
                <div>
                    <span class="text-pebble">Total:</span>
                    <span class="font-medium text-ink ml-1">Rp{{ number_format($pemesanan->total_harga, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Individual ticket cards --}}
        <div class="space-y-4">
            @foreach($pemesanan->detailPemesanan as $i => $item)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden {{ $i > 0 ? 'page-break' : '' }}" data-aos="fade-up" @if($i > 0) data-aos-delay="{{ min($i * 30, 150) }}" @endif>
                {{-- Ticket type badge --}}
                <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        @if($item->plat_kendaraan)
                            <span class="material-symbols-outlined text-forest text-sm">directions_car</span>
                        @else
                            <span class="material-symbols-outlined text-forest text-sm">person</span>
                        @endif
                        <span class="font-sans text-xs font-medium text-ink">{{ $item->nama_tiket }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full {{ $item->status_tiket === 'aktif' ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                        <span class="font-sans text-[10px] uppercase tracking-wider {{ $item->status_tiket === 'aktif' ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $item->status_tiket === 'aktif' ? 'Aktif' : ucfirst($item->status_tiket) }}
                        </span>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-5 flex items-start gap-5">
                    {{-- QR code --}}
                    <div class="w-32 h-32 bg-white rounded-xl border border-gray-200 flex items-center justify-center flex-shrink-0 p-1">
                        @php $qrUrl = request()->getSchemeAndHttpHost() . '/tiket/verifikasi/' . $item->kode_tiket; @endphp
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ urlencode($qrUrl) }}"
                             alt="QR {{ $item->kode_tiket }}"
                             class="w-full h-full object-contain"
                             loading="lazy"
                             onerror="this.parentElement.innerHTML='<span class=\'material-symbols-outlined text-gray-300\' style=\'font-size:40px;\'>qr_code</span>'">
                    </div>

                    {{-- Info --}}
                    <div class="min-w-0 flex-1">
                        <div class="grid grid-cols-2 gap-y-2.5 gap-x-4">
                            <div>
                                <p class="font-sans text-[10px] uppercase tracking-wider text-pebble">Kode Tiket</p>
                                <p class="font-mono text-sm font-bold text-forest tracking-wider mt-0.5">{{ $item->kode_tiket }}</p>
                            </div>
                            <div>
                                <p class="font-sans text-[10px] uppercase tracking-wider text-pebble">
                                    {{ $item->plat_kendaraan ? 'Plat Kendaraan' : 'Nama Pengunjung' }}
                                </p>
                                <p class="font-sans text-sm font-medium text-ink mt-0.5">
                                    {{ $item->plat_kendaraan ?? $item->nama_pengunjung ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="font-sans text-[10px] uppercase tracking-wider text-pebble">Harga</p>
                                <p class="font-sans text-sm text-ink mt-0.5">Rp{{ number_format($item->harga, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="font-sans text-[10px] uppercase tracking-wider text-pebble">Tanggal</p>
                                <p class="font-sans text-sm text-ink mt-0.5">{{ \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Actions --}}
        <div class="mt-8 flex flex-col sm:flex-row gap-3 no-print" data-aos="fade-up">
            <button onclick="window.print()"
                    class="flex-1 flex items-center justify-center gap-2 bg-forest text-white font-sans text-sm font-semibold py-3.5 rounded-xl hover:bg-leaf transition-colors">
                <span class="material-symbols-outlined text-sm">download</span>
                Cetak / Download PDF
            </button>
            <a href="{{ route('wisatawan.pemesanan.riwayat') }}"
               class="flex-1 flex items-center justify-center gap-2 border border-gray-200 text-stone font-sans text-sm font-medium py-3.5 rounded-xl hover:border-gray-300 hover:text-ink transition-colors">
                <span class="material-symbols-outlined text-sm">history</span>
                Kembali ke Riwayat
            </a>
        </div>

        {{-- Info --}}
        <div class="mt-6 flex items-start gap-2.5 bg-amber-50 border border-amber-200/60 rounded-xl px-4 py-3.5 no-print" data-aos="fade-up">
            <span class="material-symbols-outlined text-amber-500 text-sm mt-0.5 flex-shrink-0">info</span>
            <p class="font-sans text-xs text-stone leading-relaxed">
                Setiap tiket memiliki kode unik masing-masing. Pengunjung bisa masuk satu per satu tanpa harus menunggu seluruh rombongan. Tunjukkan kode tiket di pintu masuk.
            </p>
        </div>
    </div>
</div>
@endsection
