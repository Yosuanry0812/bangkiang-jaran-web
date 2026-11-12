<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, interactive-widget=resizes-content">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Bangkiang Jaran'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500;1,600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/ui.js'])
    <style>
        .material-symbols-outlined { font-family: 'Material Symbols Outlined' !important; font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .card-shadow { box-shadow: 0px 4px 20px rgba(0, 50, 40, 0.04); }
    </style>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <link rel="stylesheet" href="{{ asset('css/visual-refresh.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth-refinement.css') }}">
    <link rel="stylesheet" href="{{ asset('css/typography-motion.css') . '?v=' . filemtime(public_path('css/typography-motion.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/responsive-refinement.css') . '?v=' . filemtime(public_path('css/responsive-refinement.css')) }}">
</head>
<body data-page="{{ Route::currentRouteName() }}" data-motion-profile="balanced" class="bj-guest {{ Route::is('login', 'register', 'registration.otp*') ? 'bj-auth-page' : '' }} font-body antialiased min-h-screen flex flex-col selection:bg-emerald-100">

    {{-- ── Subtle background texture ── --}}
    <div class="fixed inset-0 pointer-events-none" style="background: radial-gradient(ellipse 80% 60% at 20% 10%, rgba(0,100,70,0.06) 0%, transparent 60%), radial-gradient(ellipse 60% 50% at 80% 90%, rgba(0,80,60,0.05) 0%, transparent 55%);"></div>

    {{-- ── Minimal top nav ── --}}
    <nav class="relative z-10 py-5 px-8">
        <div class="flex justify-between items-center max-w-6xl mx-auto">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 group">
                <span class="font-display text-[15px] font-semibold text-emerald-900 tracking-wide">Bangkiang Jaran</span>
            </a>
            <div class="flex items-center gap-1">
                <a href="{{ route('lang.switch', app()->getLocale() === 'id' ? 'en' : 'id') }}"
                   class="inline-flex items-center gap-1 font-body text-[13px] font-medium text-gray-600 hover:text-emerald-800 hover:bg-white/70 transition-all px-3 py-2 rounded-xl"
                   title="{{ app()->getLocale() === 'id' ? 'Switch to English' : 'Ganti ke Bahasa Indonesia' }}">
                    <span class="material-symbols-outlined text-[15px]">translate</span>
                    {{ app()->getLocale() === 'id' ? 'EN' : 'ID' }}
                </a>
                <a href="{{ route('login') }}" class="font-body text-[13px] font-medium text-gray-600 hover:text-emerald-800 hover:bg-white/70 transition-all px-4 py-2 rounded-xl">{{ __('messages.login') }}</a>
                <a href="{{ route('register') }}" class="font-body text-[13px] font-semibold bg-emerald-800 text-white px-5 py-2 rounded-xl hover:bg-emerald-700 shadow-sm hover:shadow-md transition-all">{{ __('messages.register') }}</a>
            </div>
        </div>
    </nav>

    {{-- Content --}}
    <main class="relative z-10 flex-grow flex items-center justify-center py-8 px-6">
        <div class="w-full flex flex-col items-center">
            @yield('content')
            {{ $slot ?? '' }}
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script defer src="{{ asset('js/site.js') . '?v=' . filemtime(public_path('js/site.js')) }}"></script>
    @stack('scripts')
</body>
</html>
