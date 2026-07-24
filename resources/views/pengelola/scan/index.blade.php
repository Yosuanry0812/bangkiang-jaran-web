@extends('pengelola.layouts.admin')
@section('title', 'Scan Tiket')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-gray-800">Scan Tiket</h1>
            <p class="text-sm text-gray-500 mt-0.5">Scan atau masukkan kode tiket pengunjung</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-xl font-sans text-sm flex items-start gap-3">
        <span class="material-symbols-outlined text-emerald-500 mt-0.5 text-base">check_circle</span>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl font-sans text-sm flex items-start gap-3">
        <span class="material-symbols-outlined text-red-500 mt-0.5 text-base">error</span>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Search form --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <p class="font-sans text-sm font-medium text-gray-800">Masukkan Kode Tiket</p>
            <button type="button" id="toggle-camera"
                    class="text-xs text-emerald-600 hover:text-emerald-700 font-medium inline-flex items-center gap-1.5 transition-colors">
                <span class="material-symbols-outlined text-sm">videocam</span>
                <span id="toggle-label">Scan via Kamera</span>
            </button>
        </div>

        {{-- Manual input --}}
        <div id="manual-input">
            <form method="POST" action="{{ route('pengelola.scan.cari') }}" id="form-scan" autocomplete="off">
                @csrf
                <div class="flex items-center gap-3">
                    <div class="relative flex-1">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <span class="material-symbols-outlined text-base">qr_code_scanner</span>
                        </span>
                        <input type="text"
                               name="kode"
                               id="input-kode"
                               value="{{ old('kode', $detail->kode_tiket ?? '') }}"
                               placeholder="Scan atau ketik kode tiket..."
                               autofocus
                               class="w-full border border-gray-200 rounded-xl pl-11 pr-4 py-3.5 text-sm font-mono font-bold tracking-wider text-gray-900 uppercase focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-white">
                    </div>
                    <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2 transition-all whitespace-nowrap">
                        <span class="material-symbols-outlined text-base">search</span>
                        Cari
                    </button>
                </div>
                <p class="font-sans text-xs text-gray-400 mt-2">Gunakan scanner barcode fisik atau ketik manual kode tiket</p>
            </form>
        </div>

        {{-- Camera scanner --}}
        <div id="camera-scanner" class="hidden">
            <div id="qr-reader" class="w-full max-w-md mx-auto rounded-xl overflow-hidden border border-gray-200"></div>
            <p class="font-sans text-xs text-gray-400 text-center mt-2">Arahkan kamera ke QR code tiket</p>
            <div id="qr-result" class="hidden mt-3 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-center">
                <p class="font-sans text-xs text-emerald-700">Kode terdeteksi:</p>
                <p class="font-mono text-sm font-bold text-emerald-800 tracking-wider" id="qr-result-code"></p>
            </div>
        </div>
    </div>

    {{-- Result --}}
    @if(isset($detail))
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Ticket info card --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                {{-- Header status --}}
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center
                            @if($belumLunas || $pemesananDibatalkan) bg-red-100 text-red-600
                            @elseif($sudahDigunakan) bg-amber-100 text-amber-600
                            @else bg-emerald-100 text-emerald-600
                            @endif">
                            @if($belumLunas || $pemesananDibatalkan)
                                <span class="material-symbols-outlined">block</span>
                            @elseif($sudahDigunakan)
                                <span class="material-symbols-outlined">check_circle</span>
                            @else
                                <span class="material-symbols-outlined">check_circle</span>
                            @endif
                        </div>
                        <div>
                            <p class="font-sans text-sm font-semibold text-gray-900">
                                @if($belumLunas || $pemesananDibatalkan)
                                    Tiket Tidak Valid
                                @elseif($sudahDigunakan)
                                    Tiket Sudah Digunakan
                                @else
                                    Tiket Valid
                                @endif
                            </p>
                            <p class="font-sans text-xs text-gray-400">
                                @if($belumLunas)
                                    Pembayaran belum diverifikasi
                                @elseif($pemesananDibatalkan)
                                    Pemesanan dibatalkan
                                @elseif($sudahDigunakan)
                                    Tiket ini sudah discan pada {{ $detail->updated_at ? \Carbon\Carbon::parse($detail->updated_at)->format('d/m/Y H:i') : '-' }}
                                @else
                                    Tiket siap digunakan
                                @endif
                            </p>
                        </div>
                    </div>
                    @if(!$sudahDigunakan && !$belumLunas && !$pemesananDibatalkan)
                    <form method="POST" action="{{ route('pengelola.scan.gunakan') }}" onsubmit="return confirm('Konfirmasi tiket ini? Pengunjung akan dipersilakan masuk.')">
                        @csrf
                        <input type="hidden" name="kode" value="{{ $detail->kode_tiket }}">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2 transition-all">
                            <span class="material-symbols-outlined text-base">check</span>
                            Konfirmasi Masuk
                        </button>
                    </form>
                    @endif
                </div>

                {{-- Visitor data --}}
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Left: ticket info --}}
                        <div class="space-y-4">
                            <h3 class="font-sans text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Data Tiket</h3>
                            <div>
                                <p class="font-sans text-[11px] text-gray-400">Kode Tiket</p>
                                <p class="font-mono text-lg font-bold text-gray-900 tracking-wider">{{ $detail->kode_tiket }}</p>
                            </div>
                            <div>
                                <p class="font-sans text-[11px] text-gray-400">Jenis Tiket</p>
                                <p class="font-sans text-sm font-medium text-gray-800">{{ $detail->nama_tiket }}</p>
                            </div>
                            <div>
                                <p class="font-sans text-[11px] text-gray-400">Status Tiket</p>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold
                                    @if($detail->status_tiket === 'aktif') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif($detail->status_tiket === 'digunakan') bg-amber-50 text-amber-700 border border-amber-200
                                    @else bg-gray-50 text-gray-500 border border-gray-200
                                    @endif">
                                    <span class="w-1.5 h-1.5 rounded-full
                                        @if($detail->status_tiket === 'aktif') bg-emerald-500
                                        @elseif($detail->status_tiket === 'digunakan') bg-amber-400
                                        @else bg-gray-400
                                        @endif">
                                    </span>
                                    {{ ucfirst($detail->status_tiket) }}
                                </span>
                            </div>
                        </div>

                        {{-- Right: visitor info --}}
                        <div class="space-y-4">
                            <h3 class="font-sans text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Data Pengunjung</h3>
                            <div>
                                <p class="font-sans text-[11px] text-gray-400">
                                    {{ $detail->plat_kendaraan ? 'Plat Kendaraan' : 'Nama Pengunjung' }}
                                </p>
                                <p class="font-sans text-base font-semibold text-gray-900">
                                    {{ $detail->plat_kendaraan ?? $detail->nama_pengunjung ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="font-sans text-[11px] text-gray-400">Pemesan</p>
                                <p class="font-sans text-sm font-medium text-gray-800">{{ $pemesanan->user->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="font-sans text-[11px] text-gray-400">Tanggal Kunjungan</p>
                                <p class="font-sans text-sm font-medium text-gray-800">
                                    {{ $pemesanan->tgl_kunjungan ? \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->isoFormat('D MMMM YYYY') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Order summary --}}
                    <div class="mt-6 pt-5 border-t border-gray-100">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-sans text-xs text-gray-400">Kode Booking</span>
                            <span class="font-mono text-sm font-bold text-emerald-700">{{ $pemesanan->kode_booking }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm mt-2">
                            <span class="font-sans text-xs text-gray-400">Status Pemesanan</span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold
                                @if($pemesanan->status === 'selesai') bg-emerald-50 text-emerald-700
                                @else bg-red-50 text-red-700
                                @endif">
                                {{ ucfirst($pemesanan->status) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm mt-2">
                            <span class="font-sans text-xs text-gray-400">Total Tiket</span>
                            <span class="font-sans text-sm font-medium text-gray-800">{{ $pemesanan->detailPemesanan->count() }} tiket</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div>
            <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                <h3 class="font-sans text-xs font-semibold uppercase tracking-wider text-gray-400">Semua Tiket Pesanan Ini</h3>
                @foreach($pemesanan->detailPemesanan as $dt)
                <div class="flex items-center gap-3 p-3 rounded-xl border {{ $dt->kode_tiket === $detail->kode_tiket ? 'border-emerald-300 bg-emerald-50' : 'border-gray-100' }}">
                    <div class="w-2 h-2 rounded-full flex-shrink-0
                        @if($dt->status_tiket === 'aktif') bg-emerald-500
                        @elseif($dt->status_tiket === 'digunakan') bg-amber-400
                        @else bg-gray-300
                        @endif">
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-mono text-xs font-bold text-gray-800 tracking-wider truncate">{{ $dt->kode_tiket }}</p>
                        <p class="font-sans text-[10px] text-gray-400 truncate">{{ $dt->plat_kendaraan ?? $dt->nama_pengunjung ?? $dt->nama_tiket }}</p>
                    </div>
                    <span class="font-sans text-[10px] text-gray-400 uppercase flex-shrink-0">{{ substr($dt->status_tiket, 0, 4) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    {{-- Empty state --}}
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center" data-aos="fade-up">
        <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gray-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-gray-300" style="font-size:48px;">qr_code_scanner</span>
        </div>
        <h3 class="font-sans text-base font-semibold text-gray-700 mb-1">Belum ada scan</h3>
        <p class="font-sans text-sm text-gray-400 max-w-xs mx-auto">Scan QR code pada tiket pengunjung atau masukkan kode tiket manual untuk verifikasi.</p>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode = null;
    let scannerActive = false;

    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('input-kode');
        if (input) input.focus();

        @if(isset($detail))
        if (input) input.select();
        @endif
    });

    // Auto-submit on Enter
    document.getElementById('form-scan')?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            this.submit();
        }
    });

    // Auto-extract kode_tiket from URL if scanned/pasted
    document.getElementById('input-kode')?.addEventListener('input', function () {
        const val = this.value.trim();
        // If it looks like a URL, extract the kode from /tiket/verifikasi/KODE or /verifikasi/KODE
        if (val.includes('/verifikasi/') || val.includes('/tiket/')) {
            const match = val.match(/\/([A-Z0-9-]+)(?:\?|$)/i) || val.match(/\/([A-Z0-9-]+)\/?$/i);
            if (match && match[1]) {
                this.value = match[1];
            }
        }
    });

    // Toggle camera scanner
    document.getElementById('toggle-camera')?.addEventListener('click', function () {
        const manual = document.getElementById('manual-input');
        const camera = document.getElementById('camera-scanner');
        const label  = document.getElementById('toggle-label');
        const icon   = this.querySelector('.material-symbols-outlined');

        if (camera.classList.contains('hidden')) {
            // Show camera
            manual.classList.add('hidden');
            camera.classList.remove('hidden');
            label.textContent = 'Input Manual';
            icon.textContent = 'keyboard';
            startScanner();
        } else {
            // Hide camera
            camera.classList.add('hidden');
            manual.classList.remove('hidden');
            label.textContent = 'Scan via Kamera';
            icon.textContent = 'videocam';
            stopScanner();
        }
    });

    function startScanner() {
        const reader = document.getElementById('qr-reader');
        if (!reader) return;

        html5QrCode = new Html5Qrcode('qr-reader');
        html5QrCode.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            function onScanSuccess(decodedText) {
                // Show result
                const resultDiv = document.getElementById('qr-result');
                const resultCode = document.getElementById('qr-result-code');
                if (resultCode) resultCode.textContent = decodedText;
                if (resultDiv) {
                    resultDiv.classList.remove('hidden');
                    // Auto redirect after 1.5s
                    setTimeout(() => {
                        // Fill input and submit
                        const input = document.getElementById('input-kode');
                        if (input) {
                            const form = document.getElementById('form-scan');
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'kode';
                            hiddenInput.value = decodedText;
                            form.appendChild(hiddenInput);
                            form.submit();
                        }
                    }, 1500);
                }
                stopScanner();
            },
            function onScanError(err) {
                // Ignore - continuous scanning
            }
        ).catch(function (err) {
            console.error('Camera error:', err);
            alert('Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.');
            document.getElementById('toggle-camera')?.click();
        });
        scannerActive = true;
    }

    function stopScanner() {
        if (html5QrCode) {
            try {
                html5QrCode.stop();
                html5QrCode.clear();
            } catch (e) {}
            html5QrCode = null;
        }
        scannerActive = false;
    }
</script>
@endpush
