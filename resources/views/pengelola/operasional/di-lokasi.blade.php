@extends('pengelola.layouts.admin')
@section('title', __('operations.onsite'))
@section('content')
<header class="admin-page-heading"><h1>{{ __('operations.onsite') }}</h1><p>{{ $total }} {{ __('operations.visitor') }}</p></header>
<section class="admin-panel bj-operations">@include('partials.booking-filters')
<div class="admin-table-wrap"><table class="admin-table admin-table--responsive"><thead><tr><th>{{ __('operations.visitor') }}</th><th>{{ __('operations.booking_code') }}</th><th>{{ __('operations.checkin') }}</th><th>{{ __('operations.details') }}</th></tr></thead><tbody>
@forelse($visitors as $visitor)<tr><td data-label="{{ __('operations.visitor') }}">{{ $visitor->nama_pengunjung }}<small>{{ $visitor->nama_tiket }}</small></td><td data-label="{{ __('operations.booking_code') }}"><code>{{ $visitor->pemesanan->kode_booking }}</code></td><td data-label="{{ __('operations.checkin') }}">{{ $visitor->check_in_at }}</td><td data-label="{{ __('operations.details') }}"><a href="{{ route('pengelola.verifikasi.show', $visitor->id_pemesanan) }}">{{ __('operations.view') }}</a></td></tr>
@empty<tr><td colspan="4">{{ __('operations.no_records') }}</td></tr>@endforelse
</tbody></table></div>{{ $visitors->links() }}</section>
@endsection
