@extends('layouts.app')

@section('title', 'Profil - ' . config('app.name'))

@section('content')
<section class="py-section px-gutter bg-coconut min-h-screen">
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="mb-10" data-aos="fade-up">
            <span class="label-sm text-jungle">Akun</span>
            <h1 class="heading-lg mt-2">{{ __('messages.my_account') }}</h1>
            <p class="body-base mt-3">{{ __('messages.manage_account') }}</p>
        </div>

        @if(session('status') === 'profile-updated')
        <div class="mb-6 p-4 rounded-lg bg-jungle/10 text-jungle font-sans text-sm flex items-center gap-2" data-aos="fade-down">
            <span class="material-symbols-outlined text-sm">check_circle</span>
            {{ __('messages.profile_updated') }}
        </div>
        @endif

        {{-- Card --}}
        <div class="bg-cream rounded-xl border border-mist overflow-hidden soft-lift" data-aos="fade-up" data-aos-delay="60">
            <div class="flex flex-col md:flex-row">
                {{-- Avatar Side --}}
                <div class="md:w-72 p-6 md:p-8 bg-sand/30 flex flex-col items-center text-center border-b md:border-b-0 md:border-r border-mist">
                    <div class="w-24 h-24 rounded-full bg-jungle/10 flex items-center justify-center mb-4 overflow-hidden">
                        @if(Auth::user()->google_id)
                            <img src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=2D5A3D&color=fff&size=96' }}"
                                 alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="font-serif text-3xl text-jungle">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                        @endif
                    </div>
                    <h2 class="font-serif text-xl text-deep">{{ Auth::user()->name }}</h2>
                    <p class="font-sans text-sm text-gray-400/60 mt-1">{{ Auth::user()->email }}</p>
                    <span class="mt-3 inline-flex items-center gap-1 px-3 py-1 rounded-full bg-jungle/10 text-jungle font-sans text-xs">
                        <span class="material-symbols-outlined text-xs">verified</span>
                        {{ Auth::user()->role === 'pengelola' ? 'Pengelola' : 'Wisatawan' }}
                    </span>
                </div>

                {{-- Form --}}
                <div class="flex-1 p-6 md:p-8">
                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label class="font-sans text-xs text-gray-400/60 block">Nama Lengkap</label>
                                <input name="name" value="{{ old('name', Auth::user()->name) }}" required
                                       class="w-full bg-sand border-0 rounded-lg px-4 py-2.5 font-sans text-sm text-deep focus:ring-2 focus:ring-jungle/30 outline-none @error('name') ring-2 ring-sunrise/30 @enderror">
                                @error('name') <p class="text-xs text-sunrise mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="font-sans text-xs text-gray-400/60 block">Username</label>
                                <input name="username" value="{{ old('username', Auth::user()->username) }}" required
                                       class="w-full bg-sand border-0 rounded-lg px-4 py-2.5 font-sans text-sm text-deep focus:ring-2 focus:ring-jungle/30 outline-none @error('username') ring-2 ring-sunrise/30 @enderror">
                                @error('username') <p class="text-xs text-sunrise mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="font-sans text-xs text-gray-400/60 block">Email</label>
                                <input name="email" type="email" value="{{ old('email', Auth::user()->email) }}" required
                                       class="w-full bg-sand border-0 rounded-lg px-4 py-2.5 font-sans text-sm text-deep focus:ring-2 focus:ring-jungle/30 outline-none @error('email') ring-2 ring-sunrise/30 @enderror">
                                @error('email') <p class="text-xs text-sunrise mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="font-sans text-xs text-gray-400/60 block">Nomor Telepon</label>
                                <input name="phone" type="tel" value="{{ old('phone', Auth::user()->phone) }}"
                                       placeholder="+62 812 3456 7890"
                                       class="w-full bg-sand border-0 rounded-lg px-4 py-2.5 font-sans text-sm text-deep focus:ring-2 focus:ring-jungle/30 outline-none @error('phone') ring-2 ring-sunrise/30 @enderror">
                                @error('phone') <p class="text-xs text-sunrise mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3 pt-2">
                            <button type="submit"
                                    class="bg-deep text-cream font-sans text-sm font-medium px-6 py-2.5 rounded-lg hover:bg-deep/90 transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">save</span>
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('landing') }}"
                               class="border border-mist text-gray-500 font-sans text-sm px-6 py-2.5 rounded-lg hover:bg-sand/50 transition-colors">
                                Batal
                            </a>
                        </div>
                    </form>

                    {{-- Info --}}
                    <div class="mt-8 pt-6 border-t border-mist">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-sans text-xs text-gray-400/60">Bergabung sejak</span>
                                <p class="font-sans text-sm text-deep mt-0.5">{{ Auth::user()->created_at->format('d M Y') }}</p>
                            </div>
                            <div>
                                <span class="font-sans text-xs text-gray-400/60">Metode Login</span>
                                <p class="font-sans text-sm text-deep mt-0.5 flex items-center gap-1.5">
                                    @if(Auth::user()->google_id)
                                        <svg width="14" height="14" viewBox="0 0 48 48" class="inline"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.54 28.59A14.5 14.5 0 0 1 9.5 24c0-1.59.28-3.14.76-4.59l-7.98-6.19A23.99 23.99 0 0 0 0 24c0 3.77.87 7.35 2.56 10.56l7.98-5.97z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 5.97C6.51 42.62 14.62 48 24 48z"/></svg>
                                        Google
                                    @else
                                        <span class="material-symbols-outlined text-sm">mail</span>
                                        Email & Password
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    @if(Auth::user()->google_id)
                    {{-- Set Password for Google Users --}}
                    <div class="mt-8 pt-6 border-t border-mist">
                        <h3 class="font-serif text-lg text-deep mb-4">Atur Kata Sandi</h3>
                        <p class="font-sans text-xs text-gray-400/60 mb-4">
                            Kamu login dengan Google. Atur kata sandi biar bisa login pakai email dan password juga.
                        </p>

                        @if(session('status') === 'password-set')
                        <div class="mb-4 p-3 rounded-lg bg-jungle/10 text-jungle font-sans text-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">check_circle</span>
                            Kata sandi berhasil diatur. Sekarang kamu bisa login pakai email dan password.
                        </div>
                        @endif

                        <form method="POST" action="{{ route('profile.set-password') }}" class="space-y-4 max-w-md">
                            @csrf
                            <div class="space-y-1.5">
                                <label class="font-sans text-xs text-gray-400/60 block">Kata Sandi Baru</label>
                                <input name="password" type="password" required
                                       class="w-full bg-sand border-0 rounded-lg px-4 py-2.5 font-sans text-sm text-deep focus:ring-2 focus:ring-jungle/30 outline-none @error('password') ring-2 ring-sunrise/30 @enderror"
                                       placeholder="Minimal 8 karakter">
                                @error('password') <p class="text-xs text-sunrise mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="font-sans text-xs text-gray-400/60 block">Konfirmasi Kata Sandi Baru</label>
                                <input name="password_confirmation" type="password" required
                                       class="w-full bg-sand border-0 rounded-lg px-4 py-2.5 font-sans text-sm text-deep focus:ring-2 focus:ring-jungle/30 outline-none"
                                       placeholder="Ulangi kata sandi">
                            </div>
                            <button type="submit"
                                    class="bg-deep text-cream font-sans text-sm font-medium px-6 py-2.5 rounded-lg hover:bg-deep/90 transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">lock</span>
                                Atur Kata Sandi
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
