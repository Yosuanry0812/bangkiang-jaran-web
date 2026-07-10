<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PemesananBerhasil;

class PemesananController extends Controller
{
    public function create(Request $request)
    {
        $id_tiket = $request->input('id_tiket');
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $tiketList = Tiket::aktif()->get();
        $selectedTiket = null;
        if ($id_tiket) {
            $selectedTiket = Tiket::aktif()->find($id_tiket);
        }

        return view('wisatawan.pemesanan', compact('tiketList', 'tanggal', 'selectedTiket'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_tiket'       => ['required', 'exists:tiket,id_tiket'],
            'tgl_kunjungan'  => ['required', 'date', 'after_or_equal:today'],
            'jumlah'         => ['required', 'integer', 'min:1', 'max:100'],
        ], [
            'id_tiket.required'      => 'Pilih tiket terlebih dahulu.',
            'tgl_kunjungan.required' => 'Tanggal kunjungan wajib diisi.',
            'tgl_kunjungan.after_or_equal' => 'Tanggal kunjungan tidak boleh sebelum hari ini.',
            'jumlah.required'        => 'Jumlah tiket wajib diisi.',
            'jumlah.min'             => 'Minimal 1 tiket.',
            'jumlah.max'             => 'Maksimal 100 tiket per pemesanan.',
        ]);

        $tiket = Tiket::aktif()->findOrFail($request->id_tiket);
        $tanggal = $request->tgl_kunjungan;
        $jumlah = $request->jumlah;

        $total_harga = $tiket->harga * $jumlah;

        // Generate kode booking unik
        $kode_booking = 'BJ-' . strtoupper(substr(uniqid(), -8));

        DB::beginTransaction();
        try {
            $pemesanan = Pemesanan::create([
                'id_user'       => Auth::id(),
                'id_tiket'      => $tiket->id_tiket,
                'tgl_kunjungan' => $tanggal,
                'jumlah'        => $jumlah,
                'total_harga'   => $total_harga,
                'status'        => 'pending',
                'kode_booking'  => $kode_booking,
            ]);

            DB::commit();

            // Kirim email notifikasi
            try {
                Mail::to(Auth::user()->email)->send(new PemesananBerhasil($pemesanan));
            } catch (\Exception $e) {
                // Log error email tapi jangan gagalkan proses
                \Log::warning('Gagal kirim email pemesanan: ' . $e->getMessage());
            }

            return redirect()->route('pemesanan.sukses', $pemesanan->id_pemesanan)
                ->with('success', 'Pemesanan berhasil! Silakan lakukan pembayaran.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan. Silakan coba lagi.'])->withInput();
        }
    }

    public function sukses($id)
    {
        $pemesanan = Pemesanan::with(['tiket', 'pembayaran'])
            ->where('id_user', Auth::id())
            ->findOrFail($id);
        return view('wisatawan.pemesanan-sukses', compact('pemesanan'));
    }

    public function riwayat()
    {
        $pemesanan = Pemesanan::with(['tiket', 'pembayaran'])
            ->where('id_user', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('wisatawan.riwayat', compact('pemesanan'));
    }

    public function detailPemesanan($id)
    {
        // Cegah IDOR: pastikan hanya pemilik yang bisa akses
        $pemesanan = Pemesanan::with(['tiket', 'pembayaran', 'user'])
            ->where('id_user', Auth::id())
            ->findOrFail($id);
        return view('wisatawan.pemesanan-detail', compact('pemesanan'));
    }
}
