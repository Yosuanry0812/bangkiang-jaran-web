@extends('pengelola.layouts.admin')
@section('title', __('messages.verification_title'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-stone-800">{{ __('messages.verification_title') }}</h1>
            <p class="text-sm text-stone-500 mt-0.5">{{ __('messages.verification_desc') }}</p>
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
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_total') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_status') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_payment_status') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pemesanan ?? [] as $p)
                    <tr class="border-b border-stone-100 hover:bg-stone-50 transition-colors">
                        <td class="py-3.5 px-4 font-mono text-stone-700">{{ $p->kode_booking }}</td>
                        <td class="py-3.5 px-4 text-stone-600">{{ $p->user->name ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-stone-600">{{ $p->tiket->nama_tiket ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-stone-500 text-xs">{{ $p->tgl_kunjungan ? \Carbon\Carbon::parse($p->tgl_kunjungan)->format('d/m/Y') : '-' }}</td>
                        <td class="py-3.5 px-4 text-stone-700 font-medium">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        <td class="py-3.5 px-4">
                        @php
                            $sc = match($p->status) {
                                'pending' => 'bg-amber-100 text-amber-700',
                                'diproses' => 'bg-blue-100 text-blue-700',
                                'selesai' => 'bg-emerald-100 text-emerald-700',
                                'dibatalkan' => 'bg-red-100 text-red-700',
                                default => 'bg-stone-100 text-stone-600'
                            };
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $sc }}">{{ ucfirst($p->status) }}</span>
                        </td>
                        <td class="py-3.5 px-4">
                        @php
                            $pc = match($p->pembayaran->status ?? null) {
                                'valid' => 'bg-emerald-100 text-emerald-700',
                                'pending' => 'bg-amber-100 text-amber-700',
                                'ditolak' => 'bg-red-100 text-red-700',
                                default => 'bg-stone-100 text-stone-600'
                            };
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $pc }}">
                            {{ ucfirst($p->pembayaran->status ?? __('messages.pending')) }}
                        </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <a href="{{ route('pengelola.verifikasi.show', $p->id_pemesanan) }}" class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-800 text-sm font-medium transition-colors">
                                <span class="material-symbols-outlined text-base">visibility</span>
                                {{ __('messages.detail') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="py-10 text-center text-stone-400">{{ __('messages.no_verification_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (method_exists($pemesanan, 'links'))
        <div class="p-4 border-t border-stone-200">
            {{ $pemesanan->links() }}
        </div>
        @endif
    </div>
</div>
@endsection