<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Tiket — Bangkiang Jaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0,1">
    <style>
        * { font-family: 'Inter', system-ui, sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
        body { background: #F8F7F5; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>
    @if(!$found || !$detail)
    <div class="max-w-sm w-full mx-4 bg-white rounded-3xl shadow-lg overflow-hidden text-center">
        <div class="px-6 py-12">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-red-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-red-500 text-3xl">search_off</span>
            </div>
            <h1 class="font-serif text-2xl text-gray-900 mb-2">Tiket Tidak Ditemukan</h1>
            <p class="text-sm text-gray-500">Kode <strong class="font-mono text-gray-700">{{ $kode }}</strong> tidak terdaftar.</p>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            <p class="text-xs text-gray-400">Bangkiang Jaran Waterfall · Gianyar, Bali</p>
        </div>
    </div>
    @else
    <div class="max-w-sm w-full mx-4">
        {{-- Header --}}
        <div class="text-center mb-4">
            <p class="text-[11px] tracking-[0.2em] uppercase text-gray-400 mb-1">Bangkiang Jaran Waterfall</p>
            <h2 class="font-serif text-xl text-gray-900">Verifikasi Tiket</h2>
        </div>

        {{-- Status badge --}}
        <div class="mb-4">
            @if($valid)
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                </div>
                <div>
                    <p class="font-semibold text-emerald-800 text-sm">Tiket Valid</p>
                    <p class="text-xs text-emerald-600">Tiket siap digunakan</p>
                </div>
            </div>
            @elseif($detail->status_tiket === 'digunakan' || $pemesanan->status === 'selesai')
            <div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-amber-600">info</span>
                </div>
                <div>
                    <p class="font-semibold text-amber-800 text-sm">Tiket {{ $detail->status_tiket === 'digunakan' ? 'Sudah Digunakan' : 'Tidak Aktif' }}</p>
                    <p class="text-xs text-amber-600">{{ $detail->status_tiket === 'digunakan' ? 'Tiket ini sudah dipakai pada ' . ($detail->updated_at ? \Carbon\Carbon::parse($detail->updated_at)->format('d/m/Y H:i') : '-') : 'Status tiket: ' . $detail->status_tiket }}</p>
                </div>
            </div>
            @else
            <div class="bg-red-50 border border-red-200 rounded-2xl px-5 py-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-red-600">block</span>
                </div>
                <div>
                    <p class="font-semibold text-red-800 text-sm">Tiket Tidak Valid</p>
                    <p class="text-xs text-red-600">Pemesanan belum lunas / dibatalkan</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Ticket card --}}
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden">
            {{-- Top accent --}}
            <div class="h-2 bg-gradient-to-r from-emerald-600 to-emerald-400"></div>

            {{-- Hero area --}}
            <div class="relative h-40 bg-gradient-to-br from-emerald-900 via-emerald-700 to-emerald-500">
                <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1555400038-63f5ba517a47?auto=format&fit=crop&w=800&q=80')] bg-cover bg-center opacity-20"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-5">
                    <p class="text-[10px] uppercase tracking-widest text-white/60">E-Ticket</p>
                    <h3 class="font-serif text-xl text-white mt-0.5">Bangkiang Jaran</h3>
                </div>
                <div class="absolute top-3 right-3 bg-white/20 backdrop-blur-md rounded-full px-3 py-1">
                    <span class="text-[10px] font-semibold text-white">{{ $detail->status_tiket === 'aktif' ? 'AKTIF' : strtoupper($detail->status_tiket) }}</span>
                </div>
            </div>

            {{-- Details --}}
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-x-4 gap-y-3">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-400">Kode Tiket</p>
                        <p class="font-mono text-sm font-bold text-emerald-700 tracking-wider mt-0.5">{{ $detail->kode_tiket }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-400">Jenis</p>
                        <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $detail->nama_tiket }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-400">
                            {{ $detail->plat_kendaraan ? 'Plat Kendaraan' : 'Pengunjung' }}
                        </p>
                        <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $detail->plat_kendaraan ?? $detail->nama_pengunjung ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-gray-400">Tanggal</p>
                        <p class="text-sm font-medium text-gray-800 mt-0.5">{{ $pemesanan->tgl_kunjungan ? \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->format('d/m/Y') : '-' }}</p>
                    </div>
                </div>

                <div class="h-px bg-gray-100"></div>

                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-400">Kode Booking</span>
                    <span class="font-mono font-bold text-gray-800">{{ $pemesanan->kode_booking }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-400">Pemesan</span>
                    <span class="font-medium text-gray-800">{{ $pemesanan->user->name ?? '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center mt-6">
            <p class="text-xs text-gray-400">© {{ date('Y') }} Bangkiang Jaran Waterfall · Gianyar, Bali</p>
        </div>
    </div>
    @endif
</body>
</html>
