<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    public function create($id_pemesanan)
    {
        $pemesanan = Pemesanan::with('tiket')
            ->where('id_user', Auth::id())
            ->findOrFail($id_pemesanan);

        if ($pemesanan->pembayaran) {
            return redirect()->route('pemesanan.detail', $pemesanan->id_pemesanan)
                ->with('info', 'Pembayaran sudah pernah diajukan.');
        }

        return view('wisatawan.pembayaran', compact('pemesanan'));
    }

    public function store(Request $request, $id_pemesanan)
    {
        $pemesanan = Pemesanan::with('tiket')
            ->where('id_user', Auth::id())
            ->findOrFail($id_pemesanan);

        if ($pemesanan->pembayaran) {
            return back()->with('error', 'Pembayaran sudah pernah diajukan.');
        }

        $request->validate([
            'metode'      => ['required', 'string', 'max:50'],
            'bukti_bayar' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'metode.required'       => 'Pilih metode pembayaran.',
            'bukti_bayar.required'  => 'Upload bukti transfer.',
            'bukti_bayar.mimes'     => 'Format file harus: jpg, jpeg, png, atau pdf.',
            'bukti_bayar.max'       => 'Ukuran file maksimal 2MB.',
        ]);

        // Handle file upload aman
        if ($request->hasFile('bukti_bayar')) {
            $file = $request->file('bukti_bayar');

            // Validasi MIME type asli
            $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                return back()->withErrors(['bukti_bayar' => 'Tipe file tidak valid.'])->withInput();
            }

            // Rename dengan nama acak
            $filename = 'bukti_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/bukti', $filename);
            $pathDisplay = str_replace('public/', '', $path);
        }

        Pembayaran::create([
            'id_pemesanan' => $pemesanan->id_pemesanan,
            'total'        => $pemesanan->total_harga,
            'metode'       => $request->metode,
            'bukti_bayar'  => $pathDisplay ?? null,
            'status'       => 'pending',
            'tgl_bayar'    => now()->toDateString(),
        ]);

        // Update status pemesanan jadi 'diproses'
        $pemesanan->update(['status' => 'diproses']);

        return redirect()->route('pemesanan.detail', $pemesanan->id_pemesanan)
            ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi pengelola.');
    }
}
