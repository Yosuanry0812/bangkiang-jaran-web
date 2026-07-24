@extends('layouts.app')

@section('title', 'Pembayaran — Bangkiang Jaran')

@section('content')
@php
$metodeBayar = [
    ['id'=>'BCA',     'nama'=>'Bank BCA',     'tipe'=>'bank',    'logo'=>'bca.jpg',     'nomor'=>'1234567890',   'atas_nama'=>'Pengelola Bangkiang Jaran'],
    ['id'=>'BRI',     'nama'=>'Bank BRI',     'tipe'=>'bank',    'logo'=>'bri.webp',    'nomor'=>'0987654321',   'atas_nama'=>'Pengelola Bangkiang Jaran'],
    ['id'=>'Mandiri', 'nama'=>'Bank Mandiri', 'tipe'=>'bank',    'logo'=>'mandiri.webp','nomor'=>'1122334455',   'atas_nama'=>'Pengelola Bangkiang Jaran'],
    ['id'=>'Dana',    'nama'=>'Dana',         'tipe'=>'ewallet', 'logo'=>'dana.webp',   'nomor'=>'081234567890', 'atas_nama'=>'Pengelola Bangkiang Jaran'],
    ['id'=>'OVO',     'nama'=>'OVO',          'tipe'=>'ewallet', 'logo'=>'ovo.png',     'nomor'=>'081234567890', 'atas_nama'=>'Pengelola Bangkiang Jaran'],
    ['id'=>'GoPay',   'nama'=>'GoPay',        'tipe'=>'ewallet', 'logo'=>'gopay.jpg',   'nomor'=>'081234567890', 'atas_nama'=>'Pengelola Bangkiang Jaran'],
    ['id'=>'QRIS',    'nama'=>'QRIS',         'tipe'=>'qris',    'logo'=>'qris.webp',   'nomor'=>null,           'atas_nama'=>'Pengelola Bangkiang Jaran'],
];
@endphp

{{-- ── Fixed top progress bar ───────────────────────── --}}
<div class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-5xl mx-auto px-gutter py-3 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <span class="font-sans text-xs text-stone">Kode Booking:</span>
            <span class="font-sans text-xs font-semibold text-forest bg-forest/8 px-3 py-1 rounded-full">
                {{ $pemesanan->kode_booking ?? '-' }}
            </span>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <div class="flex items-center gap-1.5" id="step-1-indicator">
                <div class="w-7 h-7 rounded-full bg-forest text-white flex items-center justify-center text-xs font-bold" id="step-1-circle">1</div>
                <span class="font-sans font-semibold text-forest hidden sm:inline">Metode</span>
            </div>
            <div class="w-8 h-px bg-gray-200"></div>
            <div class="flex items-center gap-1.5" id="step-2-indicator">
                <div class="w-7 h-7 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center text-xs font-bold" id="step-2-circle">2</div>
                <span class="font-sans text-gray-400 hidden sm:inline" id="step-2-label">Upload</span>
            </div>
            <div class="w-8 h-px bg-gray-200"></div>
            <div class="flex items-center gap-1.5">
                <div class="w-7 h-7 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center text-xs font-bold">3</div>
                <span class="font-sans text-gray-400 hidden sm:inline">Selesai</span>
            </div>
        </div>
    </div>
</div>

{{-- ── Page body ─────────────────────────────────────── --}}
<div class="min-h-screen pt-20 pb-16 px-gutter" style="background:#F8F7F5;">
    <div class="max-w-5xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- ── LEFT: Steps ─────────────────────────── --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Step 1: Pilih Metode --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" id="panel-step-1">
                    <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100 bg-forest">
                        <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">1</div>
                        <h2 class="font-serif text-lg text-white">Pilih Metode Pembayaran</h2>
                    </div>
                    <div class="p-6 space-y-6">

                        {{-- Bank Transfer --}}
                        <div>
                            <p class="font-sans text-[10px] font-semibold text-stone uppercase tracking-[0.15em] mb-3">Bank Transfer</p>
                            <div class="space-y-2">
                                @foreach($metodeBayar as $m)
                                @if($m['tipe'] === 'bank')
                                <div class="border border-gray-100 rounded-xl overflow-hidden transition-all duration-200" id="card-{{ $m['id'] }}">
                                    <button type="button"
                                            onclick="pilihMetode('{{ $m['id'] }}', '{{ $m['nama'] }}', '{{ $m['nomor'] }}', '{{ $m['atas_nama'] }}')"
                                            class="w-full flex items-center justify-between px-4 py-3.5 hover:bg-gray-50 transition-colors text-left">
                                        <div class="flex items-center gap-3">
                                            <div class="w-11 h-11 rounded-xl bg-white border border-gray-200 flex items-center justify-center flex-shrink-0 p-1.5">
                                                <img src="{{ asset('images/payments/' . $m['logo']) }}" alt="{{ $m['nama'] }}" class="w-full h-full object-contain">
                                            </div>
                                            <span class="font-sans text-sm font-medium text-ink">{{ $m['nama'] }}</span>
                                        </div>
                                        <span class="material-symbols-outlined text-pebble text-base transition-transform duration-200" id="icon-{{ $m['id'] }}">expand_more</span>
                                    </button>
                                    <div class="hidden px-4 pb-4 pt-0 bg-gray-50 border-t border-gray-100" id="detail-{{ $m['id'] }}">
                                        <div class="pt-4 flex items-center justify-between">
                                            <div>
                                                <p class="font-sans text-xs text-stone mb-1">Nomor Rekening</p>
                                                <p class="font-sans text-xl font-semibold text-forest tracking-widest">{{ $m['nomor'] }}</p>
                                                <p class="font-sans text-xs text-stone mt-0.5">a.n. {{ $m['atas_nama'] }}</p>
                                            </div>
                                            <button type="button"
                                                    onclick="salin('{{ $m['nomor'] }}', 'salin-{{ $m['id'] }}')"
                                                    class="flex items-center gap-1.5 text-forest border border-forest/25 hover:bg-forest hover:text-white hover:border-forest transition-colors px-3 py-2 rounded-lg text-xs font-medium">
                                                <span class="material-symbols-outlined text-sm" id="salin-{{ $m['id'] }}">content_copy</span>
                                                Salin
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>

                        {{-- E-Wallet --}}
                        <div>
                            <p class="font-sans text-[10px] font-semibold text-stone uppercase tracking-[0.15em] mb-3">E-Wallet</p>
                            <div class="space-y-2">
                                @foreach($metodeBayar as $m)
                                @if($m['tipe'] === 'ewallet')
                                <div class="border border-gray-100 rounded-xl overflow-hidden transition-all duration-200" id="card-{{ $m['id'] }}">
                                    <button type="button"
                                            onclick="pilihMetode('{{ $m['id'] }}', '{{ $m['nama'] }}', '{{ $m['nomor'] }}', '{{ $m['atas_nama'] }}')"
                                            class="w-full flex items-center justify-between px-4 py-3.5 hover:bg-gray-50 transition-colors text-left">
                                        <div class="flex items-center gap-3">
                                            <div class="w-11 h-11 rounded-xl bg-white border border-gray-200 flex items-center justify-center flex-shrink-0 p-1.5">
                                                <img src="{{ asset('images/payments/' . $m['logo']) }}" alt="{{ $m['nama'] }}" class="w-full h-full object-contain">
                                            </div>
                                            <span class="font-sans text-sm font-medium text-ink">{{ $m['nama'] }}</span>
                                        </div>
                                        <span class="material-symbols-outlined text-pebble text-base transition-transform duration-200" id="icon-{{ $m['id'] }}">expand_more</span>
                                    </button>
                                    <div class="hidden px-4 pb-4 pt-0 bg-gray-50 border-t border-gray-100" id="detail-{{ $m['id'] }}">
                                        <div class="pt-4 flex items-center justify-between">
                                            <div>
                                                <p class="font-sans text-xs text-stone mb-1">Nomor</p>
                                                <p class="font-sans text-xl font-semibold text-forest tracking-widest">{{ $m['nomor'] }}</p>
                                                <p class="font-sans text-xs text-stone mt-0.5">a.n. {{ $m['atas_nama'] }}</p>
                                            </div>
                                            <button type="button"
                                                    onclick="salin('{{ $m['nomor'] }}', 'salin-{{ $m['id'] }}')"
                                                    class="flex items-center gap-1.5 text-forest border border-forest/25 hover:bg-forest hover:text-white hover:border-forest transition-colors px-3 py-2 rounded-lg text-xs font-medium">
                                                <span class="material-symbols-outlined text-sm" id="salin-{{ $m['id'] }}">content_copy</span>
                                                Salin
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>

                        {{-- QRIS --}}
                        <div>
                            <p class="font-sans text-[10px] font-semibold text-stone uppercase tracking-[0.15em] mb-3">QRIS</p>
                            <div class="space-y-2">
                                @foreach($metodeBayar as $m)
                                @if($m['tipe'] === 'qris')
                                <div class="border border-gray-100 rounded-xl overflow-hidden transition-all duration-200" id="card-{{ $m['id'] }}">
                                    <button type="button"
                                            onclick="pilihMetode('{{ $m['id'] }}', '{{ $m['nama'] }}', null, '{{ $m['atas_nama'] }}')"
                                            class="w-full flex items-center justify-between px-4 py-3.5 hover:bg-gray-50 transition-colors text-left">
                                        <div class="flex items-center gap-3">
                                            <div class="w-11 h-11 rounded-xl bg-white border border-gray-200 flex items-center justify-center flex-shrink-0 p-1.5">
                                                <img src="{{ asset('images/payments/' . $m['logo']) }}" alt="{{ $m['nama'] }}" class="w-full h-full object-contain">
                                            </div>
                                            <span class="font-sans text-sm font-medium text-ink">{{ $m['nama'] }}</span>
                                        </div>
                                        <span class="material-symbols-outlined text-pebble text-base transition-transform duration-200" id="icon-{{ $m['id'] }}">expand_more</span>
                                    </button>
                                    <div class="hidden px-4 pb-5 pt-0 bg-gray-50 border-t border-gray-100" id="detail-{{ $m['id'] }}">
                                        <div class="pt-5 flex flex-col items-center text-center space-y-3">
                                            <div class="w-36 h-36 bg-white border border-gray-200 rounded-xl flex items-center justify-center shadow-sm">
                                                <span class="material-symbols-outlined text-gray-200" style="font-size:80px;">qr_code</span>
                                            </div>
                                            <p class="font-sans text-xs text-stone">Scan QR code dengan mobile banking atau e-wallet</p>
                                            <p class="font-sans text-xs text-pebble">a.n. {{ $m['atas_nama'] }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>

                        {{-- Continue button --}}
                        <button type="button" id="btn-lanjutkan" disabled
                                class="w-full flex items-center justify-center gap-2 bg-forest text-white font-sans text-sm font-semibold py-3.5 rounded-xl transition-all opacity-40 cursor-not-allowed">
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            Lanjutkan ke Upload Bukti
                        </button>
                    </div>
                </div>

                {{-- Step 2: Upload --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden opacity-50 pointer-events-none transition-all" id="panel-step-2">
                    <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100" id="step-2-header" style="background:#6B6B6B;">
                        <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">2</div>
                        <h2 class="font-serif text-lg text-white">Upload Bukti Pembayaran</h2>
                    </div>
                    <div class="p-6">
                        <form method="POST"
                              action="{{ route('wisatawan.pembayaran.store', $pemesanan->id_pemesanan) }}"
                              enctype="multipart/form-data"
                              id="form-pembayaran"
                              class="space-y-5">
                            @csrf
                            <input type="hidden" name="metode" id="input-metode" value="">

                            {{-- Selected method badge --}}
                            <div id="selected-method-badge" class="hidden items-center gap-3 bg-forest/6 border border-forest/15 rounded-xl px-4 py-3">
                                <div class="w-7 h-7 rounded-full bg-forest flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-white text-sm icon-fill">check</span>
                                </div>
                                <div>
                                    <p class="font-sans text-xs text-stone">Metode dipilih</p>
                                    <p class="font-sans text-sm font-semibold text-forest" id="badge-method-name"></p>
                                </div>
                            </div>

                            {{-- Drop zone --}}
                            <div class="relative border-2 border-dashed border-gray-200 rounded-2xl hover:border-forest/40 transition-all duration-200 cursor-pointer min-h-[200px] sm:min-h-[280px] md:min-h-[340px] flex items-center justify-center overflow-hidden"
                                 id="drop-zone">
                                <input type="file" name="bukti_bayar" id="bukti_bayar"
                                       accept="image/jpeg,image/jpg,image/png,application/pdf" required
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                
                                {{-- Upload prompt --}}
                                <div class="flex flex-col items-center text-center p-6 md:p-8 pointer-events-none" id="upload-prompt">
                                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-forest/10 to-forest/5 flex items-center justify-center mb-4 transition-transform duration-300 hover:scale-105">
                                        <span class="material-symbols-outlined text-forest text-3xl">cloud_upload</span>
                                    </div>
                                    <p class="font-sans text-sm font-semibold text-ink mb-1">Seret & lepas file di sini</p>
                                    <p class="font-sans text-xs text-stone mb-4">atau <span class="text-forest font-medium">klik untuk browse</span> dari perangkat Anda</p>
                                    <div class="flex flex-wrap items-center justify-center gap-2">
                                        <span class="font-sans text-xs text-pebble bg-white border border-gray-200 px-3 py-1.5 rounded-full">
                                            <span class="material-symbols-outlined text-xs align-middle mr-0.5">image</span>
                                            JPG, PNG
                                        </span>
                                        <span class="font-sans text-xs text-pebble bg-white border border-gray-200 px-3 py-1.5 rounded-full">
                                            <span class="material-symbols-outlined text-xs align-middle mr-0.5">picture_as_pdf</span>
                                            PDF
                                        </span>
                                        <span class="font-sans text-xs text-pebble bg-white border border-gray-200 px-3 py-1.5 rounded-full">
                                            <span class="material-symbols-outlined text-xs align-middle mr-0.5">data_usage</span>
                                            Maks. 5MB
                                        </span>
                                    </div>
                                </div>

                                {{-- Loading state --}}
                                <div class="hidden absolute inset-0 w-full h-full bg-white z-20 flex flex-col items-center justify-center p-6" id="upload-loading">
                                    <div class="w-16 h-16 rounded-full border-4 border-gray-100 border-t-forest animate-spin mb-4"></div>
                                    <p class="font-sans text-sm font-medium text-ink">Memproses file...</p>
                                    <p class="font-sans text-xs text-stone mt-1">Mohon tunggu sebentar</p>
                                </div>

                                {{-- Preview container --}}
                                <div class="hidden absolute inset-0 w-full h-full bg-gray-50 z-20 rounded-2xl overflow-hidden" id="upload-preview-container">
                                    {{-- Image preview (contain — full image visible) --}}
                                    <div class="relative w-full h-full flex items-center justify-center bg-gray-50" id="image-preview-wrapper" style="display:none;">
                                        <img alt="Preview"
                                             class="max-w-full max-h-full w-auto h-auto object-contain"
                                             id="upload-preview" src="">

                                        {{-- Tombol action di pojok kanan atas --}}
                                        <div class="absolute top-3 right-3 flex items-center gap-2 z-30">
                                            <button class="flex items-center gap-1.5 bg-white/90 backdrop-blur-sm text-ink hover:bg-white border border-gray-200/60 px-3 py-2 rounded-xl text-xs font-medium transition-all shadow-sm hover:shadow-md"
                                                    id="change-file" type="button">
                                                <span class="material-symbols-outlined text-sm">edit</span>
                                                <span class="hidden sm:inline">Ganti</span>
                                            </button>
                                            <button class="flex items-center gap-1.5 bg-white/90 backdrop-blur-sm text-red-600 hover:bg-white border border-gray-200/60 px-3 py-2 rounded-xl text-xs font-medium transition-all shadow-sm hover:shadow-md"
                                                    id="remove-file" type="button">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                                <span class="hidden sm:inline">Hapus</span>
                                            </button>
                                        </div>

                                        {{-- Info file di pojok kiri bawah --}}
                                        <div class="absolute bottom-0 left-0 right-0 p-3 md:p-4 z-30">
                                            <div class="inline-flex items-center gap-2.5 bg-white/90 backdrop-blur-sm rounded-xl px-3.5 py-2.5 shadow-sm border border-gray-100/60">
                                                <span class="material-symbols-outlined text-forest text-sm flex-shrink-0">check_circle</span>
                                                <div class="min-w-0">
                                                    <p class="font-sans text-xs font-medium text-ink truncate max-w-[180px] sm:max-w-[260px]" id="file-name"></p>
                                                    <p class="font-sans text-[11px] text-stone" id="file-size"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- PDF preview --}}
                                    <div class="hidden w-full h-full flex-col items-center justify-center bg-gradient-to-br from-red-50 to-white p-6" id="pdf-preview-wrapper">
                                        <div class="w-20 h-20 rounded-2xl bg-red-100 flex items-center justify-center mb-4 shadow-inner">
                                            <span class="material-symbols-outlined text-red-500" style="font-size:48px;">picture_as_pdf</span>
                                        </div>
                                        <p class="font-sans text-xs font-medium text-stone mb-3">Dokumen PDF</p>
                                        <div class="flex items-center gap-2.5 bg-white border border-gray-200 rounded-xl px-4 py-3 shadow-sm w-full max-w-xs">
                                            <span class="material-symbols-outlined text-forest text-sm flex-shrink-0">check_circle</span>
                                            <div class="min-w-0 flex-1">
                                                <p class="font-sans text-xs font-semibold text-ink truncate" id="pdf-file-name"></p>
                                                <p class="font-sans text-[11px] text-stone" id="pdf-file-size"></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 mt-4">
                                            <button class="flex items-center gap-1.5 text-stone bg-white border border-gray-200 hover:border-gray-300 hover:bg-gray-50 px-3.5 py-2 rounded-xl text-xs font-medium transition-colors shadow-sm"
                                                    id="change-file-pdf" type="button">
                                                <span class="material-symbols-outlined text-sm">edit</span>
                                                Ganti
                                            </button>
                                            <button class="flex items-center gap-1.5 text-red-600 bg-white border border-red-200 hover:bg-red-50 px-3.5 py-2 rounded-xl text-xs font-medium transition-colors shadow-sm"
                                                    id="remove-file-pdf" type="button">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Error state --}}
                                <div class="hidden absolute inset-0 w-full h-full bg-red-50 z-20 flex flex-col items-center justify-center p-6 text-center" id="upload-error">
                                    <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mb-4">
                                        <span class="material-symbols-outlined text-red-500 text-3xl">error</span>
                                    </div>
                                    <p class="font-sans text-sm font-semibold text-red-700 mb-1" id="error-title">File tidak valid</p>
                                    <p class="font-sans text-xs text-red-600 mb-4 max-w-xs" id="error-message"></p>
                                    <button class="flex items-center gap-1.5 text-red-700 border border-red-300 hover:bg-red-100 px-4 py-2 rounded-xl text-xs font-medium transition-colors"
                                            id="retry-upload" type="button">
                                        <span class="material-symbols-outlined text-sm">refresh</span>
                                        Coba Lagi
                                    </button>
                                </div>
                            </div>

                            <button type="submit" id="submit-btn"
                                    class="w-full flex items-center justify-center gap-2 bg-forest text-white font-sans text-sm font-semibold py-3.5 rounded-xl hover:bg-leaf transition-colors">
                                <span class="material-symbols-outlined text-sm">send</span>
                                Kirim Bukti Pembayaran
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Info verifikasi --}}
                <div class="flex items-start gap-3 bg-amber-50 border border-amber-200/60 rounded-xl px-4 py-3.5">
                    <span class="material-symbols-outlined text-amber-500 text-base mt-0.5 flex-shrink-0">info</span>
                    <div>
                        <p class="font-sans text-sm font-medium text-ink">Informasi Verifikasi</p>
                        <p class="font-sans text-xs text-stone mt-0.5 leading-relaxed">Pembayaran akan diverifikasi dalam 1×24 jam kerja. Anda akan mendapat notifikasi melalui email setelah pembayaran dikonfirmasi.</p>
                    </div>
                </div>

            </div>
        </div>

            {{-- ── RIGHT: Summary sidebar ───────────────── --}}
            <div class="lg:col-span-1">
                <div class="sticky top-20 space-y-4">

                    {{-- Order summary card --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="bg-forest px-5 py-5">
                            <p class="font-sans text-[10px] uppercase tracking-[0.15em] text-white/40 mb-1">Total Pembayaran</p>
                            <p class="font-serif text-3xl text-white">
                                Rp{{ number_format($pemesanan->total_harga ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="px-5 py-5 space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <span class="font-sans text-xs text-stone flex-shrink-0">Kode Booking</span>
                                <span class="font-sans text-xs font-semibold text-forest text-right break-all">{{ $pemesanan->kode_booking ?? '-' }}</span>
                            </div>
                            <div class="h-px bg-gray-100"></div>
                            <div class="flex items-start justify-between gap-3">
                                <span class="font-sans text-xs text-stone flex-shrink-0">Tiket</span>
                                <span class="font-sans text-xs font-semibold text-ink text-right text-right">
                                    @php
                                        $tSummary = $pemesanan->detailPemesanan->groupBy('nama_tiket')->map(fn($g) => $g->count() . ' ' . $g->first()->nama_tiket)->implode(', ');
                                    @endphp
                                    {{ $tSummary ?: ($pemesanan->tiket->nama_tiket ?? '-') }}
                                </span>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <span class="font-sans text-xs text-stone flex-shrink-0">Jumlah</span>
                                <span class="font-sans text-xs font-semibold text-ink">{{ $pemesanan->detailPemesanan->count() }} tiket</span>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <span class="font-sans text-xs text-stone flex-shrink-0">Tanggal</span>
                                <span class="font-sans text-xs font-semibold text-ink text-right">
                                    {{ isset($pemesanan->tgl_kunjungan) ? \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->isoFormat('D MMMM YYYY') : '-' }}
                                </span>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <span class="font-sans text-xs text-stone flex-shrink-0">Status</span>
                                <span class="font-sans text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full">
                                    {{ ucfirst($pemesanan->status ?? '-') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Help card --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-5">
                        <div class="flex items-center gap-2.5 mb-3">
                            <div class="w-8 h-8 rounded-lg bg-forest/8 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-forest text-sm">support_agent</span>
                            </div>
                            <p class="font-sans text-sm font-medium text-ink">Butuh Bantuan?</p>
                        </div>
                        <p class="font-sans text-xs text-stone leading-relaxed mb-4">Jika ada kendala dalam proses pembayaran, hubungi pengelola Bangkiang Jaran melalui kontak yang tersedia.</p>
                        <a href="https://wa.me/6281234567890" target="_blank"
                           class="flex items-center justify-center gap-2 w-full font-sans text-xs font-semibold text-white bg-[#25D366] hover:bg-[#1dbd5a] transition-colors px-4 py-2.5 rounded-xl">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Hubungi via WhatsApp
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>
@endpush

@push('scripts')
<script>
    let activeMetode = null;

    function pilihMetode(id, nama, nomor, atasNama) {
        document.querySelectorAll('[id^="detail-"]').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('[id^="card-"]').forEach(el => {
            el.classList.remove('border-forest', 'border-2', 'bg-forest/3');
            el.classList.add('border-gray-100');
        });
        document.querySelectorAll('[id^="icon-"]').forEach(el => { el.style.transform = ''; });

        if (activeMetode === id) {
            activeMetode = null;
            updateLanjutkanBtn();
            return;
        }

        activeMetode = id;
        const detail = document.getElementById('detail-' + id);
        const card   = document.getElementById('card-' + id);
        const icon   = document.getElementById('icon-' + id);
        if (detail) detail.classList.remove('hidden');
        if (card)   { card.classList.remove('border-gray-100'); card.classList.add('border-forest', 'border-2'); }
        if (icon)   icon.style.transform = 'rotate(180deg)';

        document.getElementById('input-metode').value = id;

        const badge = document.getElementById('selected-method-badge');
        const badgeName = document.getElementById('badge-method-name');
        if (badge && badgeName) {
            badge.classList.remove('hidden');
            badge.classList.add('flex');
            badgeName.textContent = nama;
        }
        updateLanjutkanBtn();
    }

    function salin(text, iconId) {
        if (!text) return;
        navigator.clipboard.writeText(text).then(() => {
            const icon = document.getElementById(iconId);
            if (icon) { icon.textContent = 'check'; setTimeout(() => { icon.textContent = 'content_copy'; }, 2000); }
        }).catch(() => {
            const el = document.createElement('textarea');
            el.value = text; document.body.appendChild(el); el.select(); document.execCommand('copy'); document.body.removeChild(el);
        });
    }

    function updateLanjutkanBtn() {
        const btn = document.getElementById('btn-lanjutkan');
        if (!btn) return;
        if (activeMetode) {
            btn.disabled = false;
            btn.classList.remove('opacity-40', 'cursor-not-allowed');
        } else {
            btn.disabled = true;
            btn.classList.add('opacity-40', 'cursor-not-allowed');
        }
    }

    const btnLanjutkan = document.getElementById('btn-lanjutkan');
    if (btnLanjutkan) {
        btnLanjutkan.addEventListener('click', () => {
            if (!activeMetode) return;
            const panel2  = document.getElementById('panel-step-2');
            const header2 = document.getElementById('step-2-header');
            panel2.classList.remove('opacity-50', 'pointer-events-none');
            header2.style.background = '#1B3A2D';
            const c2 = document.getElementById('step-2-circle');
            const l2 = document.getElementById('step-2-label');
            if (c2) { c2.classList.remove('bg-gray-100', 'text-gray-400'); c2.classList.add('bg-forest', 'text-white'); }
            if (l2) { l2.classList.remove('text-gray-400'); l2.classList.add('font-semibold', 'text-forest'); }
            panel2.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    // ── Upload zone elements ───────────────────────────────────────────────────
    const dropZone      = document.getElementById('drop-zone');
    const fileInput     = document.getElementById('bukti_bayar');
    const promptEl      = document.getElementById('upload-prompt');
    const loadingEl     = document.getElementById('upload-loading');
    const previewBox    = document.getElementById('upload-preview-container');
    const previewImg    = document.getElementById('upload-preview');
    const imgWrapper    = document.getElementById('image-preview-wrapper');
    const pdfWrapper    = document.getElementById('pdf-preview-wrapper');
    const fileNameEl    = document.getElementById('file-name');
    const fileSizeEl    = document.getElementById('file-size');
    const errorBox      = document.getElementById('upload-error');
    const errorTitle    = document.getElementById('error-title');
    const errorMsg      = document.getElementById('error-message');
    const removeBtn     = document.getElementById('remove-file');
    const changeBtn     = document.getElementById('change-file');
    const retryBtn      = document.getElementById('retry-upload');
    const pdfFileNameEl = document.getElementById('pdf-file-name');
    const pdfFileSizeEl = document.getElementById('pdf-file-size');
    const changePdfBtn  = document.getElementById('change-file-pdf');
    const removePdfBtn  = document.getElementById('remove-file-pdf');

    const MAX_SIZE_MB   = 5;
    const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function showState(state) {
        const all = [promptEl, loadingEl, previewBox, errorBox];
        all.forEach(el => { if (el) { el.classList.add('hidden'); el.classList.remove('flex'); } });
        const target = { prompt: promptEl, loading: loadingEl, preview: previewBox, error: errorBox }[state];
        if (target) { target.classList.remove('hidden'); target.classList.add('flex'); }
    }

    function resetUpload() {
        fileInput.value = '';
        fileInput.classList.remove('hidden');
        if (previewImg) previewImg.src = '';
        if (imgWrapper) imgWrapper.style.display = 'none';
        if (pdfWrapper) { pdfWrapper.classList.add('hidden'); pdfWrapper.classList.remove('flex'); }
        if (pdfFileNameEl) pdfFileNameEl.textContent = '';
        if (pdfFileSizeEl) pdfFileSizeEl.textContent = '';
        showState('prompt');
    }

    function showError(title, message) {
        if (errorTitle) errorTitle.textContent = title;
        if (errorMsg)   errorMsg.textContent   = message;
        showState('error');
        fileInput.value = '';
    }

    function handleFiles(files) {
        if (!files || files.length === 0) return;
        const file = files[0];

        // Validate type
        if (!ALLOWED_TYPES.includes(file.type)) {
            showError('Format file tidak didukung', 'Gunakan file JPG, PNG, atau PDF untuk bukti pembayaran Anda.');
            return;
        }
        // Validate size
        if (file.size > MAX_SIZE_MB * 1024 * 1024) {
            showError('Ukuran file terlalu besar', `Ukuran file maksimal ${MAX_SIZE_MB}MB. File Anda: ${formatFileSize(file.size)}.`);
            return;
        }

        showState('loading');

        const reader = new FileReader();
        reader.onload = (e) => {
            // Populate meta
            if (fileNameEl) fileNameEl.textContent = file.name;
            if (fileSizeEl) fileSizeEl.textContent  = formatFileSize(file.size);

            if (file.type.startsWith('image/')) {
                // Image preview — full-bleed
                previewImg.src = e.target.result;
                if (imgWrapper) imgWrapper.style.display = '';
                if (pdfWrapper) { pdfWrapper.classList.add('hidden'); pdfWrapper.classList.remove('flex'); }
            } else {
                // PDF preview
                if (imgWrapper) imgWrapper.style.display = 'none';
                if (pdfWrapper) { pdfWrapper.classList.remove('hidden'); pdfWrapper.classList.add('flex'); }
                if (pdfFileNameEl) pdfFileNameEl.textContent = file.name;
                if (pdfFileSizeEl) pdfFileSizeEl.textContent = formatFileSize(file.size);
            }

            // Small delay to let the browser render
            setTimeout(() => showState('preview'), 120);
        };
        reader.onerror = () => showError('Gagal membaca file', 'Terjadi kesalahan saat memproses file. Silakan coba lagi.');
        reader.readAsDataURL(file);
    }

    // Restore file input clickability from inside the change button
    if (changeBtn) {
        changeBtn.addEventListener('click', e => {
            e.stopPropagation();
            fileInput.classList.remove('hidden');
            resetUpload();
            fileInput.click();
        });
    }
    if (removeBtn) {
        removeBtn.addEventListener('click', e => { e.stopPropagation(); resetUpload(); });
    }
    if (retryBtn) {
        retryBtn.addEventListener('click', e => { e.stopPropagation(); resetUpload(); });
    // PDF action buttons
    if (changePdfBtn) {
        changePdfBtn.addEventListener('click', e => {
            e.stopPropagation();
            fileInput.classList.remove('hidden');
            resetUpload();
            fileInput.click();
        });
    }
    if (removePdfBtn) {
        removePdfBtn.addEventListener('click', e => { e.stopPropagation(); resetUpload(); });
    }
    }

    if (fileInput) fileInput.addEventListener('change', e => handleFiles(e.target.files));

    if (dropZone) {
        ['dragenter','dragover','dragleave','drop'].forEach(ev =>
            dropZone.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); })
        );
        ['dragenter','dragover'].forEach(ev =>
            dropZone.addEventListener(ev, () => {
                dropZone.classList.add('border-forest', 'bg-forest/3');
                dropZone.classList.remove('border-gray-200');
            })
        );
        ['dragleave','drop'].forEach(ev =>
            dropZone.addEventListener(ev, () => {
                dropZone.classList.remove('border-forest', 'bg-forest/3');
                dropZone.classList.add('border-gray-200');
            })
        );
        dropZone.addEventListener('drop', e => handleFiles(e.dataTransfer.files));
    }
</script>
@endpush
@endsection
