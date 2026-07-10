<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Bangkiang Jaran'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="image" href="{{ asset('images/sejarah-bangkiang-waterfall.webp') }}" fetchpriority="high">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-primary-fixed-variant": "#005143","outline": "#6e7975","surface-container": "#f0eee9","tertiary": "#5b442a","on-background": "#1b1c19","on-surface-variant": "#3e4945","primary": "#005344","surface-container-highest": "#e4e2dd","surface": "#fbf9f4","on-secondary-fixed-variant": "#23501e","on-primary-fixed": "#00201a","surface-container-low": "#f5f3ee","tertiary-fixed-dim": "#e3c19f","primary-fixed": "#9df3dc","secondary": "#3b6934","primary-fixed-dim": "#81d6c0","on-tertiary": "#ffffff","secondary-fixed": "#bcf0ae","inverse-surface": "#30312e","on-secondary-fixed": "#002201","surface-dim": "#dbdad5","on-tertiary-fixed-variant": "#5a4229","outline-variant": "#bec9c4","secondary-container": "#b9eeab","on-tertiary-fixed": "#291803","error": "#ba1a1a","surface-variant": "#e4e2dd","on-primary": "#ffffff","inverse-on-surface": "#f2f1ec","background": "#fbf9f4","inverse-primary": "#81d6c0","surface-container-lowest": "#ffffff","on-error-container": "#93000a","tertiary-container": "#755b40","surface-container-high": "#eae8e3","on-primary-container": "#96ebd5","tertiary-fixed": "#ffddbb","on-secondary-container": "#3f6d38","surface-tint": "#006b59","on-tertiary-container": "#f8d5b2","on-error": "#ffffff","on-secondary": "#ffffff","on-surface": "#1b1c19","primary-container": "#006d5b","surface-bright": "#fbf9f4","secondary-fixed-dim": "#a1d494","error-container": "#ffdad6"
                    },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "2xl": "1.5rem", "full": "9999px" },
                    spacing: { "xl": "80px", "container-max": "1280px", "lg": "48px", "md": "24px", "sm": "12px", "base": "8px", "xs": "4px", "gutter": "24px" },
                    fontFamily: { "display": ["Playfair Display", "serif"], "body": ["Plus Jakarta Sans", "sans-serif"] },
                    fontSize: {
                        "display-lg": ["64px", { lineHeight: "1.1", letterSpacing: "-0.02em", fontWeight: "700" }],
                        "display-mobile": ["40px", { lineHeight: "1.2", fontWeight: "700" }],
                        "headline-md": ["32px", { lineHeight: "1.3", fontWeight: "600" }],
                        "headline-sm": ["24px", { lineHeight: "1.4", fontWeight: "600" }],
                        "body-lg": ["18px", { lineHeight: "1.6", fontWeight: "400" }],
                        "body-md": ["16px", { lineHeight: "1.6", fontWeight: "400" }],
                        "label-md": ["14px", { lineHeight: "1.2", letterSpacing: "0.05em", fontWeight: "600" }],
                        "caption": ["12px", { lineHeight: "1.4", fontWeight: "500" }],
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .icon-fill { font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .glass-nav { background: rgba(251, 249, 244, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
        .card-shadow { box-shadow: 0 4px 20px rgba(0, 50, 40, 0.04); }
        .soft-shadow { box-shadow: 0px 4px 20px rgba(0, 50, 40, 0.04); }
        .text-shadow { text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); }
        .btn-primary { @apply bg-primary-container text-white rounded-xl font-body text-label-md hover:-translate-y-0.5 hover:shadow-lg transition-all duration-300; }
        .animate-modal-in { animation: modalIn 0.3s ease-out; }
        @keyframes modalIn { from { opacity:0; transform:scale(0.9); } to { opacity:1; transform:scale(1); } }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-surface text-on-surface font-body antialiased min-h-screen flex flex-col selection:bg-primary-container selection:text-on-primary-container">
    {{-- Header --}}
    <header class="bg-surface/80 backdrop-blur-md shadow-sm sticky top-0 z-50 transition-all duration-500 ease-in-out" id="main-nav">
        <div class="flex justify-between items-center px-gutter py-md max-w-container-max mx-auto w-full">
            <a href="{{ route('landing') }}" class="font-display text-headline-sm font-bold text-primary tracking-tight">Bangkiang Jaran</a>
            <div class="hidden md:flex items-center gap-md">
                <div class="relative group">
                    <button id="menuLabel" class="font-body text-body-md uppercase tracking-wider text-on-surface-variant hover:text-primary transition-colors duration-300 px-sm py-xs rounded-lg inline-flex items-center gap-xs">
                        <span>{{ __('messages.menu') }}</span>
                        <span class="material-symbols-outlined text-sm transition-transform duration-300 group-hover:rotate-180">expand_more</span>
                    </button>
                    <div class="absolute top-full right-0 mt-2 w-56 bg-surface-container-lowest rounded-2xl card-shadow opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform origin-top-right scale-95 group-hover:scale-100 z-50 overflow-hidden">
                        <a href="{{ route('landing') }}" onclick="document.getElementById('menuLabel').querySelector('span').textContent='{{ __('messages.home') }}'"
                           class="flex items-center gap-sm px-lg py-md font-body text-body-md text-on-surface-variant hover:text-primary hover:bg-primary/5 transition-colors @if(request()->routeIs('landing')) text-primary bg-primary/5 @endif">
                            <span class="material-symbols-outlined text-sm">home</span>
                            {{ __('messages.home') }}
                        </a>
                        <a href="{{ route('tiket.index') }}" onclick="document.getElementById('menuLabel').querySelector('span').textContent='{{ __('messages.tickets') }}'"
                           class="flex items-center gap-sm px-lg py-md font-body text-body-md text-on-surface-variant hover:text-primary hover:bg-primary/5 transition-colors @if(request()->routeIs('tiket*')) text-primary bg-primary/5 @endif">
                            <span class="material-symbols-outlined text-sm">confirmation_number</span>
                            {{ __('messages.tickets') }}
                        </a>
                        @auth
                        <a href="{{ route('wisatawan.pemesanan.riwayat') }}" onclick="document.getElementById('menuLabel').querySelector('span').textContent='{{ __('messages.history') }}'"
                           class="flex items-center gap-sm px-lg py-md font-body text-body-md text-on-surface-variant hover:text-primary hover:bg-primary/5 transition-colors">
                            <span class="material-symbols-outlined text-sm">history</span>
                            {{ __('messages.history') }}
                        </a>
                        @else
                        <a href="javascript:void(0)" onclick="document.getElementById('loginModal').classList.remove('hidden')"
                           class="flex items-center gap-sm px-lg py-md font-body text-body-md text-on-surface-variant hover:text-primary hover:bg-primary/5 transition-colors">
                            <span class="material-symbols-outlined text-sm">history</span>
                            {{ __('messages.history') }}
                        </a>
                        @endauth
                    </div>
                </div>
                {{-- Language Switcher --}}
                <a href="{{ route('lang.switch', app()->getLocale() === 'id' ? 'en' : 'id') }}"
                   class="font-body text-label-md text-primary hover:bg-primary/5 transition-colors px-4 py-2 rounded-xl flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">language</span>
                    {{ app()->getLocale() === 'id' ? 'EN' : 'ID' }}
                </a>
                @auth
                <a href="{{ route('logout.get') }}" class="font-body text-label-md text-primary hover:bg-primary/5 transition-colors px-4 py-2 rounded-xl">{{ __('messages.logout') }}</a>
                @else
                <a href="{{ route('login') }}" class="font-body text-label-md text-primary hover:bg-primary/5 transition-colors px-4 py-2 rounded-xl">{{ __('messages.login') }}</a>
                <a href="{{ route('register') }}" class="font-body text-label-md bg-primary-container text-white px-6 py-2 rounded-xl shadow-sm hover:-translate-y-0.5 transition-transform">{{ __('messages.register') }}</a>
                @endauth
            </div>
            <button class="md:hidden text-primary" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">
                <span class="material-symbols-outlined">menu</span>
            </button>
        </div>
        <div id="mobileMenu" class="hidden md:hidden bg-surface border-t border-outline-variant/20 px-gutter py-md space-y-3">
            <a href="{{ route('landing') }}" class="block font-body text-body-md text-on-surface-variant hover:text-primary">{{ __('messages.home') }}</a>
            <a href="#lokasi" class="block font-body text-body-md text-on-surface-variant hover:text-primary">{{ __('messages.location') }}</a>
            <a href="{{ route('tiket.index') }}" class="block font-body text-body-md text-on-surface-variant hover:text-primary">{{ __('messages.tickets') }}</a>
            @auth
            <a href="{{ route('wisatawan.pemesanan.riwayat') }}" class="block font-body text-body-md text-on-surface-variant hover:text-primary">{{ __('messages.history') }}</a>
            <a href="{{ route('logout.get') }}" class="block font-body text-body-md text-primary">{{ __('messages.logout') }}</a>
            @else
            <a href="javascript:void(0)" onclick="document.getElementById('loginModal').classList.remove('hidden')" class="block font-body text-body-md text-on-surface-variant hover:text-primary">{{ __('messages.history') }}</a>
            <a href="{{ route('login') }}" class="block font-body text-body-md text-primary">{{ __('messages.login') }}</a>
            <a href="{{ route('register') }}" class="block font-body text-body-md text-primary">{{ __('messages.register') }}</a>
            @endauth
        </div>
    </header>

    {{-- Modal Login Prompt --}}
    <div id="loginModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-surface-container-lowest rounded-2xl card-shadow p-xl max-w-sm mx-4 text-center transform scale-95 animate-modal-in">
            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-md">
                <span class="material-symbols-outlined text-primary text-3xl">lock</span>
            </div>
            <h3 class="font-display text-headline-sm text-on-background mb-sm">{{ __('messages.login_required') }}</h3>
            <p class="font-body text-body-md text-on-surface-variant mb-lg">{{ __('messages.login_required_desc') }}</p>
            <div class="flex flex-col sm:flex-row gap-sm justify-center">
                <a href="{{ route('login') }}"
                   class="bg-primary-container text-white font-body text-label-md px-6 py-3 rounded-xl hover:-translate-y-0.5 transition-all">{{ __('messages.login') }}</a>
                <a href="{{ route('register') }}"
                   class="border border-primary text-primary font-body text-label-md px-6 py-3 rounded-xl hover:bg-primary/5 transition-all">{{ __('messages.register_btn') }}</a>
            </div>
            <button onclick="document.getElementById('loginModal').classList.add('hidden')"
                    class="mt-md font-body text-body-md text-outline hover:text-on-surface-variant transition-colors">{{ __('messages.later') }}</button>
        </div>
    </div>

    {{-- Content --}}
    <main class="flex-grow">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    {{-- Footer --}}
    <footer id="footer" class="bg-surface-container-low dark:bg-surface-container-lowest border-t border-outline-variant/20">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-lg px-gutter py-xl max-w-container-max mx-auto w-full">
            <div class="md:col-span-2">
                <h2 class="font-display text-headline-sm text-primary mb-sm">Bangkiang Jaran Waterfall</h2>
                <p class="font-body text-body-md text-on-surface-variant mb-md max-w-md">{{ __('messages.footer_desc') }}</p>
                <p class="font-body text-caption text-outline">{{ __('messages.footer_copyright', ['year' => date('Y')]) }}</p>
            </div>
            <div>
                <h3 class="font-body text-label-md text-primary mb-sm uppercase tracking-wider">{{ __('messages.nav_title') }}</h3>
                <ul class="space-y-sm">
                    <li><a href="{{ route('landing') }}" class="font-body text-body-md text-outline hover:text-primary transition-colors">{{ __('messages.home') }}</a></li>
                    <li><a href="#lokasi" class="font-body text-body-md text-outline hover:text-primary transition-colors">{{ __('messages.location') }}</a></li>
                    <li><a href="{{ route('tiket.index') }}" class="font-body text-body-md text-outline hover:text-primary transition-colors">{{ __('messages.tickets') }}</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-body text-label-md text-primary mb-sm uppercase tracking-wider">{{ __('messages.contact_title') }}</h3>
                <ul class="space-y-sm">
                    <li class="flex items-center gap-xs text-body-md text-outline">
                        <span class="material-symbols-outlined text-sm">location_on</span>
                        {{ __('messages.address') }}
                    </li>
                    <li class="flex items-center gap-xs text-body-md text-outline">
                        <span class="material-symbols-outlined text-sm">schedule</span>
                        {{ __('messages.operating_hours') }}
                    </li>
                </ul>
            </div>
        </div>
    </footer>

    {{-- Nav auto-hide on scroll + dropdown label --}}
    <script>
        // Dropdown label init
        (function() {
            const labels = {
                home: '{{ __("messages.home") }}',
                tickets: '{{ __("messages.tickets") }}',
                history: '{{ __("messages.history") }}',
            };
            const btn = document.getElementById('menuLabel');
            if (btn) {
                const span = btn.querySelector('span');
                const path = window.location.pathname;
                if (path === '/' || path === '/landing') span.textContent = labels.home;
                else if (path.startsWith('/tiket')) span.textContent = labels.tickets;
                else if (path.startsWith('/wisatawan/pemesanan/riwayat')) span.textContent = labels.history;
            }
        })();

        // Nav auto-hide
        const nav = document.getElementById('main-nav');
        if (nav) {
            let lastScroll = 0;
            nav.style.transform = 'translateY(0)';
            
            window.addEventListener('scroll', () => {
                const curr = window.scrollY;
                if (curr > 50) {
                    nav.style.background = 'rgba(255, 255, 255, 0.95)';
                    if (curr > lastScroll) {
                        nav.style.transform = 'translateY(-100%)';
                    } else {
                        nav.style.transform = 'translateY(0)';
                    }
                } else {
                    nav.style.background = 'rgba(251, 249, 244, 0.8)';
                    nav.style.transform = 'translateY(0)';
                }
                lastScroll = curr;
            });
        }
    </script>
    @stack('scripts')
</body>
</html>