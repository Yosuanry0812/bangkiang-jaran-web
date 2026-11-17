<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use App\Services\TicketQr;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ETicketController extends Controller
{
    public function download(Request $request, $id, TicketQr $qr)
    {
        $booking = Pemesanan::with(['user', 'pembayaran', 'detailPemesanan'])->findOrFail($id);
        abort_unless($request->user()->isPengelola() || (int) $booking->id_user === (int) $request->user()->id, 404);
        abort_unless($booking->isApproved(), 403, __('operations.ticket_unavailable'));
        $images = $booking->detailPemesanan->mapWithKeys(fn ($detail) => [$detail->id_detail => $qr->dataUri($detail->kode_tiket)]);

        return Pdf::loadView('wisatawan.eticket-pdf', compact('booking', 'images'))->setPaper('a5')
            ->download('tiket-'.$booking->kode_booking.'.pdf')
            ->header('Cache-Control', 'private, no-store')->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
