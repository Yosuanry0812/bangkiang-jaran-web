@extends('pengelola.layouts.admin')
@section('title', __('messages.reports_title'))
@section('page_title', __('messages.reports_title'))
@section('content')
<header class="admin-page-head"><div><h1>Laporan operasional</h1><p>Tinjau kunjungan dan transaksi berdasarkan periode yang Anda pilih.</p></div></header>
@if($errors->any())<div class="admin-alert admin-alert--error" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="admin-report-grid">
    @foreach([
        'kunjungan' => ['title' => __('messages.visit_report'), 'icon' => 'groups', 'description' => 'Rekap jumlah pengunjung dan tiket untuk memantau kunjungan destinasi.'],
        'transaksi' => ['title' => __('messages.transaction_report'), 'icon' => 'receipt_long', 'description' => 'Rincian pembayaran dan pendapatan dari pemesanan online maupun offline.'],
    ] as $type => $report)
    <section class="admin-panel">
        <div class="admin-report-intro"><span class="material-symbols-outlined" aria-hidden="true">{{ $report['icon'] }}</span><h2>{{ $report['title'] }}</h2><p>{{ $report['description'] }}</p></div>
        <form method="GET" action="{{ route('pengelola.laporan.' . $type) }}" class="admin-report-form">
            <label class="admin-field" for="{{ $type }}-awal">{{ __('messages.start_period') }}<input id="{{ $type }}-awal" type="date" name="periode_awal" value="{{ request('periode_awal') ?: now()->startOfMonth()->toDateString() }}" required></label>
            <label class="admin-field" for="{{ $type }}-akhir">{{ __('messages.end_period') }}<input id="{{ $type }}-akhir" type="date" name="periode_akhir" value="{{ request('periode_akhir') ?: now()->toDateString() }}" required></label>
            <label class="admin-field" for="{{ $type }}-tipe">{{ __('messages.report_type') }}<select id="{{ $type }}-tipe" name="tipe"><option value="">{{ __('messages.all') }}</option><option value="online" @selected(request('tipe') === 'online')>{{ __('messages.online') }}</option><option value="offline" @selected(request('tipe') === 'offline')>{{ __('messages.offline') }}</option></select></label>
            <div class="admin-report-actions"><button type="submit" class="admin-btn"><span class="material-symbols-outlined" aria-hidden="true">filter_alt</span>Lihat laporan</button><button type="submit" name="export" value="pdf" class="admin-btn admin-btn--quiet"><span class="material-symbols-outlined" aria-hidden="true">download</span>{{ __('messages.export_pdf') }}</button></div>
        </form>
    </section>
    @endforeach
</div>
<p class="admin-note mt-6">Pilih tanggal awal dan akhir laporan. Ekspor PDF mengikuti periode dan jenis pemesanan yang dipilih.</p>
@endsection
