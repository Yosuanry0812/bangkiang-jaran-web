@extends('pengelola.layouts.admin')
@section('title', __('ui.t_scan'))
@section('page_title', __('ui.t_scan'))

@section('content')
<div class="space-y-6">
    <header class="admin-page-head">
        <div>
            <h1 class="font-heading text-2xl font-bold text-gray-800">{{ __('panel.scan_tiket_c19b') }}</h1>
            <p>{{ __('panel.validasi_tiket_catat_pengunj_4f62') }}</p>
        </div>
    </header>

    {{-- Alerts — ditampilkan via SweetAlert2 popup di JS --}}
    @if(session('success'))
    <meta name="alert-success" content="{{ session('success') }}">
    @endif
    @if(session('error'))
    <meta name="alert-error" content="{{ session('error') }}">
    @endif

    {{-- Search form --}}
    <section class="admin-panel admin-scanner">
        <div class="admin-scanner-intro">
            <h2>{{ __('panel.periksa_tiket_pengunjung_96d5') }}</h2>
            <button type="button" id="toggle-camera"
                    class="admin-btn admin-btn--quiet">
                <span class="material-symbols-outlined text-sm">videocam</span>
                <span id="toggle-label">{{ __('panel.scan_via_kamera_0046') }}</span>
            </button>
        </div>

        {{-- Manual input --}}
        <div id="manual-input">
            <form method="POST" action="{{ route('pengelola.scan.cari') }}" id="form-scan" class="admin-scan-form" autocomplete="off">
                @csrf
                <label for="input-kode">{{ __('panel.kode_tiket_5f48') }}</label>
                <div class="flex items-center gap-3">
                    <div class="relative flex-1">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <span class="material-symbols-outlined text-base">qr_code_scanner</span>
                        </span>
                        <input type="text"
                               name="kode"
                               id="input-kode"
                               value="{{ old('kode', $detail->kode_tiket ?? '') }}"
                               placeholder="{{ __('panel.scan_atau_ketik_kode_0457') }}"
                               required
                               spellcheck="false"
                               autocapitalize="characters"
                               class="w-full border border-gray-200 rounded-xl pl-11 pr-4 py-3.5 text-sm font-mono font-bold tracking-wider text-gray-900 uppercase focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-white">
                    </div>
                    <button type="submit"
                            class="admin-btn">
                        <span class="material-symbols-outlined text-base">search</span>
                        {{ __('panel.periksa_tiket_29e4') }}
                    </button>
                </div>
                <p class="font-sans text-xs text-gray-400 mt-2">{{ __('panel.gunakan_scanner_barcode_fisi_9954') }}</p>
            </form>
        </div>

        {{-- Camera scanner --}}
        <div id="camera-scanner" class="hidden">
            <div id="qr-reader" class="w-full max-w-md mx-auto rounded-xl overflow-hidden border border-gray-200"></div>
            <p class="font-sans text-xs text-gray-400 text-center mt-2">{{ __('panel.arahkan_kamera_ke_qr_62aa') }}</p>
            <div id="qr-result" class="hidden mt-3 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-center">
                <p class="font-sans text-xs text-emerald-700">{{ __('panel.kode_terdeteksi_a18a') }}</p>
                <p class="font-mono text-sm font-bold text-emerald-800 tracking-wider" id="qr-result-code"></p>
            </div>
        </div>
    </section>

    {{-- Result --}}
    @if(isset($detail))
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Ticket info card --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                {{-- Header status --}}
                <div class="admin-scan-result-head px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center
                            @if($bukanHariIni || $belumLunas || $pemesananDibatalkan) bg-red-100 text-red-600
                            @elseif($sudahDigunakan) bg-amber-100 text-amber-600
                            @else bg-emerald-100 text-emerald-600
                            @endif">
                            @if($bukanHariIni || $belumLunas || $pemesananDibatalkan)
                                <span class="material-symbols-outlined">block</span>
                            @elseif($sudahDigunakan)
                                <span class="material-symbols-outlined">check_circle</span>
                            @else
                                <span class="material-symbols-outlined">check_circle</span>
                            @endif
                        </div>
                        <div>
                            <p class="font-sans text-sm font-semibold text-gray-900">
                                @if($bukanHariIni)
                                    {{ __('ui.scan_not_today') }}
                                @elseif($belumLunas || $pemesananDibatalkan)
                                    {{ __('ui.scan_invalid') }}
                                @elseif($sudahDigunakan)
                                    {{ __('ui.scan_used') }}
                                @else
                                    {{ __('ui.scan_valid') }}
                                @endif
                            </p>
                            <p class="font-sans text-xs text-gray-400">
                                @if($bukanHariIni)
                                    {{ __('ui.scan_other_date', ['date' => \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->format('d/m/Y')]) }}
                                @elseif($belumLunas)
                                    {{ __('ui.scan_unverified') }}
                                @elseif($pemesananDibatalkan)
                                    {{ __('ui.scan_cancelled') }}
                                @elseif($sudahDigunakan)
                                    {{ __('ui.scan_used_at', ['time' => $detail->updated_at ? \Carbon\Carbon::parse($detail->updated_at)->format('d/m/Y H:i') : '-']) }}
                                @else
                                    {{ __('ui.scan_ready') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    @if(!$bukanHariIni && !$sudahDigunakan && !$belumLunas && !$pemesananDibatalkan)
                    <form method="POST" action="{{ route('pengelola.scan.gunakan') }}" class="confirm-form" data-confirm="{{ __('ui.confirm_checkin') }}">
                        @csrf
                        <input type="hidden" name="kode" value="{{ $detail->kode_tiket }}">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2 transition-all">
                            <span class="material-symbols-outlined text-base">check</span>
                            {{ __('panel.konfirmasi_masuk_2351') }}
                        </button>
                    </form>
                    @endif
                    @if(!$bukanHariIni && $sudahDigunakan && !$detail->check_out_at && !$belumLunas && !$pemesananDibatalkan)
                    <form method="POST" action="{{ route('pengelola.scan.checkout') }}" class="confirm-form" data-confirm="{{ __('ui.confirm_checkout') }}">
                        @csrf
                        <input type="hidden" name="kode" value="{{ $detail->kode_tiket }}">
                        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2 transition-all">
                            <span class="material-symbols-outlined text-base">logout</span>
                            {{ __('panel.konfirmasi_keluar_9c36') }}
                        </button>
                    </form>
                    @endif
                </div>

                {{-- Visitor data --}}
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Left: ticket info --}}
                        <div class="space-y-4">
                            <h3 class="font-sans text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">{{ __('panel.data_tiket_099d') }}</h3>
                            <div>
                                <p class="font-sans text-[11px] text-gray-400">{{ __('panel.kode_tiket_1e81') }}</p>
                                <p class="font-mono text-lg font-bold text-gray-900 tracking-wider">{{ $detail->kode_tiket }}</p>
                            </div>
                            <div>
                                <p class="font-sans text-[11px] text-gray-400">{{ __('panel.jenis_tiket_0497') }}</p>
                                <p class="font-sans text-sm font-medium text-gray-800">{{ $detail->label_tiket }}</p>
                            </div>
                            <div>
                                <p class="font-sans text-[11px] text-gray-400">{{ __('panel.status_tiket_e25a') }}</p>
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
                                    {{ \App\Support\Label::status($detail->status_tiket) }}
                                </span>
                            </div>
                        </div>

                        {{-- Right: visitor info --}}
                        <div class="space-y-4">
                            <h3 class="font-sans text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">{{ __('panel.data_pengunjung_1e68') }}</h3>
                            <div>
                                <p class="font-sans text-[11px] text-gray-400">{{ __('panel.nama_pengunjung_1355') }}</p>
                                <p class="font-sans text-base font-semibold text-gray-900">
                                    {{ $detail->nama_pengunjung ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="font-sans text-[11px] text-gray-400">{{ __('panel.pemesan_3eb0') }}</p>
                                <p class="font-sans text-sm font-medium text-gray-800">{{ $pemesanan->user->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="font-sans text-[11px] text-gray-400">{{ __('panel.tanggal_kunjungan_3ddb') }}</p>
                                <p class="font-sans text-sm font-medium text-gray-800">
                                    {{ $pemesanan->tgl_kunjungan ? \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->isoFormat('D MMMM YYYY') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Order summary --}}
                    <div class="mt-6 pt-5 border-t border-gray-100">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-sans text-xs text-gray-400">{{ __('panel.kode_booking_6179') }}</span>
                            <span class="font-mono text-sm font-bold text-emerald-700">{{ $pemesanan->kode_booking }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm mt-2">
                            <span class="font-sans text-xs text-gray-400">{{ __('panel.status_pemesanan_008a') }}</span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold
                                @if($pemesanan->status === 'selesai') bg-emerald-50 text-emerald-700
                                @else bg-red-50 text-red-700
                                @endif">
                                {{ \App\Support\Label::status($pemesanan->status) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm mt-2">
                            <span class="font-sans text-xs text-gray-400">{{ __('panel.total_tiket_6709') }}</span>
                            <span class="font-sans text-sm font-medium text-gray-800">{{ trans_choice('ui.tickets_n', $pemesanan->detailPemesanan->count()) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div>
            <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                <h3 class="font-sans text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('panel.semua_tiket_pesanan_ini_5a1b') }}</h3>
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
                        <p class="font-sans text-[10px] text-gray-400 truncate">{{ $dt->nama_pengunjung ?? $dt->label_tiket }}</p>
                    </div>
                    <span class="font-sans text-[10px] text-gray-400 uppercase flex-shrink-0">{{ substr($dt->status_tiket, 0, 4) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    {{-- Empty state --}}
    <div class="admin-scan-guide">
        <span class="material-symbols-outlined" aria-hidden="true">qr_code_scanner</span>
        <div><h3>{{ __('panel.siap_menerima_pengunjung_8efa') }}</h3><p>{{ __('panel.pindai_qr_dengan_kamera_e005') }}</p><p>{{ __('panel.tiket_harus_sesuai_tanggal_1eff') }}</p></div>
    </div>
    @endif

    {{-- ══════════════════════════════════════
         MANUAL CHECK-IN / CHECK-OUT TABLE
    ═══════════════════════════════════════ --}}
    @php
        $todayCount = isset($ticketsHariIni) ? $ticketsHariIni->count() : 0;
    @endphp

    @if($todayCount > 0)
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <p class="font-sans text-sm font-semibold text-gray-800">{{ __('panel.manual_check_in_check_dd23') }}</p>
                <p class="font-sans text-xs text-gray-400 mt-0.5">{{ trans_choice('ui.tickets_available', $todayCount) }}</p>
            </div>
            <span class="inline-flex items-center gap-1 font-sans text-[10px] font-medium text-gray-400 bg-gray-50 border border-gray-100 px-2 py-0.5 rounded-full">
                {{ now()->format('d/m/Y') }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-4 py-3 font-sans text-[10px] font-semibold uppercase tracking-wider text-gray-400">{{ __('panel.kode_tiket_1e81') }}</th>
                        <th class="text-left px-4 py-3 font-sans text-[10px] font-semibold uppercase tracking-wider text-gray-400">{{ __('panel.pengunjung_c54d') }}</th>
                        <th class="text-left px-4 py-3 font-sans text-[10px] font-semibold uppercase tracking-wider text-gray-400 hidden sm:table-cell">{{ __('panel.tiket_f17c') }}</th>
                        <th class="text-left px-4 py-3 font-sans text-[10px] font-semibold uppercase tracking-wider text-gray-400">{{ __('panel.status_ec53') }}</th>
                        <th class="text-left px-4 py-3 font-sans text-[10px] font-semibold uppercase tracking-wider text-gray-400">{{ __('panel.masuk_6388') }}</th>
                        <th class="text-left px-4 py-3 font-sans text-[10px] font-semibold uppercase tracking-wider text-gray-400">{{ __('panel.keluar_1692') }}</th>
                        <th class="text-center px-4 py-3 font-sans text-[10px] font-semibold uppercase tracking-wider text-gray-400">{{ __('panel.aksi_ae3b') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($ticketsHariIni as $t)
                    @php
                        $canCheckIn  = $t->status_tiket === 'aktif';
                        $canCheckOut = $t->status_tiket === 'digunakan' && !$t->check_out_at;
                        $isDone      = (bool) $t->check_out_at;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs font-bold text-gray-700 tracking-wider">{{ $t->kode_tiket }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-sans text-xs font-medium text-gray-600">{{ $t->nama_pengunjung ?? $t->pemesanan->user->name ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            <span class="font-sans text-xs text-gray-400">{{ $t->nama_tiket }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($isDone)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 flex-shrink-0"></span>
                                {{ __('panel.selesai_dfe1') }}
                            </span>
                            @elseif($canCheckIn)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                {{ __('panel.aktif_81c3') }}
                            </span>
                            @elseif($canCheckOut)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 flex-shrink-0"></span>
                                {{ __('panel.masuk_6388') }}
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-50 text-gray-500 border border-gray-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300 flex-shrink-0"></span>
                                {{ \App\Support\Label::status($t->status_tiket) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-sans text-xs {{ $t->check_in_at ? 'text-gray-700 font-medium' : 'text-gray-300' }}">
                                {{ $t->check_in_at ? \Carbon\Carbon::parse($t->check_in_at)->format('H:i') : '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-sans text-xs {{ $t->check_out_at ? 'text-gray-700 font-medium' : 'text-gray-300' }}">
                                {{ $t->check_out_at ? \Carbon\Carbon::parse($t->check_out_at)->format('H:i') : '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($canCheckIn)
                            <form method="POST" action="{{ route('pengelola.scan.gunakan') }}" class="confirm-form" data-confirm="{{ __('ui.confirm_checkin_code', ['code' => $t->kode_tiket]) }}">
                                @csrf
                                <input type="hidden" name="kode" value="{{ $t->kode_tiket }}">
                                <button type="submit"
                                    class="inline-flex items-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-all active:scale-[.97]">
                                    <span class="material-symbols-outlined" style="font-size:14px">login</span>
                                    Check In
                                </button>
                            </form>
                            @elseif($canCheckOut)
                            <form method="POST" action="{{ route('pengelola.scan.checkout') }}" class="confirm-form" data-confirm="{{ __('ui.confirm_checkout_code', ['code' => $t->kode_tiket]) }}">
                                @csrf
                                <input type="hidden" name="kode" value="{{ $t->kode_tiket }}">
                                <button type="submit"
                                    class="inline-flex items-center gap-1 bg-orange-500 hover:bg-orange-600 text-white px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-all active:scale-[.97]">
                                    <span class="material-symbols-outlined" style="font-size:14px">logout</span>
                                    Check Out
                                </button>
                            </form>
                            @elseif($isDone)
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 text-gray-400">
                                <span class="material-symbols-outlined" style="font-size:16px">check</span>
                            </span>
                            @else
                            <span class="font-sans text-[10px] text-gray-300">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @elseif(isset($ticketsHariIni))
    {{-- Empty state — hari ini tidak ada tiket --}}
    <div class="bg-white rounded-2xl shadow-sm p-8 text-center">
        <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-gray-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-gray-300" style="font-size:24px">confirmation_number</span>
        </div>
        <p class="font-sans text-sm font-medium text-gray-500 mb-1">{{ __('panel.tidak_ada_tiket_untuk_2fae') }}</p>
        <p class="font-sans text-xs text-gray-400">{{ __('panel.tiket_dengan_kunjungan_hari_aa63') }}</p>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/vendor/html5-qrcode-2.3.8.min.js') }}"></script>
<script>
    let html5QrCode = null;
    let scannerActive = false;

    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('input-kode');
        if (input && matchMedia('(min-width: 1024px) and (pointer: fine)').matches) input.focus({ preventScroll: true });

        @if(isset($detail))
        if (input) input.select();
        @endif

        // ── Flash alert popup ──
        var success = document.querySelector('meta[name="alert-success"]');
        var error   = document.querySelector('meta[name="alert-error"]');
        if (success) {
            Swal.fire({
                icon: 'success',
                title: @json(__('ui.js_success')),
                text: success.getAttribute('content'),
                confirmButtonColor: '#059669',
                confirmButtonText: 'OK',
                timer: 4000,
                timerProgressBar: true,
            });
        }
        if (error) {
            Swal.fire({
                icon: 'error',
                title: @json(__('ui.js_failed')),
                text: error.getAttribute('content'),
                confirmButtonColor: '#dc2626',
                confirmButtonText: @json(__('ui.js_close')),
                timer: 5000,
                timerProgressBar: true,
            });
        }
    });

    // ── SweetAlert2 confirm popup ──
    document.querySelectorAll('form.confirm-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var msg = this.getAttribute('data-confirm') || @json(__('ui.js_confirm'));
            var btn = this.querySelector('button[type="submit"]');
            var isCheckin = !(this.getAttribute('action') || '').includes('checkout');
            Swal.fire({
                title: isCheckin ? @json(__('ui.js_checkin_title')) : @json(__('ui.js_checkout_title')),
                text: msg,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: isCheckin ? '#059669' : '#f97316',
                cancelButtonColor: '#64748b',
                confirmButtonText: isCheckin ? @json(__('ui.js_yes_checkin')) : @json(__('ui.js_yes_checkout')),
                cancelButtonText: @json(__('ui.js_cancel')),
                reverseButtons: true,
            }).then(function (result) {
                if (result.isConfirmed) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:14px">hourglass_top</span> ' + @json(__('ui.js_processing')) + '...';
                    form.submit();
                }
            });
        });
    });

    // Auto-submit on Enter
    document.getElementById('form-scan')?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            this.submit();
        }
    });

    // Helper extract kode_tiket from URL or string
    function extractTicketCode(val) {
        if (!val) return '';
        val = val.trim();
        if (val.includes('/verifikasi/') || val.includes('/tiket/')) {
            const match = val.match(/\/([A-Z0-9_-]+)(?:\?|$)/i) || val.match(/\/([A-Z0-9_-]+)\/?$/i);
            if (match && match[1]) {
                return match[1].toUpperCase();
            }
        }
        return val.toUpperCase();
    }

    // Auto-extract kode_tiket from URL if scanned/pasted
    document.getElementById('input-kode')?.addEventListener('input', function () {
        this.value = extractTicketCode(this.value);
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
                const cleanCode = extractTicketCode(decodedText);
                // Show result
                const resultDiv = document.getElementById('qr-result');
                const resultCode = document.getElementById('qr-result-code');
                if (resultCode) resultCode.textContent = cleanCode;
                if (resultDiv) {
                    resultDiv.classList.remove('hidden');
                    // Auto redirect after 1s
                    setTimeout(() => {
                        const input = document.getElementById('input-kode');
                        if (input) {
                            input.value = cleanCode;
                            const form = document.getElementById('form-scan');
                            form.submit();
                        }
                    }, 1000);
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
