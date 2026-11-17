<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingFilters;
use App\Mail\PemesananBerhasil;
use App\Models\Pemesanan;
use App\Models\Tiket;
use App\Services\BookingCheckout;
use App\Services\BookingLifecycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PemesananController extends Controller
{
    public function create(Request $request)
    {
        $tiketList = Tiket::aktif()->get();
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $selectedId = $request->input('id_tiket', null);

        return view('wisatawan.pemesanan', compact('tiketList', 'tanggal', 'selectedId'));
    }

    /**
     * Step 1: Validate ticket selection, store in session, redirect to data-diri.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tgl_kunjungan' => ['required', 'date', 'after_or_equal:today'],
            'tickets' => ['required', 'array', 'min:1'],
            'tickets.*' => ['integer', 'min:0'],
        ], [
            'tgl_kunjungan.required' => 'Tanggal kunjungan wajib diisi.',
            'tgl_kunjungan.after_or_equal' => 'Tanggal kunjungan tidak boleh sebelum hari ini.',
            'tickets.required' => 'Pilih minimal satu tiket.',
            'tickets.min' => 'Pilih minimal satu tiket.',
        ]);

        $tanggal = $request->tgl_kunjungan;
        $tickets = array_filter($request->tickets, fn ($q) => $q > 0);

        if (empty($tickets)) {
            return back()->withErrors(['tickets' => 'Pilih minimal satu tiket.'])->withInput();
        }

        // Build detail & generate kode_tiket (temporary, before saving)
        $detail = [];
        $detailRows = [];
        $total_harga = 0;

        foreach ($tickets as $id_tiket => $jumlah) {
            $tiket = Tiket::aktif()->find($id_tiket);
            if (! $tiket) {
                return back()->withErrors(['tickets' => 'Tiket yang dipilih tidak tersedia. Silakan pilih kembali.'])->withInput();
            }

            $subtotal = $tiket->harga * $jumlah;
            $items = [];

            for ($i = 0; $i < $jumlah; $i++) {
                $kode = 'BJ-'.strtoupper(Str::random(16));
                $items[] = ['kode' => $kode];
                $detailRows[] = [
                    'id_tiket' => (int) $id_tiket,
                    'kode_tiket' => $kode,
                    'nama_tiket' => $tiket->nama_tiket,
                    'kategori' => $tiket->kategori,
                    'harga' => (int) $tiket->harga,
                    'nama_pengunjung' => null,
                    'plat_kendaraan' => null,
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

        $kode_booking = 'BJ-'.strtoupper(Str::random(16));

        // Save to session
        session()->put('pemesanan_flow', [
            'issued_at' => now()->toIso8601String(),
            'draft_token' => (string) Str::uuid(),
            'tgl_kunjungan' => $tanggal,
            'detail' => $detail,
            'detailRows' => $detailRows,
            'total_harga' => $total_harga,
            'kode_booking' => $kode_booking,
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
        if (! $flow) {
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
        if (! $flow) {
            $last = Pemesanan::where('id_user', Auth::id())->find(session('last_booking_id'));

            return $last ? redirect()->route('wisatawan.pemesanan.detail', $last->id_pemesanan)
                : redirect()->route('wisatawan.pemesanan.create')->with('error', __('operations.draft_expired'));
        }
        $pemesanan = app(BookingCheckout::class)->finish($flow, $request);
        session()->put('last_booking_id', $pemesanan->id_pemesanan);
        session()->forget('pemesanan_flow');
        if ($pemesanan->wasRecentlyCreated) {
            try {
                Mail::to(Auth::user()->email)->send(new PemesananBerhasil($pemesanan));
            } catch (\Throwable $e) {
                \Log::warning('Booking mail enqueue failed.', ['exception_type' => get_class($e)]);
            }
        }

        return redirect()->route('wisatawan.pembayaran.create', $pemesanan->id_pemesanan)
            ->with('success', __('operations.booking_created'));
    }

    public function cancel($id)
    {
        $booking = Pemesanan::where('id_user', Auth::id())->findOrFail($id);
        app(BookingLifecycle::class)->cancel($booking);

        return redirect()->route('wisatawan.pemesanan.detail', $id)->with('success', __('operations.booking_cancelled'));
    }

    public function sukses($id)
    {
        $pemesanan = Pemesanan::with(['tiket', 'pembayaran', 'detailPemesanan'])
            ->where('id_user', Auth::id())
            ->findOrFail($id);

        return view('wisatawan.pemesanan-sukses', compact('pemesanan'));
    }

    public function riwayat(BookingFilters $request)
    {
        $pemesanan = Pemesanan::with(['tiket', 'pembayaran', 'detailPemesanan'])
            ->where('id_user', Auth::id())->filter($request->validated())
            ->orderByDesc('id_pemesanan')->paginate(15)->withQueryString();

        return view('wisatawan.riwayat', compact('pemesanan'));
    }

    public function status($id)
    {
        $booking = Pemesanan::with('pembayaran')->where('id_user', Auth::id())->findOrFail($id);

        app(BookingLifecycle::class)->refresh($booking);

        return response()->json([
            'token' => $booking->statusToken(),
            'approved' => $booking->isApproved(),
        ])->header('Cache-Control', 'no-store, private');
    }

    public function statuses()
    {
        $bookings = Pemesanan::with('pembayaran')->where('id_user', Auth::id())
            ->when(request()->filled('ids'), fn ($q) => $q->whereIn('id_pemesanan', array_slice(array_filter(explode(',', (string) request('ids')), 'ctype_digit'), 0, 30)))
            ->latest('id_pemesanan')->limit(30)->get();

        foreach ($bookings as $booking) {
            if ($booking->isOverdue() && $booking->pembayaran?->status !== 'pending') {
                app(BookingLifecycle::class)->refresh($booking);
            }
        }

        return response()->json($bookings->mapWithKeys(fn ($booking) => [
            $booking->id_pemesanan => $booking->statusToken(),
        ])->all())->header('Cache-Control', 'no-store, private');
    }

    public function detailPemesanan($id)
    {
        $pemesanan = Pemesanan::with(['tiket', 'pembayaran', 'user', 'detailPemesanan'])
            ->where('id_user', Auth::id())
            ->findOrFail($id);
        app(BookingLifecycle::class)->refresh($pemesanan);
        $pemesanan->load('events');

        return view('wisatawan.pemesanan-detail', compact('pemesanan'));
    }
}
