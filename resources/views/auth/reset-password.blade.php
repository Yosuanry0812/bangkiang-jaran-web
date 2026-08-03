@extends('layouts.guest')

@section('content')
<style>
    .bg-pattern {
        background-color: #fbf9f4;
        background-image: radial-gradient(#bec9c4 1px, transparent 1px);
        background-size: 20px 20px;
    }
</style>

<div class="relative w-full max-w-[400px]">
    <div class="relative z-10 bg-surface-container-lowest/80 backdrop-blur-xl rounded-2xl shadow-[0px_12px_40px_rgba(0,50,68,0.08)] p-8 md:p-10 border border-surface-variant/50">
        <div class="mb-8">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-primary">lock_reset</span>
            </div>
            <h1 class="font-display text-headline-md text-on-surface mb-2">Atur Password Baru</h1>
            <p class="font-body text-body-md text-on-surface-variant">
                Masukkan password baru untuk akun Anda.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-md p-sm rounded-lg bg-primary/10 border border-primary/20 text-sm text-primary">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.store') }}" class="space-y-md">
            @csrf

            {{-- Reset Token --}}
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            {{-- Email --}}
            <div class="space-y-xs">
                <label for="email" class="font-body text-label-md text-on-surface-variant block">Email</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-outline">mail</span>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="email"
                           class="w-full bg-surface-container-lowest border border-outline-variant/30 text-on-surface rounded-xl py-sm pl-lg pr-sm focus:border-primary focus:ring-4 focus:ring-primary/20 focus:outline-none transition-all duration-300 @error('email') border-error ring-2 ring-error/30 @enderror"
                           placeholder="nama@email.com">
                </div>
                @error('email')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="space-y-xs">
                <label for="password" class="font-body text-label-md text-on-surface-variant block">Password Baru</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-outline">lock</span>
                    <input id="password" type="password" name="password" required autocomplete="new-password" minlength="8"
                           class="w-full bg-surface-container-lowest border border-outline-variant/30 text-on-surface rounded-xl py-sm pl-lg pr-sm focus:border-primary focus:ring-4 focus:ring-primary/20 focus:outline-none transition-all duration-300 @error('password') border-error ring-2 ring-error/30 @enderror"
                           placeholder="Minimal 8 karakter">
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="space-y-xs">
                <label for="password_confirmation" class="font-body text-label-md text-on-surface-variant block">Konfirmasi Password</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-outline">lock</span>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                           class="w-full bg-surface-container-lowest border border-outline-variant/30 text-on-surface rounded-xl py-sm pl-lg pr-sm focus:border-primary focus:ring-4 focus:ring-primary/20 focus:outline-none transition-all duration-300"
                           placeholder="Ulangi password">
                </div>
            </div>

            {{-- Submit --}}
            <div class="pt-sm">
                <button type="submit"
                        class="w-full flex justify-center py-sm px-4 border border-transparent rounded-xl shadow-sm font-body text-label-md text-white bg-primary-container hover:-translate-y-0.5 hover:shadow-md transition-all duration-300">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
