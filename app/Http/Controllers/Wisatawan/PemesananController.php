<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\Pemesanan;
use App\Models\DetailPemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PemesananBerhasil;

class PemesananController extends Controller
{
    public function create(Request $request)
    {
        $tiketList = Tiket::aktif()->get();
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        return view('wisatawan.pemesanan', compact('tiketList', 'tanggal'));
    }

    /**
     * Step 1: Validate ticket selection, store in session, redirect to data-diri.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tgl_kunjungan' => ['required', 'date', 'after_or_equal:today'],
            'tickets'       => ['required', 'array', 'min:1'],
            'tickets.*'     => ['integer', 'min:0', 'max:100'],
        ], [
            'tgl_kunjungan.required'       => 'Tanggal kunjungan wajib diisi.',
            'tgl_kunjungan.after_or_equal' => 'Tanggal kunjungan tidak boleh sebelum hari ini.',
            'tickets.required'             => 'Pilih minimal satu tiket.',
            'tickets.min'                  => 'Pilih minimal satu tiket.',
        ]);

        $tanggal = $request->tgl_kunjungan;
        $tickets = array_filter($request->tickets, fn($q) => $q > 0);

        if (empty($tickets)) {
            return back()->withErrors(['tickets' => 'Pilih minimal satu tiket.'])->withInput();
        }

        // Build detail & generate kode_tiket (temporary, before saving)
        $detail = [];
        $detailRows = [];
        $total_harga = 0;

        foreach ($tickets as $id_tiket => $jumlah) {
            $tiket = Tiket::aktif()->find($id_tiket);
            if (!$tiket) continue;

            $subtotal = $tiket->harga * $jumlah;
            $items = [];

            if ($tiket->kategori === 'kendaraan') {
                for ($i = 0; $i < $jumlah; $i++) {
                    $kode = 'BJ-' . strtoupper(substr(uniqid(), -6)) . chr(rand(65, 90));
                    $items[] = ['kode' => $kode];
                    $detailRows[] = [
                        'id_tiket'        => (int) $id_tiket,
                        'kode_tiket'      => $kode,
                        'nama_tiket'      => $tiket->nama_tiket,
                        'kategori'        => $tiket->kategori,
                        'harga'           => (int) $tiket->harga,
                        'nama_pengunjung' => null,
                        'plat_kendaraan'  => null,
                    ];
                }
            } else {
                // perorangan
                for ($i = 0; $i < $jumlah; $i++) {
                    $kode = 'BJ-' . strtoupper(substr(uniqid(), -6)) . chr(rand(65, 90));
                    $items[] = ['kode' => $kode];
                    $detailRows[] = [
                        'id_tiket'        => (int) $id_tiket,
                        'kode_tiket'      => $kode,
                        'nama_tiket'      => $tiket->nama_tiket,
                        'kategori'        => $tiket->kategori,
                        'harga'           => (int) $tiket->harga,
                        'nama_pengunjung' => null,
                        'plat_kendaraan'  => null,
                    ];
                }
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

        $kode_booking = 'BJ-' . strtoupper(substr(uniqid(), -8));

        // Save to session
        session()->put('pemesanan_flow', [
            'tgl_kunjungan' => $tanggal,
            'detail'        => $detail,
            'detailRows'    => $detailRows,
            'total_harga'   => $total_harga,
            'kode_booking'  => $kode_booking,
        ]);

        return redirect()->route('wisatawan.pemesanan.data-diri')
            ->with('success', 'Silakan lengkapi data diri pengunjung.');
    }

    /**
     * Step 2: Show form to fill in passenger data per ticket.
     */
    public function dataDiri()
    {
        $flow = session('pemesanan_flow');
        if (!$flow) {
            return redirect()->route('wisatawan.pemesanan.create')
                ->with('error', 'Sesi pemesanan habis. Silakan mulai lagi.');
        }

        return view('wisatawan.pemesanan-data-diri', compact('flow'));
    }

    /**
     * Step 2 (submit): Save passenger data + create pemesanan + detail_pemesanan in DB.
     */
    public function storeDataDiri(Request $request)
    {
        $flow = session('pemesanan_flow');
        if (!$flow) {
            return redirect()->route('wisatawan.pemesanan.create')
                ->with('error', 'Sesi pemesanan habis. Silakan mulai lagi.');
        }

        $detailRows = $flow['detailRows'];

        // Build validation rules dynamically
        $rules = [];
        $messages = [];
        foreach ($detailRows as $i => $row) {
            if ($row['kategori'] === 'kendaraan') {
                $rules["plat_$i"] = ['required', 'string', 'max:20'];
                $messages["plat_$i.required"] = 'Plat kendaraan untuk ' . $row['nama_tiket'] . ' wajib diisi.';
            } else {
                // perorangan
                $rules["nama_$i"] = ['required', 'string', 'max:100'];
                $messages["nama_$i.required"] = 'Nama pengunjung untuk ' . $row['nama_tiket'] . ' wajib diisi.';
                $rules["gender_$i"] = ['required', 'in:L,P'];
                $messages["gender_$i.required"] = 'Jenis kelamin untuk ' . $row['nama_tiket'] . ' wajib diisi.';
                $messages["gender_$i.in"] = 'Jenis kelamin harus Laki-laki atau Perempuan.';
            }
        }

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
                'status'        => 'pending',
                'kode_booking'  => $flow['kode_booking'],
            ]);

            // Insert detail with passenger data
            foreach ($detailRows as $i => $row) {
                $data = [
                    'id_pemesanan'    => $pemesanan->id_pemesanan,
                    'id_tiket'        => $row['id_tiket'],
                    'kode_tiket'      => $row['kode_tiket'],
                    'nama_tiket'      => $row['nama_tiket'],
                    'nama_pengunjung' => $row['kategori'] === 'kendaraan' ? null : $request->input("nama_$i"),
                    'jenis_kelamin'   => $row['kategori'] === 'kendaraan' ? null : $request->input("gender_$i"),
                    'plat_kendaraan'  => $row['kategori'] === 'kendaraan' ? $request->input("plat_$i") : null,
                    'harga'           => $row['harga'],
                    'status_tiket'    => 'aktif',
                ];
                DetailPemesanan::create($data);
            }

            DB::commit();

            // Clear session
            session()->forget('pemesanan_flow');

            try {
                Mail::to(Auth::user()->email)->send(new PemesananBerhasil($pemesanan));
            } catch (\Exception $e) {
                \Log::warning('Gagal kirim email pemesanan: ' . $e->getMessage());
            }

            return redirect()->route('wisatawan.pembayaran.create', $pemesanan->id_pemesanan)
                ->with('success', 'Pemesanan berhasil! Silakan lakukan pembayaran.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Pemesanan error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()])->withInput();
        }
    }

    public function sukses($id)
    {
        $pemesanan = Pemesanan::with(['tiket', 'pembayaran', 'detailPemesanan'])
            ->where('id_user', Auth::id())
            ->findOrFail($id);
        return view('wisatawan.pemesanan-sukses', compact('pemesanan'));
    }

    public function riwayat()
    {
        $pemesanan = Pemesanan::with(['tiket', 'pembayaran', 'detailPemesanan'])
            ->where('id_user', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('wisatawan.riwayat', compact('pemesanan'));
    }

    public function detailPemesanan($id)
    {
        $pemesanan = Pemesanan::with(['tiket', 'pembayaran', 'user', 'detailPemesanan'])
            ->where('id_user', Auth::id())
            ->findOrFail($id);
        return view('wisatawan.pemesanan-detail', compact('pemesanan'));
    }
}
