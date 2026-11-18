@extends('emails.layout')
@section('title', __('mail.title_welcome'))
@section('content')
<p style="margin:0 0 12px;color:#a46147;font-size:11px;font-weight:bold;letter-spacing:2px;text-transform:uppercase;">{{ __('panel.akun_pengunjung_3c06') }}</p>
<h1 style="margin:0 0 20px;color:#173d34;font-family:Georgia,serif;font-size:36px;font-weight:normal;line-height:1.13;">{{ __('mail.welcome_title', ['name' => $user->name]) }}</h1>
<p style="margin:0 0 22px;color:#516059;font-size:14px;line-height:1.8;">{{ __('panel.akun_bangkiang_jaran_anda_11ad') }}</p>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-top:1px solid #ded8cd;border-bottom:1px solid #ded8cd;margin:25px 0;">
    <tr><td style="padding:13px 0;color:#66736c;font-size:12px;">{{ __('panel.nama_pengguna_9c06') }}</td><td align="right" style="padding:13px 0;color:#202b27;font-size:13px;font-weight:bold;">{{ $user->username }}</td></tr>
    <tr><td style="padding:0 0 13px;color:#66736c;font-size:12px;">Email</td><td align="right" style="padding:0 0 13px;color:#202b27;font-size:13px;">{{ $user->email }}</td></tr>
</table>
<a href="{{ route('tiket.index') }}" style="display:inline-block;padding:15px 24px;background:#173d34;color:#fff;text-decoration:none;font-size:13px;font-weight:bold;">{{ __('panel.lihat_tiket_kunjungan_eb54') }}</a>
@endsection
