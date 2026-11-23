<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingFilters;
use App\Mail\PembayaranDitolak;
use App\Mail\PembayaranValid;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Services\AuditTrail;
use App\Services\DestinationSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class VerifikasiController extends Controller
{
    public function index(BookingFilters $request)
    {
        // Semua pemesanan yg sudah punya pembayaran.
        // Prioritas: belum diverifikasi (pembayaran pending) di atas, lalu sisanya — masing-masing urut terbaru.
        $pemesanan = Pemesanan::with(['user', 'tiket', 'pembayaran', 'detailPemesanan'])
            ->filter($request->validated())
            ->join('pembayaran', 'pembayaran.id_pemesanan', '=', 'pemesanan.id_pemesanan')
            ->select('pemesanan.*')
            ->orderByRaw("CASE WHEN pembayaran.status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('pemesanan.created_at', 'desc')
            ->paginate(20)->withQueryString();

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
                    'nama_user' => $b->pemesanan->user->name ?? '-',
                    'total' => $b->pemesanan->total_harga ?? 0,
                    'metode' => $b->metode,
                    'waktu' => $b->created_at?->diffForHumans(),
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
                    'nama_user' => $p->user->name ?? '-',
                    'total' => $p->total_harga,
                    'waktu' => $p->created_at?->diffForHumans(),
                ];
            });

        return response()->json([
            'count' => Pembayaran::where('status', 'pending')->whereHas('pemesanan', fn ($q) => $q->where('status', 'pending'))->count()
                + Pemesanan::where('status', 'pending')->doesntHave('pembayaran')->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))->count(),
            'verifikasi' => $verifikasi,
            'pesanan_baru' => $pesananBaru,
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
            'rejection_reason' => ['required_if:action,ditolak', 'nullable', 'string', 'min:5', 'max:1000'],
        ]);

        DB::beginTransaction();
        try {
            $pemesanan = Pemesanan::whereKey($id)->lockForUpdate()->firstOrFail();
            $pembayaran = Pembayaran::where('id_pemesanan', $id)->lockForUpdate()->first();
            if (! $pembayaran || $pembayaran->status !== 'pending' || $pemesanan->status !== 'pending') {
                DB::rollBack();

                return back()->with('error', __('flash.payment_not_pending'));
            }
            $pemesanan->setRelation('pembayaran', $pembayaran);

            if ($request->action === 'valid') {
                $pemesanan->pembayaran->update([
                    'status' => 'valid',
                    // Tanggal bayar = saat pembeli mengunggah bukti, bukan saat pengelola memvalidasi.
                    'tgl_bayar' => $pemesanan->pembayaran->tgl_bayar ?: now()->toDateString(),
                    'verified_at' => now(), 'rejection_reason' => null,
                ]);
                $pemesanan->update(['status' => 'selesai']);

                $pemesanan->recordEvent('payment_approved');
                AuditTrail::record('Verifikasi pembayaran VALID', $pemesanan->kode_booking);

                DB::commit();

                // Kirim email notifikasi
                try {
                    Mail::to($pemesanan->user->email)->send(new PembayaranValid($pemesanan));
                } catch (\Exception $e) {
                    \Log::warning('Mail enqueue failed.', ['exception_type' => get_class($e)]);
                }

                return redirect()->route('pengelola.verifikasi.index')
                    ->with('success', __('flash.payment_valid'));
            } else {
                $pemesanan->pembayaran->update([
                    'status' => 'ditolak',
                    'rejection_reason' => $request->rejection_reason, 'verified_at' => now(),
                ]);
                $pemesanan->update(['status' => 'pending', 'expires_at' => now()->addMinutes((int) app(DestinationSettings::class)->values()['payment_minutes'])]);

                $pemesanan->recordEvent('payment_rejected', $request->rejection_reason);
                AuditTrail::record('Verifikasi pembayaran DITOLAK', $pemesanan->kode_booking);

                DB::commit();

                // Kirim email notifikasi
                try {
                    Mail::to($pemesanan->user->email)->send(new PembayaranDitolak($pemesanan));
                } catch (\Exception $e) {
                    \Log::warning('Mail enqueue failed.', ['exception_type' => get_class($e)]);
                }

                return redirect()->route('pengelola.verifikasi.index')
                    ->with('success', __('flash.payment_rejected'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Verifikasi pembayaran gagal.', ['exception' => get_class($e)]);

            return back()->with('error', __('flash.generic_error'));
        }
    }
}
