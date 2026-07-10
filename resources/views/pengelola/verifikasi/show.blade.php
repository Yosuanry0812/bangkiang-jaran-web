@extends('pengelola.layouts.admin')
@section('title', 'Detail Pemesanan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-stone-800">Detail Pemesanan</h1>
            <p class="text-sm text-stone-500 mt-0.5">Informasi lengkap pemesanan dan pembayaran</p>
        </div>
        <a href="{{ route('pengelola.verifikasi.index') }}" class="bg-stone-200 hover:bg-stone-300 text-stone-700 px-4 py-2 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-emerald-600">person</span>
                <h2 class="font-heading text-lg font-semibold text-stone-800">Informasi User</h2>
            </div>
            <table class="w-full text-sm">
                <tr><td class="py-2.5 text-stone-500 w-1/3">Nama</td><td class="py-2.5 font-medium text-stone-800">{{ $pemesanan->user->name ?? '-' }}</td></tr>
                <tr><td class="py-2.5 text-stone-500">Email</td><td class="py-2.5 text-stone-700">{{ $pemesanan->user->email ?? '-' }}</td></tr>
                <tr><td class="py-2.5 text-stone-500">No. HP</td><td class="py-2.5 text-stone-700">{{ $pemesanan->user->phone ?? '-' }}</td></tr>
            </table>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-emerald-600">confirmation_number</span>
                <h2 class="font-heading text-lg font-semibold text-stone-800">Detail Booking</h2>
            </div>
            <table class="w-full text-sm">
                <tr><td class="py-2.5 text-stone-500 w-1/3">Kode Booking</td><td class="py-2.5 font-mono font-medium text-stone-800">{{ $pemesanan->kode_booking }}</td></tr>
                <tr><td class="py-2.5 text-stone-500">Tiket</td><td class="py-2.5 text-stone-700">{{ $pemesanan->tiket->nama_tiket ?? '-' }}</td></tr>
                <tr><td class="py-2.5 text-stone-500">Jumlah</td><td class="py-2.5 text-stone-700">{{ $pemesanan->jumlah }}</td></tr>
                <tr><td class="py-2.5 text-stone-500">Total</td><td class="py-2.5 font-semibold text-stone-800">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</td></tr>
                <tr><td class="py-2.5 text-stone-500">Tanggal Kunjungan</td><td class="py-2.5 text-stone-700">{{ $pemesanan->tgl_kunjungan ? \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->format('d/m/Y') : '-' }}</td></tr>
                <tr><td class="py-2.5 text-stone-500">Status</td><td class="py-2.5">
                    @php
                        $sc = match($pemesanan->status) {
                            'pending' => 'bg-amber-100 text-amber-700',
                            'diproses' => 'bg-blue-100 text-blue-700',
                            'selesai' => 'bg-emerald-100 text-emerald-700',
                            'dibatalkan' => 'bg-red-100 text-red-700',
                            default => 'bg-stone-100 text-stone-600'
                        };
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $sc }}">{{ ucfirst($pemesanan->status) }}</span>
                </td></tr>
            </table>
        </div>
    </div>

    @if ($pemesanan->pembayaran)
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-emerald-600">payments</span>
            <h2 class="font-heading text-lg font-semibold text-stone-800">Informasi Pembayaran</h2>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <table class="w-full text-sm">
                <tr><td class="py-2.5 text-stone-500 w-1/3">Metode</td><td class="py-2.5 font-medium text-stone-800">{{ $pemesanan->pembayaran->metode ?? '-' }}</td></tr>
                <tr><td class="py-2.5 text-stone-500">Total</td><td class="py-2.5 font-semibold text-stone-800">Rp {{ number_format($pemesanan->pembayaran->total ?? 0, 0, ',', '.') }}</td></tr>
                <tr><td class="py-2.5 text-stone-500">Status</td><td class="py-2.5">
                    @php
                        $pc = match($pemesanan->pembayaran->status) {
                            'valid' => 'bg-emerald-100 text-emerald-700',
                            'pending' => 'bg-amber-100 text-amber-700',
                            'ditolak' => 'bg-red-100 text-red-700',
                            default => 'bg-stone-100 text-stone-600'
                        };
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $pc }}">{{ ucfirst($pemesanan->pembayaran->status) }}</span>
                </td></tr>
            </table>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">Bukti Bayar</p>
                @if ($pemesanan->pembayaran->bukti_bayar)
                    <a href="{{ asset('storage/' . $pemesanan->pembayaran->bukti_bayar) }}" target="_blank">
                        <img src="{{ asset('storage/' . $pemesanan->pembayaran->bukti_bayar) }}" alt="Bukti Bayar"
                            class="max-h-48 rounded-xl border border-stone-200 cursor-pointer hover:opacity-90 transition"
                            onclick="window.open(this.src, '_blank')">
                    </a>
                @else
                    <p class="text-stone-400 italic">Tidak ada bukti bayar</p>
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
                Validasi
            </button>
        </form>
        <form method="POST" action="{{ route('pengelola.verifikasi.validasi', $pemesanan->id_pemesanan) }}" onsubmit="return confirm('Yakin ingin menolak pembayaran ini?')">
            @csrf
            <input type="hidden" name="action" value="tolak">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-base">cancel</span>
                Tolak
            </button>
        </form>
    </div>
    @endif
    @else
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-stone-400">info</span>
            <p class="text-stone-400 italic">Belum ada pembayaran</p>
        </div>
    </div>
    @endif
</div>
@endsection