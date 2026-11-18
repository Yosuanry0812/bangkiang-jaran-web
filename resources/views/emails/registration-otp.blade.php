@extends('emails.layout')
@section('title', __('mail.title_otp'))
@section('content')
<h1 style="margin:0 0 18px;font-family:Georgia,serif;font-size:28px;color:#173d34;">{{ __('panel.verifikasi_email_anda_e7e4') }}</h1>
<p style="line-height:1.7;">{{ __('mail.otp_intro', ['name' => $name]) }}</p>
<p style="margin:28px 0;padding:20px;text-align:center;font-size:32px;font-weight:bold;letter-spacing:8px;color:#173d34;background:#f1f5ef;">{{ $code }}</p>
<p style="line-height:1.7;">{!! __('mail.otp_expiry', ['minutes' => '<strong>'.e(__('mail.otp_minutes')).'</strong>']) !!}</p>
<p style="line-height:1.7;color:#66736c;">{{ __('panel.jika_anda_tidak_mendaftar_d73c') }}</p>
@endsection
