@extends('pengelola.layouts.admin')
@section('title', __('messages.detail_title'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-gray-500-800">{{ __('messages.detail_title') }}</h1>
            <p class="text-sm text-gray-500-500 mt-0.5">{{ __('messages.detail_desc') }}</p>
        </div>
        <a href="{{ route('pengelola.verifikasi.index') }}" class="bg-stone-200 hover:bg-stone-300 text-gray-500-700 px-4 py-2 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            {{ __('messages.back') }}
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-emerald-600">person</span>
                <h2 class="font-heading text-lg font-semibold text-gray-500-800">{{ __('messages.user_info') }}</h2>
            </div>
            <table class="w-full text-sm">
                <tr><td class="py-2.5 text-gray-500-500 w-1/3">{{ __('messages.th_name') }}</td><td class="py-2.5 font-medium text-gray-500-800">{{ $pemesanan->user->name ?? '-' }}</td></tr>
                <tr><td class="py-2.5 text-gray-500-500">{{ __('messages.th_email') }}</td><td class="py-2.5 text-gray-500-700">{{ $pemesanan->user->email ?? '-' }}</td></tr>
                <tr><td class="py-2.5 text-gray-500-500">{{ __('messages.th_phone') }}</td><td class="py-2.5 text-gray-500-700">{{ $pemesanan->user->phone ?? '-' }}</td></tr>
            </table>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-emerald-600">confirmation_number</span>
                <h2 class="font-heading text-lg font-semibold text-gray-500-800">{{ __('messages.booking_info') }}</h2>
            </div>
            <table class="w-full text-sm">
                <tr><td class="py-2.5 text-gray-500-500 w-1/3">{{ __('messages.th_booking_code') }}</td><td class="py-2.5 font-mono font-medium text-gray-500-800">{{ $pemesanan->kode_booking }}</td></tr>
                <tr><td class="py-2.5 text-gray-500-500">{{ __('messages.th_ticket') }}</td><td class="py-2.5 text-gray-500-700">@php $tSum = $pemesanan->detailPemesanan->groupBy('nama_tiket')->map(fn($g) => $g->count() . ' ' . $g->first()->nama_tiket)->implode(', '); @endphp {{ $tSum ?: ($pemesanan->tiket->nama_tiket ?? '-') }}</td></tr>
                <tr><td class="py-2.5 text-gray-500-500">{{ __('messages.quantity_label') }}</td><td class="py-2.5 text-gray-500-700">{{ $pemesanan->jumlah }}</td></tr>
                <tr><td class="py-2.5 text-gray-500-500">{{ __('messages.th_total') }}</td><td class="py-2.5 font-semibold text-gray-500-800">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</td></tr>
                <tr><td class="py-2.5 text-gray-500-500">{{ __('messages.visit_date_label') }}</td><td class="py-2.5 text-gray-500-700">{{ $pemesanan->tgl_kunjungan ? \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->format('d/m/Y') : '-' }}</td></tr>
                <tr><td class="py-2.5 text-gray-500-500">{{ __('messages.th_status') }}</td><td class="py-2.5">
                    @php
                        $sc = match($pemesanan->status) {
                            'pending' => 'bg-amber-100 text-amber-700',
                            'selesai' => 'bg-emerald-100 text-emerald-700',
                            'dibatalkan' => 'bg-red-100 text-red-700',
                            default => 'bg-stone-100 text-gray-500-600'
                        };
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $sc }}">{{ ucfirst($pemesanan->status) }}</span>
                </td></tr>
            </table>
        </div>
    </div>

    {{-- Ticket Codes --}}
    @php $allItems = $pemesanan->detailPemesanan()->get(); @endphp
    @if($allItems->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-emerald-600">confirmation_number</span>
            <h2 class="font-heading text-lg font-semibold text-gray-500-800">Kode Tiket Individu</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left">
                        <th class="py-2 pr-4 text-xs font-semibold uppercase tracking-wider text-gray-500">No</th>
                        <th class="py-2 pr-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Jenis</th>
                        <th class="py-2 pr-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Pengunjung</th>
                        <th class="py-2 pr-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Jenis Kelamin</th>
                        <th class="py-2 text-xs font-semibold uppercase tracking-wider text-gray-500">Kode Tiket</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allItems as $idx => $item)
                    <tr class="border-b border-gray-100">
                        <td class="py-2.5 pr-4 text-gray-500">{{ $idx + 1 }}</td>
                        <td class="py-2.5 pr-4 text-gray-700">{{ $item->nama_tiket }}</td>
                        <td class="py-2.5 pr-4 text-gray-700">{{ $item->nama_pengunjung ?? '-' }}</td>
                        <td class="py-2.5 pr-4 text-gray-700">
                            {{ $item->jenis_kelamin == 'L' ? 'Laki-laki' : ($item->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                        </td>
                        <td class="py-2.5 font-mono font-bold text-gray-800 tracking-wider">{{ $item->kode_tiket }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if ($pemesanan->pembayaran)
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-emerald-600">payments</span>
            <h2 class="font-heading text-lg font-semibold text-gray-500-800">{{ __('messages.payment_info') }}</h2>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <table class="w-full text-sm">
                <tr><td class="py-2.5 text-gray-500-500 w-1/3">{{ __('messages.method') }}</td><td class="py-2.5 font-medium text-gray-500-800">{{ $pemesanan->pembayaran->metode ?? '-' }}</td></tr>
                <tr><td class="py-2.5 text-gray-500-500">{{ __('messages.th_total') }}</td><td class="py-2.5 font-semibold text-gray-500-800">Rp {{ number_format($pemesanan->pembayaran->total ?? 0, 0, ',', '.') }}</td></tr>
                <tr><td class="py-2.5 text-gray-500-500">{{ __('messages.th_status') }}</td><td class="py-2.5">
                    @php
                        $pc = match($pemesanan->pembayaran->status) {
                            'valid' => 'bg-emerald-100 text-emerald-700',
                            'pending' => 'bg-amber-100 text-amber-700',
                            'ditolak' => 'bg-red-100 text-red-700',
                            default => 'bg-stone-100 text-gray-500-600'
                        };
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $pc }}">{{ ucfirst($pemesanan->pembayaran->status) }}</span>
                </td></tr>
            </table>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500-500 mb-2">{{ __('messages.payment_proof') }}</p>
                @if ($pemesanan->pembayaran->bukti_bayar)
                    <a href="{{ asset('storage/' . $pemesanan->pembayaran->bukti_bayar) }}" target="_blank">
                        <img src="{{ asset('storage/' . $pemesanan->pembayaran->bukti_bayar) }}" alt="{{ __('messages.payment_proof') }}"
                            class="max-h-48 rounded-xl border border-gray-300-200 cursor-pointer hover:opacity-90 transition"
                            onclick="window.open(this.src, '_blank')">
                    </a>
                @else
                    <p class="text-gray-500-400 italic">{{ __('messages.no_payment_proof') }}</p>
                @endif
            </div>
        </div>
    </div>

    @if ($pemesanan->pembayaran->status == 'pending')
    <div class="flex gap-3">
        <form method="POST" action="{{ route('pengelola.verifikasi.validasi', $pemesanan->id_pemesanan) }}">
            @csrf
            <input type="hidden" name="action" value="valid">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-base">check_circle</span>
                {{ __('messages.validate') }}
            </button>
        </form>
        <form method="POST" action="{{ route('pengelola.verifikasi.validasi', $pemesanan->id_pemesanan) }}" onsubmit="return confirm('{{ __('messages.reject_confirm') }}')">
            @csrf
            <input type="hidden" name="action" value="ditolak">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-base">cancel</span>
                {{ __('messages.reject') }}
            </button>
        </form>
    </div>
    @endif
    @else
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-gray-500-400">info</span>
            <p class="text-gray-500-400 italic">{{ __('messages.no_payment_yet') }}</p>
        </div>
    </div>
    @endif
</div>
@endsection
