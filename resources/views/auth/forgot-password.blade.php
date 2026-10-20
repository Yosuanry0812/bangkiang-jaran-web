@extends('layouts.guest')

@section('content')
<style>
    .bj-reset-card { width:100%; max-width:440px; padding:clamp(24px, 7vw, 44px); background:#fff; border:1px solid #e9e7e1; border-radius:18px; box-shadow:0 20px 55px rgba(27, 45, 35, .10); }
    .bj-reset-top { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:30px; }
    .bj-reset-icon { display:grid; place-items:center; width:48px; height:48px; border:1px solid #e5e7e2; border-radius:12px; background:#f4f5f2; color:#315b4a; }
    .bj-reset-badge { padding:6px 10px; border:1px solid #e8e7e1; border-radius:999px; background:#f5f4f0; color:#666c65; font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; }
    .bj-reset-title { margin:0 0 16px; color:#18382e; font-family:Fraunces, serif; font-size:clamp(34px, 8vw, 42px); font-weight:400; line-height:1.12; letter-spacing:-.035em; }
    .bj-reset-description { margin:0 0 30px; color:#59655e; font-size:15px; line-height:1.65; }
    .bj-reset-label { display:block; margin-bottom:8px; color:#48594f; font-size:12px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
    .bj-reset-field { position:relative; }
    .bj-reset-field > span { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#819087; font-size:20px; pointer-events:none; }
    .bj-reset-input { width:100%; min-height:48px; padding:12px 14px 12px 44px; border:1px solid #d8dfd9; border-radius:12px; background:#fff; color:#1b1c19; font-size:16px; }
    .bj-reset-input::placeholder { color:#849088; }
    .bj-reset-input:focus { border-color:#006d5b; outline:3px solid rgba(0,109,91,.2); outline-offset:2px; }
    .bj-reset-input[aria-invalid="true"] { border-color:#ba1a1a; }
    .bj-reset-error { margin-top:8px; color:#a31818; font-size:14px; }
    .bj-reset-status { margin-bottom:24px; padding:12px; border:1px solid #b7d9c8; border-radius:12px; background:#eff8f1; color:#174a39; font-size:14px; }
    .bj-reset-submit { display:flex; align-items:center; justify-content:center; gap:10px; width:100%; min-height:48px; margin-top:24px; padding:12px 16px; border:0; border-radius:12px; background:#103b2e; color:#fff; font-size:14px; font-weight:700; cursor:pointer; }
    .bj-reset-submit:hover { background:#205a43; }
    .bj-reset-submit:active { background:#0b3024; }
    .bj-reset-card :is(button, a):focus-visible { outline:3px solid #006d5b; outline-offset:3px; }
    .bj-reset-meta { display:flex; align-items:center; justify-content:center; gap:7px; margin-top:24px; padding-top:20px; border-top:1px solid #eceee9; color:#596a60; font-size:12px; line-height:1.5; text-align:center; }
    .bj-reset-meta .material-symbols-outlined { color:#006d5b; font-size:17px; }
    .bj-reset-login { margin-top:20px; color:#59655e; font-size:14px; text-align:center; }
    .bj-reset-login a { color:#174a39; font-weight:700; text-decoration:underline; text-underline-offset:4px; }
</style>

<section class="bj-reset-card" aria-labelledby="reset-title">
    <div class="bj-reset-top">
        <span class="bj-reset-icon" aria-hidden="true"><span class="material-symbols-outlined">key</span></span>
        <span class="bj-reset-badge">Autentikasi</span>
    </div>

    <h1 id="reset-title" class="bj-reset-title">Lupa<br><em>Password?</em></h1>
    <p class="bj-reset-description">Tidak masalah. Masukkan email Anda dan kami akan kirim link untuk mengatur password baru.</p>

    @if (session('status'))
        <div class="bj-reset-status" role="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <label for="email" class="bj-reset-label">Email</label>
        <div class="bj-reset-field">
            <span class="material-symbols-outlined" aria-hidden="true">mail</span>
            <input id="email" class="bj-reset-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="nama@email.com" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
        </div>
        @error('email')
            <p id="email-error" class="bj-reset-error">{{ $message }}</p>
        @enderror
        <button type="submit" class="bj-reset-submit">
            Kirim Link Reset Password <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
        </button>
    </form>

    <p class="bj-reset-meta"><span class="material-symbols-outlined" aria-hidden="true">verified_user</span> Tautan berlaku selama {{ config('auth.passwords.users.expire') }} menit</p>
    <p class="bj-reset-login">Ingat password? <a href="{{ route('login') }}">Kembali ke Login</a></p>
</section>
@endsection
