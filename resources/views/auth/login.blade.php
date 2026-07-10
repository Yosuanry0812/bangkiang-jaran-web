@extends('layouts.guest')

@section('content')
<div class="w-full max-w-4xl grid md:grid-cols-2 bg-surface-container-lowest rounded-2xl overflow-hidden card-shadow">
    {{-- Image Side --}}
    <div class="hidden md:block relative h-full min-h-[500px]">
        <div class="absolute inset-0 bg-cover bg-center"
             style="background-image: url('https://images.unsplash.com/photo-1555400038-63f5ba517a47?auto=format&fit=crop&w=800&q=80')">
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex flex-col justify-end p-lg">
            <h2 class="font-display text-headline-md text-white mb-base">Bangkiang Jaran</h2>
            <p class="font-body text-body-md text-white/80">Tropical Elegance in Bali.</p>
        </div>
    </div>

    {{-- Login Form Side --}}
    <div class="p-lg md:p-xl flex flex-col justify-center">
        <div class="mb-lg">
            <h1 class="font-display text-headline-md text-on-surface mb-base">Welcome Back</h1>
            <p class="font-body text-body-md text-on-surface-variant">Sign in to manage your tropical adventure.</p>
        </div>

        @if (session('status'))
            <div class="mb-md p-sm rounded-lg bg-primary/10 border border-primary/20 text-sm text-primary">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-md p-sm rounded-lg bg-error/10 border border-error/20 text-sm text-error">{{ session('error') }}</div>
        @endif
        @if ($errors->has('login') && str_contains($errors->first('login'), 'Terlalu banyak'))
            <div class="mb-md p-sm rounded-lg bg-error/10 border border-error/20 text-sm text-error flex items-center gap-2">
                <span class="material-symbols-outlined text-base">error</span>
                <span>{{ $errors->first('login') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-md">
            @csrf

            {{-- Email/Username --}}
            <div class="space-y-xs">
                <label for="login" class="font-body text-label-md text-on-surface-variant block">Email or Username</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-outline">mail</span>
                    <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus autocomplete="username"
                           class="w-full bg-surface-container-lowest border border-outline-variant/30 text-on-surface rounded-xl py-sm pl-lg pr-sm focus:border-primary focus:ring-4 focus:ring-primary/20 focus:outline-none transition-all duration-300 @error('login') border-error ring-2 ring-error/30 @enderror"
                           placeholder="Enter your email">
                </div>
                @error('login')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="space-y-xs">
                <label for="password" class="font-body text-label-md text-on-surface-variant block">Password</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-outline">lock</span>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           class="w-full bg-surface-container-lowest border border-outline-variant/30 text-on-surface rounded-xl py-sm pl-lg pr-sm focus:border-primary focus:ring-4 focus:ring-primary/20 focus:outline-none transition-all duration-300 @error('password') border-error ring-2 ring-error/30 @enderror"
                           placeholder="Password">
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember + Forgot --}}
            <div class="flex items-center justify-between mt-sm">
                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember"
                           class="h-4 w-4 rounded border-outline-variant text-primary focus:ring-primary bg-surface-container-lowest">
                    <label for="remember_me" class="ml-2 block font-body text-body-md text-on-surface-variant">Ingat saya</label>
                </div>
                @if (Route::has('password.request'))
                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-body text-label-md text-primary hover:text-primary/80 transition-colors">Lupa Password?</a>
                </div>
                @endif
            </div>

            {{-- Submit --}}
            <div class="pt-sm">
                <button type="submit"
                        class="w-full flex justify-center py-sm px-4 border border-transparent rounded-xl shadow-sm font-body text-label-md text-white bg-primary-container hover:-translate-y-0.5 hover:shadow-md transition-all duration-300">
                    Masuk
                </button>
            </div>
        </form>

        <div class="mt-lg text-center">
            <p class="font-body text-body-md text-on-surface-variant">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-body text-label-md text-tertiary underline hover:text-tertiary/80 transition-colors">Daftar di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection