@extends('pengelola.layouts.admin')
@section('title', __('operations.quotas'))
@section('content')
<header class="admin-page-heading"><h1>{{ __('operations.quotas') }}</h1><p>{{ __('operations.quota_hint') }}</p></header>
<section class="admin-panel bj-operations">
<form method="GET" class="bj-operations-filter"><label>{{ __('operations.month') }}<input type="month" name="month" value="{{ $month->format('Y-m') }}"></label><button type="submit">{{ __('operations.filter') }}</button></form>
<div class="admin-table-wrap"><table class="admin-table admin-table--responsive"><thead><tr><th>{{ __('operations.visit_date') }}</th><th>{{ __('operations.held') }}</th><th>{{ __('operations.remaining') }}</th><th>{{ __('operations.capacity') }}</th></tr></thead><tbody>
@foreach($days as $day)<tr><td data-label="{{ __('operations.visit_date') }}">{{ $day['date'] }}</td><td data-label="{{ __('operations.held') }}">{{ $day['used'] }}</td><td data-label="{{ __('operations.remaining') }}">{{ $day['remaining'] ?? __('operations.unlimited') }}</td><td data-label="{{ __('operations.capacity') }}">
@if($day['date'] >= now()->toDateString())<form method="POST" action="{{ route('pengelola.quotas.update') }}" class="bj-quota-row">@csrf @method('PUT')<input type="hidden" name="visit_date" value="{{ $day['date'] }}"><label class="sr-only" for="quota-{{ $day['date'] }}">{{ __('operations.capacity') }} {{ $day['date'] }}</label><input id="quota-{{ $day['date'] }}" name="capacity" type="number" min="{{ max(1, $day['used']) }}" max="1000000" value="{{ $day['capacity'] }}" placeholder="{{ __('operations.unlimited') }}"><button type="submit">{{ __('operations.save') }}</button></form>
@else{{ $day['capacity'] ?? __('operations.unlimited') }}@endif
</td></tr>@endforeach
</tbody></table></div></section>
@endsection
