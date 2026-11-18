@extends('emails.layout')
@section('title', __('mail.title_rejected'))
@section('content')
<p style="margin:0 0 12px;color:#a46147;font-size:11px;font-weight:bold;letter-spacing:2px;text-transform:uppercase;">{{ __('panel.tindakan_diperlukan_5368') }}</p>
<h1 style="margin:0 0 20px;color:#173d34;font-family:Georgia,serif;font-size:36px;font-weight:normal;line-height:1.13;">{{ __('panel.bukti_pembayaran_perlu_diper_cf1f') }}</h1>
<p style="margin:0 0 22px;color:#516059;font-size:14px;line-height:1.8;">{!! __('mail.rejected_intro', ['name' => e($pemesanan->user->name), 'code' => '<strong>'.e($pemesanan->kode_booking).'</strong>']) !!}</p>
<div style="padding:18px 22px;margin-bottom:25px;background:#f3ece3;border-left:3px solid #a46147;color:#5d4b41;font-size:13px;line-height:1.7;">{{ __('panel.status_tiket_tetap_menunggu_1521') }}</div>
<a href="{{ route('wisatawan.pembayaran.create', $pemesanan->id_pemesanan) }}" style="display:inline-block;padding:15px 24px;background:#173d34;color:#fff;text-decoration:none;font-size:13px;font-weight:bold;">{{ __('panel.unggah_bukti_baru_d8f8') }}</a>
@endsection
