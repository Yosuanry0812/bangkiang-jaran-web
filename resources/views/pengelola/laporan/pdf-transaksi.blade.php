<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi Bangkiang Jaran Waterfall</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background: #0d9488; color: white; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 18px; margin: 0 0 5px; }
        .header p { font-size: 12px; margin: 2px 0; color: #555; }
        .footer { margin-top: 20px; font-size: 11px; color: #777; text-align: right; }
        .grand-total { font-weight: bold; font-size: 14px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Transaksi</h1>
        <p>Bangkiang Jaran Waterfall</p>
        <p>Periode: {{ request('periode_awal') ?? 'Semua' }} - {{ request('periode_akhir') ?? 'Semua' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Booking</th>
                <th>User</th>
                <th>Tiket</th>
                <th>Tgl Bayar</th>
                <th>Metode</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data ?? [] as $i => $t)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $t->pemesanan->kode_booking ?? '-' }}</td>
                <td>{{ $t->pemesanan->user->name ?? '-' }}</td>
                <td>{{ $t->pemesanan->tiket->nama_tiket ?? '-' }}</td>
                <td>{{ $t->created_at ? \Carbon\Carbon::parse($t->created_at)->format('d/m/Y') : '-' }}</td>
                <td>{{ $t->metode ?? '-' }}</td>
                <td>Rp {{ number_format($t->total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="grand-total">Grand Total: Rp {{ number_format($total ?? 0, 0, ',', '.') }}</div>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
