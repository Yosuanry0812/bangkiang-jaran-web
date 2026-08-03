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
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:opsz,wght@14..32,300..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL@20..48,100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forest:   '#1B3A2D',
                        leaf:     '#2D5940',
                        moss:     '#4A7C59',
                        sage:     '#7FAF8A',
                        mist:     '#C8DDD0',
                        ivory:    '#F7F3EE',
                        warm:     '#EDE8E1',
                        gold:     '#C9A96E',
                        sand:     '#D4C5A9',
                        charcoal: '#1C1C1C',
                        ink:      '#2C2C2C',
                        stone:    '#6B6B6B',
                        pebble:   '#A0A0A0',
                        cream:    '#F7F3EE',
                        deep:     '#1B3A2D',
                    },
                    fontFamily: {
                        serif: ['"Instrument Serif"', 'Georgia', 'serif'],
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    spacing: {
                        'gutter': '24px',
                        'section': '96px',
                    },
                }
            }
        }
    </script>
    <style>
        @view-transition { navigation: auto; }
        /* Lock horizontal scroll at root — prevents swipe left/right on mobile */
        html, body { overflow-x: clip; }
        @supports not (overflow: clip) { html, body { overflow-x: hidden; } }
        ::selection { background: #1B3A2D; color: #F7F3EE; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24; }
        .icon-fill { font-variation-settings: 'FILL' 1, 'wght' 300, 'GRAD' 0, 'opsz' 24; }
        [x-cloak] { display: none !important; }

        /* ─── Navbar ─── */
        #main-nav {
            transition:
                transform 0.38s cubic-bezier(0.4,0,0.2,1),
                background 0.32s ease,
                border-color 0.32s ease,
                box-shadow 0.32s ease;
        }
        #main-nav.nav-hidden   { transform: translateY(-100%); }

        /* dark mode — over hero images */
        #main-nav.nav-dark-clear  { background: transparent; border-color: transparent; box-shadow: none; }
        #main-nav.nav-transparent { background: transparent; border-color: transparent; box-shadow: none; }
        #main-nav.nav-dark-tinted { background: rgba(10,20,14,0.55); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); border-color: rgba(255,255,255,0.06); }
        #main-nav.nav-dark-solid  { background: rgba(10,20,14,0.96); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-color: rgba(255,255,255,0.05); box-shadow: 0 1px 0 rgba(255,255,255,0.03); }

        /* light mode — over ivory/white pages */
        #main-nav.nav-light       { background: rgba(247,243,238,0.92); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-color: rgba(27,58,45,0.08); box-shadow: 0 1px 24px rgba(0,0,0,0.06); }

        /* mobile: slim bar, lighter glass so it doesn't read as a huge black slab */
        @media (max-width: 767px) {
            #main-nav.nav-dark-tinted { background: rgba(10,20,14,0.40); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
            #main-nav.nav-dark-solid  { background: rgba(10,20,14,0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }
            #main-nav.nav-light       { background: rgba(247,243,238,0.88); }
        }

        /* nav link tokens */
        .nav-link-dark  { color: rgba(255,255,255,0.75); }
        .nav-link-dark:hover { color: #fff; }
        .nav-link-light { color: #4B5563; }
        .nav-link-light:hover { color: #1B3A2D; }

        /* active indicator */
        .nav-link-active-dark  { color: #fff !important; }
        .nav-link-active-light { color: #1B3A2D !important; font-weight: 500; }

        /* ─── Mobile menu ─── */
        /* Closed menu must NOT take layout height inside the fixed header —
           otherwise the invisible dropdown inflates the navbar's black
           background into a thick slab on mobile. */
        #mobileMenu {
            transition:
                opacity 0.22s ease,
                transform 0.22s cubic-bezier(0.4,0,0.2,1),
                max-height 0.24s ease,
                visibility 0.24s;
            transform-origin: top;
            overflow: hidden;
        }
        #mobileMenu.menu-open {
            opacity: 1;
            transform: scaleY(1);
            pointer-events: auto;
            visibility: visible;
            max-height: calc(100vh - 48px);
            max-height: calc(100svh - 48px);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
        #mobileMenu.menu-close {
            opacity: 0;
            transform: scaleY(0.97);
            pointer-events: none;
            visibility: hidden;
            max-height: 0;
        }

        @media (prefers-reduced-motion: reduce) {
            #main-nav, #mobileMenu { transition: none; }
        }

        /* ─── Misc ─── */
        .blur-jungle  { background: radial-gradient(ellipse at center, rgba(74,124,89,0.10) 0%, transparent 70%); }
        .blur-teal    { background: radial-gradient(ellipse at center, rgba(127,175,138,0.07) 0%, transparent 70%); }
        .blur-sunrise { background: radial-gradient(ellipse at center, rgba(201,169,110,0.07) 0%, transparent 70%); }

        .soft-lift { transition: transform 0.4s cubic-bezier(0.22,1,0.36,1), box-shadow 0.4s cubic-bezier(0.22,1,0.36,1); }
        .soft-lift:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,0.06); }

        .fade-in { opacity:0; transform:translateY(24px); transition:all 0.7s cubic-bezier(0.22,1,0.36,1); }
        .fade-in.show { opacity:1; transform:translateY(0); }
        .fade-in-left { opacity:0; transform:translateX(-30px); transition:all 0.7s cubic-bezier(0.22,1,0.36,1); }
        .fade-in-left.show { opacity:1; transform:translateX(0); }
        .fade-in-right { opacity:0; transform:translateX(30px); transition:all 0.7s cubic-bezier(0.22,1,0.36,1); }
        .fade-in-right.show { opacity:1; transform:translateX(0); }

        @keyframes sway { 0%,100%{transform:rotate(0deg);}50%{transform:rotate(2deg);} }
        .sway { animation:sway 6s ease-in-out infinite; transform-origin:bottom center; }
        @keyframes pulse-soft { 0%,100%{opacity:1;}50%{opacity:0.6;} }
        .pulse-soft { animation:pulse-soft 3s ease-in-out infinite; }

        .divider-leaf { height:1px; background:linear-gradient(to right,transparent,rgba(75,83,99,0.12),transparent); }
        .divider-gold { height:1px; background:linear-gradient(to right,transparent,rgba(156,163,175,0.15),transparent); }

        .heading-xl { @apply font-serif text-5xl md:text-7xl leading-[1.08] tracking-tight text-deep; }
        .heading-lg { @apply font-serif text-3xl md:text-5xl leading-[1.12] tracking-tight text-deep; }
        .heading-md { @apply font-serif text-2xl md:text-3xl leading-[1.2] tracking-tight text-deep; }
        .body-large { @apply font-sans text-[17px] md:text-[19px] leading-relaxed text-gray-500; }
        .body-base  { @apply font-sans text-[15px] leading-relaxed text-gray-500; }
        .label-sm   { @apply font-sans text-[13px] font-medium uppercase tracking-[0.1em] text-forest/60; }
    </style>
    @stack('styles')
</head>
<body class="bg-ivory text-ink font-sans antialiased min-h-screen flex flex-col overflow-x-clip" style="selection-background: #1B3A2D;">

    {{-- Navigation --}}
    {{-- data-nav="dark"  → over hero (transparent→solid dark)  --}}
    {{-- data-nav="light" → over ivory pages (always light)     --}}
    <header id="main-nav"
            data-nav="{{ $navMode ?? (View::hasSection('nav-mode') ? View::getSection('nav-mode') : 'light') }}"
            class="fixed top-0 left-0 right-0 z-50 border-b border-transparent"
            aria-label="Navigasi utama">
        <div class="max-w-7xl mx-auto px-gutter">
            <div class="flex items-center justify-between h-12 md:h-[68px]">

                {{-- Logo --}}
                <a href="{{ route('landing') }}"
                   id="nav-logo"
                   class="font-serif text-base md:text-[1.35rem] tracking-tight flex-shrink-0 whitespace-nowrap transition-colors duration-300">
                    Bangkiang Jaran
                </a>

                {{-- Desktop links --}}
                <nav class="hidden md:flex items-center gap-1" aria-label="Menu desktop">
                    @php
                        $currentRoute = Route::currentRouteName();
                        $links = [
                            ['route' => 'landing',                    'label' => __('messages.home'),    'icon' => 'home'],
                            ['route' => 'tiket.index',                'label' => __('messages.tickets'), 'icon' => 'confirmation_number'],
                        ];
                        if(auth()->check()) {
                            $links[] = ['route' => 'wisatawan.pemesanan.riwayat', 'label' => __('messages.history'), 'icon' => 'history'];
                        }
                    @endphp

                    @foreach($links as $link)
                    <a href="{{ route($link['route']) }}"
                       class="nav-link relative font-sans text-sm px-4 py-2 rounded-full transition-all duration-200 {{ $currentRoute === $link['route'] ? 'nav-active' : '' }}">
                        {{ $link['label'] }}
                        @if($currentRoute === $link['route'])
                        <span class="absolute bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-current opacity-50"></span>
                        @endif
                    </a>
                    @endforeach

                    {{-- Divider --}}
                    <div id="nav-divider" class="w-px h-4 mx-1 transition-colors duration-300"></div>

                    @auth
                    {{-- User pill --}}
                    <a href="{{ route('profile.edit') }}"
                       class="nav-link inline-flex items-center gap-2 font-sans text-sm px-3.5 py-1.5 rounded-full transition-all duration-200">
                        <span class="w-6 h-6 rounded-full bg-forest/15 flex items-center justify-center text-[10px] font-semibold text-forest uppercase leading-none flex-shrink-0">
                            {{ substr(Auth::user()->name ?? Auth::user()->username, 0, 1) }}
                        </span>
                        <span class="max-w-[100px] truncate">{{ Auth::user()->username }}</span>
                    </a>
                    <a href="{{ route('logout.get') }}"
                       id="nav-logout"
                       class="font-sans text-sm px-4 py-2 rounded-full border transition-all duration-200 hover:opacity-80">
                        {{ __('messages.logout') }}
                    </a>
                    @else
                    <a href="{{ route('login') }}"
                       class="nav-link font-sans text-sm px-4 py-2 rounded-full transition-all duration-200">
                        {{ __('messages.login') }}
                    </a>
                    <a href="{{ route('register') }}"
                       id="nav-register"
                       class="font-sans text-sm font-medium px-5 py-2 rounded-full transition-all duration-200">
                        {{ __('messages.register') }}
                    </a>
                    @endauth

                    {{-- Lang --}}
                    <a href="{{ route('lang.switch', app()->getLocale() === 'id' ? 'en' : 'id') }}"
                       class="nav-link font-sans text-[11px] tracking-widest uppercase px-3 py-2 rounded-full transition-all duration-200 opacity-60 hover:opacity-100">
                        {{ app()->getLocale() === 'id' ? 'EN' : 'ID' }}
                    </a>
                </nav>

                {{-- Hamburger --}}
                <button id="mobileMenuBtn"
                        class="md:hidden w-8 h-8 flex items-center justify-center rounded-lg transition-colors duration-200"
                        id-icon="menuIcon"
                        aria-label="{{ __('messages.open_menu') }}" aria-expanded="false" aria-controls="mobileMenu">
                    <span class="material-symbols-outlined text-[18px]" id="menuIcon">menu</span>
                </button>

            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobileMenu"
             class="menu-close md:hidden"
             style="background: rgba(10,18,14,0.97); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-top: 1px solid rgba(255,255,255,0.06);"
             aria-label="Menu mobile">
            <div class="max-w-7xl mx-auto px-gutter py-2.5 pb-4 space-y-0.5">
                <a href="{{ route('landing') }}"          class="nav-mobile-link flex items-center gap-3 font-sans text-sm text-white/75 hover:text-white hover:bg-white/6 px-3 py-2 rounded-xl transition-all"><span class="material-symbols-outlined text-base text-white/30">home</span>{{ __('messages.home') }}</a>
                <a href="{{ route('tiket.index') }}"      class="nav-mobile-link flex items-center gap-3 font-sans text-sm text-white/75 hover:text-white hover:bg-white/6 px-3 py-2 rounded-xl transition-all"><span class="material-symbols-outlined text-base text-white/30">confirmation_number</span>{{ __('messages.tickets') }}</a>
                @auth
                <a href="{{ route('wisatawan.pemesanan.riwayat') }}" class="nav-mobile-link flex items-center gap-3 font-sans text-sm text-white/75 hover:text-white hover:bg-white/6 px-3 py-2 rounded-xl transition-all"><span class="material-symbols-outlined text-base text-white/30">history</span>{{ __('messages.history') }}</a>
                <a href="{{ route('profile.edit') }}"     class="nav-mobile-link flex items-center gap-3 font-sans text-sm text-white/75 hover:text-white hover:bg-white/6 px-3 py-2 rounded-xl transition-all"><span class="material-symbols-outlined text-base text-white/30">person</span>{{ Auth::user()->username }}</a>
                <div class="pt-2 mt-1 border-t border-white/8 flex gap-2">
                    <a href="{{ route('logout.get') }}" class="flex-1 flex items-center justify-center gap-2 font-sans text-sm text-white/70 border border-white/15 py-1.5 rounded-xl hover:bg-white/8 transition-all">
                        <span class="material-symbols-outlined text-base">logout</span>{{ __('messages.logout') }}
                    </a>
                    <a href="{{ route('lang.switch', app()->getLocale() === 'id' ? 'en' : 'id') }}" class="font-sans text-xs text-white/40 border border-white/10 px-4 py-1.5 rounded-xl hover:text-white hover:bg-white/6 transition-all uppercase tracking-widest">{{ app()->getLocale() === 'id' ? 'EN' : 'ID' }}</a>
                </div>
                @else
                <div class="pt-2 mt-1 border-t border-white/8 flex gap-2">
                    <a href="{{ route('login') }}"    class="flex-1 flex items-center justify-center font-sans text-sm text-white/70 border border-white/15 py-1.5 rounded-xl hover:bg-white/8 transition-all">{{ __('messages.login') }}</a>
                    <a href="{{ route('register') }}" class="flex-1 flex items-center justify-center font-sans text-sm font-medium text-white bg-forest py-1.5 rounded-xl hover:bg-leaf transition-all">{{ __('messages.register') }}</a>
                    <a href="{{ route('lang.switch', app()->getLocale() === 'id' ? 'en' : 'id') }}" class="font-sans text-xs text-white/40 border border-white/10 px-4 py-1.5 rounded-xl hover:text-white hover:bg-white/6 transition-all uppercase tracking-widest">{{ app()->getLocale() === 'id' ? 'EN' : 'ID' }}</a>
                </div>
                @endauth
            </div>
        </div>
    </header>

    {{-- Content --}}
    <main class="flex-grow">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    {{-- Footer --}}
    <footer class="text-cream/70" style="background-color: #0F1E14;">
        <div class="max-w-7xl mx-auto px-gutter pt-14 pb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 md:gap-16">
                <div class="md:col-span-1">
                    <span class="font-serif text-2xl" style="color: #C9A96E;">Bangkiang Jaran</span>
                    <p class="font-sans text-sm mt-3 max-w-xs leading-relaxed" style="color: rgba(247,243,238,0.45);">{{ __('messages.footer_desc') }}</p>
                    <div class="flex items-center gap-3 mt-6">
                        <a href="https://www.instagram.com/bangkiangjaranwaterfall?igsh=MWhnZzBvZXlpZ2h6bw==" target="_blank" rel="noopener"
                           class="w-9 h-9 rounded-full flex items-center justify-center transition-all hover:-translate-y-0.5"
                           style="background:rgba(255,255,255,0.07); color:rgba(247,243,238,0.6);"
                           aria-label="Instagram">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="#" target="_blank" rel="noopener"
                           class="w-9 h-9 rounded-full flex items-center justify-center transition-all hover:-translate-y-0.5"
                           style="background:rgba(255,255,255,0.07); color:rgba(247,243,238,0.6);"
                           aria-label="YouTube">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                        <a href="https://www.tiktok.com/@bangkiang.jaran.w?_r=1&_t=ZS-98KJbSvTEWe" target="_blank" rel="noopener"
                           class="w-9 h-9 rounded-full flex items-center justify-center transition-all hover:-translate-y-0.5"
                           style="background:rgba(255,255,255,0.07); color:rgba(247,243,238,0.6);"
                           aria-label="TikTok">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="font-sans text-xs uppercase tracking-[0.12em] mb-4" style="color:rgba(247,243,238,0.3);">{{ __('messages.nav_title') }}</h4>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('landing') }}" class="font-sans text-sm transition-colors hover:text-white" style="color:rgba(247,243,238,0.55);">{{ __('messages.home') }}</a></li>
                        <li><a href="#lokasi" class="font-sans text-sm transition-colors hover:text-white" style="color:rgba(247,243,238,0.55);">{{ __('messages.location') }}</a></li>
                        <li><a href="{{ route('tiket.index') }}" class="font-sans text-sm transition-colors hover:text-white" style="color:rgba(247,243,238,0.55);">{{ __('messages.tickets') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-sans text-xs uppercase tracking-[0.12em] mb-4" style="color:rgba(247,243,238,0.3);">{{ __('messages.contact_title') }}</h4>
                    <ul class="space-y-2.5">
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-base mt-0.5" style="color:rgba(201,169,110,0.7);">location_on</span>
                            <span class="font-sans text-sm" style="color:rgba(247,243,238,0.55);">{{ __('messages.address') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-base mt-0.5" style="color:rgba(201,169,110,0.7);">schedule</span>
                            <span class="font-sans text-sm" style="color:rgba(247,243,238,0.55);">{{ __('messages.operating_hours') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-base mt-0.5" style="color:rgba(201,169,110,0.7);">mail</span>
                            <a href="mailto:info@bangkiangjaran.com" class="font-sans text-sm transition-colors hover:text-white" style="color:rgba(247,243,238,0.55);">info@bangkiangjaran.com</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3"
                 style="border-top: 1px solid rgba(255,255,255,0.06);">
                <p class="font-sans text-xs" style="color:rgba(247,243,238,0.3);">&copy; {{ date('Y') }} Bangkiang Jaran Waterfall. {{ __('messages.copyright_reserved') }}</p>
                <p class="font-sans text-xs" style="color:rgba(247,243,238,0.2);">Gianyar, Bali, Indonesia</p>
            </div>
        </div>
    </footer>

    <script>
    (function () {
        const nav      = document.getElementById('main-nav');
        const logo     = document.getElementById('nav-logo');
        const navLinks = nav.querySelectorAll('.nav-link');
        const divider  = document.getElementById('nav-divider');
        const logout   = document.getElementById('nav-logout');
        const register = document.getElementById('nav-register');
        const menuBtn  = document.getElementById('mobileMenuBtn');
        const menuIcon = document.getElementById('menuIcon');
        const mobileMenu = document.getElementById('mobileMenu');

        /* ── Determine page mode ── */
        const mode = nav.dataset.nav || 'light';   // 'dark' | 'light'

        /* ── Token sets ── */
        const DARK = {
            clear:  'nav-dark-clear',
            tinted: 'nav-dark-tinted',
            solid:  'nav-dark-solid',
            logoColor:    '#ffffff',
            linkColor:    'rgba(255,255,255,0.78)',
            linkHover:    '#ffffff',
            dividerColor: 'rgba(255,255,255,0.12)',
            logoutBorder: 'rgba(255,255,255,0.22)',
            logoutColor:  'rgba(255,255,255,0.78)',
            registerBg:   'transparent',
            registerBorder: 'rgba(255,255,255,0.25)',
            registerColor:  '#ffffff',
            menuBtnColor:   '#ffffff',
        };
        const LIGHT = {
            clear:  'nav-light',
            tinted: 'nav-light',
            solid:  'nav-light',
            logoColor:    '#1B3A2D',
            linkColor:    '#4B5563',
            linkHover:    '#1B3A2D',
            dividerColor: 'rgba(27,58,45,0.12)',
            logoutBorder: 'rgba(27,58,45,0.2)',
            logoutColor:  '#4B5563',
            registerBg:   '#1B3A2D',
            registerBorder: 'transparent',
            registerColor:  '#ffffff',
            menuBtnColor:   '#1B3A2D',
        };
        /* hero pages (landing): transparent over hero → light after scrolling past */
        const HERO = {
            clear:  'nav-transparent',
            tinted: 'nav-transparent',
            solid:  'nav-light',
            logoColor:    '#ffffff',
            linkColor:    'rgba(255,255,255,0.85)',
            linkHover:    '#ffffff',
            dividerColor: 'rgba(255,255,255,0.2)',
            logoutBorder: 'rgba(255,255,255,0.25)',
            logoutColor:  'rgba(255,255,255,0.85)',
            registerBg:   'rgba(27,58,45,0.85)',
            registerBorder: 'transparent',
            registerColor:  '#ffffff',
            menuBtnColor:   '#ffffff',
        };

        const T = mode === 'light' ? LIGHT : (mode === 'hero' ? HERO : DARK);

        /* ── Apply visual tokens ── */
        function applyTokens(tokens) {
            if (logo)     logo.style.color = tokens.logoColor;
            if (divider)  divider.style.backgroundColor = tokens.dividerColor;
            if (menuBtn)  menuBtn.style.color = tokens.menuBtnColor;

            navLinks.forEach(function(a) {
                a.style.color = tokens.linkColor;
            });

            if (logout) {
                logout.style.color        = tokens.logoutColor;
                logout.style.borderColor  = tokens.logoutBorder;
                logout.style.background   = 'transparent';
            }
            if (register) {
                register.style.backgroundColor = tokens.registerBg;
                register.style.borderColor      = tokens.registerBorder;
                register.style.color            = tokens.registerColor;
                register.style.border           = tokens.registerBg !== 'transparent'
                    ? 'none'
                    : '1px solid ' + tokens.registerBorder;
            }
        }

        /* ── Navbar state machine ── */
        const TINTED_PX  = 50;
        const SOLID_PX   = 130;
        const DEAD_PX    = 6;
        let lastY        = window.scrollY;
        let hidden       = false;
        let menuOpen     = false;
        let ticking      = false;

        function clearNavClasses() {
            nav.classList.remove(
                'nav-dark-clear','nav-dark-tinted','nav-dark-solid',
                'nav-transparent',
                'nav-light','nav-hidden'
            );
        }

        function setNav(cls) {
            clearNavClasses();
            nav.classList.add(cls);
        }

        function update() {
            const curr  = window.scrollY;
            const delta = curr - lastY;

            if (menuOpen) {
                hidden = false;
                setNav(T.solid);
                applyTokens(DARK);   // mobile menu always dark
                lastY = curr;
                ticking = false;
                return;
            }

            if (mode === 'hero') {
                // Transparent while the hero is on screen; once scrolled past
                // the hero, the normal light navbar kicks in.
                const heroEl   = document.getElementById('hero-section');
                const heroEnd  = heroEl ? heroEl.offsetHeight - (window.innerWidth < 768 ? 48 : 68) : SOLID_PX;
                if (curr <= heroEnd) {
                    hidden = false;
                    setNav('nav-transparent');
                    applyTokens(HERO);
                } else {
                    if (Math.abs(delta) > DEAD_PX) {
                        if (delta > 0 && !hidden) {
                            hidden = true;
                            setNav('nav-hidden');
                        } else if (delta < 0 && hidden) {
                            hidden = false;
                            setNav(T.solid);
                            applyTokens(LIGHT);
                        } else if (!hidden) {
                            setNav(T.solid);
                            applyTokens(LIGHT);
                        }
                    } else if (!hidden) {
                        setNav(T.solid);
                        applyTokens(LIGHT);
                    }
                }
            } else if (mode === 'light') {
                // Pages with `no-nav-hide` (e.g. payment with fixed progress bar):
                // navbar must stay put, never hide-on-scroll.
                if (document.body.classList.contains('no-nav-hide')) {
                    setNav(T.solid);
                    applyTokens(T);
                } else if (curr > SOLID_PX && Math.abs(delta) > DEAD_PX) {
                    if (delta > 0 && !hidden) {
                        hidden = true;
                        setNav('nav-hidden');
                    } else if (delta < 0 && hidden) {
                        hidden = false;
                        setNav(T.solid);
                        applyTokens(T);
                    }
                } else if (!hidden) {
                    setNav(T.solid);
                    applyTokens(T);
                }
            } else {
                // Dark mode (hero pages)
                if (curr <= TINTED_PX) {
                    hidden = false;
                    setNav(T.clear);
                    applyTokens(DARK);
                } else if (curr <= SOLID_PX) {
                    hidden = false;
                    setNav(T.tinted);
                    applyTokens(DARK);
                } else {
                    if (Math.abs(delta) > DEAD_PX) {
                        if (delta > 0 && !hidden) {
                            hidden = true;
                            setNav('nav-hidden');
                        } else if (delta < 0 && hidden) {
                            hidden = false;
                            setNav(T.solid);
                            applyTokens(DARK);
                        } else if (!hidden) {
                            setNav(T.solid);
                            applyTokens(DARK);
                        }
                    }
                }
            }

            lastY = curr;
            ticking = false;
        }

        if (nav) {
            applyTokens(T);
            update();
            window.addEventListener('scroll', function() {
                if (!ticking) { requestAnimationFrame(update); ticking = true; }
            }, { passive: true });
        }

        /* ── Mobile menu ── */
        function openMenu() {
            menuOpen = true;
            mobileMenu.classList.replace('menu-close','menu-open');
            menuIcon.textContent = 'close';
            menuBtn.setAttribute('aria-expanded','true');
            setNav(T.solid);
            applyTokens(DARK);
            document.body.style.overflow = 'hidden';
        }
        function closeMenu() {
            menuOpen = false;
            mobileMenu.classList.replace('menu-open','menu-close');
            menuIcon.textContent = 'menu';
            menuBtn.setAttribute('aria-expanded','false');
            document.body.style.overflow = '';
            update();
        }

        if (menuBtn && mobileMenu) {
            menuBtn.addEventListener('click', function() { menuOpen ? closeMenu() : openMenu(); });
            mobileMenu.querySelectorAll('a').forEach(function(a) { a.addEventListener('click', closeMenu); });
            document.addEventListener('click', function(e) { if (menuOpen && !nav.contains(e.target)) closeMenu(); });
            document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && menuOpen) closeMenu(); });
            window.addEventListener('resize', function() { if (window.innerWidth >= 768 && menuOpen) closeMenu(); });
        }

        /* ── Nav link hover color ── */
        navLinks.forEach(function(a) {
            const orig = a.style.color;
            a.addEventListener('mouseenter', function() { a.style.color = T.linkHover; });
            a.addEventListener('mouseleave', function() { a.style.color = orig || T.linkColor; });
        });
    })();

    /* ── AOS + GSAP ── */
    gsap.registerPlugin(ScrollTrigger);
    AOS.init({ duration: 800, easing: 'ease-out', once: true, offset: 80 });
    const _observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(e) { if (e.isIntersecting) e.target.classList.add('show'); });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right').forEach(function(el) {
        _observer.observe(el);
    });
    </script>
    @stack('scripts')
</body>
</html>
