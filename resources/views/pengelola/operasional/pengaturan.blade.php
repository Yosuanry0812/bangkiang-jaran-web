@extends('pengelola.layouts.admin')
@section('title', __('operations.settings'))
@section('content')
<header class="admin-page-heading"><h1>{{ __('operations.settings') }}</h1><p>{{ __('operations.settings_hint') }}</p></header>
<section class="admin-panel bj-operations"><form method="POST" action="{{ route('pengelola.settings.update') }}" class="bj-settings-form">@csrf @method('PUT')
<div class="bj-settings-grid">
@foreach(['operating_hours', 'address', 'phone', 'whatsapp', 'email', 'maps_url', 'maps_embed'] as $key)
<label for="setting-{{ $key }}">{{ __('operations.'.$key) }}<input id="setting-{{ $key }}" name="{{ $key }}" type="{{ str_starts_with($key, 'maps_') ? 'url' : ($key === 'email' ? 'email' : 'text') }}" value="{{ old($key, $settings[$key]) }}" @required(in_array($key, ['operating_hours', 'address', 'maps_url', 'maps_embed'])) maxlength="{{ str_starts_with($key, 'maps_') ? '2048' : '255' }}">@error($key)<span class="bj-field-error">{{ $message }}</span>@enderror</label>
@endforeach
@foreach(['operating_hours', 'address'] as $key)<label for="setting-{{ $key }}-en">{{ __('operations.'.$key) }} (English)<input id="setting-{{ $key }}-en" name="{{ $key }}_en" value="{{ old($key.'_en', $settings[$key.'_en']) }}" required maxlength="255">@error($key.'_en')<span class="bj-field-error">{{ $message }}</span>@enderror</label>@endforeach
<label for="setting-payment-minutes">{{ __('operations.payment_minutes') }}<input id="setting-payment-minutes" type="number" name="payment_minutes" min="15" max="1440" value="{{ old('payment_minutes', $settings['payment_minutes']) }}" required></label>
</div>
@foreach(['rules', 'rules_en'] as $key)<label for="setting-{{ $key }}">{{ __('operations.visit_rules') }}{{ $key === 'rules_en' ? ' (English)' : '' }}<textarea id="setting-{{ $key }}" name="{{ $key }}" rows="5" maxlength="5000">{{ old($key, $settings[$key]) }}</textarea></label>@endforeach
<button type="submit">{{ __('operations.save') }}</button></form></section>
<section class="admin-panel bj-operations"><h2>{{ __('operations.queue_status') }}</h2><dl><dt>{{ __('operations.queued') }}</dt><dd>{{ $queuedMail }}</dd><dt>{{ __('operations.failed') }}</dt><dd>{{ $failedMail }}</dd></dl><p>{{ __('operations.queue_hint') }}</p></section>
@endsection
