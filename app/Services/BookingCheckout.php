<?php

namespace App\Services;

use App\Models\DetailPemesanan;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\Tiket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingCheckout
{
    public function finish(array $flow, Request $request, bool $offline = false): Pemesanan
    {
        $rules = [];
        foreach ($flow['detailRows'] as $i => $row) {
            $rules['nama_'.$i] = ['required', 'string', 'max:100'];
            $rules['gender_'.$i] = ['required', 'in:L,P'];
        }
        if ($offline) {
            $rules['metode_bayar'] = ['required', 'in:Tunai,BCA,BRI,Mandiri,Dana,OVO,GoPay,QRIS'];
        }
        $request->validate($rules);

        return DB::transaction(function () use ($flow, $request, $offline) {
            $quota = app(VisitQuota::class)->lock($flow['tgl_kunjungan']);
            $token = $flow['draft_token'] ?? (string) Str::uuid();
            $existing = Pemesanan::where('draft_token', $token)->where('id_user', $request->user()->id)->first();
            if ($existing) {
                return $existing;
            }
            Validator::make($flow, ['tgl_kunjungan' => $offline ? ['required', 'date'] : ['required', 'date', 'after_or_equal:today']])->validate();
            if (isset($flow['issued_at']) && Carbon::parse($flow['issued_at'])->addHour()->lte(now())) {
                throw ValidationException::withMessages(['booking' => __('operations.draft_expired')]);
            }
            $tickets = Tiket::aktif()->whereIn('id_tiket', array_column($flow['detail'], 'id_tiket'))
                ->orderBy('id_tiket')->lockForUpdate()->get()->keyBy('id_tiket');
            $total = 0;
            $count = 0;
            foreach ($flow['detail'] as $line) {
                $ticket = $tickets->get($line['id_tiket']);
                if (! $ticket || (float) $ticket->harga !== (float) (int) $ticket->harga || (int) $ticket->harga !== (int) $line['harga']) {
                    throw ValidationException::withMessages(['booking' => __('operations.selection_changed')]);
                }
                $count += $line['jumlah'];
                $total += (int) $ticket->harga * $line['jumlah'];
            }
            if ($count < 1 || $total > 99999999) {
                throw ValidationException::withMessages(['booking' => __('operations.invalid_total')]);
            }
            app(VisitQuota::class)->assertAvailable($quota, $count);
            $deadline = now()->addMinutes((int) app(DestinationSettings::class)->values()['payment_minutes']);
            $visitEnd = Carbon::parse($flow['tgl_kunjungan'])->endOfDay();
            $booking = Pemesanan::create([
                'id_user' => $request->user()->id, 'id_tiket' => $flow['detail'][0]['id_tiket'],
                'tgl_kunjungan' => $flow['tgl_kunjungan'], 'jumlah' => $count, 'total_harga' => $total,
                'detail' => $flow['detail'], 'status' => $offline ? 'selesai' : 'pending',
                'kode_booking' => $flow['kode_booking'], 'draft_token' => $token,
                'expires_at' => $offline ? null : $deadline->min($visitEnd),
            ]);
            foreach ($flow['detailRows'] as $i => $row) {
                DetailPemesanan::create([
                    'id_pemesanan' => $booking->id_pemesanan, 'id_tiket' => $row['id_tiket'],
                    'kode_tiket' => $row['kode_tiket'], 'nama_tiket' => $row['nama_tiket'],
                    'nama_pengunjung' => $request->input('nama_'.$i), 'jenis_kelamin' => $request->input('gender_'.$i),
                    'harga' => $row['harga'], 'status_tiket' => 'aktif',
                ]);
            }
            $booking->recordEvent('created');
            if ($offline) {
                Pembayaran::create(['id_pemesanan' => $booking->id_pemesanan, 'total' => $total,
                    'metode' => $request->metode_bayar, 'status' => 'valid', 'tgl_bayar' => now()->toDateString(),
                    'verified_at' => now(), 'proof_disk' => 'private']);
                $booking->recordEvent('offline_paid');
                AuditTrail::record('Pemesanan loket', $booking->kode_booking);
            }

            return $booking;
        }, 3);
    }
}
