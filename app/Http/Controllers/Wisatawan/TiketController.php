<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\DetailPemesanan;
use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TiketController extends Controller
{
    public function index(Request $request)
    {
        $tikets = Tiket::aktif()->orderBy('harga', 'asc')->get();
        $galeriWisata = Galeri::wisata()
            ->latest()
            ->get()
            ->filter(fn (Galeri $photo) => Storage::disk('public')->exists($photo->file)
                && !str_starts_with($photo->file, 'galeri/galeri-'))
            ->values();

        return view('wisatawan.tiket', compact('tikets', 'galeriWisata'));
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
