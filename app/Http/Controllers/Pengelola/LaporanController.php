<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\DetailPemesanan;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Services\CsvExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        return view('pengelola.laporan.index');
    }

    private function filters(Request $request): array
    {
        return $request->validate([
            'periode_awal' => ['required', 'date_format:Y-m-d'],
            'periode_akhir' => ['required', 'date_format:Y-m-d', 'after_or_equal:periode_awal'],
            'tipe' => ['nullable', 'in:online,offline'], 'export' => ['nullable', 'in:pdf,csv'],
        ]);
    }

    public function kunjungan(Request $request, CsvExport $csv)
    {
        $filters = $this->filters($request);
        $periode_awal = $filters['periode_awal'];
        $periode_akhir = $filters['periode_akhir'];
        $tipe = $filters['tipe'] ?? null;
        $query = Pemesanan::with(['user', 'tiket', 'pembayaran', 'detailPemesanan'])->approved()
            ->whereBetween('tgl_kunjungan', [$periode_awal, $periode_akhir])->filter(['channel' => $tipe]);
        $terbayar = (int) (clone $query)->sum('jumlah');
        $details = DetailPemesanan::whereHas('pemesanan', fn ($q) => $q->approved()->whereBetween('tgl_kunjungan', [$periode_awal, $periode_akhir])->filter(['channel' => $tipe]));
        $total = (clone $details)->whereNotNull('check_in_at')->count();
        $keluar = (clone $details)->whereNotNull('check_out_at')->count();
        if ($request->input('export') === 'csv') {
            $rows = (function () use ($query) {
                foreach ($query->orderBy('id_pemesanan')->lazyById(200, 'id_pemesanan') as $b) {
                    foreach ($b->detailPemesanan as $d) {
                        yield [$b->kode_booking, str_starts_with($b->kode_booking, 'BJ-OFF-') ? 'offline' : 'online',
                            $d->kode_tiket, $d->nama_tiket, $d->nama_pengunjung, $b->tgl_kunjungan, $d->harga,
                            $d->check_in_at, $d->check_out_at];
                    }
                }
            })();

            return $csv->download('kunjungan-'.$periode_awal.'-'.$periode_akhir.'.csv',
                ['Kode booking', 'Kanal', 'Kode tiket', 'Jenis tiket', 'Pengunjung', 'Tanggal kunjungan', 'Harga', 'Check-in', 'Check-out'], $rows);
        }
        $data = $request->input('export') === 'pdf' ? (clone $query)->orderBy('tgl_kunjungan')->get() : (clone $query)->orderBy('tgl_kunjungan')->paginate(25)->withQueryString();
        $payload = compact('data', 'total', 'terbayar', 'keluar', 'periode_awal', 'periode_akhir', 'tipe');
        if ($request->input('export') === 'pdf') {
            return Pdf::loadView('pengelola.laporan.pdf-kunjungan', $payload)->setPaper('a4', 'landscape')->download('laporan-kunjungan-'.$periode_awal.'.pdf');
        }

        return view('pengelola.laporan.kunjungan', $payload);
    }

    public function transaksi(Request $request, CsvExport $csv)
    {
        $filters = $this->filters($request);
        $periode_awal = $filters['periode_awal'];
        $periode_akhir = $filters['periode_akhir'];
        $tipe = $filters['tipe'] ?? null;
        $query = Pembayaran::with(['pemesanan.user', 'pemesanan.tiket', 'pemesanan.detailPemesanan'])
            ->where('status', 'valid')->whereHas('pemesanan', fn ($q) => $q->where('status', 'selesai')->filter(['channel' => $tipe]))
            ->whereBetween('tgl_bayar', [$periode_awal, $periode_akhir]);
        $total = (clone $query)->sum('total');
        if ($request->input('export') === 'csv') {
            $rows = (function () use ($query) {
                foreach ($query->orderBy('id_bayar')->lazyById(200, 'id_bayar') as $p) {
                    $summary = $p->pemesanan->detailPemesanan->groupBy('nama_tiket')->map(fn ($items) => $items->count().' x '.$items->first()->nama_tiket)->implode(', ');
                    yield [$p->pemesanan->kode_booking, str_starts_with($p->pemesanan->kode_booking, 'BJ-OFF-') ? 'offline' : 'online',
                        $p->pemesanan->user?->name, $summary, $p->tgl_bayar, $p->metode, $p->total];
                }
            })();

            return $csv->download('transaksi-'.$periode_awal.'-'.$periode_akhir.'.csv',
                ['Kode booking', 'Kanal', 'Pemesan', 'Rincian tiket', 'Tanggal pembayaran', 'Metode', 'Total'], $rows);
        }
        $data = $request->input('export') === 'pdf' ? $query->orderBy('tgl_bayar')->get() : $query->orderBy('tgl_bayar')->paginate(25)->withQueryString();
        $payload = compact('data', 'total', 'periode_awal', 'periode_akhir', 'tipe');

        return $request->input('export') === 'pdf'
            ? Pdf::loadView('pengelola.laporan.pdf-transaksi', $payload)->setPaper('a4', 'landscape')->download('laporan-transaksi-'.$periode_awal.'.pdf')
            : view('pengelola.laporan.transaksi', $payload);
    }
}
