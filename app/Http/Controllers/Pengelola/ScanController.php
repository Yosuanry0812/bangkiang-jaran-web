<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\DetailPemesanan;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScanController extends Controller
{
    public function index()
    {
        $ticketsHariIni = $this->getTodayTickets();

        return view('pengelola.scan.index', compact('ticketsHariIni'));
    }

    private function getTodayTickets()
    {
        $today = now()->toDateString();

        return DetailPemesanan::with(['pemesanan.user', 'pemesanan.tiket'])
            ->whereHas('pemesanan', function ($q) use ($today) {
                $q->where('tgl_kunjungan', $today)
                  ->where('status', 'selesai');
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function cari(Request $request)
    {
        $request->validate([
            'kode' => ['required', 'string', 'max:20'],
        ]);

        $kode = strtoupper(trim($request->kode));

        $detail = DetailPemesanan::with(['pemesanan.user', 'pemesanan.tiket', 'tiket'])
            ->where('kode_tiket', $kode)
            ->first();

        if (!$detail) {
            return redirect()->route('pengelola.scan.index')
                ->with('error', 'Tiket dengan kode "' . $kode . '" tidak ditemukan.');
        }

        $pemesanan = $detail->pemesanan;

        // Cek apakah tiket sudah digunakan
        $sudahDigunakan = $detail->status_tiket === 'digunakan';
        $kadaluarsa = $detail->status_tiket === 'kadaluarsa';
        $pemesananDibatalkan = $pemesanan->status === 'dibatalkan';
        $belumLunas = $pemesanan->status !== 'selesai';

        $ticketsHariIni = $this->getTodayTickets();

        return view('pengelola.scan.index', compact(
            'detail', 'pemesanan',
            'sudahDigunakan', 'kadaluarsa',
            'pemesananDibatalkan', 'belumLunas',
            'ticketsHariIni'
        ));
    }

    public function gunakan(Request $request)
    {
        $request->validate([
            'kode' => ['required', 'string', 'max:20'],
        ]);

        $kode = strtoupper(trim($request->kode));

        DB::beginTransaction();
        try {
            $detail = DetailPemesanan::where('kode_tiket', $kode)->first();

            if (!$detail) {
                DB::rollBack();
                return redirect()->route('pengelola.scan.index')
                    ->with('error', 'Tiket dengan kode "' . $kode . '" tidak ditemukan.');
            }

            if ($detail->status_tiket !== 'aktif') {
                $statusLabel = $detail->status_tiket === 'digunakan' ? 'sudah digunakan' : $detail->status_tiket;
                DB::rollBack();
                return redirect()->route('pengelola.scan.index')
                    ->with('error', 'Tiket ini ' . $statusLabel . '. Tidak bisa digunakan lagi.');
            }

            // Cek pemesanan harus selesai (lunas)
            $pemesanan = $detail->pemesanan;
            if ($pemesanan->status !== 'selesai') {
                DB::rollBack();
                return redirect()->route('pengelola.scan.index')
                    ->with('error', 'Tiket belum lunas. Status pemesanan: ' . $pemesanan->status . '.');
            }

            $detail->update([
                'status_tiket' => 'digunakan',
                'check_in_at'  => now(),
            ]);

            DB::commit();

            return redirect()->route('pengelola.scan.index')
                ->with('success', 'Tiket ' . $kode . ' berhasil discan. Pengunjung dipersilakan masuk.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pengelola.scan.index')
                ->with('error', 'Gagal memproses tiket: ' . $e->getMessage());
        }
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'kode' => ['required', 'string', 'max:20'],
        ]);

        $kode = strtoupper(trim($request->kode));

        DB::beginTransaction();
        try {
            $detail = DetailPemesanan::where('kode_tiket', $kode)->first();

            if (!$detail) {
                DB::rollBack();
                return redirect()->route('pengelola.scan.index')
                    ->with('error', 'Tiket dengan kode "' . $kode . '" tidak ditemukan.');
            }

            if ($detail->status_tiket !== 'digunakan') {
                DB::rollBack();
                return redirect()->route('pengelola.scan.index')
                    ->with('error', 'Tiket harus sudah check-in sebelum check-out.');
            }

            if ($detail->check_out_at) {
                DB::rollBack();
                return redirect()->route('pengelola.scan.index')
                    ->with('error', 'Tiket ini sudah check-out sebelumnya.');
            }

            $detail->update([
                'check_out_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('pengelola.scan.index')
                ->with('success', 'Tiket ' . $kode . ' berhasil check-out. Terima kasih.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pengelola.scan.index')
                ->with('error', 'Gagal check-out: ' . $e->getMessage());
        }
    }
}
