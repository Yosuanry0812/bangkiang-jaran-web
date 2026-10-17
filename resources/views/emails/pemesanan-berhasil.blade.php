@extends('emails.layout')
@section('title', 'Pemesanan dibuat')
@section('content')
<p style="margin:0 0 12px;color:#a46147;font-size:11px;font-weight:bold;letter-spacing:2px;text-transform:uppercase;">PEMESANAN DIBUAT</p>
<h1 style="margin:0 0 20px;color:#173d34;font-family:Georgia,serif;font-size:36px;font-weight:normal;line-height:1.13;">Kunjungan Anda mulai terencana.</h1>
<p style="margin:0 0 23px;color:#516059;font-size:14px;line-height:1.8;">Halo {{ $pemesanan->user->name }}, pesanan Anda sudah tercatat. Simpan kode di bawah dan selesaikan pembayaran melalui halaman akun.</p>
<div style="padding:20px 22px;background:#eae7dc;border-left:3px solid #a46147;">
    <span style="display:block;margin-bottom:7px;color:#66736c;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;">Kode pemesanan</span>
    <strong style="color:#173d34;font-family:Georgia,serif;font-size:25px;font-weight:normal;">{{ $pemesanan->kode_booking }}</strong>
</div>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:22px 0 25px;border-bottom:1px solid #ded8cd;">
    <tr><td style="padding:11px 0;border-bottom:1px solid #ded8cd;color:#66736c;font-size:12px;">Tanggal kunjungan</td><td align="right" style="padding:11px 0;border-bottom:1px solid #ded8cd;color:#202b27;font-size:13px;">{{ \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->format('d/m/Y') }}</td></tr>
    <tr><td style="padding:11px 0;border-bottom:1px solid #ded8cd;color:#66736c;font-size:12px;">Jumlah tiket</td><td align="right" style="padding:11px 0;border-bottom:1px solid #ded8cd;color:#202b27;font-size:13px;">{{ $pemesanan->detailPemesanan->count() }}</td></tr>
    <tr><td style="padding:11px 0;color:#66736c;font-size:12px;">Total</td><td align="right" style="padding:11px 0;color:#173d34;font-size:16px;font-weight:bold;">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</td></tr>
</table>
<p style="margin:0 0 25px;color:#516059;font-size:13px;line-height:1.75;">Tiket dapat digunakan setelah bukti pembayaran diperiksa dan disetujui pengelola.</p>
<a href="{{ route('wisatawan.pemesanan.detail', $pemesanan->id_pemesanan) }}" style="display:inline-block;padding:15px 24px;background:#173d34;color:#fff;text-decoration:none;font-size:13px;font-weight:bold;">Lihat pesanan →</a>
@endsection
