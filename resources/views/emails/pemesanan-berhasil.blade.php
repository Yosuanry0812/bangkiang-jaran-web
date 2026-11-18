@extends('emails.layout')
@section('title', __('mail.title_created'))
@section('content')
<p style="margin:0 0 12px;color:#a46147;font-size:11px;font-weight:bold;letter-spacing:2px;text-transform:uppercase;">{{ __('panel.pemesanan_dibuat_0162') }}</p>
<h1 style="margin:0 0 20px;color:#173d34;font-family:Georgia,serif;font-size:36px;font-weight:normal;line-height:1.13;">{{ __('panel.kunjungan_anda_mulai_terenca_4abd') }}</h1>
<p style="margin:0 0 23px;color:#516059;font-size:14px;line-height:1.8;">{{ __('mail.created_intro', ['name' => $pemesanan->user->name]) }}</p>
<div style="padding:20px 22px;background:#eae7dc;border-left:3px solid #a46147;">
    <span style="display:block;margin-bottom:7px;color:#66736c;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;">{{ __('panel.kode_pemesanan_040c') }}</span>
    <strong style="color:#173d34;font-family:Georgia,serif;font-size:25px;font-weight:normal;">{{ $pemesanan->kode_booking }}</strong>
</div>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:22px 0 25px;border-bottom:1px solid #ded8cd;">
    <tr><td style="padding:11px 0;border-bottom:1px solid #ded8cd;color:#66736c;font-size:12px;">{{ __('panel.tanggal_kunjungan_0dff') }}</td><td align="right" style="padding:11px 0;border-bottom:1px solid #ded8cd;color:#202b27;font-size:13px;">{{ \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->format('d/m/Y') }}</td></tr>
    <tr><td style="padding:11px 0;border-bottom:1px solid #ded8cd;color:#66736c;font-size:12px;">{{ __('panel.jumlah_tiket_334d') }}</td><td align="right" style="padding:11px 0;border-bottom:1px solid #ded8cd;color:#202b27;font-size:13px;">{{ $pemesanan->detailPemesanan->count() }}</td></tr>
    <tr><td style="padding:11px 0;color:#66736c;font-size:12px;">{{ __('panel.total_96b0') }}</td><td align="right" style="padding:11px 0;color:#173d34;font-size:16px;font-weight:bold;">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</td></tr>
</table>
<p style="margin:0 0 25px;color:#516059;font-size:13px;line-height:1.75;">{{ __('panel.tiket_dapat_digunakan_setela_f272') }}</p>
<a href="{{ route('wisatawan.pemesanan.detail', $pemesanan->id_pemesanan) }}" style="display:inline-block;padding:15px 24px;background:#173d34;color:#fff;text-decoration:none;font-size:13px;font-weight:bold;">{{ __('panel.lihat_pesanan_e64d') }}</a>
@endsection
