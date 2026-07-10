@extends('layouts.guest')

@section('content')
<style>
    .input-glass {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(110, 121, 117, 0.2);
        transition: all 0.3s ease;
    }
    .input-glass:focus {
        background: rgba(255, 255, 255, 0.95);
        border-color: #005344;
        box-shadow: 0 0 0 4px rgba(0, 83, 68, 0.1);
        outline: none;
    }
    .bg-pattern {
        background-color: #fbf9f4;
        background-image: radial-gradient(#bec9c4 1px, transparent 1px);
        background-size: 20px 20px;
    }
</style>

<div class="relative w-full max-w-[500px]">
    {{-- Decorative Ambient Elements --}}
    <div class="absolute top-[-10%] left-[-5%] w-[40%] h-[50%] bg-primary/10 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-5%] w-[30%] h-[40%] bg-tertiary/10 rounded-full blur-[80px] pointer-events-none"></div>

    {{-- Registration Card --}}
    <div class="relative z-10 bg-surface-container-lowest/80 backdrop-blur-xl rounded-2xl shadow-[0px_12px_40px_rgba(0,50,68,0.08)] p-8 md:p-10 border border-surface-variant/50">
        <div class="text-center mb-10">
            <h1 class="font-display text-headline-md text-on-surface mb-2">Create Account</h1>
            <p class="font-body text-body-md text-on-surface-variant">Join us to explore the tropical elegance of Bali.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            {{-- Nama Lengkap --}}
            <div class="space-y-1.5">
                <label for="name" class="font-body text-label-md text-on-surface-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">person</span>
                    Full Name
                </label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                       class="input-glass w-full rounded-xl px-4 py-3 font-body text-body-md text-on-surface placeholder:text-outline-variant focus:outline-none @error('name') border-error @enderror"
                       placeholder="John Doe">
                @error('name')
                    <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Username --}}
            <div class="space-y-1.5">
                <label for="username" class="font-body text-label-md text-on-surface-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">badge</span>
                    Username
                </label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autocomplete="username"
                       class="input-glass w-full rounded-xl px-4 py-3 font-body text-body-md text-on-surface placeholder:text-outline-variant focus:outline-none @error('username') border-error @enderror"
                       placeholder="Choose a username">
                @error('username')
                    <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="space-y-1.5">
                <label for="email" class="font-body text-label-md text-on-surface-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">mail</span>
                    Email Address
                </label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                       class="input-glass w-full rounded-xl px-4 py-3 font-body text-body-md text-on-surface placeholder:text-outline-variant focus:outline-none @error('email') border-error @enderror"
                       placeholder="john@example.com">
                @error('email')
                    <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone --}}
            <div class="space-y-1.5">
                <label for="phone" class="font-body text-label-md text-on-surface-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">phone</span>
                    Phone Number
                </label>
                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel"
                       class="input-glass w-full rounded-xl px-4 py-3 font-body text-body-md text-on-surface placeholder:text-outline-variant focus:outline-none @error('phone') border-error @enderror"
                       placeholder="+62 812 3456 7890">
                @error('phone')
                    <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="space-y-1.5">
                <label for="password" class="font-body text-label-md text-on-surface-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">lock</span>
                    Password
                </label>
                <div class="relative">
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                           class="input-glass w-full rounded-xl px-4 py-3 font-body text-body-md text-on-surface placeholder:text-outline-variant focus:outline-none @error('password') border-error @enderror"
                           placeholder="Min. 8 characters">
                    <button type="button" onclick="togglePassword('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors" tabindex="-1">
                        <span class="material-symbols-outlined text-[20px] pointer-events-none">visibility</span>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="space-y-1.5">
                <label for="password_confirmation" class="font-body text-label-md text-on-surface-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">lock_reset</span>
                    Confirm Password
                </label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                       class="input-glass w-full rounded-xl px-4 py-3 font-body text-body-md text-on-surface placeholder:text-outline-variant focus:outline-none @error('password_confirmation') border-error @enderror"
                       placeholder="Repeat password">
                @error('password_confirmation')
                    <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="pt-2">
                <button type="submit"
                        class="w-full bg-primary-container text-white font-body text-label-md py-3.5 px-6 rounded-xl hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300 flex items-center justify-center gap-2 group">
                    Daftar
                    <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </button>
            </div>
        </form>

        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" class="font-body text-body-md text-tertiary hover:text-primary underline decoration-tertiary/30 hover:decoration-primary transition-colors inline-flex items-center gap-1">
                Sudah punya akun? Login di sini
            </a>
        </div>
    </div>
</div>

<script>
    function togglePassword(id, btn) {
        const input = document.getElementById(id);
        const icon = btn.querySelector('.material-symbols-outlined');
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }
</script>
@endsection