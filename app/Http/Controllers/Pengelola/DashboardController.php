<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $pengunjungHariIni = Pemesanan::where('tgl_kunjungan', $today)
            ->whereIn('status', ['diproses', 'selesai'])
            ->sum('jumlah');

        $pendapatanBulanIni = Pembayaran::where('status', 'valid')
            ->whereMonth('tgl_bayar', now()->month)
            ->whereYear('tgl_bayar', now()->year)
            ->sum('total');

        $pemesananPending = Pemesanan::where('status', 'diproses')->count();

        $totalWisatawan = User::where('role', 'wisatawan')->count();

        // Grafik kunjungan — komposisi per tiket (pie)
        $grafik = Pemesanan::select(
                'id_tiket',
                DB::raw('SUM(jumlah) as total')
            )
            ->whereIn('status', ['diproses', 'selesai'])
            ->groupBy('id_tiket')
            ->with('tiket:id_tiket,nama_tiket')
            ->get();

        $grafikLabels = $grafik->pluck('tiket.nama_tiket')->toArray();
        $grafikData = $grafik->pluck('total')->map(fn($v) => (int)$v)->toArray();

        $pemesananTerbaru = Pemesanan::with(['user', 'tiket'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('pengelola.dashboard', compact(
            'pengunjungHariIni',
            'pendapatanBulanIni',
            'pemesananPending',
            'totalWisatawan',
            'grafikLabels',
            'grafikData',
            'pemesananTerbaru'
        ));
    }
}
