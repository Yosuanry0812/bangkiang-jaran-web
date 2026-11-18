<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingFilters;
use App\Models\ActivityLog;
use App\Models\DetailPemesanan;
use App\Models\Pemesanan;
use App\Services\AuditTrail;
use App\Services\DestinationSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OperationsController extends Controller
{
    public function bookings(BookingFilters $request)
    {
        $pemesanan = Pemesanan::with(['user', 'pembayaran', 'detailPemesanan'])
            ->filter($request->validated())->latest('id_pemesanan')->paginate(20)->withQueryString();

        return view('pengelola.operasional.pesanan', compact('pemesanan'));
    }

    public function onsite(BookingFilters $request)
    {
        $filters = $request->validated();
        $term = $filters['q'] ?? null;
        unset($filters['q']);
        $query = DetailPemesanan::with('pemesanan.user')->whereNotNull('check_in_at')->whereNull('check_out_at')
            ->whereHas('pemesanan', fn ($q) => $q->approved()->filter($filters))
            ->when($term, fn ($q) => $q->where(fn ($search) => $search->where('nama_pengunjung', 'like', '%'.$term.'%')->orWhereHas('pemesanan', fn ($b) => $b->where('kode_booking', 'like', '%'.$term.'%'))));
        $total = (clone $query)->count();
        $visitors = $query->orderBy('check_in_at')->paginate(25)->withQueryString();

        return view('pengelola.operasional.di-lokasi', compact('visitors', 'total'));
    }

    public function audit(Request $request)
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', ...($request->filled('from') ? ['after_or_equal:from'] : [])]]);
        $logs = ActivityLog::with('user')->when($filters['q'] ?? null, fn ($q, $term) => $q->where('aktivitas', 'like', '%'.$term.'%'))
            ->when($filters['from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($filters['to'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->latest('id_log')->paginate(30)->withQueryString();

        return view('pengelola.operasional.audit', compact('logs'));
    }

    public function settings(DestinationSettings $destination)
    {
        return view('pengelola.operasional.pengaturan', ['settings' => $destination->values(),
            'queuedMail' => DB::table('jobs')->where('queue', 'mail')->count(),
            'failedMail' => DB::table('failed_jobs')->where('queue', 'mail')->count()]);
    }

    public function updateSettings(Request $request, DestinationSettings $destination)
    {
        $values = $request->validate([
            'operating_hours' => ['required', 'string', 'max:120'], 'operating_hours_en' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:255'], 'address_en' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'], 'whatsapp' => ['nullable', 'string', 'regex:/^[0-9]{8,20}$/'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9 +().-]+$/'],
            'maps_url' => ['required', 'url:http,https', 'max:1500'],
            'maps_embed' => ['required', 'url:https', 'max:1500', 'regex:~^https://www\.google\.com/maps(?:[/?])~'],
            'rules' => ['nullable', 'string', 'max:5000'], 'rules_en' => ['nullable', 'string', 'max:5000'],
            'payment_minutes' => ['required', 'integer', 'min:15', 'max:1440'],
        ]);
        DB::transaction(function () use ($destination, $values) {
            $destination->save($values);
            AuditTrail::record('Ubah pengaturan destinasi', implode(', ', array_keys($values)));
        });

        return back()->with('success', __('operations.saved'));
    }
}
