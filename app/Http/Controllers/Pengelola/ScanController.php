<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\DetailPemesanan;
use App\Models\Pemesanan;
use App\Services\AuditTrail;
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
                    ->approved();
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function normalizeKode(?string $input): string
    {
        $trimmed = trim((string) $input);
        if (preg_match('#/(?:tiket/verifikasi|verifikasi)/([A-Za-z0-9_-]+)#i', $trimmed, $matches)) {
            return strtoupper($matches[1]);
        }
        if (filter_var($trimmed, FILTER_VALIDATE_URL)) {
            $path = parse_url($trimmed, PHP_URL_PATH);
            if ($path) {
                $segments = explode('/', trim($path, '/'));
                return strtoupper(end($segments));
            }
        }
        return strtoupper($trimmed);
    }

    public function cari(Request $request)
    {
        $raw = (string) $request->input('kode');
        $kode = $this->normalizeKode($raw);
        $request->merge(['kode' => $kode]);

        $request->validate([
            'kode' => ['required', 'string', 'max:50'],
        ]);

        $detail = DetailPemesanan::with(['pemesanan.user', 'pemesanan.tiket', 'tiket'])
            ->where('kode_tiket', $kode)
            ->first();

        if (! $detail) {
            return redirect()->route('pengelola.scan.index')
                ->with('error', 'Tiket dengan kode "'.$kode.'" tidak ditemukan.');
        }

        $pemesanan = $detail->pemesanan;

        // Cek apakah tiket sudah digunakan
        $sudahDigunakan = $detail->status_tiket === 'digunakan';
        $kadaluarsa = $detail->status_tiket === 'kadaluarsa';
        $pemesananDibatalkan = $pemesanan->status === 'dibatalkan';
        $belumLunas = ! $pemesanan->isApproved();
        $bukanHariIni = $pemesanan->tgl_kunjungan !== now()->toDateString();

        $ticketsHariIni = $this->getTodayTickets();

        return view('pengelola.scan.index', compact(
            'detail', 'pemesanan',
            'sudahDigunakan', 'kadaluarsa',
            'pemesananDibatalkan', 'belumLunas',
            'bukanHariIni',
            'ticketsHariIni'
        ));
    }

    public function gunakan(Request $request)
    {
        $raw = (string) $request->input('kode');
        $kode = $this->normalizeKode($raw);
        $request->merge(['kode' => $kode]);

        $request->validate([
            'kode' => ['required', 'string', 'max:50'],
        ]);

        DB::beginTransaction();
        try {
            $detail = DetailPemesanan::where('kode_tiket', $kode)->lockForUpdate()->first();

            if (! $detail) {
                DB::rollBack();

                return redirect()->route('pengelola.scan.index')
                    ->with('error', 'Tiket dengan kode "'.$kode.'" tidak ditemukan.');
            }

            if ($detail->status_tiket !== 'aktif') {
                $statusLabel = $detail->status_tiket === 'digunakan' ? 'sudah digunakan' : $detail->status_tiket;
                DB::rollBack();

                return redirect()->route('pengelola.scan.index')
                    ->with('error', 'Tiket ini '.$statusLabel.'. Tidak bisa digunakan lagi.');
            }

            // Cek pemesanan harus selesai (lunas)
            $pemesanan = $detail->pemesanan;
            if (! $pemesanan->isApproved()) {
                DB::rollBack();

                return redirect()->route('pengelola.scan.index')
                    ->with('error', 'Tiket belum lunas. Status pemesanan: '.$pemesanan->status.'.');
            }

            // Hanya tiket dengan kunjungan HARI INI yang bisa check-in
            if ($pemesanan->tgl_kunjungan !== now()->toDateString()) {
                DB::rollBack();

                return redirect()->route('pengelola.scan.index')
                    ->with('error', 'Tiket ini untuk kunjungan '.$pemesanan->tgl_kunjungan.'. Hanya tiket hari ini ('.now()->format('d/m/Y').') yang bisa di-scan.');
            }

            if (! $detail->canCheckIn()) {
                DB::rollBack();

                return back()->with('error', __('operations.ticket_unavailable'));
            }
            $detail->update([
                'status_tiket' => 'digunakan',
                'check_in_at' => now(),
            ]);

            $detail->pemesanan->recordEvent($request->routeIs('pengelola.scan.checkout') ? 'checked_out' : 'checked_in', $detail->kode_tiket);
            AuditTrail::record($request->routeIs('pengelola.scan.checkout') ? 'Check-out' : 'Check-in', $detail->kode_tiket);
            DB::commit();

            return redirect()->route('pengelola.scan.index')
                ->with('success', 'Tiket '.$kode.' berhasil discan. Pengunjung dipersilakan masuk.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('pengelola.scan.index')
                ->with('error', 'Gagal memproses tiket. Silakan coba lagi.');
        }
    }

    public function checkout(Request $request)
    {
        $raw = (string) $request->input('kode');
        $kode = $this->normalizeKode($raw);
        $request->merge(['kode' => $kode]);

        $request->validate([
            'kode' => ['required', 'string', 'max:50'],
        ]);

        DB::beginTransaction();
        try {
            $detail = DetailPemesanan::where('kode_tiket', $kode)->lockForUpdate()->first();

            if (! $detail) {
                DB::rollBack();

                return redirect()->route('pengelola.scan.index')
                    ->with('error', 'Tiket dengan kode "'.$kode.'" tidak ditemukan.');
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

            // Hanya tiket dengan kunjungan HARI INI yang bisa check-out
            if ($detail->pemesanan->tgl_kunjungan !== now()->toDateString()) {
                DB::rollBack();

                return redirect()->route('pengelola.scan.index')
                    ->with('error', 'Tiket ini untuk kunjungan '.$detail->pemesanan->tgl_kunjungan.'. Hanya tiket hari ini ('.now()->format('d/m/Y').') yang bisa di-scan.');
            }

            $detail->update([
                'check_out_at' => now(),
            ]);

            $detail->pemesanan->recordEvent($request->routeIs('pengelola.scan.checkout') ? 'checked_out' : 'checked_in', $detail->kode_tiket);
            AuditTrail::record($request->routeIs('pengelola.scan.checkout') ? 'Check-out' : 'Check-in', $detail->kode_tiket);
            DB::commit();

            return redirect()->route('pengelola.scan.index')
                ->with('success', 'Tiket '.$kode.' berhasil check-out. Terima kasih.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('pengelola.scan.index')
                ->with('error', 'Gagal check-out. Silakan coba lagi.');
        }
    }
}
