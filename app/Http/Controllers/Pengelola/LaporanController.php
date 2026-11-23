<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Services\XlsxExport;
use App\Services\RevenueReport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    private function month(Request $request): string
    {
        $request->validate(['bulan' => ['nullable', 'date_format:Y-m']]);

        return $request->input('bulan') ?: now()->format('Y-m');
    }

    public function index(Request $request)
    {
        $bulan = $this->month($request);
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,selesai,dibatalkan,expired'],
            'channel' => ['nullable', 'in:online,offline'],
        ]);
        $report = RevenueReport::forMonth($bulan);
        $start = Carbon::parse($report->from);
        $breakdown = $report->breakdown();

        $bookings = Pemesanan::with(['user', 'pembayaran', 'detailPemesanan'])
            ->whereBetween('created_at', [$start->copy()->startOfDay(), $start->copy()->endOfMonth()->endOfDay()])
            ->filter($filters)->latest('id_pemesanan')->paginate(15)->withQueryString();

        return view('pengelola.laporan.index', [
            'bulan' => $bulan,
            'prevMonth' => $start->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $start->copy()->addMonth()->format('Y-m'),
            'monthLabel' => __('report.months')[$start->month - 1].' '.$start->year,
            'breakdown' => $breakdown,
            'visitorTotal' => array_sum(array_column($breakdown, 'count')),
            'chart' => $this->chartPayload($report, $start),
            'bookings' => $bookings,
            'filters' => $filters,
        ]);
    }

    /** Data grafik; dipanggil berkala oleh halaman agar tampil realtime. */
    public function data(Request $request)
    {
        $bulan = $this->month($request);
        $report = RevenueReport::forMonth($bulan);

        return response()->json($this->chartPayload($report, Carbon::parse($report->from)))
            ->header('Cache-Control', 'no-store');
    }

    private function chartPayload(RevenueReport $report, Carbon $start): array
    {
        $revenue = $report->totalRevenue();
        $count = $report->transactionCount();
        $breakdown = $report->breakdown();

        return [
            'bulan' => $start->format('Y-m'),
            'monthLabel' => __('report.months')[$start->month - 1].' '.$start->year,
            'monthShort' => __('report.months_short')[$start->month - 1],
            'days' => $report->daily(),
            'revenue' => $revenue,
            'transactions' => $count,
            'visitors' => array_sum(array_column($breakdown, 'count')),
            'average' => $count ? intdiv($revenue, $count) : 0,
            'breakdown' => $breakdown,
            'updatedAt' => now()->format('H:i:s'),
        ];
    }

    public function xlsx(Request $request, XlsxExport $xlsx)
    {
        $bulan = $this->month($request);
        $report = RevenueReport::forMonth($bulan);
        $names = $report->ticketNames();

        $headers = [__('report.col_no'), __('report.col_booking'), __('report.col_channel'), __('report.col_name'),
            __('report.col_paid_on'), __('report.col_visit_date'), __('report.col_method')];
        foreach ($names as $name) {
            $headers[] = $name.' ('.__('report.unit_people').')';
        }
        $headers[] = __('report.col_visitors_total');
        foreach ($names as $name) {
            $headers[] = $name.' (Rp)';
        }
        $headers[] = __('report.col_total').' (Rp)';

        $rows = (function () use ($report, $names) {
            $count = array_fill_keys($names, 0);
            $money = array_fill_keys($names, 0);
            $grand = 0;
            $people = 0;
            $no = 0;
            $query = $report->payments()->with(['pemesanan.user', 'pemesanan.detailPemesanan'])->orderBy('tgl_bayar')->orderBy('id_bayar');
            foreach ($query->lazyById(200, 'id_bayar') as $p) {
                $b = $p->pemesanan;
                $perName = [];
                foreach ($b->detailPemesanan as $d) {
                    $perName[$d->nama_tiket]['n'] = ($perName[$d->nama_tiket]['n'] ?? 0) + 1;
                    $perName[$d->nama_tiket]['rp'] = ($perName[$d->nama_tiket]['rp'] ?? 0) + (int) $d->harga;
                }
                $checkIn = $b->detailPemesanan->whereNotNull('check_in_at')->min('check_in_at');
                $row = [++$no, $b->kode_booking, __('report.channel_'.(str_starts_with($b->kode_booking, 'BJ-OFF-') ? 'offline' : 'online')),
                    $b->user?->name ?? '-', Carbon::parse($p->tgl_bayar)->startOfDay(),
                    $checkIn ? Carbon::parse($checkIn)->startOfDay() : __('report.not_checked_in'), $p->metode ?? '-'];
                $rowPeople = 0;
                foreach ($names as $name) {
                    $n = $perName[$name]['n'] ?? 0;
                    $row[] = $n;
                    $count[$name] += $n;
                    $rowPeople += $n;
                }
                $row[] = $rowPeople;
                $people += $rowPeople;
                foreach ($names as $name) {
                    $rp = $perName[$name]['rp'] ?? 0;
                    $row[] = $rp;
                    $money[$name] += $rp;
                }
                $row[] = (int) $p->total;
                $grand += (int) $p->total;
                yield $row;
            }
            $total = [__('report.row_total'), '', '', '', '', '', ''];
            foreach ($names as $name) {
                $total[] = $count[$name];
            }
            $total[] = $people;
            foreach ($names as $name) {
                $total[] = $money[$name];
            }
            $total[] = $grand;
            yield $total;
        })();

        return $xlsx->download('laporan-pendapatan-'.$bulan.'.xlsx', $headers, $rows);
    }

}
