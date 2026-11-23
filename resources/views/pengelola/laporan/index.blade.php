@extends('pengelola.layouts.admin')
@section('title', __('report.title'))
@section('page_title', __('report.title'))
@section('content')
@php
    $rp = fn ($n) => 'Rp'.number_format((int) $n, 0, ',', '.');
    $queryKeep = array_filter($filters);
    $catLabels = [];
    foreach (['dewasa', 'anak', 'wisman', 'lainnya'] as $k) { $catLabels[$k] = __('report.cat_'.$k); }
@endphp
<style>
    .rpt-bar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
    .rpt-month { display: inline-flex; align-items: center; gap: 4px; background: #fff; border: 1px solid var(--admin-line, #e5e0d5); border-radius: 12px; padding: 4px; }
    .rpt-month a, .rpt-month button { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 9px; color: #31483b; background: transparent; border: 0; cursor: pointer; }
    .rpt-month a:hover, .rpt-month button:hover { background: #f1f2ea; }
    .rpt-month input[type=month] { border: 0; background: transparent; font: inherit; font-weight: 600; color: #1f2a24; min-width: 150px; text-align: center; padding: 6px 4px; }
    .rpt-live { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; color: #5b675f; }
    .rpt-live i { width: 8px; height: 8px; border-radius: 50%; background: #2e9d6a; box-shadow: 0 0 0 0 rgba(46,157,106,.5); animation: rptPulse 2s infinite; }
    @keyframes rptPulse { 70% { box-shadow: 0 0 0 7px rgba(46,157,106,0); } 100% { box-shadow: 0 0 0 0 rgba(46,157,106,0); } }
    .rpt-kpis { display: grid; grid-template-columns: 1.4fr 1fr 1fr 1fr; margin: 20px 0; background: #fff; border: 1px solid var(--admin-line, #e5e0d5); border-radius: 14px; }
    .rpt-kpis > div { padding: 20px 24px; min-width: 0; }
    .rpt-kpis > div + div { border-left: 1px solid var(--admin-line, #e5e0d5); }
    .rpt-kpis dt { font-size: 12px; text-transform: uppercase; letter-spacing: .06em; color: #6b6b6b; }
    .rpt-kpis dd { margin: 6px 0 2px; font-size: 24px; font-weight: 700; color: #1f2a24; overflow-wrap: anywhere; font-variant-numeric: tabular-nums; }
    .rpt-kpis dd.rpt-main { font-size: 30px; color: #005344; }
    .rpt-kpis small { color: #6b6b6b; font-size: 12px; }
    .rpt-chart-box { position: relative; overflow-x: auto; padding: 8px 20px 20px; }
    .rpt-chart-box svg { display: block; width: 100%; min-width: 680px; height: auto; }
    .rpt-chart-box text { font-family: inherit; font-size: 11px; fill: #6b6b6b; }
    .rpt-chart-box .rpt-weekend { fill: #8a6d3b; }
    .rpt-tip { position: absolute; pointer-events: none; z-index: 5; background: #1f2a24; color: #fff; border-radius: 10px; padding: 8px 12px; font-size: 12px; line-height: 1.5; white-space: nowrap; opacity: 0; transition: opacity .12s; box-shadow: 0 6px 20px rgba(0,0,0,.18); }
    .rpt-tip.on { opacity: 1; }
    .rpt-tip b { display: block; font-size: 13px; margin-bottom: 2px; }
    .rpt-tip span.sw { display: inline-block; width: 8px; height: 8px; border-radius: 2px; margin-right: 6px; }
    .rpt-legend { display: flex; flex-wrap: wrap; gap: 14px; padding: 0 20px 4px; font-size: 12px; color: #31483b; }
    .rpt-legend span::before { content: ''; display: inline-block; width: 10px; height: 10px; border-radius: 3px; margin-right: 6px; background: var(--c); vertical-align: -1px; }
    .rpt-stack { margin-top: 20px; }
    .bj-admin .rpt-table { table-layout: fixed; width: 100%; }
    .bj-admin .rpt-table th, .bj-admin .rpt-table td { padding: 14px 16px; vertical-align: middle; }
    .bj-admin .rpt-table th.rpt-num, .bj-admin .rpt-table td.rpt-num { text-align: right; white-space: nowrap; font-variant-numeric: tabular-nums; }
    .bj-admin .rpt-table th.rpt-ctr, .bj-admin .rpt-table td.rpt-ctr { text-align: center; }
    .bj-admin .rpt-table tfoot td { font-weight: 700; border-top: 2px solid #d9d2c0; background: #faf9f4; }
    .rpt-list-filter { display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; padding: 0 0 16px; }
    .rpt-list-filter label { display: grid; gap: 4px; font-size: 12px; color: #5b675f; flex: 1 1 150px; min-width: 0; }
    .rpt-list-filter input, .rpt-list-filter select { height: 40px; border: 1px solid var(--admin-line, #d9d2c0); border-radius: 10px; padding: 0 12px; font: inherit; color: #1f2a24; background: #fff; width: 100%; }
    .rpt-list-filter .rpt-q { flex: 2 1 220px; }
    .rpt-pill { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; background: #eee9dc; color: #5b4a22; white-space: nowrap; }
    .rpt-pill.ok { background: #dcefe4; color: #14573a; }
    .rpt-pill.bad { background: #f6dede; color: #8a2424; }
    @media (max-width: 900px) {
        .rpt-kpis { grid-template-columns: 1fr 1fr; }
        .rpt-kpis > div:nth-child(odd) { border-left: 0; }
        .rpt-kpis > div:nth-child(n+3) { border-top: 1px solid var(--admin-line, #e5e0d5); }
    }
    @media (max-width: 520px) { .rpt-kpis { grid-template-columns: 1fr; } .rpt-kpis > div + div { border-left: 0; border-top: 1px solid var(--admin-line, #e5e0d5); } }
    @media (prefers-reduced-motion: reduce) { .rpt-live i { animation: none; } }
</style>

<header class="admin-page-head">
    <div><h1>{{ __('report.title') }}</h1><p>{{ __('report.subtitle') }}</p></div>
    <div class="rpt-bar">
        <form method="GET" action="{{ route('pengelola.laporan.index') }}" class="rpt-month" id="rpt-month-form">
            @foreach($queryKeep as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
            <a href="{{ route('pengelola.laporan.index', $queryKeep + ['bulan' => $prevMonth]) }}" aria-label="{{ __('report.prev_month') }}"><span class="material-symbols-outlined" aria-hidden="true">chevron_left</span></a>
            <input type="month" name="bulan" value="{{ $bulan }}" aria-label="{{ __('report.pick_month') }}" onchange="this.form.submit()">
            <a href="{{ route('pengelola.laporan.index', $queryKeep + ['bulan' => $nextMonth]) }}" aria-label="{{ __('report.next_month') }}"><span class="material-symbols-outlined" aria-hidden="true">chevron_right</span></a>
        </form>
        <a href="{{ route('pengelola.laporan.xlsx', ['bulan' => $bulan]) }}" class="admin-btn"><span class="material-symbols-outlined" aria-hidden="true">download</span>{{ __('report.download_xlsx') }}</a>
    </div>
</header>

@if($errors->any())<div class="admin-alert admin-alert--error" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

<dl class="rpt-kpis">
    <div><dt>{{ __('report.period_revenue') }}</dt><dd class="rpt-main" id="k-revenue">{{ $rp($chart['revenue']) }}</dd><small>{{ $monthLabel }}</small></div>
    <div><dt>{{ __('report.transactions') }}</dt><dd id="k-trx">{{ $chart['transactions'] }}</dd><small>{{ __('report.transactions_note') }}</small></div>
    <div><dt>{{ __('report.visitors') }}</dt><dd id="k-visitors">{{ $chart['visitors'] }}</dd><small>{{ __('report.visitors_note') }}</small></div>
    <div><dt>{{ __('report.avg') }}</dt><dd id="k-avg">{{ $rp($chart['average']) }}</dd><small class="rpt-live"><i></i>{{ __('report.live') }} · <span id="k-updated">{{ $chart['updatedAt'] }}</span></small></div>
</dl>

<section class="admin-panel">
    <div class="admin-panel-head"><div><h2>{{ __('report.chart_revenue_title') }} · {{ $monthLabel }}</h2></div></div>
    <div class="rpt-chart-box" id="rpt-revenue-chart"><svg role="img" aria-label="{{ __('report.chart_revenue_title') }}"></svg><div class="rpt-tip"></div></div>
</section>

<section class="admin-panel rpt-stack">
    <div class="admin-panel-head"><div><h2>{{ __('report.chart_tickets_title') }} · {{ $monthLabel }}</h2></div></div>
    <div class="rpt-legend">
        @foreach(['dewasa' => '#005344', 'anak' => '#bd965f', 'wisman' => '#74916e', 'lainnya' => '#a9b8a0'] as $key => $color)
            @continue($key === 'lainnya' && $breakdown['lainnya']['count'] === 0)
            <span style="--c: {{ $color }}">{{ __('report.cat_'.$key) }}</span>
        @endforeach
    </div>
    <div class="rpt-chart-box" id="rpt-tickets-chart"><svg role="img" aria-label="{{ __('report.chart_tickets_title') }}"></svg><div class="rpt-tip"></div></div>
</section>

<section class="admin-panel rpt-stack">
    <div class="admin-panel-head"><div><h2>{{ __('report.breakdown_title') }}</h2></div></div>
    <div class="admin-table-wrap">
        <table class="admin-table admin-table--responsive rpt-table">
            <colgroup><col style="width:40%"><col style="width:18%"><col style="width:24%"><col style="width:18%"></colgroup>
            <thead><tr><th>{{ __('report.col_ticket') }}</th><th class="rpt-num">{{ __('report.col_visitors') }}</th><th class="rpt-num">{{ __('report.col_revenue') }}</th><th class="rpt-num">{{ __('report.col_share') }}</th></tr></thead>
            <tbody>
                @foreach($breakdown as $key => $row)
                    @continue($key === 'lainnya' && $row['count'] === 0)
                    <tr>
                        <td data-label="{{ __('report.col_ticket') }}">{{ __('report.cat_'.$key) }}</td>
                        <td data-label="{{ __('report.col_visitors') }}" class="rpt-num">{{ $row['count'] }}</td>
                        <td data-label="{{ __('report.col_revenue') }}" class="rpt-num">{{ $rp($row['revenue']) }}</td>
                        <td data-label="{{ __('report.col_share') }}" class="rpt-num">{{ $chart['revenue'] > 0 ? round($row['revenue'] / $chart['revenue'] * 100) : 0 }}%</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot><tr><td>{{ __('report.row_total') }}</td><td class="rpt-num">{{ $visitorTotal }}</td><td class="rpt-num">{{ $rp($chart['revenue']) }}</td><td class="rpt-num">{{ $chart['revenue'] > 0 ? '100%' : '0%' }}</td></tr></tfoot>
        </table>
    </div>
</section>

<section class="admin-panel rpt-stack">
    <div class="admin-panel-head"><div><h2>{{ __('report.list_title') }}</h2><p>{{ __('report.list_note') }}</p></div></div>
    <form method="GET" action="{{ route('pengelola.laporan.index') }}" class="rpt-list-filter" style="padding: 0 20px 16px">
        <input type="hidden" name="bulan" value="{{ $bulan }}">
        <label class="rpt-q">{{ __('report.search') }}<input type="search" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100"></label>
        <label>{{ __('report.status') }}<select name="status"><option value="">{{ __('report.all_statuses') }}</option>
            @foreach(['pending' => 'status_pending', 'selesai' => 'status_done', 'dibatalkan' => 'status_cancelled', 'expired' => 'status_expired'] as $value => $label)<option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ __('report.'.$label) }}</option>@endforeach
        </select></label>
        <label>{{ __('report.filter_channel') }}<select name="channel"><option value="">{{ __('report.all_channels') }}</option><option value="online" @selected(($filters['channel'] ?? '') === 'online')>{{ __('report.channel_online') }}</option><option value="offline" @selected(($filters['channel'] ?? '') === 'offline')>{{ __('report.channel_offline') }}</option></select></label>
        <div class="rpt-filter-actions" style="display:flex;gap:8px"><button type="submit" class="admin-btn">{{ __('report.filter') }}</button><a class="admin-btn admin-btn--quiet" href="{{ route('pengelola.laporan.index', ['bulan' => $bulan]) }}">{{ __('report.reset') }}</a></div>
    </form>
    <div class="admin-table-wrap">
        <table class="admin-table admin-table--responsive rpt-table">
            <colgroup><col style="width:22%"><col style="width:17%"><col style="width:12%"><col style="width:11%"><col style="width:13%"><col style="width:15%"><col style="width:10%"></colgroup>
            <thead><tr><th>{{ __('report.col_booking') }}</th><th>{{ __('report.col_name') }}</th><th>{{ __('report.col_ordered_on') }}</th><th class="rpt-num">{{ __('report.col_visitors') }}</th><th class="rpt-num">{{ __('report.col_total') }}</th><th class="rpt-ctr">{{ __('report.col_status') }}</th><th class="rpt-ctr">{{ __('report.col_action') }}</th></tr></thead>
            <tbody>
                @forelse($bookings as $b)
                    @php $ok = $b->isApproved(); $bad = in_array($b->status, ['dibatalkan'], true) || $b->closed_reason === 'expired'; @endphp
                    <tr>
                        <td data-label="{{ __('report.col_booking') }}"><code>{{ $b->kode_booking }}</code><small>{{ __('report.channel_'.(str_starts_with($b->kode_booking, 'BJ-OFF-') ? 'offline' : 'online')) }}</small></td>
                        <td data-label="{{ __('report.col_name') }}">{{ $b->user?->name ?? '-' }}</td>
                        <td data-label="{{ __('report.col_ordered_on') }}">{{ $b->created_at?->format('d/m/Y') }}</td>
                        <td data-label="{{ __('report.col_visitors') }}" class="rpt-num">{{ $b->detailPemesanan->count() }}</td>
                        <td data-label="{{ __('report.col_total') }}" class="rpt-num">{{ $rp($b->total_harga) }}</td>
                        <td data-label="{{ __('report.col_status') }}" class="rpt-ctr"><span class="rpt-pill {{ $ok ? 'ok' : ($bad ? 'bad' : '') }}">{{ $b->statusLabel() }}</span></td>
                        <td data-label="{{ __('report.col_action') }}" class="rpt-ctr"><a href="{{ route('pengelola.verifikasi.show', $b->id_pemesanan) }}">{{ __('report.view') }}</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7">{{ __('report.empty') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 0 20px 16px">{{ $bookings->links() }}</div>
</section>

<script type="application/json" id="rpt-data">@json($chart)</script>
<script>
(function () {
    'use strict';
    var DATA_URL = @json(route('pengelola.laporan.data', ['bulan' => $bulan]));
    var COLORS = { dewasa: '#005344', anak: '#bd965f', wisman: '#74916e', lainnya: '#a9b8a0' };
    var CATS = ['dewasa', 'anak', 'wisman', 'lainnya'];
    var CAT_LABEL = @json($catLabels);
    var MONTHS = @json(__('report.months'));
    var UNIT = @json(__('report.tickets_unit'));
    var W = 960, H = 300, PL = 64, PR = 16, PT = 16, PB = 40;
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var data = JSON.parse(document.getElementById('rpt-data').textContent);

    function rp(n) { return 'Rp' + Math.round(n).toLocaleString('id-ID'); }
    function short(n) { return n >= 1e6 ? (n / 1e6).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' jt' : n >= 1e3 ? Math.round(n / 1e3) + ' rb' : String(n); }
    function niceMax(max, parts) {
        if (max <= 0) return parts;
        var raw = max / parts, mag = Math.pow(10, Math.floor(Math.log10(raw))), f = raw / mag;
        var step = (f <= 1 ? 1 : f <= 2 ? 2 : f <= 2.5 ? 2.5 : f <= 5 ? 5 : 10) * mag;
        if (step < 1) step = 1;
        return Math.ceil(max / step) * step;
    }
    function ease(t) { return 1 - Math.pow(1 - t, 3); }

    function Chart(boxId, kind) {
        this.box = document.getElementById(boxId);
        this.svg = this.box.querySelector('svg');
        this.tip = this.box.querySelector('.rpt-tip');
        this.kind = kind;
        this.svg.setAttribute('viewBox', '0 0 ' + W + ' ' + H);
        this.hover = -1;
        this.cur = null;
        this.raf = 0;
        var self = this;
        this.svg.addEventListener('mousemove', function (e) { self.onMove(e); });
        this.svg.addEventListener('mouseleave', function () { self.hover = -1; self.tip.classList.remove('on'); self.draw(self.cur); });
    }
    // Nilai target per hari: revenue -> angka, tickets -> [d,a,w,l]; null = tanggal yang belum terjadi.
    Chart.prototype.target = function (d) {
        var self = this;
        return d.days.map(function (day) {
            if (day.pendapatan === null) return null;
            return self.kind === 'rev' ? day.pendapatan : CATS.map(function (c) { return day.tiket[c] || 0; });
        });
    };
    Chart.prototype.zero = function (t) {
        var self = this;
        return t.map(function (v) { return v === null ? null : self.kind === 'rev' ? 0 : [0, 0, 0, 0]; });
    };
    Chart.prototype.total = function (v) { return this.kind === 'rev' ? v : v.reduce(function (a, b) { return a + b; }, 0); };
    Chart.prototype.update = function (d, instant) {
        var self = this, to = this.target(d), from = this.cur ? this.cur.vals : this.zero(to);
        if (this.cur && this.cur.vals.length !== to.length) from = this.zero(to);
        this.data = d;
        cancelAnimationFrame(this.raf);
        var maxT = Math.max.apply(null, to.map(function (v) { return v === null ? 0 : self.total(v); }).concat([0]));
        var yTo = niceMax(maxT, 4), yFrom = this.cur ? this.cur.y : 0;
        if (instant || reduce) { this.cur = { vals: to, y: yTo }; this.draw(this.cur); return; }
        var t0 = null, dur = 700;
        function step(ts) {
            if (t0 === null) t0 = ts;
            var p = Math.min(1, (ts - t0) / dur), e = ease(p);
            var vals = to.map(function (v, i) {
                if (v === null) return null;
                var f = from[i] === null ? (self.kind === 'rev' ? 0 : [0, 0, 0, 0]) : from[i];
                return self.kind === 'rev' ? f + (v - f) * e : v.map(function (x, k) { return f[k] + (x - f[k]) * e; });
            });
            self.cur = { vals: vals, y: yFrom + (yTo - yFrom) * e };
            self.draw(self.cur);
            if (p < 1) self.raf = requestAnimationFrame(step); else self.cur = { vals: to, y: yTo };
        }
        this.raf = requestAnimationFrame(step);
    };
    Chart.prototype.draw = function (s) {
        if (!s || !this.data) return;
        var n = s.vals.length, iw = W - PL - PR, ih = H - PT - PB, col = iw / n, ymax = s.y || 4, out = '', i;
        function Y(v) { return PT + ih * (1 - v / ymax); }
        for (i = 0; i <= 4; i++) {
            var gv = ymax * i / 4, gy = Y(gv);
            out += '<line x1="' + PL + '" x2="' + (W - PR) + '" y1="' + gy + '" y2="' + gy + '" stroke="#e8e4d8" stroke-width="1"/>';
            out += '<text x="' + (PL - 8) + '" y="' + (gy + 4) + '" text-anchor="end">' + (this.kind === 'rev' ? short(gv) : Math.round(gv * 10) / 10) + '</text>';
        }
        // Label tanggal: tiap hari, bulan di awal dan akhir
        for (i = 0; i < n; i++) {
            var d = this.data.days[i], cx = PL + col * (i + 0.5), wk = d.dow === 0 || d.dow === 6;
            out += '<text x="' + cx + '" y="' + (H - PB + 16) + '" text-anchor="middle"' + (wk ? ' class="rpt-weekend"' : '') + '>' + d.hari + '</text>';
            if (i === 0 || i === n - 1) out += '<text x="' + cx + '" y="' + (H - PB + 30) + '" text-anchor="middle">' + this.data.monthShort + '</text>';
        }
        if (this.hover >= 0) out += '<rect x="' + (PL + col * this.hover) + '" y="' + PT + '" width="' + col + '" height="' + ih + '" fill="#005344" opacity=".06"/>';
        if (this.kind === 'rev') out += this.drawLine(s, col, Y);
        else out += this.drawBars(s, col, Y);
        this.svg.innerHTML = out;
    };
    Chart.prototype.drawLine = function (s, col, Y) {
        var pts = [], i, out = '';
        for (i = 0; i < s.vals.length; i++) if (s.vals[i] !== null) pts.push([PL + col * (i + 0.5), Y(s.vals[i]), i]);
        if (!pts.length) return '';
        var line = pts.map(function (p, k) { return (k ? 'L' : 'M') + p[0].toFixed(1) + ' ' + p[1].toFixed(1); }).join(' ');
        var base = Y(0);
        out += '<path d="' + line + ' L' + pts[pts.length - 1][0].toFixed(1) + ' ' + base + ' L' + pts[0][0].toFixed(1) + ' ' + base + ' Z" fill="#005344" opacity=".10"/>';
        out += '<path d="' + line + '" fill="none" stroke="#005344" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/>';
        for (i = 0; i < pts.length; i++) out += '<circle cx="' + pts[i][0].toFixed(1) + '" cy="' + pts[i][1].toFixed(1) + '" r="' + (pts[i][2] === this.hover ? 5 : 3.2) + '" fill="#fff" stroke="#005344" stroke-width="2"/>';
        return out;
    };
    Chart.prototype.drawBars = function (s, col, Y) {
        var out = '', i, k, bw = Math.min(26, col * 0.62);
        for (i = 0; i < s.vals.length; i++) {
            var v = s.vals[i]; if (v === null) continue;
            var x = PL + col * (i + 0.5) - bw / 2, acc = 0;
            for (k = 0; k < 4; k++) {
                if (v[k] <= 0) continue;
                var y1 = Y(acc + v[k]), y0 = Y(acc);
                out += '<rect x="' + x.toFixed(1) + '" y="' + y1.toFixed(1) + '" width="' + bw.toFixed(1) + '" height="' + Math.max(0, y0 - y1).toFixed(1) + '" fill="' + COLORS[CATS[k]] + '"' + (k === 3 || acc + v[k] >= this.total(v) - 0.001 ? ' rx="3"' : '') + '/>';
                acc += v[k];
            }
        }
        return out;
    };
    Chart.prototype.onMove = function (e) {
        if (!this.data) return;
        var r = this.svg.getBoundingClientRect(), n = this.data.days.length;
        var x = (e.clientX - r.left) / r.width * W, i = Math.floor((x - PL) / ((W - PL - PR) / n));
        if (i < 0 || i >= n) { this.tip.classList.remove('on'); this.hover = -1; this.draw(this.cur); return; }
        var day = this.data.days[i], html = '<b>' + day.hari + ' ' + MONTHS[parseInt(this.data.bulan.slice(5), 10) - 1] + ' ' + this.data.bulan.slice(0, 4) + '</b>';
        if (day.pendapatan === null) html += '–';
        else if (this.kind === 'rev') html += rp(day.pendapatan);
        else {
            var tot = 0;
            CATS.forEach(function (c) { var q = day.tiket[c] || 0; tot += q; if (q) html += '<div><span class="sw" style="background:' + COLORS[c] + '"></span>' + CAT_LABEL[c] + ': ' + q + '</div>'; });
            html += '<div>Total: ' + tot + ' ' + UNIT + '</div>';
        }
        this.hover = i; this.draw(this.cur);
        this.tip.innerHTML = html; this.tip.classList.add('on');
        var br = this.box.getBoundingClientRect(), left = e.clientX - br.left + this.box.scrollLeft + 14;
        if (left + this.tip.offsetWidth > this.box.scrollLeft + this.box.clientWidth) left = e.clientX - br.left + this.box.scrollLeft - this.tip.offsetWidth - 14;
        this.tip.style.left = left + 'px'; this.tip.style.top = Math.max(0, e.clientY - br.top - 10) + 'px';
    };

    var rev = new Chart('rpt-revenue-chart', 'rev'), tix = new Chart('rpt-tickets-chart', 'tix');
    function setKpi(d) {
        document.getElementById('k-revenue').textContent = rp(d.revenue);
        document.getElementById('k-trx').textContent = d.transactions;
        document.getElementById('k-visitors').textContent = d.visitors;
        document.getElementById('k-avg').textContent = rp(d.average);
        document.getElementById('k-updated').textContent = d.updatedAt;
    }
    // Tampil langsung dari data yang sudah ada di halaman (tanpa menunggu jaringan).
    rev.update(data); tix.update(data);

    // Realtime: ambil data terbaru berkala; grafik bergerak halus ke nilai baru.
    var busy = false;
    function refresh() {
        if (busy || document.hidden) return;
        busy = true;
        fetch(DATA_URL, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin', cache: 'no-store' })
            .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
            .then(function (d) { data = d; setKpi(d); rev.update(d); tix.update(d); })
            .catch(function () {})
            .then(function () { busy = false; });
    }
    setInterval(refresh, 8000);
    document.addEventListener('visibilitychange', function () { if (!document.hidden) refresh(); });
})();
</script>
@endsection
