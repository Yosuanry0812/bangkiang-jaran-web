<?php

namespace App\Services;

use App\Models\DetailPemesanan;
use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Laporan pendapatan berdasarkan tanggal bayar pembayaran yang valid.
 */
class RevenueReport
{
    public const CATEGORIES = ['dewasa', 'anak', 'wisman', 'lainnya'];

    public function __construct(
        public readonly string $from,
        public readonly string $to,
        public readonly ?string $channel = null,
    ) {}

    /** Pembayaran valid pada pesanan selesai dalam periode (tanggal bayar). */
    public function payments(): Builder
    {
        return Pembayaran::query()
            ->where('status', 'valid')
            ->whereHas('pemesanan', fn ($q) => $q->where('status', 'selesai')->filter(['channel' => $this->channel]))
            ->whereBetween('tgl_bayar', [$this->from, $this->to]);
    }

    /** Petakan nama tiket ke kategori laporan; tiket tambahan masuk "lainnya". */
    public static function category(?string $name): string
    {
        $name = mb_strtolower((string) $name);

        return match (true) {
            str_contains($name, 'mancanegara'), str_contains($name, 'international'), str_contains($name, 'wisman') => 'wisman',
            str_contains($name, 'anak'), str_contains($name, 'child') => 'anak',
            str_contains($name, 'dewasa'), str_contains($name, 'adult') => 'dewasa',
            default => 'lainnya',
        };
    }

    /** @return array<string, array{count: int, revenue: int}> kunci = kategori */
    public function breakdown(): array
    {
        $result = array_fill_keys(self::CATEGORIES, ['count' => 0, 'revenue' => 0]);
        $rows = DetailPemesanan::query()
            ->whereIn('id_pemesanan', $this->payments()->select('id_pemesanan'))
            ->selectRaw('nama_tiket, COUNT(*) as jumlah, SUM(harga) as pendapatan')
            ->groupBy('nama_tiket')->get();
        foreach ($rows as $row) {
            $key = self::category($row->nama_tiket);
            $result[$key]['count'] += (int) $row->jumlah;
            $result[$key]['revenue'] += (int) $row->pendapatan;
        }

        return $result;
    }

    public function totalRevenue(): int
    {
        return (int) $this->payments()->sum('total');
    }

    public function transactionCount(): int
    {
        return $this->payments()->count();
    }

    public static function forMonth(string $month, ?string $channel = null): self
    {
        $start = Carbon::createFromFormat('Y-m-d', $month.'-01')->startOfDay();

        return new self($start->toDateString(), $start->copy()->endOfMonth()->toDateString(), $channel);
    }

    /**
     * Data per tanggal untuk satu bulan penuh (grafik). Tanggal setelah hari ini bernilai null.
     *
     * @return array<int, array{tgl: string, hari: int, pendapatan: ?int, tiket: ?array<string,int>}>
     */
    public function daily(): array
    {
        $start = Carbon::parse($this->from)->startOfDay();
        $end = Carbon::parse($this->to)->startOfDay();
        $today = now()->startOfDay();
        $revenue = [];
        $tickets = [];
        foreach ($this->payments()->with('pemesanan.detailPemesanan')->lazyById(200, 'id_bayar') as $p) {
            $key = Carbon::parse($p->tgl_bayar)->toDateString();
            $revenue[$key] = ($revenue[$key] ?? 0) + (int) $p->total;
            foreach ($p->pemesanan->detailPemesanan as $detail) {
                $cat = self::category($detail->nama_tiket);
                $tickets[$key][$cat] = ($tickets[$key][$cat] ?? 0) + 1;
            }
        }
        $days = [];
        for ($d = $start->copy(); $d <= $end; $d->addDay()) {
            $key = $d->toDateString();
            $future = $d > $today;
            $days[] = ['tgl' => $key, 'hari' => (int) $d->format('j'), 'dow' => (int) $d->format('w'),
                'pendapatan' => $future ? null : ($revenue[$key] ?? 0),
                'tiket' => $future ? null : array_replace(array_fill_keys(self::CATEGORIES, 0), $tickets[$key] ?? [])];
        }

        return $days;
    }

    /** Nama tiket yang benar-benar terbayar pada periode (urut: Dewasa, Anak, Wisman, lalu lainnya A-Z). */
    public function ticketNames(): array
    {
        $names = DetailPemesanan::query()
            ->whereIn('id_pemesanan', $this->payments()->select('id_pemesanan'))
            ->distinct()->pluck('nama_tiket')->all();
        $rank = array_flip(self::CATEGORIES);
        usort($names, fn ($a, $b) => [$rank[self::category($a)], $a] <=> [$rank[self::category($b)], $b]);

        return $names;
    }
}
