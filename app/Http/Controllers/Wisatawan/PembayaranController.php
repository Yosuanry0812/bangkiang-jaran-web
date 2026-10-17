<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PembayaranController extends Controller
{
    public function create($id_pemesanan)
    {
        $pemesanan = Pemesanan::with('tiket')
            ->where('id_user', Auth::id())
            ->findOrFail($id_pemesanan);

        if ($pemesanan->pembayaran && $pemesanan->pembayaran->status !== 'ditolak') {
            return redirect()->route('wisatawan.pemesanan.detail', $pemesanan->id_pemesanan)
                ->with('info', 'Pembayaran sudah pernah diajukan.');
        }

        $metodeBayar = config('payment.methods');

        return view('wisatawan.pembayaran', compact('pemesanan', 'metodeBayar'));
    }

    public function store(Request $request, $id_pemesanan)
    {
        $pemesanan = Pemesanan::with('tiket')
            ->where('id_user', Auth::id())
            ->findOrFail($id_pemesanan);

        if ($pemesanan->pembayaran && $pemesanan->pembayaran->status !== 'ditolak') {
            return back()->with('error', 'Pembayaran sudah pernah diajukan.');
        }

        $availableMethods = array_column(config('payment.methods'), 'id');
        if ($availableMethods === []) {
            return back()->with('error', 'Metode pembayaran belum tersedia. Hubungi pengelola sebelum melakukan transfer.');
        }

        $request->validate([
            'metode'      => ['required', 'string', Rule::in($availableMethods)],
            'bukti_bayar' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'metode.required'       => 'Pilih metode pembayaran.',
            'bukti_bayar.required'  => 'Upload bukti transfer.',
            'bukti_bayar.mimes'     => 'Format file harus: jpg, jpeg, png, atau pdf.',
            'bukti_bayar.max'       => 'Ukuran file maksimal 5MB.',
        ]);

        // Handle file upload aman
        if (!$request->hasFile('bukti_bayar')) {
            return back()->withErrors(['bukti_bayar' => 'File bukti pembayaran tidak ditemukan.'])->withInput();
        }

        $file = $request->file('bukti_bayar');

        // Validasi MIME type asli
        $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return back()->withErrors(['bukti_bayar' => 'Tipe file tidak valid. Gunakan JPG, PNG, atau PDF.'])->withInput();
        }

        // Rename dengan nama acak
        $path = $file->store('bukti', 'public');

        if (!$path) {
            return back()->withErrors(['bukti_bayar' => 'Gagal menyimpan file. Coba lagi.'])->withInput();
        }

        $oldPath = $pemesanan->pembayaran?->bukti_bayar;
        $attributes = [
            'total'        => $pemesanan->total_harga,
            'metode'       => $request->metode,
            'bukti_bayar'  => $path,
            'status'       => 'pending',
            'tgl_bayar'    => now()->toDateString(),
        ];

        try {
            if ($pemesanan->pembayaran) {
                $pemesanan->pembayaran->update($attributes);
            } else {
                Pembayaran::create(['id_pemesanan' => $pemesanan->id_pemesanan] + $attributes);
            }
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($path);
            throw $e;
        }

        if ($oldPath && $oldPath !== $path) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()->route('wisatawan.pemesanan.detail', $pemesanan->id_pemesanan)
            ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi pengelola.');
    }
}
