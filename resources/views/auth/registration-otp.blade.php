@extends('layouts.guest')
@section('title', 'Verifikasi email — Bangkiang Jaran')
@section('content')
<div class="bj-auth-card bj-otp-card w-full bg-white">
    <div class="bj-auth-form">
        <div>
            <p class="bj-otp-kicker">VERIFIKASI EMAIL</p>
            <h1>Periksa email Anda</h1>
            <p>Kode verifikasi 6 digit dikirim ke <strong class="bj-otp-email">{{ $email }}</strong>. Masukkan kode untuk menyelesaikan pendaftaran.</p>
        </div>

        @if(session('status') === 'otp-sent')
            <p class="bj-otp-status" role="status">Kode baru berhasil dikirim. Periksa kotak masuk atau folder spam.</p>
        @endif
        @if($errors->any())
            <div id="otp-error" class="bj-otp-error" role="alert">
                @foreach($errors->all() as $message)<p>{{ $message }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('registration.otp.verify') }}" class="bj-otp-form">
            @csrf
            <label for="otp">Kode OTP</label>
            <input id="otp" name="otp" type="text" inputmode="numeric" autocomplete="one-time-code"
                   pattern="[0-9]{6}" minlength="6" maxlength="6" required autofocus
                   placeholder="6 digit angka" aria-describedby="otp-expiry{{ $errors->any() ? ' otp-error' : '' }}"
                   @if($errors->has('otp')) aria-invalid="true" @endif>
            <p id="otp-expiry" data-expires-at="{{ $expiresAt }}">Kode berlaku 10 menit sejak dikirim.</p>
            <button type="submit">Verifikasi &amp; Buat Akun</button>
        </form>

        <form method="POST" action="{{ route('registration.otp.resend') }}" class="bj-otp-resend">
            @csrf
            <p>Belum menerima email? Periksa folder spam atau kirim kode baru.</p>
            <button type="submit" data-resend-at="{{ $resendAt }}">Kirim ulang kode</button>
            <span id="otp-resend-countdown" role="status" aria-live="polite"></span>
        </form>
        <form method="POST" action="{{ route('registration.otp.cancel') }}" class="bj-otp-cancel">
            @csrf
            <button type="submit">Ganti email atau data pendaftaran</button>
        </form>
        <p class="bj-otp-note">Akun dibuat setelah email terverifikasi. Jangan bagikan kode Anda kepada siapa pun.</p>
    </div>
</div>
@endsection
@push('scripts')
<script defer src="{{ asset('js/registration-otp.js') }}"></script>
@endpush
