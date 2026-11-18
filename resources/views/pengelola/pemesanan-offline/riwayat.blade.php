@extends('pengelola.layouts.admin')

@section('title', __('ui.t_offline_history'))
@section('page_title', __('ui.t_offline_history_short'))

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <p class="font-sans text-[13px] text-slate-400">{{ __('panel.semua_pemesanan_offline_yang_ea6c') }}</p>
    </div>
    <a href="{{ route('pengelola.pemesanan-offline.create') }}"
       class="inline-flex items-center gap-1.5 bg-slate-900 text-white font-sans text-[12px] font-semibold px-4 py-2.5 rounded-xl hover:bg-slate-800 active:scale-[.98] transition-all self-start">
        <span class="material-symbols-outlined" style="font-size:14px">add</span>
        {{ __('panel.buat_baru_a3e6') }}
    </a>
</div>

<div class="bg-white border border-slate-100/80 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px]">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="text-left px-5 py-3.5 font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400 bg-slate-50/50">{{ __('panel.kode_booking_6179') }}</th>
                    <th class="text-left px-4 py-3.5 font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400 bg-slate-50/50">{{ __('panel.tanggal_6b26') }}</th>
                    <th class="text-left px-4 py-3.5 font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400 bg-slate-50/50 hidden sm:table-cell">{{ __('panel.tiket_f17c') }}</th>
                    <th class="text-left px-4 py-3.5 font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400 bg-slate-50/50">{{ __('panel.jumlah_972d') }}</th>
                    <th class="text-left px-4 py-3.5 font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400 bg-slate-50/50">{{ __('panel.total_96b0') }}</th>
                    <th class="text-left px-4 py-3.5 font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400 bg-slate-50/50">{{ __('panel.pembayaran_71e2') }}</th>
                    <th class="text-left px-4 py-3.5 font-sans text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400 bg-slate-50/50">{{ __('panel.aksi_ae3b') }}</th>
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
                            {{ \App\Support\Label::method($p->pembayaran->metode ?? null) ?: __('panel.lunas_3928') }}
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
                            {{ __('panel.detail_a254') }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-14 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-3">
                            <span class="material-symbols-outlined text-slate-300" style="font-size:24px">receipt_long</span>
                        </div>
                        <p class="font-sans text-[13px] font-medium text-slate-500 mb-1">{{ __('panel.belum_ada_pemesanan_offline_8147') }}</p>
                        <p class="font-sans text-[11px] text-slate-400">{{ __('panel.pemesanan_offline_akan_tampi_7295') }}</p>
                        <a href="{{ route('pengelola.pemesanan-offline.create') }}"
                           class="inline-flex items-center gap-1.5 mt-4 font-sans text-[12px] font-semibold text-slate-900 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl transition-colors">
                            <span class="material-symbols-outlined" style="font-size:14px">add</span>
                            {{ __('panel.buat_pemesanan_offline_ac49') }}
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
