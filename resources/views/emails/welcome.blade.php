@extends('emails.layout')
@section('title', 'Akun Anda siap')
@section('content')
<p style="margin:0 0 12px;color:#a46147;font-size:11px;font-weight:bold;letter-spacing:2px;text-transform:uppercase;">AKUN PENGUNJUNG</p>
<h1 style="margin:0 0 20px;color:#173d34;font-family:Georgia,serif;font-size:36px;font-weight:normal;line-height:1.13;">Selamat datang, {{ $user->name }}.</h1>
<p style="margin:0 0 22px;color:#516059;font-size:14px;line-height:1.8;">Akun Bangkiang Jaran Anda sudah dibuat. Dari sini Anda bisa melihat tiket, merencanakan tanggal kunjungan, dan memantau pesanan.</p>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-top:1px solid #ded8cd;border-bottom:1px solid #ded8cd;margin:25px 0;">
    <tr><td style="padding:13px 0;color:#66736c;font-size:12px;">Nama pengguna</td><td align="right" style="padding:13px 0;color:#202b27;font-size:13px;font-weight:bold;">{{ $user->username }}</td></tr>
    <tr><td style="padding:0 0 13px;color:#66736c;font-size:12px;">Email</td><td align="right" style="padding:0 0 13px;color:#202b27;font-size:13px;">{{ $user->email }}</td></tr>
</table>
<a href="{{ route('tiket.index') }}" style="display:inline-block;padding:15px 24px;background:#173d34;color:#fff;text-decoration:none;font-size:13px;font-weight:bold;">Lihat tiket kunjungan →</a>
@endsection
