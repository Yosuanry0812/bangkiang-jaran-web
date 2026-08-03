@extends('layouts.guest')

@section('content')
<div class="w-full max-w-5xl grid md:grid-cols-[1fr_1.1fr] bg-white rounded-3xl overflow-hidden shadow-2xl shadow-black/10 border border-gray-100/80">

    {{-- ── Left: Image Panel ───────────────────────────── --}}
    <div class="hidden md:flex relative flex-col justify-between overflow-hidden min-h-[600px]">
        {{-- Background image --}}
        <img src="{{ asset('images/sejarah-bangkiang-waterfall.webp') }}"
             onerror="this.src='https://images.unsplash.com/photo-1555400038-63f5ba517a47?auto=format&fit=crop&w=800&q=80'"
             alt="Bangkiang Jaran"
             class="absolute inset-0 w-full h-full object-cover">

        {{-- Gradient overlays --}}
        <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(0,40,30,0.65) 0%, rgba(0,0,0,0.15) 50%, transparent 100%);"></div>
        <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,30,20,0.85) 0%, transparent 55%);"></div>

        {{-- Top logo area --}}
        <div class="relative z-10 p-8">
            <div class="inline-flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-white/15 backdrop-blur-sm flex items-center justify-center border border-white/20">
                    <span class="material-symbols-outlined text-white text-[16px]" style="font-variation-settings:'FILL' 1;">water</span>
                </div>
                <span class="font-display text-white font-semibold text-lg tracking-wide">Bangkiang Jaran</span>
            </div>
        </div>

        {{-- Spacer --}}
        <div class="flex-1"></div>
    </div>

    {{-- ── Right: Login Form ───────────────────────────── --}}
    <div class="flex flex-col justify-center px-8 py-10 md:px-12 md:py-12 bg-white">

        {{-- Header --}}
        <div class="mb-8">
            <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-100 text-emerald-700 text-[11px] font-body font-semibold tracking-[0.15em] uppercase px-3 py-1.5 rounded-full mb-5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Selamat Datang Kembali
            </div>
            <h1 class="font-display text-[2rem] font-semibold text-gray-900 leading-tight mb-2">Masuk ke Akun</h1>
            <p class="font-body text-[14px] text-gray-400 leading-relaxed">Kelola petualangan tropis Anda bersama kami.</p>
        </div>

        {{-- Alert messages --}}
        @if (session('status'))
            <div class="mb-5 flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border border-emerald-100">
                <span class="material-symbols-outlined text-emerald-600 text-[18px]">check_circle</span>
                <p class="font-body text-sm text-emerald-700">{{ session('status') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-5 flex items-center gap-3 p-4 rounded-2xl bg-red-50 border border-red-100">
                <span class="material-symbols-outlined text-red-500 text-[18px]">error</span>
                <p class="font-body text-sm text-red-600">{{ session('error') }}</p>
            </div>
        @endif
        @if ($errors->has('login') && str_contains($errors->first('login'), 'Terlalu banyak'))
            <div class="mb-5 flex items-center gap-3 p-4 rounded-2xl bg-red-50 border border-red-100">
                <span class="material-symbols-outlined text-red-500 text-[18px]">timer</span>
                <p class="font-body text-sm text-red-600">{{ $errors->first('login') }}</p>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Email / Username --}}
            <div>
                <label for="login" class="block font-body text-[12px] font-semibold tracking-[0.08em] uppercase text-gray-500 mb-2">Email atau Username</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-[18px] transition-colors group-focus-within:text-emerald-600">mail</span>
                    <input id="login" type="text" name="login" value="{{ old('login') }}"
                           required autofocus autocomplete="username"
                           class="w-full h-12 bg-gray-50 border border-gray-200 text-gray-900 text-[14px] rounded-2xl pl-11 pr-4 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 focus:outline-none transition-all duration-200 placeholder:text-gray-300 @error('login') border-red-400 bg-red-50 focus:border-red-400 focus:ring-red-400/10 @enderror"
                           placeholder="email@contoh.com">
                </div>
                @error('login')
                    <p class="mt-1.5 flex items-center gap-1 text-[12px] text-red-500 font-body">
                        <span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block font-body text-[12px] font-semibold tracking-[0.08em] uppercase text-gray-500">Password</label>
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="font-body text-[12px] font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">Lupa password?</a>
                    @endif
                </div>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-[18px] transition-colors group-focus-within:text-emerald-600">lock</span>
                    <input id="password" type="password" name="password"
                           required autocomplete="current-password"
                           class="w-full h-12 bg-gray-50 border border-gray-200 text-gray-900 text-[14px] rounded-2xl pl-11 pr-12 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 focus:outline-none transition-all duration-200 placeholder:text-gray-300 @error('password') border-red-400 bg-red-50 @enderror"
                           placeholder="••••••••">
                    {{-- Toggle password visibility --}}
                    <button type="button" onclick="togglePwd('password','eye-login')"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500 transition-colors">
                        <span id="eye-login" class="material-symbols-outlined text-[18px]">visibility_off</span>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1.5 flex items-center gap-1 text-[12px] text-red-500 font-body">
                        <span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Remember me --}}
            <div class="flex items-center gap-3">
                <button type="button" id="remember-toggle" onclick="toggleRemember()"
                        class="relative w-10 h-5 rounded-full transition-colors duration-300 bg-gray-200 flex-shrink-0 focus:outline-none focus:ring-4 focus:ring-emerald-500/20"
                        role="switch" aria-checked="false">
                    <span id="remember-knob" class="absolute left-0.5 top-0.5 w-4 h-4 rounded-full bg-white shadow-sm transition-transform duration-300"></span>
                </button>
                <input type="checkbox" id="remember_me" name="remember" class="sr-only">
                <label for="remember-toggle" class="font-body text-[13px] text-gray-500 select-none cursor-pointer">Ingat saya</label>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="group w-full h-12 flex items-center justify-center gap-2.5 bg-emerald-800 hover:bg-emerald-700 text-white font-body text-[14px] font-semibold rounded-2xl transition-all duration-300 hover:shadow-lg hover:shadow-emerald-800/25 hover:-translate-y-0.5 active:translate-y-0 mt-2">
                Masuk
                <span class="material-symbols-outlined text-[16px] transition-transform duration-300 group-hover:translate-x-0.5">arrow_forward</span>
            </button>
        </form>

        {{-- Divider --}}
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <span class="w-full border-t border-gray-100"></span>
            </div>
            <div class="relative flex justify-center">
                <span class="bg-white px-4 font-body text-[12px] text-gray-300 tracking-wider">atau lanjutkan dengan</span>
            </div>
        </div>

        {{-- Google --}}
        <a href="{{ route('auth.google') }}"
           class="group w-full h-12 flex items-center justify-center gap-3 bg-white border border-gray-200 hover:border-gray-300 hover:bg-gray-50 rounded-2xl font-body text-[14px] font-medium text-gray-700 transition-all duration-300 hover:shadow-md">
            <svg width="18" height="18" viewBox="0 0 48 48" class="flex-shrink-0">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.54 28.59A14.5 14.5 0 0 1 9.5 24c0-1.59.28-3.14.76-4.59l-7.98-6.19A23.99 23.99 0 0 0 0 24c0 3.77.87 7.35 2.56 10.56l7.98-5.97z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 5.97C6.51 42.62 14.62 48 24 48z"/>
            </svg>
            Masuk dengan Google
        </a>

        <p class="mt-4 text-center font-body text-[12px] text-gray-400">Akun Google baru wajib set password setelah login pertama.</p>

        {{-- Register link --}}
        <p class="mt-6 text-center font-body text-[13px] text-gray-400">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-semibold text-emerald-700 hover:text-emerald-600 transition-colors ml-1">Daftar sekarang</a>
        </p>
    </div>
</div>

@push('scripts')
<script>
function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (!input || !icon) return;
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    icon.textContent = isHidden ? 'visibility' : 'visibility_off';
}

let rememberOn = false;
function toggleRemember() {
    rememberOn = !rememberOn;
    const btn   = document.getElementById('remember-toggle');
    const knob  = document.getElementById('remember-knob');
    const chk   = document.getElementById('remember_me');
    btn.setAttribute('aria-checked', rememberOn);
    btn.classList.toggle('bg-emerald-600', rememberOn);
    btn.classList.toggle('bg-gray-200', !rememberOn);
    knob.style.transform = rememberOn ? 'translateX(20px)' : 'translateX(0)';
    chk.checked = rememberOn;
}
</script>
@endpush
@endsection
