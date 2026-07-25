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
        // Tampilkan semua pemesanan yg sudah punya pembayaran — urut dari yg terbaru
        $pemesanan = Pemesanan::with(['user', 'tiket', 'pembayaran', 'detailPemesanan'])
            ->has('pembayaran')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pengelola.verifikasi.index', compact('pemesanan'));
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
