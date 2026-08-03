@extends('layouts.guest')

@section('content')
<div class="w-full max-w-5xl grid md:grid-cols-[1fr_1.2fr] bg-white rounded-3xl overflow-hidden shadow-2xl shadow-black/10 border border-gray-100/80">

    {{-- ── Left: Image Panel ───────────────────────────── --}}
    <div class="hidden md:flex relative flex-col justify-between overflow-hidden min-h-[640px]">
        {{-- Background image --}}
        <img src="{{ asset('images/sejarah-bangkiang-waterfall.webp') }}"
             onerror="this.src='https://images.unsplash.com/photo-1555400038-63f5ba517a47?auto=format&fit=crop&w=800&q=80'"
             alt="Bangkiang Jaran"
             class="absolute inset-0 w-full h-full object-cover">

        {{-- Gradient overlays --}}
        <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(0,40,30,0.65) 0%, rgba(0,0,0,0.15) 50%, transparent 100%);"></div>
        <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,30,20,0.88) 0%, transparent 55%);"></div>

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

    {{-- ── Right: Register Form ────────────────────────── --}}
    <div class="flex flex-col justify-center px-8 py-10 md:px-12 md:py-10 bg-white overflow-y-auto">

        {{-- Header --}}
        <div class="mb-7">
            <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-100 text-emerald-700 text-[11px] font-body font-semibold tracking-[0.15em] uppercase px-3 py-1.5 rounded-full mb-5">
                <span class="material-symbols-outlined text-[13px]">person_add</span>
                Akun Baru
            </div>
            <h1 class="font-display text-[1.9rem] font-semibold text-gray-900 leading-tight mb-2">Buat Akun</h1>
            <p class="font-body text-[14px] text-gray-400 leading-relaxed">Mulai petualangan Anda bersama Bangkiang Jaran.</p>
        </div>

        {{-- Alert --}}
        @if (session('error'))
            <div class="mb-5 flex items-center gap-3 p-4 rounded-2xl bg-red-50 border border-red-100">
                <span class="material-symbols-outlined text-red-500 text-[18px]">error</span>
                <p class="font-body text-sm text-red-600">{{ session('error') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            {{-- Name + Username: 2 cols --}}
            <div class="grid grid-cols-2 gap-4">
                {{-- Name --}}
                <div>
                    <label for="name" class="block font-body text-[11px] font-semibold tracking-[0.08em] uppercase text-gray-500 mb-2">Nama Lengkap</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-[17px] transition-colors group-focus-within:text-emerald-600">person</span>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                               required autofocus autocomplete="name"
                               class="w-full h-11 bg-gray-50 border border-gray-200 text-gray-900 text-[13px] rounded-xl pl-10 pr-3 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 focus:outline-none transition-all duration-200 placeholder:text-gray-300 @error('name') border-red-400 bg-red-50 @enderror"
                               placeholder="Nama Anda">
                    </div>
                    @error('name')
                        <p class="mt-1 flex items-center gap-1 text-[11px] text-red-500"><span class="material-symbols-outlined text-[12px]">error</span>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Username --}}
                <div>
                    <label for="username" class="block font-body text-[11px] font-semibold tracking-[0.08em] uppercase text-gray-500 mb-2">Username</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-[17px] transition-colors group-focus-within:text-emerald-600">alternate_email</span>
                        <input id="username" type="text" name="username" value="{{ old('username') }}"
                               required autocomplete="username" pattern="[a-zA-Z0-9_.-]+"
                               class="w-full h-11 bg-gray-50 border border-gray-200 text-gray-900 text-[13px] rounded-xl pl-10 pr-3 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 focus:outline-none transition-all duration-200 placeholder:text-gray-300 @error('username') border-red-400 bg-red-50 @enderror"
                               placeholder="yosua123">
                    </div>
                    @error('username')
                        <p class="mt-1 flex items-center gap-1 text-[11px] text-red-500"><span class="material-symbols-outlined text-[12px]">error</span>{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block font-body text-[11px] font-semibold tracking-[0.08em] uppercase text-gray-500 mb-2">Email</label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-[17px] transition-colors group-focus-within:text-emerald-600">mail</span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           required autocomplete="email"
                           class="w-full h-11 bg-gray-50 border border-gray-200 text-gray-900 text-[13px] rounded-xl pl-10 pr-3 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 focus:outline-none transition-all duration-200 placeholder:text-gray-300 @error('email') border-red-400 bg-red-50 @enderror"
                           placeholder="nama@email.com">
                </div>
                @error('email')
                    <p class="mt-1 flex items-center gap-1 text-[11px] text-red-500"><span class="material-symbols-outlined text-[12px]">error</span>{{ $message }}</p>
                @enderror
            </div>

            {{-- Password + Confirm: 2 cols --}}
            <div class="grid grid-cols-2 gap-4">
                {{-- Password --}}
                <div>
                    <label for="password" class="block font-body text-[11px] font-semibold tracking-[0.08em] uppercase text-gray-500 mb-2">Password</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-[17px] transition-colors group-focus-within:text-emerald-600">lock</span>
                        <input id="password" type="password" name="password"
                               required autocomplete="new-password" minlength="8"
                               class="w-full h-11 bg-gray-50 border border-gray-200 text-gray-900 text-[13px] rounded-xl pl-10 pr-9 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 focus:outline-none transition-all duration-200 placeholder:text-gray-300 @error('password') border-red-400 bg-red-50 @enderror"
                               placeholder="Min. 8 karakter">
                        <button type="button" onclick="togglePwd('password','eye-pwd')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500 transition-colors">
                            <span id="eye-pwd" class="material-symbols-outlined text-[16px]">visibility_off</span>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 flex items-center gap-1 text-[11px] text-red-500"><span class="material-symbols-outlined text-[12px]">error</span>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block font-body text-[11px] font-semibold tracking-[0.08em] uppercase text-gray-500 mb-2">Konfirmasi</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-[17px] transition-colors group-focus-within:text-emerald-600">lock_reset</span>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               required autocomplete="new-password"
                               oninput="checkMatch()"
                               class="w-full h-11 bg-gray-50 border border-gray-200 text-gray-900 text-[13px] rounded-xl pl-10 pr-9 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 focus:outline-none transition-all duration-200 placeholder:text-gray-300"
                               placeholder="Ulangi password">
                        <button type="button" onclick="togglePwd('password_confirmation','eye-pwd2')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500 transition-colors">
                            <span id="eye-pwd2" class="material-symbols-outlined text-[16px]">visibility_off</span>
                        </button>
                    </div>
                    <p id="match-msg" class="mt-1 text-[11px] hidden"></p>
                </div>
            </div>

            {{-- Password strength bar --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <p class="font-body text-[11px] text-gray-400">Kekuatan password</p>
                    <p id="strength-label" class="font-body text-[11px] font-semibold text-gray-300">—</p>
                </div>
                <div class="h-1 bg-gray-100 rounded-full overflow-hidden">
                    <div id="strength-bar" class="h-full rounded-full transition-all duration-500 w-0 bg-gray-300"></div>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="group w-full h-12 flex items-center justify-center gap-2.5 bg-emerald-800 hover:bg-emerald-700 text-white font-body text-[14px] font-semibold rounded-2xl transition-all duration-300 hover:shadow-lg hover:shadow-emerald-800/25 hover:-translate-y-0.5 active:translate-y-0 mt-1">
                Buat Akun
                <span class="material-symbols-outlined text-[16px] transition-transform duration-300 group-hover:translate-x-0.5">person_add</span>
            </button>
        </form>

        {{-- Divider --}}
        <div class="relative my-5">
            <div class="absolute inset-0 flex items-center">
                <span class="w-full border-t border-gray-100"></span>
            </div>
            <div class="relative flex justify-center">
                <span class="bg-white px-4 font-body text-[12px] text-gray-300 tracking-wider">atau daftar dengan</span>
            </div>
        </div>

        {{-- Google --}}
        <a href="{{ route('auth.google') }}"
           class="w-full h-12 flex items-center justify-center gap-3 bg-white border border-gray-200 hover:border-gray-300 hover:bg-gray-50 rounded-2xl font-body text-[14px] font-medium text-gray-700 transition-all duration-300 hover:shadow-md">
            <svg width="18" height="18" viewBox="0 0 48 48" class="flex-shrink-0">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.54 28.59A14.5 14.5 0 0 1 9.5 24c0-1.59.28-3.14.76-4.59l-7.98-6.19A23.99 23.99 0 0 0 0 24c0 3.77.87 7.35 2.56 10.56l7.98-5.97z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 5.97C6.51 42.62 14.62 48 24 48z"/>
            </svg>
            Daftar dengan Google
        </a>

        {{-- Login link --}}
        <p class="mt-5 text-center font-body text-[13px] text-gray-400">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-emerald-700 hover:text-emerald-600 transition-colors ml-1">Masuk di sini</a>
        </p>
    </div>
</div>

@push('scripts')
<script>
/* ── Toggle password visibility ── */
function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (!input || !icon) return;
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    icon.textContent = isHidden ? 'visibility' : 'visibility_off';
}

/* ── Password strength meter ── */
const pwdInput     = document.getElementById('password');
const strengthBar  = document.getElementById('strength-bar');
const strengthLbl  = document.getElementById('strength-label');

const levels = [
    { label: '—',        color: '#d1d5db', pct: '0%'   },
    { label: 'Lemah',    color: '#ef4444', pct: '25%'  },
    { label: 'Sedang',   color: '#f97316', pct: '50%'  },
    { label: 'Kuat',     color: '#22c55e', pct: '75%'  },
    { label: 'Sangat Kuat', color: '#16a34a', pct: '100%' },
];

function calcStrength(val) {
    let score = 0;
    if (val.length >= 8)  score++;
    if (val.length >= 12) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    return Math.min(score, 4);
}

if (pwdInput) {
    pwdInput.addEventListener('input', function () {
        const score = this.value.length === 0 ? 0 : Math.max(1, calcStrength(this.value));
        const level = levels[score];
        strengthBar.style.width = level.pct;
        strengthBar.style.backgroundColor = level.color;
        strengthLbl.textContent = level.label;
        strengthLbl.style.color = level.color;
    });
}

/* ── Confirm password match ── */
function checkMatch() {
    const pwd  = document.getElementById('password');
    const conf = document.getElementById('password_confirmation');
    const msg  = document.getElementById('match-msg');
    if (!pwd || !conf || !msg || conf.value === '') {
        msg.classList.add('hidden');
        return;
    }
    const match = pwd.value === conf.value;
    msg.classList.remove('hidden', 'text-red-500', 'text-emerald-600');
    if (match) {
        msg.classList.add('text-emerald-600');
        msg.innerHTML = '<span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[12px]" style="font-variation-settings:\'FILL\' 1;">check_circle</span>Password cocok</span>';
        conf.classList.add('border-emerald-400');
        conf.classList.remove('border-red-400');
    } else {
        msg.classList.add('text-red-500');
        msg.innerHTML = '<span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[12px]">error</span>Password tidak cocok</span>';
        conf.classList.add('border-red-400');
        conf.classList.remove('border-emerald-400');
    }
}
</script>
@endpush
@endsection
