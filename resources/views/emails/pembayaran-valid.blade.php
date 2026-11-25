@extends('emails.layout')
@section('title', __('mail.title_valid'))
@section('content')
<p style="margin:0 0 12px;color:#a46147;font-size:11px;font-weight:bold;letter-spacing:2px;text-transform:uppercase;">{{ __('panel.pembayaran_terverifikasi_3ab8') }}</p>
<h1 style="margin:0 0 20px;color:#173d34;font-family:Georgia,serif;font-size:36px;font-weight:normal;line-height:1.13;">{{ __('panel.tiket_anda_siap_digunakan_6df6') }}</h1>
<p style="margin:0 0 23px;color:#516059;font-size:14px;line-height:1.8;">{!! __('mail.valid_intro', ['name' => e($pemesanan->user->name), 'code' => '<strong>'.e($pemesanan->kode_booking).'</strong>']) !!}</p>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 0 25px;border-collapse:collapse;">
    <tr style="background:#eae7dc;"><th align="left" style="padding:11px;color:#173d34;font-size:11px;">{{ __('panel.tiket_f17c') }}</th><th align="left" style="padding:11px;color:#173d34;font-size:11px;">{{ __('panel.pengunjung_c54d') }}</th><th align="left" style="padding:11px;color:#173d34;font-size:11px;">{{ __('panel.kode_25cf') }}</th><th align="center" style="padding:11px;color:#173d34;font-size:11px;">QR</th></tr>
    @foreach($pemesanan->detailPemesanan as $item)
    <tr><td style="padding:11px;border-bottom:1px solid #ded8cd;color:#516059;font-size:12px;">{{ $item->label_tiket }}</td><td style="padding:11px;border-bottom:1px solid #ded8cd;color:#516059;font-size:12px;">{{ $item->nama_pengunjung ?? '—' }}</td><td style="padding:11px;border-bottom:1px solid #ded8cd;color:#173d34;font-size:12px;font-weight:bold;">{{ $item->kode_tiket }}</td>
        <td align="center" style="padding:11px;border-bottom:1px solid #ded8cd;">@php $qrPng = app(\App\Services\TicketQr::class)->png($item->kode_tiket); @endphp
            @if($qrPng)<img src="{{ $message->embedData($qrPng, 'qr-'.$item->kode_tiket.'.png', 'image/png') }}" alt="QR {{ $item->kode_tiket }}" width="120" height="120" style="display:block;width:120px;height:120px;border:0;">@else<span style="color:#66736c;font-size:11px;">—</span>@endif
        </td></tr>
    @endforeach
</table>
<p style="margin:0 0 25px;color:#516059;font-size:13px;line-height:1.75;">{!! __('mail.valid_date', ['date' => '<strong>'.e(\Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->format('d/m/Y')).'</strong>']) !!}</p>
<a href="{{ route('wisatawan.pemesanan.detail', $pemesanan->id_pemesanan) }}" style="display:inline-block;padding:15px 24px;background:#173d34;color:#fff;text-decoration:none;font-size:13px;font-weight:bold;">{{ __('panel.buka_e_ticket_fc13') }}</a>
@endsection
