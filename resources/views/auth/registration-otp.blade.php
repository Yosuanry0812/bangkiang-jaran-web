@extends('layouts.guest')
@section('title', __('ui.t_email_verif'))
@section('content')
<div class="bj-auth-card bj-otp-card w-full bg-white">
    <div class="bj-auth-form">
        <div>
            <p class="bj-otp-kicker">{{ __('auth_ui.otp_badge') }}</p>
            <h1>{{ __('auth_ui.otp_title') }}</h1>
            <p>{{ __('auth_ui.otp_sent_to') }}<strong class="bj-otp-email">{{ $email }}</strong>{{ __('auth_ui.otp_enter') }}</p>
        </div>

        @if(session('status') === 'otp-sent')
            <p class="bj-otp-status" role="status">{{ __('auth_ui.otp_resent') }}</p>
        @endif
        @if($errors->any())
            <div id="otp-error" class="bj-otp-error" role="alert">
                @foreach($errors->all() as $message)<p>{{ $message }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('registration.otp.verify') }}" class="bj-otp-form">
            @csrf
            <label for="otp">{{ __('auth_ui.otp_label') }}</label>
            <input id="otp" name="otp" type="text" inputmode="numeric" autocomplete="one-time-code"
                   pattern="[0-9]{6}" minlength="6" maxlength="6" required autofocus
                   placeholder="{{ __('auth_ui.otp_placeholder') }}" aria-describedby="otp-expiry{{ $errors->any() ? ' otp-error' : '' }}"
                   @if($errors->has('otp')) aria-invalid="true" @endif>
            <p id="otp-expiry" data-expires-at="{{ $expiresAt }}">{{ __('auth_ui.otp_expiry') }}</p>
            <button type="submit">{{ __('auth_ui.otp_submit') }}</button>
        </form>

        <form method="POST" action="{{ route('registration.otp.resend') }}" class="bj-otp-resend">
            @csrf
            <p>{{ __('auth_ui.otp_noemail') }}</p>
            <button type="submit" data-resend-at="{{ $resendAt }}">{{ __('auth_ui.otp_resend') }}</button>
            <span id="otp-resend-countdown" role="status" aria-live="polite"></span>
        </form>
        <form method="POST" action="{{ route('registration.otp.cancel') }}" class="bj-otp-cancel">
            @csrf
            <button type="submit">{{ __('auth_ui.otp_change') }}</button>
        </form>
        <p class="bj-otp-note">{{ __('auth_ui.otp_footer') }}</p>
    </div>
</div>
@endsection
@push('scripts')
<script defer src="{{ asset('js/registration-otp.js') }}"></script>
@endpush
