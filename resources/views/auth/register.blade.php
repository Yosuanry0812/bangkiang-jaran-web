@extends('layouts.guest')

@section('content')
<div class="bj-auth-simple w-full max-w-md bg-white rounded-3xl shadow-2xl shadow-black/10 border border-gray-100/80 px-8 py-10 md:px-10">

    {{-- Header --}}
    <div class="flex flex-col items-center text-center mb-8">
        <span class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-[30px]" style="font-variation-settings:'FILL' 1;">person_add</span>
        </span>
        <h1 class="font-display text-[1.9rem] font-semibold text-gray-900 leading-tight mb-2">{{ __('auth_ui.reg_title') }}</h1>
        <p class="font-body text-[14px] text-gray-500 leading-relaxed">{{ __('auth_ui.reg_lead') }}</p>
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

        {{-- Username --}}
        <div>
            <label for="username" class="block font-body text-[11px] font-semibold tracking-[0.08em] uppercase text-gray-500 mb-2">{{ __('ui.username') }}</label>
            <div class="relative group">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-[17px] transition-colors group-focus-within:text-emerald-600">person</span>
                <input id="username" type="text" name="username" value="{{ old('username') }}"
                       required autofocus autocomplete="username" pattern="[a-zA-Z0-9_.-]+"
                       class="w-full h-12 bg-gray-50 border border-gray-200 text-gray-900 text-[14px] rounded-xl pl-11 pr-3 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 focus:outline-none transition-all duration-200 placeholder:text-gray-300 @error('username') border-red-400 bg-red-50 @enderror"
                       placeholder="yosua123">
            </div>
            @error('username')
                <p class="mt-1 flex items-center gap-1 text-[11px] text-red-500"><span class="material-symbols-outlined text-[12px]">error</span>{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block font-body text-[11px] font-semibold tracking-[0.08em] uppercase text-gray-500 mb-2">{{ __('ui.email') }}</label>
            <div class="relative group">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-[17px] transition-colors group-focus-within:text-emerald-600">mail</span>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       required autocomplete="email"
                       class="w-full h-12 bg-gray-50 border border-gray-200 text-gray-900 text-[14px] rounded-xl pl-11 pr-3 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 focus:outline-none transition-all duration-200 placeholder:text-gray-300 @error('email') border-red-400 bg-red-50 @enderror"
                       placeholder="{{ __('auth_ui.email_placeholder') }}">
            </div>
            @error('email')
                <p class="mt-1 flex items-center gap-1 text-[11px] text-red-500"><span class="material-symbols-outlined text-[12px]">error</span>{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block font-body text-[11px] font-semibold tracking-[0.08em] uppercase text-gray-500 mb-2">{{ __('ui.password') }}</label>
            <div class="relative group">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-[17px] transition-colors group-focus-within:text-emerald-600">lock</span>
                <input id="password" type="password" name="password"
                       required autocomplete="new-password" minlength="8"
                       class="w-full h-12 bg-gray-50 border border-gray-200 text-gray-900 text-[14px] rounded-xl pl-11 pr-10 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 focus:outline-none transition-all duration-200 placeholder:text-gray-300 @error('password') border-red-400 bg-red-50 @enderror"
                       placeholder="{{ __('auth_ui.pw_min8') }}">
                <button type="button" onclick="togglePwd('password','eye-pwd')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500 transition-colors">
                    <span id="eye-pwd" class="material-symbols-outlined text-[18px]">visibility_off</span>
                </button>
            </div>
            @error('password')
                <p class="mt-1 flex items-center gap-1 text-[11px] text-red-500"><span class="material-symbols-outlined text-[12px]">error</span>{{ $message }}</p>
            @enderror
        </div>

        {{-- Password strength bar --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <p class="font-body text-[11px] text-gray-400">{{ __('auth_ui.pw_strength') }}</p>
                <p id="strength-label" class="font-body text-[11px] font-semibold text-gray-300">—</p>
            </div>
            <div class="h-1 bg-gray-100 rounded-full overflow-hidden">
                <div id="strength-bar" class="h-full rounded-full transition-all duration-500 w-0 bg-gray-300"></div>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="group w-full h-12 flex items-center justify-center gap-2.5 bg-emerald-800 hover:bg-emerald-700 text-white font-body text-[14px] font-semibold rounded-2xl transition-all duration-300 hover:shadow-lg hover:shadow-emerald-800/25 hover:-translate-y-0.5 active:translate-y-0 mt-1">
            {{ __(config('features.otp_registration') ? 'auth_ui.reg_submit' : 'auth_ui.reg_submit_direct') }}
            <span class="material-symbols-outlined text-[16px] transition-transform duration-300 group-hover:translate-x-0.5">person_add</span>
        </button>
    </form>

    @if (config('features.otp_registration'))
    <p class="mt-3 font-body text-[12px] leading-relaxed text-gray-500">{{ __('auth_ui.reg_otp_note') }}</p>
    @endif

    @if (config('features.google_login'))
    {{-- Divider --}}
    <div class="relative my-5">
        <div class="absolute inset-0 flex items-center">
            <span class="w-full border-t border-gray-100"></span>
        </div>
        <div class="relative flex justify-center">
            <span class="bg-white px-4 font-body text-[12px] text-gray-300 tracking-wider">{{ __('auth_ui.reg_or') }}</span>
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
        {{ __('auth_ui.reg_google') }}
    </a>
    @endif

    {{-- Login link --}}
    <p class="mt-5 text-center font-body text-[13px] text-gray-400">
        {{ __('auth_ui.have_account') }}
        <a href="{{ route('login') }}" class="font-semibold text-emerald-700 hover:text-emerald-600 transition-colors ml-1">{{ __('auth_ui.sign_in_here') }}</a>
    </p>
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
    { label: @json(__('auth_ui.pw_weak')),    color: '#ef4444', pct: '25%'  },
    { label: @json(__('auth_ui.pw_fair')),   color: '#f97316', pct: '50%'  },
    { label: @json(__('auth_ui.pw_strong')),     color: '#22c55e', pct: '75%'  },
    { label: @json(__('auth_ui.pw_very_strong')), color: '#16a34a', pct: '100%' },
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
</script>
@endpush
@endsection
