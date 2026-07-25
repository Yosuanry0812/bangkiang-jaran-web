@extends('pengelola.layouts.admin')

@section('title', 'Riwayat Pemesanan Offline — Bangkiang Jaran')
@section('page_title', 'Riwayat Offline')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <p class="font-sans text-[13px] text-slate-400">Semua pemesanan offline yang pernah dibuat</p>
    </div>
    <a href="{{ route('pengelola.pemesanan-offline.create') }}"
       class="inline-flex items-center gap-1.5 bg-slate-900 text-white font-sans text-[12px] font-semibold px-4 py-2.5 rounded-xl hover:bg-slate-800 active:scale-[.98] transition-all self-start">
        <span class="material-symbols-outlined" style="font-size:14px">add</span>
        Buat Baru
    </a>
</div>

<div class="bg-white border border-slate-100/80 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px]">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="text-left px-5 py-3.5 font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400 bg-slate-50/50">Kode Booking</th>
                    <th class="text-left px-4 py-3.5 font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400 bg-slate-50/50">Tanggal</th>
                    <th class="text-left px-4 py-3.5 font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400 bg-slate-50/50 hidden sm:table-cell">Tiket</th>
                    <th class="text-left px-4 py-3.5 font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400 bg-slate-50/50">Jumlah</th>
                    <th class="text-left px-4 py-3.5 font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400 bg-slate-50/50">Total</th>
                    <th class="text-left px-4 py-3.5 font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400 bg-slate-50/50">Pembayaran</th>
                    <th class="text-left px-4 py-3.5 font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400 bg-slate-50/50">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($pemesanan as $p)
                <tr class="tbl-row group hover:bg-slate-50/50 transition-colors">
                    <td class="px-5 py-4">
                        <span class="font-mono text-[12px] font-semibold text-slate-700">{{ $p->kode_booking }}</span>
                    </td>
                    <td class="px-4 py-4">
                        <span class="font-sans text-[12px] text-slate-600">{{ \Carbon\Carbon::parse($p->tgl_kunjungan)->format('d M Y') }}</span>
                    </td>
                    <td class="px-4 py-4 hidden sm:table-cell">
                        <span class="font-sans text-[11px] text-slate-500">
                            @php
                                $dSum = $p->detailPemesanan->groupBy('nama_tiket')->map(fn($g) => $g->count() . '× ' . $g->first()->nama_tiket)->implode(', ');
                            @endphp
                            {{ $dSum ?: ($p->tiket->nama_tiket ?? '—') }}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <span class="font-sans text-[12px] font-medium text-slate-700">{{ $p->jumlah }}</span>
                    </td>
                    <td class="px-4 py-4">
                        <span class="font-sans text-[12px] font-semibold text-slate-800">Rp{{ number_format($p->total_harga, 0, ',', '.') }}</span>
                    </td>
                    <td class="px-4 py-4">
                        @if($p->pembayaran && $p->pembayaran->status === 'valid')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border border-emerald-200 bg-emerald-50 font-sans text-[10px] font-semibold text-emerald-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ $p->pembayaran->metode ?? 'Lunas' }}
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border border-amber-200 bg-amber-50 font-sans text-[10px] font-semibold text-amber-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                            {{ $p->pembayaran->status ?? 'Pending' }}
                        </span>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        <a href="{{ route('pengelola.pemesanan-offline.sukses', $p->id_pemesanan) }}"
                           class="inline-flex items-center gap-1 font-sans text-[11px] font-semibold text-slate-500 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-100 px-3 py-1.5 rounded-lg transition-all">
                            <span class="material-symbols-outlined" style="font-size:12px">visibility</span>
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-14 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-3">
                            <span class="material-symbols-outlined text-slate-300" style="font-size:24px">receipt_long</span>
                        </div>
                        <p class="font-sans text-[13px] font-medium text-slate-500 mb-1">Belum ada pemesanan offline</p>
                        <p class="font-sans text-[11px] text-slate-400">Pemesanan offline akan tampil di sini</p>
                        <a href="{{ route('pengelola.pemesanan-offline.create') }}"
                           class="inline-flex items-center gap-1.5 mt-4 font-sans text-[12px] font-semibold text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl transition-colors">
                            <span class="material-symbols-outlined" style="font-size:14px">add</span>
                            Buat Pemesanan Offline
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($pemesanan, 'links'))
    <div class="px-5 py-4 border-t border-slate-100">
        {{ $pemesanan->links() }}
    </div>
    @endif
</div>
@endsection
