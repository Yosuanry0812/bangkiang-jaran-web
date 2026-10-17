@extends('emails.layout')
@section('title', 'Bukti pembayaran perlu diperbarui')
@section('content')
<p style="margin:0 0 12px;color:#a46147;font-size:11px;font-weight:bold;letter-spacing:2px;text-transform:uppercase;">TINDAKAN DIPERLUKAN</p>
<h1 style="margin:0 0 20px;color:#173d34;font-family:Georgia,serif;font-size:36px;font-weight:normal;line-height:1.13;">Bukti pembayaran perlu diperbarui.</h1>
<p style="margin:0 0 22px;color:#516059;font-size:14px;line-height:1.8;">Halo {{ $pemesanan->user->name }}, bukti pembayaran untuk pesanan <strong>{{ $pemesanan->kode_booking }}</strong> belum dapat disetujui. Silakan periksa kembali detail transfer, lalu unggah bukti yang jelas melalui akun Anda.</p>
<div style="padding:18px 22px;margin-bottom:25px;background:#f3ece3;border-left:3px solid #a46147;color:#5d4b41;font-size:13px;line-height:1.7;">Status tiket tetap menunggu sampai bukti pembayaran baru diverifikasi.</div>
<a href="{{ route('wisatawan.pembayaran.create', $pemesanan->id_pemesanan) }}" style="display:inline-block;padding:15px 24px;background:#173d34;color:#fff;text-decoration:none;font-size:13px;font-weight:bold;">Unggah bukti baru →</a>
@endsection
