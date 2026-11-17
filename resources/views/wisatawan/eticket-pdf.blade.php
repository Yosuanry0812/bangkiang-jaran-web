<!DOCTYPE html><html lang="{{ app()->getLocale() }}"><head><meta charset="utf-8"><title>E-ticket</title>
<style>body{font-family:DejaVu Sans,sans-serif;color:#153c30;font-size:12px}h1{font-size:24px}section{padding:18px;border:1px solid #b6c9bf}section+section{page-break-before:always}img{width:160px;height:160px}dl{line-height:1.8}dt{color:#666;font-size:10px}dd{margin:0 0 10px}code{font-size:13px}p{line-height:1.6}</style></head><body>
@foreach($booking->detailPemesanan as $detail)
<section><p>Bangkiang Jaran Waterfall</p><h1>{{ __('operations.eticket') }}</h1>
<img src="{{ $images[$detail->id_detail] }}" alt="QR"><p><code>{{ $detail->kode_tiket }}</code></p>
<dl><dt>{{ __('operations.visitor') }}</dt><dd>{{ $detail->nama_pengunjung }}</dd>
<dt>{{ __('operations.ticket') }}</dt><dd>{{ $detail->nama_tiket }}</dd>
<dt>{{ __('operations.visit_date') }}</dt><dd>{{ \Carbon\Carbon::parse($booking->tgl_kunjungan)->format('d/m/Y') }}</dd>
<dt>{{ __('operations.booking_code') }}</dt><dd>{{ $booking->kode_booking }}</dd>
<dt>{{ __('operations.status') }}</dt><dd>{{ __('operations.verification_'.$detail->verificationState()) }}</dd></dl>
<p>{{ __('operations.ticket_instructions') }}</p></section>
@endforeach</body></html>
