@extends('pengelola.layouts.admin')
@section('title', __('operations.all_bookings'))
@section('content')
<header class="admin-page-heading"><h1>{{ __('operations.all_bookings') }}</h1></header>
<section class="admin-panel bj-operations">@include('partials.booking-filters', ['showChannel' => true])
<div class="admin-table-wrap"><table class="admin-table admin-table--responsive"><thead><tr><th>{{ __('operations.booking_code') }}</th><th>{{ __('operations.buyer') }}</th><th>{{ __('operations.visit_date') }}</th><th>{{ __('operations.status') }}</th><th>{{ __('operations.details') }}</th></tr></thead><tbody>
@forelse($pemesanan as $booking)<tr><td data-label="{{ __('operations.booking_code') }}"><code>{{ $booking->kode_booking }}</code><small>{{ str_starts_with($booking->kode_booking, 'BJ-OFF-') ? __('operations.offline') : __('operations.online') }}</small></td><td data-label="{{ __('operations.buyer') }}">{{ $booking->user?->name }}</td><td data-label="{{ __('operations.visit_date') }}">{{ $booking->tgl_kunjungan }}</td><td data-label="{{ __('operations.status') }}">{{ $booking->statusLabel() }}</td><td data-label="{{ __('operations.details') }}"><a href="{{ route('pengelola.verifikasi.show', $booking->id_pemesanan) }}">{{ __('operations.view') }}</a></td></tr>
@empty<tr><td colspan="5">{{ __('operations.no_records') }}</td></tr>@endforelse
</tbody></table></div>{{ $pemesanan->links() }}</section>
@endsection
