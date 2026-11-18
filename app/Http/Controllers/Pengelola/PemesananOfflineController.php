<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingFilters;
use App\Models\Pemesanan;
use App\Models\Tiket;
use App\Services\BookingCheckout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
            'tickets' => ['required', 'array', 'min:1'],
            'tickets.*' => ['integer', 'min:0'],
        ], [
            'tgl_kunjungan.required' => __('flash.visit_date_required'),
            'tickets.required' => __('flash.select_one_ticket'),
            'tickets.min' => __('flash.select_one_ticket'),
        ]);

        $tanggal = $request->tgl_kunjungan;
        $tickets = array_filter($request->tickets, fn ($q) => $q > 0);

        if (empty($tickets)) {
            return back()->withErrors(['tickets' => __('flash.select_one_ticket')])->withInput();
        }

        $detail = [];
        $detailRows = [];
        $total_harga = 0;

        foreach ($tickets as $id_tiket => $jumlah) {
            $tiket = Tiket::aktif()->find($id_tiket);
            if (! $tiket) {
                return back()->withErrors(['tickets' => __('operations.selection_changed')])->withInput();
            }

            $subtotal = $tiket->harga * $jumlah;

            for ($i = 0; $i < $jumlah; $i++) {
                $kode = 'BJ-'.strtoupper(Str::random(16));
                $detailRows[] = [
                    'id_tiket' => (int) $id_tiket,
                    'kode_tiket' => $kode,
                    'nama_tiket' => $tiket->nama_tiket,
                    'kategori' => $tiket->kategori,
                    'harga' => (int) $tiket->harga,
                    'nama_pengunjung' => null,
                    'jenis_kelamin' => null,
                ];
            }

            $detail[] = [
                'id_tiket' => (int) $id_tiket,
                'nama' => $tiket->nama_tiket,
                'kategori' => $tiket->kategori,
                'jumlah' => (int) $jumlah,
                'harga' => (int) $tiket->harga,
                'subtotal' => $subtotal,
            ];
            $total_harga += $subtotal;
        }

        $kode_booking = 'BJ-OFF-'.strtoupper(Str::random(12));

        session()->put('pemesanan_offline_flow', [
            'issued_at' => now()->toIso8601String(),
            'draft_token' => (string) Str::uuid(),
            'tgl_kunjungan' => $tanggal,
            'detail' => $detail,
            'detailRows' => $detailRows,
            'total_harga' => $total_harga,
            'kode_booking' => $kode_booking,
        ]);

        return redirect()->route('pengelola.pemesanan-offline.data-diri')
            ->with('success', __('flash.fill_visitor_data'));
    }

    public function dataDiri()
    {
        $flow = session('pemesanan_offline_flow');
        if (! $flow) {
            return redirect()->route('pengelola.pemesanan-offline.create')
                ->with('error', __('flash.booking_session_expired'));
        }

        return view('pengelola.pemesanan-offline.data-diri', compact('flow'));
    }

    public function storeDataDiri(Request $request)
    {
        $flow = session('pemesanan_offline_flow');
        if (! $flow) {
            $last = Pemesanan::where('id_user', Auth::id())->find(session('last_offline_booking_id'));

            return $last ? redirect()->route('pengelola.pemesanan-offline.sukses', $last->id_pemesanan)
                : redirect()->route('pengelola.pemesanan-offline.create')->with('error', __('operations.draft_expired'));
        }
        $pemesanan = app(BookingCheckout::class)->finish($flow, $request, true);
        session()->put('last_offline_booking_id', $pemesanan->id_pemesanan);
        session()->forget('pemesanan_offline_flow');

        return redirect()->route('pengelola.pemesanan-offline.sukses', $pemesanan->id_pemesanan)
            ->with('success', __('operations.booking_created'));
    }

    public function sukses($id)
    {
        $pemesanan = Pemesanan::with(['tiket', 'pembayaran', 'detailPemesanan'])
            ->where('id_user', Auth::id())
            ->findOrFail($id);

        return view('pengelola.pemesanan-offline.sukses', compact('pemesanan'));
    }

    public function riwayat(BookingFilters $request)
    {
        $pemesanan = Pemesanan::with(['tiket', 'pembayaran', 'detailPemesanan'])
            ->where('kode_booking', 'like', 'BJ-OFF-%')->filter($request->validated())
            ->orderBy('created_at', 'desc')
            ->paginate(20)->withQueryString();

        return view('pengelola.pemesanan-offline.riwayat', compact('pemesanan'));
    }
}
