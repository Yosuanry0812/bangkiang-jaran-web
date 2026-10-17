@extends('emails.layout')
@section('title', 'Pembayaran terverifikasi')
@section('content')
<p style="margin:0 0 12px;color:#a46147;font-size:11px;font-weight:bold;letter-spacing:2px;text-transform:uppercase;">PEMBAYARAN TERVERIFIKASI</p>
<h1 style="margin:0 0 20px;color:#173d34;font-family:Georgia,serif;font-size:36px;font-weight:normal;line-height:1.13;">Tiket Anda siap digunakan.</h1>
<p style="margin:0 0 23px;color:#516059;font-size:14px;line-height:1.8;">Halo {{ $pemesanan->user->name }}, pembayaran untuk pesanan <strong>{{ $pemesanan->kode_booking }}</strong> telah disetujui. Tunjukkan kode tiket masing-masing pengunjung saat masuk.</p>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 0 25px;border-collapse:collapse;">
    <tr style="background:#eae7dc;"><th align="left" style="padding:11px;color:#173d34;font-size:11px;">Tiket</th><th align="left" style="padding:11px;color:#173d34;font-size:11px;">Pengunjung / Plat</th><th align="left" style="padding:11px;color:#173d34;font-size:11px;">Kode</th></tr>
    @foreach($pemesanan->detailPemesanan as $item)
    <tr><td style="padding:11px;border-bottom:1px solid #ded8cd;color:#516059;font-size:12px;">{{ $item->nama_tiket }}</td><td style="padding:11px;border-bottom:1px solid #ded8cd;color:#516059;font-size:12px;">{{ $item->plat_kendaraan ?? $item->nama_pengunjung ?? '—' }}</td><td style="padding:11px;border-bottom:1px solid #ded8cd;color:#173d34;font-size:12px;font-weight:bold;">{{ $item->kode_tiket }}</td></tr>
    @endforeach
</table>
<p style="margin:0 0 25px;color:#516059;font-size:13px;line-height:1.75;">Tanggal kunjungan: <strong>{{ \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->format('d/m/Y') }}</strong>. Setiap tiket memiliki kode tersendiri.</p>
<a href="{{ route('wisatawan.pemesanan.detail', $pemesanan->id_pemesanan) }}" style="display:inline-block;padding:15px 24px;background:#173d34;color:#fff;text-decoration:none;font-size:13px;font-weight:bold;">Buka e-ticket →</a>
@endsection
