<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Services\BookingExpirationService;
use App\Services\BookingLifecycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PembayaranController extends Controller
{
    public function create($id_pemesanan)
    {

        $pemesanan = Pemesanan::with(['tiket', 'pembayaran'])
            ->where('id_user', Auth::id())
            ->findOrFail($id_pemesanan);

        app(BookingLifecycle::class)->refresh($pemesanan);
        if ($pemesanan->status !== 'pending') {
            return redirect()->route('wisatawan.pemesanan.detail', $pemesanan->id_pemesanan)
                ->with('error', __('flash.booking_expired'));
        }

        if ($pemesanan->pembayaran && $pemesanan->pembayaran->status !== 'ditolak') {
            return redirect()->route('wisatawan.pemesanan.detail', $pemesanan->id_pemesanan)
                ->with('info', __('flash.payment_already_submitted'));
        }

        $deadline = app(BookingExpirationService::class)->getDeadline($pemesanan);
        $metodeBayar = config('payment.methods');

        return view('wisatawan.pembayaran', compact('pemesanan', 'metodeBayar', 'deadline'));
    }

    public function store(Request $request, $id_pemesanan)
    {

        $pemesanan = Pemesanan::with(['tiket', 'pembayaran'])
            ->where('id_user', Auth::id())
            ->findOrFail($id_pemesanan);

        app(BookingLifecycle::class)->refresh($pemesanan);
        if ($pemesanan->status !== 'pending') {
            return back()->with('error', __('operations.ticket_unavailable'));
        }

        if ($pemesanan->pembayaran && $pemesanan->pembayaran->status !== 'ditolak') {
            return back()->with('error', __('flash.payment_already_submitted'));
        }

        $availableMethods = array_column(config('payment.methods'), 'id');
        if ($availableMethods === []) {
            return back()->with('error', __('flash.payment_method_unavailable'));
        }

        $request->validate([
            'metode' => ['required', 'string', Rule::in($availableMethods)],
            'bukti_bayar' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'metode.required' => __('flash.method_required'),
            'bukti_bayar.required' => __('flash.proof_required'),
            'bukti_bayar.mimes' => __('flash.proof_mimes'),
            'bukti_bayar.max' => __('flash.proof_max'),
        ]);

        // Handle file upload aman
        if (! $request->hasFile('bukti_bayar')) {
            return back()->withErrors(['bukti_bayar' => __('flash.proof_not_found')])->withInput();
        }

        $file = $request->file('bukti_bayar');

        // Validasi MIME type asli
        $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
        if (! in_array($file->getMimeType(), $allowedMimes)) {
            return back()->withErrors(['bukti_bayar' => __('flash.proof_type_invalid')])->withInput();
        }

        // Rename dengan nama acak
        $path = $file->store('bukti', 'private');

        if (! $path) {
            return back()->withErrors(['bukti_bayar' => __('flash.proof_save_failed')])->withInput();
        }

        $oldPath = null;
        $oldDisk = 'public';
        try {
            $saved = DB::transaction(function () use ($id_pemesanan, $request, $path, $pemesanan, &$oldPath, &$oldDisk) {
                $booking = Pemesanan::where('id_user', Auth::id())->whereKey($id_pemesanan)->lockForUpdate()->firstOrFail();
                $payment = Pembayaran::where('id_pemesanan', $booking->id_pemesanan)->lockForUpdate()->first();
                app(BookingLifecycle::class)->expire($booking);
                if ($booking->status !== 'pending' || $booking->isOverdue() || ($payment && $payment->status !== 'ditolak')) {
                    return false;
                }
                $oldPath = $payment?->bukti_bayar;
                $oldDisk = $payment?->proof_disk === 'private' ? 'private' : 'public';
                $attributes = [
                    'total' => $booking->total_harga,
                    'metode' => $request->metode,
                    'bukti_bayar' => $path,
                    'proof_disk' => 'private', 'rejection_reason' => null, 'verified_at' => null,
                    'status' => 'pending',
                    'tgl_bayar' => now()->toDateString(),
                ];
                if ($payment) {
                    $payment->update($attributes);
                } else {
                    Pembayaran::create(['id_pemesanan' => $booking->id_pemesanan] + $attributes);
                }
                $booking->recordEvent('proof_submitted');

                return true;
            });
        } catch (\Throwable $e) {
            Storage::disk('private')->delete($path);
            throw $e;
        }
        if (! $saved) {
            Storage::disk('private')->delete($path);

            return back()->with('error', __('flash.payment_status_invalid'));
        }

        if ($oldPath && $oldPath !== $path) {
            Storage::disk($oldDisk)->delete($oldPath);
        }

        return redirect()->route('wisatawan.pemesanan.detail', $pemesanan->id_pemesanan)
            ->with('success', __('flash.proof_uploaded'));
    }
}
