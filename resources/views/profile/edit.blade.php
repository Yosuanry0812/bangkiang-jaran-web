@extends('layouts.app')
@section('nav-mode', 'light')
@section('title', 'Profil — ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-[#F5F4F1] pt-24 pb-16 px-gutter">
<div class="max-w-5xl mx-auto">

{{-- ── Page header ─────────────────────────────── --}}
<div class="mb-10" data-aos="fade-up">
    <p class="font-sans text-[10px] tracking-[0.25em] uppercase text-stone mb-2">Akun Saya</p>
    <h1 class="font-serif text-[clamp(2rem,4vw,3rem)] text-ink leading-tight">{{ __('messages.my_account') }}</h1>
    <p class="font-sans text-sm text-stone mt-2">{{ __('messages.manage_account') }}</p>
</div>

{{-- ── Success toast ────────────────────────────── --}}
@if(session('status') === 'profile-updated')
<div class="mb-6 flex items-center gap-3 px-5 py-3.5 rounded-2xl bg-forest/8 border border-forest/15 text-forest font-sans text-sm" data-aos="fade-down">
    <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1,'wght' 300;">check_circle</span>
    {{ __('messages.profile_updated') }}
</div>
@endif

{{-- ── Main grid ────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-6" data-aos="fade-up" data-aos-delay="60">

{{-- ══ LEFT: Identity card ══════════════════════════ --}}
<aside class="flex flex-col gap-5">

    {{-- Profile card --}}
    <div class="bg-white rounded-3xl p-7 flex flex-col items-center text-center shadow-sm border border-gray-100">
        {{-- Avatar --}}
        <div class="relative mb-5">
            <div class="w-20 h-20 rounded-2xl bg-gray-200 flex items-center justify-center overflow-hidden shadow-md">
                <span class="material-symbols-outlined text-stone text-[2.5rem] select-none leading-none" style="font-variation-settings:'FILL' 1,'wght' 300;">person</span>
            </div>
            {{-- online dot --}}
            <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-400 border-2 border-white"></span>
        </div>

        <h2 class="font-serif text-[1.2rem] text-ink leading-snug mb-0.5">{{ Auth::user()->name }}</h2>
        <p class="font-sans text-xs text-stone truncate max-w-full mb-4">{{ Auth::user()->email }}</p>

        {{-- Role badge --}}
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-forest/8 border border-forest/12 font-sans text-[11px] font-medium text-forest">
            <span class="material-symbols-outlined text-[13px]" style="font-variation-settings:'FILL' 1,'wght' 300;">verified</span>
            {{ Auth::user()->role === 'pengelola' ? 'Pengelola' : 'Wisatawan' }}
        </span>
    </div>

    {{-- Meta card --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 space-y-4">
        <div>
            <p class="font-sans text-[10px] tracking-[0.18em] uppercase text-stone/50 mb-1">Bergabung sejak</p>
            <p class="font-sans text-sm font-medium text-ink">{{ Auth::user()->created_at->translatedFormat('d F Y') }}</p>
        </div>
        <div class="h-px bg-gray-100"></div>
        <div>
            <p class="font-sans text-[10px] tracking-[0.18em] uppercase text-stone/50 mb-1">Metode Login</p>
            <p class="font-sans text-sm font-medium text-ink flex items-center gap-1.5">
                @if(Auth::user()->google_id)
                    <svg width="13" height="13" viewBox="0 0 48 48" class="flex-shrink-0"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.54 28.59A14.5 14.5 0 0 1 9.5 24c0-1.59.28-3.14.76-4.59l-7.98-6.19A23.99 23.99 0 0 0 0 24c0 3.77.87 7.35 2.56 10.56l7.98-5.97z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 5.97C6.51 42.62 14.62 48 24 48z"/></svg>
                    Google
                @else
                    <span class="material-symbols-outlined text-[15px] text-stone">mail</span>
                    Email & Password
                @endif
            </p>
        </div>
        <div class="h-px bg-gray-100"></div>
        <div>
            <p class="font-sans text-[10px] tracking-[0.18em] uppercase text-stone/50 mb-1">Username</p>
            <p class="font-sans text-sm font-medium text-ink">@{{ Auth::user()->username }}</p>
        </div>
    </div>

</aside>

{{-- ══ RIGHT: Tabs + Forms ══════════════════════════ --}}
<div class="flex flex-col gap-5" x-data="{ tab: 'profile' }">

    {{-- Tab switcher --}}
    <div class="bg-white rounded-2xl p-1.5 shadow-sm border border-gray-100 inline-flex gap-1 w-fit">
        <button @click="tab='profile'"
                :class="tab==='profile' ? 'bg-forest text-white shadow-sm' : 'text-stone hover:text-ink'"
                class="font-sans text-[12px] font-medium px-5 py-2 rounded-xl transition-all duration-200">
            Informasi Akun
        </button>
        @if(Auth::user()->google_id)
        <button @click="tab='password'"
                :class="tab==='password' ? 'bg-forest text-white shadow-sm' : 'text-stone hover:text-ink'"
                class="font-sans text-[12px] font-medium px-5 py-2 rounded-xl transition-all duration-200">
            Atur Kata Sandi
        </button>
        @endif
    </div>

    {{-- ── Tab: Informasi Akun ── --}}
    <div x-show="tab==='profile'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">

            <div class="mb-7">
                <h3 class="font-serif text-[1.2rem] text-ink">Informasi Pribadi</h3>
                <p class="font-sans text-xs text-stone mt-1">Perbarui nama, email, dan nomor telepon Anda.</p>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Nama --}}
                    <div>
                        <label class="block font-sans text-[11px] font-semibold tracking-[0.08em] uppercase text-stone/60 mb-2">Nama Lengkap</label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[17px] text-gray-300 transition-colors group-focus-within:text-forest">person</span>
                            <input name="name" type="text" value="{{ old('name', Auth::user()->name) }}" required
                                   class="w-full h-11 bg-gray-50 border border-gray-200 text-ink text-[13px] rounded-xl pl-10 pr-4 focus:bg-white focus:border-forest/40 focus:ring-4 focus:ring-forest/8 focus:outline-none transition-all duration-200 placeholder:text-gray-300 @error('name') border-red-400 bg-red-50 @enderror">
                        </div>
                        @error('name') <p class="mt-1 text-[11px] text-red-500 flex items-center gap-1"><span class="material-symbols-outlined text-[12px]">error</span>{{ $message }}</p> @enderror
                    </div>

                    {{-- Username --}}
                    <div>
                        <label class="block font-sans text-[11px] font-semibold tracking-[0.08em] uppercase text-stone/60 mb-2">Username</label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[17px] text-gray-300 transition-colors group-focus-within:text-forest">alternate_email</span>
                            <input name="username" type="text" value="{{ old('username', Auth::user()->username) }}" required
                                   class="w-full h-11 bg-gray-50 border border-gray-200 text-ink text-[13px] rounded-xl pl-10 pr-4 focus:bg-white focus:border-forest/40 focus:ring-4 focus:ring-forest/8 focus:outline-none transition-all duration-200 placeholder:text-gray-300 @error('username') border-red-400 bg-red-50 @enderror">
                        </div>
                        @error('username') <p class="mt-1 text-[11px] text-red-500 flex items-center gap-1"><span class="material-symbols-outlined text-[12px]">error</span>{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block font-sans text-[11px] font-semibold tracking-[0.08em] uppercase text-stone/60 mb-2">Email</label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[17px] text-gray-300 transition-colors group-focus-within:text-forest">mail</span>
                            <input name="email" type="email" value="{{ old('email', Auth::user()->email) }}" required
                                   class="w-full h-11 bg-gray-50 border border-gray-200 text-ink text-[13px] rounded-xl pl-10 pr-4 focus:bg-white focus:border-forest/40 focus:ring-4 focus:ring-forest/8 focus:outline-none transition-all duration-200 placeholder:text-gray-300 @error('email') border-red-400 bg-red-50 @enderror">
                        </div>
                        @error('email') <p class="mt-1 text-[11px] text-red-500 flex items-center gap-1"><span class="material-symbols-outlined text-[12px]">error</span>{{ $message }}</p> @enderror
                    </div>

                    {{-- Telepon --}}
                    <div>
                        <label class="block font-sans text-[11px] font-semibold tracking-[0.08em] uppercase text-stone/60 mb-2">Nomor Telepon</label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[17px] text-gray-300 transition-colors group-focus-within:text-forest">phone</span>
                            <input name="phone" type="tel" value="{{ old('phone', Auth::user()->phone) }}" placeholder="+62 812 3456 7890"
                                   class="w-full h-11 bg-gray-50 border border-gray-200 text-ink text-[13px] rounded-xl pl-10 pr-4 focus:bg-white focus:border-forest/40 focus:ring-4 focus:ring-forest/8 focus:outline-none transition-all duration-200 placeholder:text-gray-300 @error('phone') border-red-400 bg-red-50 @enderror">
                        </div>
                        @error('phone') <p class="mt-1 text-[11px] text-red-500 flex items-center gap-1"><span class="material-symbols-outlined text-[12px]">error</span>{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-2 border-t border-gray-100 mt-6">
                    <button type="submit"
                            class="group inline-flex items-center gap-2 bg-forest hover:bg-leaf text-white font-sans text-[13px] font-semibold px-6 py-2.5 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                        <span class="material-symbols-outlined text-[15px]">save</span>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('landing') }}"
                       class="font-sans text-[13px] text-stone border border-gray-200 px-6 py-2.5 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Tab: Atur Kata Sandi (Google users) ── --}}
    @if(Auth::user()->google_id)
    <div x-show="tab==='password'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">

            <div class="mb-7">
                <h3 class="font-serif text-[1.2rem] text-ink">Atur Kata Sandi</h3>
                <p class="font-sans text-xs text-stone mt-1">Tambahkan kata sandi agar bisa login dengan email & password.</p>
            </div>

            {{-- Google info banner --}}
            <div class="flex items-start gap-3 p-4 bg-blue-50 border border-blue-100 rounded-2xl mb-6">
                <svg width="16" height="16" viewBox="0 0 48 48" class="flex-shrink-0 mt-0.5"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.54 28.59A14.5 14.5 0 0 1 9.5 24c0-1.59.28-3.14.76-4.59l-7.98-6.19A23.99 23.99 0 0 0 0 24c0 3.77.87 7.35 2.56 10.56l7.98-5.97z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 5.97C6.51 42.62 14.62 48 24 48z"/></svg>
                <p class="font-sans text-[12px] text-blue-700 leading-relaxed">Akun Anda terhubung dengan Google. Atur kata sandi di bawah untuk mengaktifkan login email.</p>
            </div>

            @if(session('status') === 'password-set')
            <div class="mb-5 flex items-center gap-3 px-5 py-3 rounded-2xl bg-forest/8 border border-forest/15 text-forest font-sans text-sm">
                <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 1,'wght' 300;">check_circle</span>
                Kata sandi berhasil diatur. Sekarang Anda bisa login dengan email dan password.
            </div>
            @endif

            <form method="POST" action="{{ route('profile.set-password') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-sans text-[11px] font-semibold tracking-[0.08em] uppercase text-stone/60 mb-2">Kata Sandi Baru</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[17px] text-gray-300 group-focus-within:text-forest transition-colors">lock</span>
                        <input name="password" type="password" required minlength="8"
                               class="w-full h-11 bg-gray-50 border border-gray-200 text-ink text-[13px] rounded-xl pl-10 pr-10 focus:bg-white focus:border-forest/40 focus:ring-4 focus:ring-forest/8 focus:outline-none transition-all duration-200 placeholder:text-gray-300 @error('password') border-red-400 bg-red-50 @enderror"
                               placeholder="Minimal 8 karakter">
                        <button type="button" onclick="togglePwd('new-pwd','eye-new')" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-300 hover:text-stone transition-colors">
                            <span id="eye-new" class="material-symbols-outlined text-[16px]">visibility_off</span>
                        </button>
                    </div>
                    @error('password') <p class="mt-1 text-[11px] text-red-500 flex items-center gap-1"><span class="material-symbols-outlined text-[12px]">error</span>{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-sans text-[11px] font-semibold tracking-[0.08em] uppercase text-stone/60 mb-2">Konfirmasi Kata Sandi</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[17px] text-gray-300 group-focus-within:text-forest transition-colors">lock_reset</span>
                        <input id="new-pwd-confirm" name="password_confirmation" type="password" required
                               class="w-full h-11 bg-gray-50 border border-gray-200 text-ink text-[13px] rounded-xl pl-10 pr-10 focus:bg-white focus:border-forest/40 focus:ring-4 focus:ring-forest/8 focus:outline-none transition-all duration-200 placeholder:text-gray-300"
                               placeholder="Ulangi kata sandi">
                        <button type="button" onclick="togglePwd('new-pwd-confirm','eye-conf')" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-300 hover:text-stone transition-colors">
                            <span id="eye-conf" class="material-symbols-outlined text-[16px]">visibility_off</span>
                        </button>
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-100 mt-4">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-forest hover:bg-leaf text-white font-sans text-[13px] font-semibold px-6 py-2.5 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md">
                        <span class="material-symbols-outlined text-[15px]">lock</span>
                        Atur Kata Sandi
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>{{-- end right col --}}
</div>{{-- end grid --}}
</div>{{-- end container --}}
</div>{{-- end outer --}}

@push('scripts')
<script>
function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (!input || !icon) return;
    const hidden = input.type === 'password';
    input.type  = hidden ? 'text' : 'password';
    icon.textContent = hidden ? 'visibility' : 'visibility_off';
}
</script>
@endpush
@endsection
