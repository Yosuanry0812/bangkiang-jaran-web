<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\DetailPemesanan;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $pengunjungHariIni = Pemesanan::where('tgl_kunjungan', $today)
            ->approved()->sum('jumlah');

        $pendapatanBulanIni = Pembayaran::where('status', 'valid')->whereHas('pemesanan', fn ($q) => $q->where('status', 'selesai'))
            ->whereMonth('tgl_bayar', now()->month)
            ->whereYear('tgl_bayar', now()->year)
            ->sum('total');

        $pemesananPending = Pemesanan::whereHas('pembayaran', function ($q) {
            $q->where('status', 'pending');
        })->count();

        $totalWisatawan = User::where('role', 'wisatawan')->count();

        // Grafik kunjungan — komposisi per tiket (pie)
        $grafik = DetailPemesanan::whereHas('pemesanan', fn ($q) => $q->approved())
            ->select('nama_tiket', DB::raw('COUNT(*) as total'))->groupBy('nama_tiket')->get();
        $grafikLabels = $grafik->pluck('nama_tiket')->all();
        $grafikData = $grafik->pluck('total')->map(fn ($n) => (int) $n)->all();
        $arrivals = DetailPemesanan::whereHas('pemesanan', fn ($q) => $q->approved());
        $scanHariIni = (clone $arrivals)->whereDate('check_in_at', $today)->count();
        $keluarHariIni = (clone $arrivals)->whereDate('check_out_at', $today)->count();
        $diLokasi = (clone $arrivals)->whereNotNull('check_in_at')->whereNull('check_out_at')->count();
        $terpesanHariIni = Pemesanan::where('tgl_kunjungan', $today)->holdingCapacity()->sum('jumlah');

        $pemesananTerbaru = Pemesanan::with(['user', 'tiket', 'detailPemesanan'])
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
            'pemesananTerbaru',
            'scanHariIni', 'keluarHariIni', 'diLokasi', 'terpesanHariIni'
        ));
    }
}
