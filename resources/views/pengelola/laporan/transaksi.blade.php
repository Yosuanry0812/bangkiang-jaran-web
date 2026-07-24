@extends('pengelola.layouts.admin')
@section('title', __('messages.transaction_report_title'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-gray-500-800">{{ __('messages.transaction_report_title') }}</h1>
            <p class="text-sm text-gray-500-500 mt-0.5">
                {{ __('messages.period') }} {{ $periode_awal ? \Carbon\Carbon::parse($periode_awal)->format('d/m/Y') : __('messages.all') }}
                -
                {{ $periode_akhir ? \Carbon\Carbon::parse($periode_akhir)->format('d/m/Y') : __('messages.all') }}
            </p>
        </div>
        <a href="{{ route('pengelola.laporan.transaksi', array_merge(request()->all(), ['export' => 'pdf'])) }}"
            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
            <span class="material-symbols-outlined text-base">picture_as_pdf</span>
            Export PDF
        </a>
    </div>

    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
            <span class="material-symbols-outlined text-2xl">payments</span>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">{{ __('messages.grand_total_label') }}</p>
            <p class="text-2xl font-bold text-emerald-900">Rp {{ number_format($total ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-300-200 bg-stone-50 text-left">
                        <th class="text-xs font-semibold uppercase tracking-wider text-gray-500-500 py-3.5 px-4">{{ __('messages.th_booking_code') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-gray-500-500 py-3.5 px-4">{{ __('messages.th_user') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-gray-500-500 py-3.5 px-4">{{ __('messages.th_ticket') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-gray-500-500 py-3.5 px-4">{{ __('messages.payment_date') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-gray-500-500 py-3.5 px-4">{{ __('messages.method_label') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-gray-500-500 py-3.5 px-4">{{ __('messages.th_total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data ?? [] as $t)
                    <tr class="border-b border-gray-300-100 hover:bg-stone-50 transition-colors">
                        <td class="py-3.5 px-4 font-mono text-gray-500-700">{{ $t->pemesanan->kode_booking ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-gray-500-600">{{ $t->pemesanan->user->name ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-gray-500-600">
                            @php
                                $tSum = $t->pemesanan->detailPemesanan->groupBy('nama_tiket')->map(fn($g) => $g->count() . ' ' . $g->first()->nama_tiket)->implode(', ');
                            @endphp
                            {{ $tSum ?: ($t->pemesanan->tiket->nama_tiket ?? '-') }}
                        </td>
                        <td class="py-3.5 px-4 text-gray-500-500 text-xs">{{ $t->created_at ? \Carbon\Carbon::parse($t->created_at)->format('d/m/Y') : '-' }}</td>
                        <td class="py-3.5 px-4 text-gray-500-700">{{ $t->metode ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-gray-500-700 font-medium">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-10 text-center text-gray-500-400">{{ __('messages.no_data_transaction') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('pengelola.laporan.index') }}" class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-800 text-sm font-medium transition-colors">
        <span class="material-symbols-outlined text-base">arrow_back</span>
        {{ __('messages.back_to_reports') }}
    </a>
</div>
@endsection
