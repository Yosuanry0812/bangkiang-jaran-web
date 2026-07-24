<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index()
    {
        return view('pengelola.laporan.index');
    }

    public function kunjungan(Request $request)
    {
        $request->validate([
            'periode_awal'  => ['required', 'date'],
            'periode_akhir' => ['required', 'date', 'after_or_equal:periode_awal'],
        ]);

        $periode_awal = $request->periode_awal;
        $periode_akhir = $request->periode_akhir;

        $data = Pemesanan::with(['user', 'tiket', 'pembayaran', 'detailPemesanan'])
            ->whereBetween('tgl_kunjungan', [$periode_awal, $periode_akhir])
            ->orderBy('tgl_kunjungan')
            ->get();

        $total = $data->sum('jumlah');

        if ($request->has('export')) {
            $pdf = Pdf::loadView('pengelola.laporan.pdf-kunjungan', [
                'data' => $data,
                'total' => $total,
                'periode_awal' => $request->periode_awal,
                'periode_akhir' => $request->periode_akhir,
            ]);
            return $pdf->download('laporan-kunjungan-' . $request->periode_awal . '-sampai-' . $request->periode_akhir . '.pdf');
        }

        return view('pengelola.laporan.kunjungan', compact('data', 'total', 'periode_awal', 'periode_akhir'));
    }

    public function transaksi(Request $request)
    {
        $request->validate([
            'periode_awal'  => ['required', 'date'],
            'periode_akhir' => ['required', 'date', 'after_or_equal:periode_awal'],
        ]);

        $periode_awal = $request->periode_awal;
        $periode_akhir = $request->periode_akhir;

        $data = Pembayaran::with('pemesanan.user', 'pemesanan.tiket', 'pemesanan.detailPemesanan')
            ->where('status', 'valid')
            ->whereBetween('tgl_bayar', [$periode_awal, $periode_akhir])
            ->orderBy('tgl_bayar')
            ->get();

        $total = $data->sum('total');

        if ($request->has('export')) {
            $pdf = Pdf::loadView('pengelola.laporan.pdf-transaksi', [
                'data' => $data,
                'total' => $total,
                'periode_awal' => $request->periode_awal,
                'periode_akhir' => $request->periode_akhir,
            ]);
            return $pdf->download('laporan-transaksi-' . $request->periode_awal . '-sampai-' . $request->periode_akhir . '.pdf');
        }

        return view('pengelola.laporan.transaksi', compact('data', 'total', 'periode_awal', 'periode_akhir'));
    }
}
