@extends('pengelola.layouts.admin')
@section('title', __('operations.audit'))
@section('content')
<header class="admin-page-heading"><h1>{{ __('operations.audit') }}</h1></header>
<section class="admin-panel bj-operations">@include('partials.booking-filters', ['auditOnly' => true])
<div class="admin-table-wrap"><table class="admin-table admin-table--responsive"><thead><tr><th>{{ __('operations.time') }}</th><th>{{ __('operations.actor') }}</th><th>{{ __('operations.action') }}</th><th>{{ __('operations.details') }}</th></tr></thead><tbody>
@forelse($logs as $log)<tr><td data-label="{{ __('operations.time') }}">{{ $log->created_at?->format('d/m/Y H:i:s') }}</td><td data-label="{{ __('operations.actor') }}">{{ $log->user?->name ?? __('ui.system') }}</td><td data-label="{{ __('operations.action') }}">{{ $log->label_aktivitas }}</td><td data-label="{{ __('operations.details') }}" class="bj-preserve-lines">{{ $log->label_detail }}</td></tr>
@empty<tr><td colspan="4">{{ __('operations.no_records') }}</td></tr>@endforelse
</tbody></table></div>{{ $logs->links() }}</section>
@endsection
