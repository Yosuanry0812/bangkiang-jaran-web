@extends('pengelola.layouts.admin')
@section('title', __('messages.reports_title'))

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="font-heading text-2xl font-bold text-stone-800">{{ __('messages.reports_title') }}</h1>
        <p class="text-sm text-stone-500 mt-0.5">{{ __('messages.reports_desc') }}</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-2 mb-5">
            <span class="material-symbols-outlined text-emerald-600">groups</span>
            <h2 class="font-heading text-lg font-semibold text-stone-800">{{ __('messages.visit_report') }}</h2>
        </div>
        <form method="GET" action="{{ route('pengelola.laporan.kunjungan') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-1.5">{{ __('messages.start_period') }}</label>
                <input type="date" name="periode_awal" value="{{ request('periode_awal') }}"
                    class="border border-stone-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-stone-50/50 text-stone-800">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-1.5">{{ __('messages.end_period') }}</label>
                <input type="date" name="periode_akhir" value="{{ request('periode_akhir') }}"
                    class="border border-stone-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-stone-50/50 text-stone-800">
            </div>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-base">search</span>
                {{ __('messages.filter') }}
            </button>
            <a href="{{ route('pengelola.laporan.kunjungan', ['periode_awal' => request('periode_awal'), 'periode_akhir' => request('periode_akhir'), 'export' => 'pdf']) }}"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-base">picture_as_pdf</span>
                {{ __('messages.export_pdf') }}
            </a>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-2 mb-5">
            <span class="material-symbols-outlined text-emerald-600">receipt_long</span>
            <h2 class="font-heading text-lg font-semibold text-stone-800">{{ __('messages.transaction_report') }}</h2>
        </div>
        <form method="GET" action="{{ route('pengelola.laporan.transaksi') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-1.5">{{ __('messages.start_period') }}</label>
                <input type="date" name="periode_awal" value="{{ request('periode_awal') }}"
                    class="border border-stone-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-stone-50/50 text-stone-800">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-1.5">{{ __('messages.end_period') }}</label>
                <input type="date" name="periode_akhir" value="{{ request('periode_akhir') }}"
                    class="border border-stone-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-stone-50/50 text-stone-800">
            </div>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-base">search</span>
                {{ __('messages.filter') }}
            </button>
            <a href="{{ route('pengelola.laporan.transaksi', ['periode_awal' => request('periode_awal'), 'periode_akhir' => request('periode_akhir'), 'export' => 'pdf']) }}"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-base">picture_as_pdf</span>
                {{ __('messages.export_pdf') }}
            </a>
        </form>
    </div>
</div>
@endsection