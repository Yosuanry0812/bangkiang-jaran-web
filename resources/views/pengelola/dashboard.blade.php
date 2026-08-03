@extends('pengelola.layouts.admin')

@section('title', 'Dashboard — Bangkiang Jaran')
@section('page_title', 'Dashboard')

@push('styles')
<style>
    /* ── Metric card ── */
    .metric-card {
        position: relative;
        overflow: hidden;
        transition: box-shadow .22s ease, transform .22s ease;
    }
    .metric-card:hover {
        box-shadow: 0 6px 28px rgba(15,26,23,.08);
        transform: translateY(-2px);
    }
    .metric-card::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        background: linear-gradient(135deg, rgba(255,255,255,.6) 0%, transparent 60%);
        pointer-events: none;
    }

    /* ── Table row ── */
    .tbl-row { transition: background .14s ease; }
    .tbl-row:hover { background: #f8fafc; }

    /* ── Quick action ── */
    .qa-btn {
        position: relative;
        overflow: hidden;
        transition: all .2s ease;
    }
    .qa-btn::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(15,26,23,.04), transparent);
        opacity: 0;
        transition: opacity .2s ease;
    }
    .qa-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(15,26,23,.08); }
    .qa-btn:hover::after { opacity: 1; }

    /* ── Section divider ── */
    .section-rule {
        height: 1px;
        background: linear-gradient(to right, transparent, #e2e8f0 20%, #e2e8f0 80%, transparent);
        margin: 28px 0;
    }

    /* ── Pending badge pulse ── */
    @keyframes pulse-ring {
        0%   { transform: scale(1); opacity: .6; }
        100% { transform: scale(1.6); opacity: 0; }
    }
    .pulse-ring::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: #f59e0b;
        animation: pulse-ring 1.8s ease-out infinite;
    }

    /* ── Chart tooltip ── */
    .chartjs-tooltip { font-family: 'Inter', sans-serif !important; font-size: 12px !important; }

    /* ── Scrollbar ── */
    .thin-scroll::-webkit-scrollbar       { height: 3px; }
    .thin-scroll::-webkit-scrollbar-track { background: transparent; }
    .thin-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 99px; }
</style>
@endpush

@section('content')
@php
    use Carbon\Carbon;
    $locale = app()->getLocale();
    $today = now()->locale($locale)->translatedFormat('l, d F Y');
    $hasPending = ($pemesananPending ?? 0) > 0;
@endphp

{{-- ══════════════════════════════════════
     PAGE HEADER
═══════════════════════════════════════ --}}
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8">

    <div class="space-y-0.5">
        <h1 class="font-serif text-[26px] text-slate-900 leading-tight tracking-tight">
            Selamat datang kembali
        </h1>
        <p class="font-sans text-[13px] text-slate-400">{{ $today }}</p>
    </div>

    <a href="{{ route('pengelola.verifikasi.index') }}"
       class="group relative inline-flex items-center gap-2.5 bg-slate-900 text-white font-sans text-[12px] font-semibold px-4 py-2.5 rounded-xl hover:bg-slate-800 active:scale-[.98] transition-all self-start">
        <span class="material-symbols-outlined" style="font-size:15px">verified_user</span>
        Verifikasi Pembayaran
        @if($hasPending)
        <span class="relative inline-flex items-center justify-center">
            <span class="pulse-ring absolute w-4 h-4 rounded-full"></span>
            <span class="relative bg-amber-400 text-slate-900 text-[10px] font-bold px-1.5 py-0.5 rounded-md leading-none z-10">
                {{ $pemesananPending }}
            </span>
        </span>
        @endif
    </a>
</div>

{{-- ══════════════════════════════════════
     METRIC CARDS
═══════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mb-7">

    {{-- Pengunjung Hari Ini --}}
    <div class="metric-card bg-white border border-slate-100/80 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-5">
            <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-slate-500" style="font-size:16px">groups</span>
            </div>
            <span class="inline-flex items-center gap-1 font-sans text-[10px] font-medium text-slate-400 bg-slate-50 border border-slate-100 px-2 py-0.5 rounded-full">
                Hari ini
            </span>
        </div>
        <p class="font-serif text-[34px] text-slate-900 leading-none mb-1.5">{{ $pengunjungHariIni ?? 0 }}</p>
        <p class="font-sans text-[11px] text-slate-400">orang terkonfirmasi</p>
        <p class="font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-300 mt-3">Pengunjung</p>
    </div>

    {{-- Pendapatan Bulan Ini --}}
    <div class="metric-card bg-white border border-slate-100/80 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-5">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-emerald-600" style="font-size:16px">payments</span>
            </div>
            <span class="inline-flex items-center gap-1 font-sans text-[10px] font-medium text-emerald-600 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-full">
                Bulan ini
            </span>
        </div>
        <p class="font-serif text-[26px] text-slate-900 leading-none mb-1.5 break-all">
            Rp{{ number_format($pendapatanBulanIni ?? 0, 0, ',', '.') }}
        </p>
        <p class="font-sans text-[11px] text-slate-400">pembayaran valid</p>
        <p class="font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-300 mt-3">Pendapatan</p>
    </div>

    {{-- Menunggu Verifikasi --}}
    <div class="metric-card bg-white border {{ $hasPending ? 'border-amber-200/70' : 'border-slate-100/80' }} rounded-2xl p-5 {{ $hasPending ? 'bg-gradient-to-br from-amber-50/60 to-white' : '' }}">
        <div class="flex items-center justify-between mb-5">
            <div class="w-8 h-8 rounded-xl {{ $hasPending ? 'bg-amber-100' : 'bg-slate-100' }} flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined {{ $hasPending ? 'text-amber-500' : 'text-slate-400' }}" style="font-size:16px">pending_actions</span>
            </div>
            @if($hasPending)
            <span class="inline-flex items-center gap-1 font-sans text-[10px] font-medium text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">
                Perlu tindakan
            </span>
            @else
            <span class="inline-flex items-center gap-1 font-sans text-[10px] font-medium text-slate-400 bg-slate-50 border border-slate-100 px-2 py-0.5 rounded-full">
                Semua bersih
            </span>
            @endif
        </div>
        <p class="font-serif text-[34px] leading-none mb-1.5 {{ $hasPending ? 'text-amber-500' : 'text-slate-900' }}">
            {{ $pemesananPending ?? 0 }}
        </p>
        <p class="font-sans text-[11px] text-slate-400">pesanan perlu ditinjau</p>
        <p class="font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-300 mt-3">Verifikasi</p>
    </div>

    {{-- Total Wisatawan --}}
    <div class="metric-card bg-white border border-slate-100/80 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-5">
            <div class="w-8 h-8 rounded-xl bg-sky-50 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-sky-500" style="font-size:16px">travel_explore</span>
            </div>
            <span class="inline-flex items-center gap-1 font-sans text-[10px] font-medium text-slate-400 bg-slate-50 border border-slate-100 px-2 py-0.5 rounded-full">
                Total
            </span>
        </div>
        <p class="font-serif text-[34px] text-slate-900 leading-none mb-1.5">{{ $totalWisatawan ?? 0 }}</p>
        <p class="font-sans text-[11px] text-slate-400">akun terdaftar</p>
        <p class="font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-300 mt-3">Wisatawan</p>
    </div>

</div>

{{-- ══════════════════════════════════════
     CHART + RECENT TABLE
═══════════════════════════════════════ --}}
<div class="grid grid-cols-1 xl:grid-cols-[300px_1fr] gap-4 mb-4">

    {{-- ── Donut Chart ── --}}
    <div class="bg-white border border-slate-100/80 rounded-2xl p-6 flex flex-col min-h-0">

        <div class="mb-5 flex-shrink-0">
            <p class="font-sans text-[13px] font-semibold text-slate-800">Komposisi Tiket</p>
            <p class="font-sans text-[11px] text-slate-400 mt-0.5">distribusi tiket terjual</p>
        </div>

        @if(!empty($grafikData) && array_sum($grafikData) > 0)

        {{-- Chart wrapper: flex-shrink-0 so legend never pushes it off --}}
        <div class="flex items-center justify-center flex-shrink-0 mb-5">
            {{-- Outer: explicit size + position:relative for center overlay --}}
            <div id="donutWrap" style="position:relative; width:200px; height:200px; flex-shrink:0;">
                <canvas id="chartDonut" width="200" height="200"></canvas>
                {{-- Center label — positioned via JS after render to stay perfectly centered --}}
                <div id="donutCenter"
                     style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
                            display:flex; flex-direction:column; align-items:center; pointer-events:none; user-select:none;">
                    <span id="donutTotal"
                          style="font-family:'Instrument Serif',Georgia,serif; font-size:28px; line-height:1; color:#0f172a;"></span>
                    <span style="font-family:Inter,sans-serif; font-size:10px; color:#94a3b8; margin-top:3px; text-transform:uppercase; letter-spacing:.06em;">tiket</span>
                </div>
            </div>
        </div>

        {{-- Legend — grows to fill remaining space, scrollable if too many items --}}
        <div id="chartLegend"
             class="flex-1 overflow-y-auto min-h-0"
             style="display:flex; flex-direction:column; gap:6px; max-height:160px;"></div>

        @else
        <div class="flex-1 flex flex-col items-center justify-center py-10 text-center">
            <div class="w-14 h-14 rounded-full bg-slate-50 flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-slate-300" style="font-size:28px">donut_large</span>
            </div>
            <p class="font-sans text-[13px] font-medium text-slate-500 mb-1">Belum ada data</p>
            <p class="font-sans text-[11px] text-slate-400">Data tiket akan tampil di sini</p>
        </div>
        @endif
    </div>

    {{-- ── Recent Bookings ── --}}
    <div class="bg-white border border-slate-100/80 rounded-2xl flex flex-col overflow-hidden">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 flex-shrink-0">
            <div>
                <p class="font-sans text-[13px] font-semibold text-slate-800">Pemesanan Terbaru</p>
                <p class="font-sans text-[11px] text-slate-400 mt-0.5">5 transaksi terakhir</p>
            </div>
            <a href="{{ route('pengelola.verifikasi.index') }}"
               class="inline-flex items-center gap-1 font-sans text-[11px] font-semibold text-slate-500 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-100 px-3 py-1.5 rounded-lg transition-all">
                Lihat semua
                <span class="material-symbols-outlined" style="font-size:12px">arrow_forward</span>
            </a>
        </div>

        <div class="overflow-x-auto flex-1 thin-scroll">
            <table class="w-full min-w-[540px]">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left px-6 py-3 font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400 bg-slate-50/50">Kode</th>
                        <th class="text-left px-4 py-3 font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400 bg-slate-50/50">Pemesan</th>
                        <th class="text-left px-4 py-3 font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400 bg-slate-50/50 hidden md:table-cell">Tiket</th>
                        <th class="text-left px-4 py-3 font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400 bg-slate-50/50">Status</th>
                        <th class="text-left px-4 py-3 font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400 bg-slate-50/50 hidden sm:table-cell">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($pemesananTerbaru ?? [] as $p)
                    <tr class="tbl-row group">
                        <td class="px-6 py-3.5">
                            <span class="font-mono text-[12px] font-semibold text-slate-700 group-hover:text-slate-900">
                                {{ $p->kode_booking }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0">
                                    <span class="font-sans text-[9px] font-semibold text-slate-500">
                                        {{ strtoupper(substr($p->user->name ?? 'U', 0, 1)) }}
                                    </span>
                                </div>
                                <span class="font-sans text-[12px] text-slate-600 truncate max-w-[140px]">{{ $p->user->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 hidden md:table-cell">
                                                        <span class="font-sans text-[11px] text-slate-500">
                                @php
                                    $dSum = $p->detailPemesanan->groupBy('nama_tiket')->map(fn($g) => $g->count() . '× ' . $g->first()->nama_tiket)->implode(', ');
                                @endphp
                                {{ $dSum ?: ($p->tiket->nama_tiket ?? '—') }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            @php
                                $badge = match($p->status) {
                                    'selesai'    => ['bg' => 'bg-emerald-50 border-emerald-200', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'label' => 'Selesai'],
                                    'pending'    => ['bg' => 'bg-amber-50  border-amber-200',   'text' => 'text-amber-700',   'dot' => 'bg-amber-400',  'label' => 'Pending'],
                                    'dibatalkan' => ['bg' => 'bg-red-50    border-red-200',      'text' => 'text-red-700',     'dot' => 'bg-red-400',    'label' => 'Batal'],
                                    default      => ['bg' => 'bg-slate-50  border-slate-200',    'text' => 'text-slate-600',   'dot' => 'bg-slate-400',  'label' => ucfirst($p->status)],
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border font-sans text-[10px] font-semibold {{ $badge['bg'] }} {{ $badge['text'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }} flex-shrink-0"></span>
                                {{ $badge['label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 hidden sm:table-cell">
                            <span class="font-sans text-[11px] text-slate-400">{{ $p->created_at->format('d M Y') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-14 text-center">
                            <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-3">
                                <span class="material-symbols-outlined text-slate-300" style="font-size:24px">receipt_long</span>
                            </div>
                            <p class="font-sans text-[13px] font-medium text-slate-500 mb-1">Belum ada pemesanan</p>
                            <p class="font-sans text-[11px] text-slate-400">Pemesanan masuk akan tampil di sini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════
     QUICK ACTIONS
═══════════════════════════════════════ --}}
<div class="bg-white border border-slate-100/80 rounded-2xl p-5">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="font-sans text-[13px] font-semibold text-slate-800">Akses Cepat</p>
            <p class="font-sans text-[11px] text-slate-400 mt-0.5">navigasi ke fitur utama</p>
        </div>
    </div>
    <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
        @php
        $actions = [
            ['href' => route('pengelola.tiket.index'),      'icon' => 'confirmation_number', 'label' => 'Tiket',      'color' => 'text-slate-600',   'bg' => 'bg-slate-100'],
            ['href' => route('pengelola.verifikasi.index'), 'icon' => 'verified_user',        'label' => 'Verifikasi', 'color' => 'text-amber-600',   'bg' => 'bg-amber-50'],
            ['href' => route('pengelola.laporan.index'),    'icon' => 'monitoring',           'label' => 'Laporan',    'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50'],
            ['href' => route('pengelola.galeri.index'),     'icon' => 'photo_library',        'label' => 'Galeri',     'color' => 'text-sky-600',     'bg' => 'bg-sky-50'],
            ['href' => route('pengelola.user.index'),       'icon' => 'group',                'label' => 'Pengguna',   'color' => 'text-rose-600',    'bg' => 'bg-rose-50'],
        ];
        @endphp

        @foreach($actions as $a)
        <a href="{{ $a['href'] }}"
           class="qa-btn flex flex-col items-center gap-2.5 py-4 px-2 border border-slate-100 rounded-xl text-center cursor-pointer">
            <div class="w-10 h-10 rounded-xl {{ $a['bg'] }} flex items-center justify-center">
                <span class="material-symbols-outlined {{ $a['color'] }}" style="font-size:18px">{{ $a['icon'] }}</span>
            </div>
            <span class="font-sans text-[11px] font-semibold text-slate-600">{{ $a['label'] }}</span>
        </a>
        @endforeach

    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    var labels = {!! json_encode($grafikLabels ?? []) !!};
    var data   = {!! json_encode($grafikData   ?? []) !!};
    var total  = data.reduce(function(a, b) { return a + b; }, 0);
    var el     = document.getElementById('chartDonut');

    if (!el || total === 0) return;

    /* ── Set canvas explicit pixel size before Chart.js init ── */
    var SIZE = 200;
    el.width  = SIZE;
    el.height = SIZE;
    el.style.width  = SIZE + 'px';
    el.style.height = SIZE + 'px';
    el.style.display = 'block';

    /* ── Center label ── */
    var totalEl = document.getElementById('donutTotal');
    if (totalEl) totalEl.textContent = total;

    /* ── Palette ── */
    var palette = ['#1e293b', '#334155', '#64748b', '#94a3b8', '#cbd5e1', '#e2e8f0'];

    /* ── Chart ── */
    new Chart(el.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: palette.slice(0, data.length),
                borderColor: '#ffffff',
                borderWidth: 3,
                hoverOffset: 6,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            cutout: '68%',
            animation: { animateRotate: true, duration: 800, easing: 'easeInOutQuart' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            var pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                            return '  ' + ctx.parsed + ' tiket  (' + pct + '%)';
                        }
                    },
                    backgroundColor: '#1e293b',
                    titleColor: '#94a3b8',
                    bodyColor: '#f1f5f9',
                    cornerRadius: 10,
                    padding: 12,
                }
            }
        }
    });

    /* ── Custom legend ── */
    var legendEl = document.getElementById('chartLegend');
    if (!legendEl) return;

    labels.forEach(function(lbl, i) {
        var pct = total > 0 ? ((data[i] / total) * 100).toFixed(0) : 0;
        var row = document.createElement('div');
        row.style.cssText = 'display:flex; align-items:center; justify-content:space-between; padding:4px 0; border-bottom:1px solid #f1f5f9;';
        row.innerHTML =
            '<div style="display:flex; align-items:center; gap:8px; min-width:0; overflow:hidden;">' +
                '<span style="width:8px; height:8px; border-radius:2px; background:' + palette[i] + '; flex-shrink:0; display:inline-block;"></span>' +
                '<span style="font-family:Inter,sans-serif; font-size:11px; color:#64748b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">' + lbl + '</span>' +
            '</div>' +
            '<div style="display:flex; align-items:center; gap:6px; flex-shrink:0; margin-left:8px;">' +
                '<span style="font-family:Inter,sans-serif; font-size:11px; color:#94a3b8;">' + data[i] + '</span>' +
                '<span style="font-family:Inter,sans-serif; font-size:10px; font-weight:700; color:#1e293b; background:#f1f5f9; padding:1px 6px; border-radius:99px;">' + pct + '%</span>' +
            '</div>';
        legendEl.appendChild(row);
    });

    /* Remove bottom border from last item */
    var lastRow = legendEl.lastChild;
    if (lastRow) lastRow.style.borderBottom = 'none';
})();
</script>
@endpush
