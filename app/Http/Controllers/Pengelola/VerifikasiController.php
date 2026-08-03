<?php

namespace App\Http\Controllers\Pengelola;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PembayaranValid;
use App\Mail\PembayaranDitolak;

class VerifikasiController extends Controller
{
    public function index()
    {
        // Semua pemesanan yg sudah punya pembayaran.
        // Prioritas: belum diverifikasi (pembayaran pending) di atas, lalu sisanya — masing-masing urut terbaru.
        $pemesanan = Pemesanan::with(['user', 'tiket', 'pembayaran', 'detailPemesanan'])
            ->join('pembayaran', 'pembayaran.id_pemesanan', '=', 'pemesanan.id_pemesanan')
            ->select('pemesanan.*')
            ->orderByRaw("CASE WHEN pembayaran.status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('pemesanan.created_at', 'desc')
            ->paginate(20);

        return view('pengelola.verifikasi.index', compact('pemesanan'));
    }

    /**
     * Data notifikasi realtime (polling) untuk topbar pengelola.
     */
    public function notifikasi()
    {
        // Prioritas 1: pembayaran pending — butuh verifikasi
        $verifikasi = Pembayaran::with(['pemesanan.user'])
            ->where('status', 'pending')
            ->latest('created_at')
            ->take(10)
            ->get()
            ->map(function ($b) {
                return [
                    'id_pemesanan' => $b->pemesanan->id_pemesanan ?? null,
                    'kode_booking' => $b->pemesanan->kode_booking ?? '-',
                    'nama_user'    => $b->pemesanan->user->name ?? '-',
                    'total'        => $b->pemesanan->total_harga ?? 0,
                    'metode'       => $b->metode,
                    'waktu'        => $b->created_at?->diffForHumans(),
                ];
            });

        // Prioritas 2: pesanan baru, belum bayar
        $pesananBaru = Pemesanan::with('user')
            ->doesntHave('pembayaran')
            ->where('status', 'pending')
            ->latest('created_at')
            ->take(10)
            ->get()
            ->map(function ($p) {
                return [
                    'id_pemesanan' => $p->id_pemesanan,
                    'kode_booking' => $p->kode_booking,
                    'nama_user'    => $p->user->name ?? '-',
                    'total'        => $p->total_harga,
                    'waktu'        => $p->created_at?->diffForHumans(),
                ];
            });

        return response()->json([
            'count'         => $verifikasi->count() + $pesananBaru->count(),
            'verifikasi'    => $verifikasi,
            'pesanan_baru'  => $pesananBaru,
        ]);
    }

    public function show($id)
    {
        $pemesanan = Pemesanan::with(['user', 'tiket', 'pembayaran', 'detailPemesanan'])->findOrFail($id);
        return view('pengelola.verifikasi.show', compact('pemesanan'));
    }

    public function validasi(Request $request, $id)
    {
        $request->validate([
            'action' => ['required', 'in:valid,ditolak'],
        ]);

        $pemesanan = Pemesanan::with('pembayaran')->findOrFail($id);

        if (!$pemesanan->pembayaran) {
            return back()->with('error', 'Belum ada pembayaran untuk pemesanan ini.');
        }

        DB::beginTransaction();
        try {
            if ($request->action === 'valid') {
                $pemesanan->pembayaran->update([
                    'status' => 'valid',
                    'tgl_bayar' => now()->toDateString(),
                ]);
                $pemesanan->update(['status' => 'selesai']);

                DB::commit();

                ActivityLogger::log('Verifikasi pembayaran VALID', 'Kode booking: ' . $pemesanan->kode_booking);

                // Kirim email notifikasi
                try {
                    Mail::to($pemesanan->user->email)->send(new PembayaranValid($pemesanan));
                } catch (\Exception $e) {
                    \Log::warning('Gagal kirim email validasi: ' . $e->getMessage());
                }

                return redirect()->route('pengelola.verifikasi.index')
                    ->with('success', 'Pembayaran diverifikasi VALID. Tiket siap digunakan.');
            } else {
                $pemesanan->pembayaran->update([
                    'status' => 'ditolak',
                ]);
                $pemesanan->update(['status' => 'pending']);

                DB::commit();

                ActivityLogger::log('Verifikasi pembayaran DITOLAK', 'Kode booking: ' . $pemesanan->kode_booking);

                // Kirim email notifikasi
                try {
                    Mail::to($pemesanan->user->email)->send(new PembayaranDitolak($pemesanan));
                } catch (\Exception $e) {
                    \Log::warning('Gagal kirim email ditolak: ' . $e->getMessage());
                }

                return redirect()->route('pengelola.verifikasi.index')
                    ->with('success', 'Pembayaran ditolak. Wisatawan akan diminta upload ulang.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
