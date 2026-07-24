<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\DetailPemesanan;
use Illuminate\Http\Request;

class TiketController extends Controller
{
    public function index(Request $request)
    {
        $tikets = Tiket::aktif()->orderBy('harga', 'asc')->get();
        return view('wisatawan.tiket', compact('tikets'));
    }

    public function detail($id)
    {
        $tiket = Tiket::aktif()->findOrFail($id);
        return view('wisatawan.tiket-detail', compact('tiket'));
    }

    /**
     * Public ticket verification — accessed via QR scan.
     */
    public function verifikasi($kode)
    {
        $kode = strtoupper(trim($kode));

        $detail = DetailPemesanan::with(['pemesanan.user', 'pemesanan.tiket', 'tiket'])
            ->where('kode_tiket', $kode)
            ->first();

        if (!$detail) {
            return view('wisatawan.tiket-verifikasi', [
                'found' => false,
                'kode'  => $kode,
                'detail' => null,
                'pemesanan' => null,
            ]);
        }

        $pemesanan = $detail->pemesanan;
        $valid = $pemesanan->status === 'selesai' && $detail->status_tiket === 'aktif';

        return view('wisatawan.tiket-verifikasi', compact('detail', 'pemesanan', 'kode', 'valid') + ['found' => true]);
    }
}
