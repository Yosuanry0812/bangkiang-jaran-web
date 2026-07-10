@extends('pengelola.layouts.admin')
@section('title', 'Dashboard - Panel Pengelola')

@push('styles')
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-stone-800">Dashboard</h1>
            <p class="text-sm text-stone-500 mt-0.5">Overview pengelolaan Bangkiang Jaran</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl shadow-sm p-5 flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-teal-100 flex items-center justify-center text-teal-600 shrink-0">
                <span class="material-symbols-outlined text-2xl">groups</span>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-stone-500">Pengunjung Hari Ini</p>
                <p class="text-2xl font-bold text-stone-800 mt-1">{{ $pengunjungHariIni ?? 0 }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5 flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                <span class="material-symbols-outlined text-2xl">payments</span>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-stone-500">Pendapatan Bulan Ini</p>
                <p class="text-2xl font-bold text-stone-800 mt-1">Rp {{ number_format($pendapatanBulanIni ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5 flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                <span class="material-symbols-outlined text-2xl">hourglass_empty</span>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-stone-500">Pemesanan Pending</p>
                <p class="text-2xl font-bold text-stone-800 mt-1">{{ $pemesananPending ?? 0 }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5 flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                <span class="material-symbols-outlined text-2xl">travel_explore</span>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-stone-500">Total Wisatawan</p>
                <p class="text-2xl font-bold text-stone-800 mt-1">{{ $totalWisatawan ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="font-heading text-lg font-semibold text-stone-800 mb-4">Grafik Kunjungan</h2>
            <div class="max-w-sm mx-auto">
                <canvas id="chartKunjungan" height="260"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="font-heading text-lg font-semibold text-stone-800 mb-4">Pemesanan Terbaru</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-stone-200 text-left">
                            <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3 pr-4">Kode Booking</th>
                            <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3 pr-4">User</th>
                            <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3 pr-4">Tiket</th>
                            <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3 pr-4">Status</th>
                            <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pemesananTerbaru ?? [] as $p)
                        <tr class="border-b border-stone-100 hover:bg-stone-50 transition-colors">
                            <td class="py-3 pr-4 font-mono text-stone-700">{{ $p->kode_booking }}</td>
                            <td class="py-3 pr-4 text-stone-600">{{ $p->user->name ?? '-' }}</td>
                            <td class="py-3 pr-4 text-stone-600">{{ $p->tiket->nama_tiket ?? '-' }}</td>
                            <td class="py-3 pr-4">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'diproses' => 'bg-blue-100 text-blue-700',
                                    'selesai' => 'bg-emerald-100 text-emerald-700',
                                    'dibatalkan' => 'bg-red-100 text-red-700',
                                ];
                                $statusClass = $statusColors[$p->status] ?? 'bg-stone-100 text-stone-600';
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ ucfirst($p->status) }}
                            </span>
                            </td>
                            <td class="py-3 text-stone-500 text-xs">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-8 text-center text-stone-400">Belum ada pemesanan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('chartKunjungan').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($grafikLabels ?? []) !!},
            datasets: [{
                data: {!! json_encode($grafikData ?? []) !!},
                backgroundColor: [
                    'rgba(20, 184, 166, 0.85)',
                    'rgba(16, 185, 129, 0.85)',
                    'rgba(245, 158, 11, 0.85)',
                    'rgba(99, 102, 241, 0.85)',
                    'rgba(236, 72, 153, 0.85)',
                ],
                borderColor: '#fff',
                borderWidth: 2,
                hoverOffset: 12
            }]
        },
        options: {
            responsive: true,
            cutout: '55%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 16, usePointStyle: true, font: { size: 12 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            let total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            let val = ctx.parsed;
                            let pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                            return ' ' + val + ' tiket (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });
</script>
@endpush