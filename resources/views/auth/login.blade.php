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

        <div class="mt-md">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <span class="w-full border-t border-surface-variant/50"></span>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="bg-surface-container-lowest px-3 font-body text-body-md text-outline">atau</span>
                </div>
            </div>
            <a href="{{ route('auth.google') }}"
               class="mt-md w-full flex items-center justify-center gap-3 bg-white border border-surface-variant rounded-xl px-4 py-3 font-body text-label-md text-on-surface hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                <svg width="20" height="20" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.54 28.59A14.5 14.5 0 0 1 9.5 24c0-1.59.28-3.14.76-4.59l-7.98-6.19A23.99 23.99 0 0 0 0 24c0 3.77.87 7.35 2.56 10.56l7.98-5.97z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 5.97C6.51 42.62 14.62 48 24 48z"/>
                </svg>
                Sign in with Google
            </a>
        </div>

    </div>
</div>
@endsection
