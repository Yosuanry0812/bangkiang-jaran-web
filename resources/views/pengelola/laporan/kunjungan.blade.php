@extends('pengelola.layouts.admin')
@section('title', __('messages.visit_report_title'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-stone-800">{{ __('messages.visit_report_title') }}</h1>
            <p class="text-sm text-stone-500 mt-0.5">
                {{ __('messages.period') }} {{ $periode_awal ? \Carbon\Carbon::parse($periode_awal)->format('d/m/Y') : __('messages.all') }}
                -
                {{ $periode_akhir ? \Carbon\Carbon::parse($periode_akhir)->format('d/m/Y') : __('messages.all') }}
            </p>
        </div>
        <a href="{{ route('pengelola.laporan.kunjungan', array_merge(request()->all(), ['export' => 'pdf'])) }}"
            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
            <span class="material-symbols-outlined text-base">picture_as_pdf</span>
            Export PDF
        </a>
    </div>

    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
            <span class="material-symbols-outlined text-2xl">groups</span>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">{{ __('messages.total_visitors_label') }}</p>
            <p class="text-2xl font-bold text-emerald-900">{{ $total ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-200 bg-stone-50 text-left">
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_booking_code') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_user') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_ticket') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_date') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.quantity_label') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_total') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data ?? [] as $k)
                    <tr class="border-b border-stone-100 hover:bg-stone-50 transition-colors">
                        <td class="py-3.5 px-4 font-mono text-stone-700">{{ $k->kode_booking }}</td>
                        <td class="py-3.5 px-4 text-stone-600">{{ $k->user->name ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-stone-600">{{ $k->tiket->nama_tiket ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-stone-500 text-xs">{{ $k->tgl_kunjungan ? \Carbon\Carbon::parse($k->tgl_kunjungan)->format('d/m/Y') : '-' }}</td>
                        <td class="py-3.5 px-4 text-stone-700">{{ $k->jumlah }}</td>
                        <td class="py-3.5 px-4 text-stone-700 font-medium">Rp {{ number_format($k->total_harga, 0, ',', '.') }}</td>
                        <td class="py-3.5 px-4">
                        @php
                            $sc = match($k->status) {
                                'pending' => 'bg-amber-100 text-amber-700',
                                'diproses' => 'bg-blue-100 text-blue-700',
                                'selesai' => 'bg-emerald-100 text-emerald-700',
                                'dibatalkan' => 'bg-red-100 text-red-700',
                                default => 'bg-stone-100 text-stone-600'
                            };
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $sc }}">{{ ucfirst($k->status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-10 text-center text-stone-400">{{ __('messages.no_data_visit') }}</td></tr>
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