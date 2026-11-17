@extends('layouts.guest')
@section('title', 'Buat Username — Bangkiang Jaran')

@section('content')
<div class="bj-auth-card w-full max-w-4xl grid md:grid-cols-[1fr_1.2fr] bg-white rounded-3xl overflow-hidden shadow-2xl shadow-black/10 border border-gray-100/80">

    {{-- ── Left: Image Panel ── --}}
    <div class="bj-auth-image hidden md:flex relative flex-col justify-between overflow-hidden min-h-[560px]">
        <img src="{{ asset('images/sejarah-bangkiang-waterfall.webp') }}"
             alt="Bangkiang Jaran Waterfall"
             class="absolute inset-0 w-full h-full object-cover">

        <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(0,40,30,0.7) 0%, rgba(0,0,0,0.2) 50%, transparent 100%);"></div>
        <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,30,20,0.9) 0%, transparent 55%);"></div>

        <div class="relative z-10 p-8">
            <div class="inline-flex items-center gap-2.5">
                <span class="font-display text-white font-semibold text-lg tracking-wide">Bangkiang Jaran</span>
            </div>
        </div>

        <div class="relative z-10 p-8 text-white">
            <p class="text-[11px] uppercase tracking-[.2em] text-white/70 mb-2">Pendaftaran Akun Baru</p>
            <p class="font-display text-[2.2rem] leading-tight max-w-xs">Satu langkah lagi menuju petualangan Anda.</p>
        </div>
    </div>

    {{-- ── Right: Username Form ── --}}
    <div class="bj-auth-form flex flex-col justify-center px-8 py-10 md:px-12 md:py-12 bg-white">

        {{-- Badge --}}
        <div class="mb-6">
            <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-100 text-emerald-800 text-[11px] font-body font-semibold tracking-[0.1em] uppercase px-3 py-1.5 rounded-full mb-4">
                <svg width="14" height="14" viewBox="0 0 48 48" class="flex-shrink-0">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.54 28.59A14.5 14.5 0 0 1 9.5 24c0-1.59.28-3.14.76-4.59l-7.98-6.19A23.99 23.99 0 0 0 0 24c0 3.77.87 7.35 2.56 10.56l7.98-5.97z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 5.97C6.51 42.62 14.62 48 24 48z"/>
                </svg>
                Google Terhubung
            </div>
            <h1 class="font-display text-[1.85rem] font-semibold text-gray-900 leading-tight mb-2">Buat Username Anda</h1>
            <p class="font-body text-[13.5px] text-gray-600 leading-relaxed">
                Halo <strong class="text-gray-900">{{ $name }}</strong> (<span class="text-gray-500 text-xs">{{ $email }}</span>), silakan tentukan username Anda secara mandiri untuk kemudahan transaksi dan pemesanan tiket.
            </p>
        </div>

        {{-- Role Wisatawan Notice --}}
        <div class="mb-5 p-3.5 rounded-2xl bg-stone-50 border border-stone-200/80 flex items-start gap-2.5">
            <span class="material-symbols-outlined text-[18px] text-emerald-700 mt-0.5">verified_user</span>
            <p class="font-body text-[12px] text-stone-600 leading-snug">
                Akun ini otomatis terdaftar sebagai <strong>Wisatawan</strong> untuk melakukan reservasi tiket, e-ticket, dan menikmati fasilitas Bangkiang Jaran.
            </p>
        </div>

        {{-- Form Buat Username --}}
        <form method="POST" action="{{ route('auth.google.username.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="username" class="block font-body text-[11px] font-semibold tracking-[0.08em] uppercase text-gray-500 mb-2">
                    Username Pilihan Anda <span class="text-red-500">*</span>
                </label>
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-[18px] transition-colors group-focus-within:text-emerald-700">alternate_email</span>
                    <input id="username"
                           name="username"
                           type="text"
                           value="{{ old('username') }}"
                           required
                           autofocus
                           minlength="3"
                           maxlength="50"
                           pattern="[a-zA-Z0-9_.-]+"
                           placeholder="contoh: nama_wisatawan"
                           class="w-full h-12 bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-2xl pl-11 pr-4 focus:bg-white focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/10 focus:outline-none transition-all placeholder:text-gray-400 @error('username') border-red-400 bg-red-50/50 @enderror">
                </div>

                @error('username')
                    <p class="mt-2 text-xs text-red-600 flex items-center gap-1 font-body">
                        <span class="material-symbols-outlined text-[14px]">error</span>
                        {{ $message }}
                    </p>
                @enderror

                <p class="mt-2 text-[11.5px] text-gray-400 font-body leading-relaxed">
                    Gunakan 3–50 karakter: huruf, angka, titik (.), strip (-), atau garis bawah (_). Username ini unik dan mewakili profil pemesanan Anda.
                </p>
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="w-full h-12 bg-emerald-800 hover:bg-emerald-700 active:bg-emerald-900 text-white font-body font-semibold text-sm rounded-2xl transition-all shadow-md shadow-emerald-900/15 flex items-center justify-center gap-2 group cursor-pointer">
                    <span>Simpan Username &amp; Lanjutkan</span>
                    <span class="material-symbols-outlined text-[17px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                </button>
            </div>
        </form>

        {{-- Batal --}}
        <div class="mt-5 pt-4 border-t border-gray-100 flex justify-center">
            <form method="POST" action="{{ route('auth.google.cancel') }}">
                @csrf
                <button type="submit" class="font-body text-xs text-gray-400 hover:text-gray-700 hover:underline transition-colors cursor-pointer bg-transparent border-0 p-0">
                    Batal dan masuk dengan akun lain
                </button>
            </form>
        </div>

    </div>

</div>
@endsection
