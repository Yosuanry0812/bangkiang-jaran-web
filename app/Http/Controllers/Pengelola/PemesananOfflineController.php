<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\Pemesanan;
use App\Models\DetailPemesanan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PemesananOfflineController extends Controller
{
    public function create(Request $request)
    {
        $tiketList = Tiket::aktif()->get();
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        return view('pengelola.pemesanan-offline.index', compact('tiketList', 'tanggal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_kunjungan' => ['required', 'date'],
            'tickets'       => ['required', 'array', 'min:1'],
            'tickets.*'     => ['integer', 'min:0', 'max:100'],
        ], [
            'tgl_kunjungan.required' => 'Tanggal kunjungan wajib diisi.',
            'tickets.required'       => 'Pilih minimal satu tiket.',
            'tickets.min'            => 'Pilih minimal satu tiket.',
        ]);

        $tanggal = $request->tgl_kunjungan;
        $tickets = array_filter($request->tickets, fn($q) => $q > 0);

        if (empty($tickets)) {
            return back()->withErrors(['tickets' => 'Pilih minimal satu tiket.'])->withInput();
        }

        $detail = [];
        $detailRows = [];
        $total_harga = 0;

        foreach ($tickets as $id_tiket => $jumlah) {
            $tiket = Tiket::aktif()->find($id_tiket);
            if (!$tiket) continue;

            $subtotal = $tiket->harga * $jumlah;

            for ($i = 0; $i < $jumlah; $i++) {
                $kode = 'BJ-' . strtoupper(substr(uniqid(), -6)) . chr(rand(65, 90));
                $detailRows[] = [
                    'id_tiket'        => (int) $id_tiket,
                    'kode_tiket'      => $kode,
                    'nama_tiket'      => $tiket->nama_tiket,
                    'kategori'        => $tiket->kategori,
                    'harga'           => (int) $tiket->harga,
                    'nama_pengunjung' => null,
                    'jenis_kelamin'   => null,
                ];
            }

            $detail[] = [
                'id_tiket' => (int) $id_tiket,
                'nama'     => $tiket->nama_tiket,
                'kategori' => $tiket->kategori,
                'jumlah'   => (int) $jumlah,
                'harga'    => (int) $tiket->harga,
                'subtotal' => $subtotal,
            ];
            $total_harga += $subtotal;
        }

        $kode_booking = 'BJ-OFF-' . strtoupper(substr(uniqid(), -8));

        session()->put('pemesanan_offline_flow', [
            'tgl_kunjungan' => $tanggal,
            'detail'        => $detail,
            'detailRows'    => $detailRows,
            'total_harga'   => $total_harga,
            'kode_booking'  => $kode_booking,
        ]);

        return redirect()->route('pengelola.pemesanan-offline.data-diri')
            ->with('success', 'Silakan lengkapi data diri pengunjung.');
    }

    public function dataDiri()
    {
        $flow = session('pemesanan_offline_flow');
        if (!$flow) {
            return redirect()->route('pengelola.pemesanan-offline.create')
                ->with('error', 'Sesi pemesanan habis. Silakan mulai lagi.');
        }

        return view('pengelola.pemesanan-offline.data-diri', compact('flow'));
    }

    public function storeDataDiri(Request $request)
    {
        $flow = session('pemesanan_offline_flow');
        if (!$flow) {
            return redirect()->route('pengelola.pemesanan-offline.create')
                ->with('error', 'Sesi pemesanan habis. Silakan mulai lagi.');
        }

        $detailRows = $flow['detailRows'];

        // Build validation: nama + gender required for all tickets (no plat)
        $rules = [];
        $messages = [];
        foreach ($detailRows as $i => $row) {
            $rules["nama_$i"]   = ['required', 'string', 'max:100'];
            $rules["gender_$i"] = ['required', 'in:L,P'];
            $messages["nama_$i.required"]   = 'Nama pengunjung untuk ' . $row['nama_tiket'] . ' wajib diisi.';
            $messages["gender_$i.required"] = 'Jenis kelamin untuk ' . $row['nama_tiket'] . ' wajib diisi.';
            $messages["gender_$i.in"]       = 'Jenis kelamin harus Laki-laki atau Perempuan.';
        }

        // Payment method validation
        $rules['metode_bayar'] = ['required', 'string', 'in:Tunai,BCA,BRI,Mandiri,Dana,OVO,GoPay,QRIS'];
        $messages['metode_bayar.required'] = 'Metode pembayaran wajib dipilih.';

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
            $first = $flow['detail'][0];

            $pemesanan = Pemesanan::create([
                'id_user'       => Auth::id(),
                'id_tiket'      => $first['id_tiket'],
                'tgl_kunjungan' => $flow['tgl_kunjungan'],
                'jumlah'        => array_sum(array_column($flow['detail'], 'jumlah')),
                'total_harga'   => $flow['total_harga'],
                'detail'        => $flow['detail'],
                'status'        => 'selesai',
                'kode_booking'  => $flow['kode_booking'],
            ]);

            // Insert detail with passenger data
            foreach ($detailRows as $i => $row) {
                DetailPemesanan::create([
                    'id_pemesanan'    => $pemesanan->id_pemesanan,
                    'id_tiket'        => $row['id_tiket'],
                    'kode_tiket'      => $row['kode_tiket'],
                    'nama_tiket'      => $row['nama_tiket'],
                    'nama_pengunjung' => $request->input("nama_$i"),
                    'jenis_kelamin'   => $request->input("gender_$i"),
                    'plat_kendaraan'  => null,
                    'harga'           => $row['harga'],
                    'status_tiket'    => 'aktif',
                ]);
            }

            // Auto-create payment with status valid
            Pembayaran::create([
                'id_pemesanan' => $pemesanan->id_pemesanan,
                'total'        => $flow['total_harga'],
                'metode'       => $request->metode_bayar,
                'bukti_bayar'  => null,
                'status'       => 'valid',
                'tgl_bayar'    => now()->toDateString(),
            ]);

            DB::commit();

            session()->forget('pemesanan_offline_flow');

            return redirect()->route('pengelola.pemesanan-offline.sukses', $pemesanan->id_pemesanan)
                ->with('success', 'Pemesanan tiket offline berhasil! Tiket sudah aktif.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Pemesanan offline error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()])->withInput();
        }
    }

    public function sukses($id)
    {
        $pemesanan = Pemesanan::with(['tiket', 'pembayaran', 'detailPemesanan'])
            ->where('id_user', Auth::id())
            ->findOrFail($id);
        return view('pengelola.pemesanan-offline.sukses', compact('pemesanan'));
    }

    public function riwayat()
    {
        $pemesanan = Pemesanan::with(['tiket', 'pembayaran', 'detailPemesanan'])
            ->where('id_user', Auth::id())
            ->where('status', 'selesai')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('pengelola.pemesanan-offline.riwayat', compact('pemesanan'));
    }
}
