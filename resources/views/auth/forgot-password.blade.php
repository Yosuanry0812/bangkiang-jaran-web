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
                <span class="material-symbols-outlined text-primary">key</span>
            </div>
            <h1 class="font-display text-headline-md text-on-surface mb-2">Lupa Password?</h1>
            <p class="font-body text-body-md text-on-surface-variant">
                Tidak masalah. Masukkan email Anda dan kami akan kirim link untuk mengatur password baru.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-md p-sm rounded-lg bg-primary/10 border border-primary/20 text-sm text-primary">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-md">
            @csrf

            {{-- Email --}}
            <div class="space-y-xs">
                <label for="email" class="font-body text-label-md text-on-surface-variant block">Email</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-outline">mail</span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                           class="w-full bg-surface-container-lowest border border-outline-variant/30 text-on-surface rounded-xl py-sm pl-lg pr-sm focus:border-primary focus:ring-4 focus:ring-primary/20 focus:outline-none transition-all duration-300 @error('email') border-error ring-2 ring-error/30 @enderror"
                           placeholder="nama@email.com">
                </div>
                @error('email')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="pt-sm">
                <button type="submit"
                        class="w-full flex justify-center py-sm px-4 border border-transparent rounded-xl shadow-sm font-body text-label-md text-white bg-primary-container hover:-translate-y-0.5 hover:shadow-md transition-all duration-300">
                    Kirim Link Reset Password
                </button>
            </div>
        </form>

        <p class="mt-lg text-center font-body text-body-md text-on-surface-variant">
            Ingat password?
            <a href="{{ route('login') }}" class="font-body text-label-md text-primary hover:text-primary/80 transition-colors">Kembali ke Login</a>
        </p>
    </div>
</div>
@endsection
