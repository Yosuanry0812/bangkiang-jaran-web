@extends('pengelola.layouts.admin')

@section('title', 'Data Pengunjung — Pemesanan Offline')
@section('page_title', 'Data Pengunjung')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Steps --}}
    <div class="flex items-center gap-3 mb-6 font-sans text-sm">
        <div class="flex items-center gap-1.5">
            <div class="w-7 h-7 rounded-full bg-slate-900 text-white flex items-center justify-center text-[11px] font-bold">✓</div>
            <span class="text-slate-900 font-medium hidden sm:inline text-[12px]">Pilih Tiket</span>
        </div>
        <div class="w-8 h-px bg-slate-900"></div>
        <div class="flex items-center gap-1.5">
            <div class="w-7 h-7 rounded-full bg-slate-900 text-white flex items-center justify-center text-[11px] font-bold">2</div>
            <span class="text-slate-900 font-medium hidden sm:inline text-[12px]">Data Pengunjung</span>
        </div>
        <div class="w-8 h-px bg-slate-200"></div>
        <div class="flex items-center gap-1.5">
            <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-[11px] font-bold">3</div>
            <span class="text-slate-400 hidden sm:inline text-[12px]">Selesai</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- LEFT: Form --}}
        <div class="lg:col-span-7">
            <form method="POST" action="{{ route('pengelola.pemesanan-offline.store-data-diri') }}">
                @csrf

                <div class="bg-white border border-slate-100/80 rounded-2xl overflow-hidden">
                    <div class="bg-slate-900 px-5 py-4">
                        <h2 class="font-serif text-lg text-white">Lengkapi Data Pengunjung</h2>
                        <p class="font-sans text-[11px] text-white/50 mt-0.5">Isikan nama & jenis kelamin setiap pengunjung</p>
                    </div>

                    <div class="p-5 space-y-5">
                        @php $currentGroup = null; @endphp

                        @foreach($flow['detailRows'] as $i => $row)
                            @php
                                $groupKey = $row['nama_tiket'] . '_' . $row['id_tiket'];
                            @endphp

                            @if($currentGroup !== $groupKey)
                                @php $currentGroup = $groupKey; @endphp
                                @if($i > 0)
                                    <hr class="border-slate-100">
                                @endif
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                        <span class="material-symbols-outlined text-slate-600" style="font-size:15px">person</span>
                                    </div>
                                    <div>
                                        <p class="font-sans text-[13px] font-semibold text-slate-800">{{ $row['nama_tiket'] }}</p>
                                        <p class="font-sans text-[11px] text-slate-400">Masukkan data pengunjung</p>
                                    </div>
                                </div>
                            @endif

                            <div class="ml-11 space-y-3">
                                <div>
                                    <label class="block font-sans text-[11px] font-medium text-slate-500 mb-1">
                                        Nama Lengkap <span class="text-red-400">*</span>
                                    </label>
                                    <input type="text"
                                           name="nama_{{ $i }}"
                                           value="{{ old('nama_' . $i) }}"
                                           placeholder="Nama pengunjung"
                                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400 transition-all bg-white">
                                    @error('nama_' . $i)
                                        <p class="font-sans text-[11px] text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block font-sans text-[11px] font-medium text-slate-500 mb-1">
                                        Jenis Kelamin <span class="text-red-400">*</span>
                                    </label>
                                    <select name="gender_{{ $i }}"
                                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400 transition-all bg-white">
                                        <option value="" disabled {{ old('gender_' . $i) ? '' : 'selected' }}>Pilih jenis kelamin</option>
                                        <option value="L" {{ old('gender_' . $i) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('gender_' . $i) === 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('gender_' . $i)
                                        <p class="font-sans text-[11px] text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Payment method --}}
                <div class="bg-white border border-slate-100/80 rounded-2xl overflow-hidden mt-5">
                    <div class="px-5 py-4 border-b border-slate-100">
                        <h3 class="font-sans text-[13px] font-semibold text-slate-800">Metode Pembayaran</h3>
                    </div>
                    <div class="p-5">
                        <label class="block font-sans text-[11px] font-medium text-slate-500 mb-2">
                            Pilih Metode Bayar <span class="text-red-400">*</span>
                        </label>
                        <select name="metode_bayar"
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400 transition-all bg-white">
                            <option value="" disabled {{ old('metode_bayar') ? '' : 'selected' }}>Pilih metode</option>
                            <option value="Tunai" {{ old('metode_bayar') === 'Tunai' ? 'selected' : '' }}>Tunai</option>
                            <option value="QRIS" {{ old('metode_bayar') === 'QRIS' ? 'selected' : '' }}>QRIS</option>
                            <option value="BCA" {{ old('metode_bayar') === 'BCA' ? 'selected' : '' }}>Transfer BCA</option>
                            <option value="BRI" {{ old('metode_bayar') === 'BRI' ? 'selected' : '' }}>Transfer BRI</option>
                            <option value="Mandiri" {{ old('metode_bayar') === 'Mandiri' ? 'selected' : '' }}>Transfer Mandiri</option>
                            <option value="Dana" {{ old('metode_bayar') === 'Dana' ? 'selected' : '' }}>Dana</option>
                            <option value="OVO" {{ old('metode_bayar') === 'OVO' ? 'selected' : '' }}>OVO</option>
                            <option value="GoPay" {{ old('metode_bayar') === 'GoPay' ? 'selected' : '' }}>GoPay</option>
                        </select>
                        @error('metode_bayar')
                            <p class="font-sans text-[11px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                        <p class="font-sans text-[11px] text-slate-400 mt-1.5">Pembayaran akan dicatat otomatis sebagai Lunas</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between gap-3 mt-5">
                    <a href="{{ route('pengelola.pemesanan-offline.create') }}"
                       class="inline-flex items-center gap-1.5 font-sans text-[12px] text-slate-500 hover:text-slate-800 border border-slate-200 hover:border-slate-300 px-5 py-2.5 rounded-xl transition-colors bg-white">
                        <span class="material-symbols-outlined" style="font-size:14px">arrow_back</span>
                        Kembali
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-slate-900 text-white font-sans text-[12px] font-semibold px-6 py-2.5 rounded-xl hover:bg-slate-800 active:scale-[.98] transition-all">
                        Konfirmasi & Cetak Tiket
                        <span class="material-symbols-outlined" style="font-size:14px">check</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- RIGHT: Summary --}}
        <div class="lg:col-span-5">
            <div class="sticky top-24 space-y-4">
                <div class="bg-white border border-slate-100/80 rounded-2xl overflow-hidden">
                    <div class="bg-slate-900 px-5 py-4">
                        <p class="font-sans text-[9px] uppercase tracking-[.15em] text-white/40 mb-0.5">Total</p>
                        <p class="font-serif text-[24px] text-white">
                            Rp{{ number_format($flow['total_harga'], 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="px-5 py-4 space-y-3">
                        <p class="font-sans text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-3">Ringkasan Tiket</p>
                        @foreach($flow['detail'] as $d)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="font-sans text-[12px] text-slate-700">{{ $d['nama'] }}</span>
                                <span class="font-sans text-[10px] text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded-full">×{{ $d['jumlah'] }}</span>
                            </div>
                            <span class="font-sans text-[12px] text-slate-600 tabular-nums">
                                Rp{{ number_format($d['subtotal'], 0, ',', '.') }}
                            </span>
                        </div>
                        @endforeach
                        <div class="h-px bg-slate-100"></div>
                        <div class="flex items-center justify-between">
                            <span class="font-sans text-[11px] text-slate-400">Tanggal Kunjungan</span>
                            <span class="font-sans text-[12px] font-medium text-slate-700">
                                {{ \Carbon\Carbon::parse($flow['tgl_kunjungan'])->isoFormat('D MMMM YYYY') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-sans text-[11px] text-slate-400">Kode Booking</span>
                            <span class="font-sans text-[12px] font-semibold text-slate-900 font-mono">{{ $flow['kode_booking'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-sans text-[11px] text-slate-400">Pengelola</span>
                            <span class="font-sans text-[12px] text-slate-700">{{ Auth::user()->name }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-emerald-50/60 border border-emerald-200/50 rounded-xl px-4 py-3.5 flex items-start gap-2.5">
                    <span class="material-symbols-outlined text-emerald-600" style="font-size:15px; flex-shrink:0; margin-top:1px;">info</span>
                    <p class="font-sans text-[11px] text-slate-600 leading-relaxed">
                        Tiket akan langsung aktif setelah dikonfirmasi. Cetak atau screenshot QR code untuk diberikan ke wisatawan.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
