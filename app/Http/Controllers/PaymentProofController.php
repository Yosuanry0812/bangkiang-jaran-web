<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentProofController extends Controller
{
    public function show(Request $request, $id)
    {
        $payment = Pembayaran::with('pemesanan')->findOrFail($id);
        abort_unless($payment->pemesanan && ($request->user()->isPengelola()
            || (int) $payment->pemesanan->id_user === (int) $request->user()->id), 404);
        abort_unless($payment->bukti_bayar, 404);
        abort_unless(str_starts_with($payment->bukti_bayar, 'bukti/') && ! str_contains($payment->bukti_bayar, '..'), 404);
        $disk = Storage::disk($payment->proof_disk === 'private' ? 'private' : 'public');
        abort_unless($disk->exists($payment->bukti_bayar), 404);

        return $disk->response($payment->bukti_bayar, 'bukti-pembayaran.'.pathinfo($payment->bukti_bayar, PATHINFO_EXTENSION), [
            'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "sandbox; default-src 'none'", 'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }
}
