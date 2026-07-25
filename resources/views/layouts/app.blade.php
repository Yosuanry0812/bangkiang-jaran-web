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
        ::selection { background: #1B3A2D; color: #F7F3EE; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24; }
        .icon-fill { font-variation-settings: 'FILL' 1, 'wght' 300, 'GRAD' 0, 'opsz' 24; }
        [x-cloak] { display: none !important; }

        /* ─── Navbar transition ─── */
        #main-nav {
            transition:
                transform 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                background-color 0.35s ease,
                backdrop-filter 0.35s ease,
                box-shadow 0.35s ease;
            will-change: transform, background-color;
        }
        #main-nav.nav-hidden  { transform: translateY(-100%); }
        #main-nav.nav-solid   { background-color: rgba(15,28,20,0.97); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); box-shadow: 0 1px 0 rgba(255,255,255,0.04); }
        #main-nav.nav-tinted  { background-color: rgba(15,28,20,0.50); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); box-shadow: none; }
        #main-nav.nav-clear   { background-color: transparent;          backdrop-filter: none;       -webkit-backdrop-filter: none;       box-shadow: none; }

        /* ─── Mobile menu ─── */
        #mobileMenu {
            transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), max-height 0.3s ease;
            transform-origin: top;
            overflow: hidden;
        }
        #mobileMenu.menu-open  { opacity: 1; transform: scaleY(1); max-height: 400px; }
        #mobileMenu.menu-close { opacity: 0; transform: scaleY(0.97); max-height: 0; pointer-events: none; }

        /* ─── Gray blur shapes ─── */
        .blur-jungle { background: radial-gradient(ellipse at center, rgba(74,124,89,0.10) 0%, transparent 70%); }
        .blur-teal   { background: radial-gradient(ellipse at center, rgba(127,175,138,0.07) 0%, transparent 70%); }
        .blur-sunrise { background: radial-gradient(ellipse at center, rgba(201,169,110,0.07) 0%, transparent 70%); }

        /* ─── Hover ─── */
        .soft-lift { transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.4s cubic-bezier(0.22, 1, 0.36, 1); }
        .soft-lift:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,0.06); }

        /* ─── Animations ─── */
        .fade-in { opacity: 0; transform: translateY(24px); transition: all 0.7s cubic-bezier(0.22, 1, 0.36, 1); }
        .fade-in.show { opacity: 1; transform: translateY(0); }
        .fade-in-left { opacity: 0; transform: translateX(-30px); transition: all 0.7s cubic-bezier(0.22, 1, 0.36, 1); }
        .fade-in-left.show { opacity: 1; transform: translateX(0); }
        .fade-in-right { opacity: 0; transform: translateX(30px); transition: all 0.7s cubic-bezier(0.22, 1, 0.36, 1); }
        .fade-in-right.show { opacity: 1; transform: translateX(0); }

        @keyframes sway { 0%,100% { transform: rotate(0deg); } 50% { transform: rotate(2deg); } }
        .sway { animation: sway 6s ease-in-out infinite; transform-origin: bottom center; }

        @keyframes pulse-soft { 0%,100% { opacity: 1; } 50% { opacity: 0.6; } }
        .pulse-soft { animation: pulse-soft 3s ease-in-out infinite; }

        /* ─── Divider ─── */
        .divider-leaf { height: 1px; background: linear-gradient(to right, transparent, rgba(75,83,99,0.12), transparent); }
        .divider-gold { height: 1px; background: linear-gradient(to right, transparent, rgba(156,163,175,0.15), transparent); }

        /* ─── Typography helpers ─── */
        .heading-xl { @apply font-serif text-5xl md:text-7xl leading-[1.08] tracking-tight text-deep; }
        .heading-lg { @apply font-serif text-3xl md:text-5xl leading-[1.12] tracking-tight text-deep; }
        .heading-md { @apply font-serif text-2xl md:text-3xl leading-[1.2] tracking-tight text-deep; }
        .body-large { @apply font-sans text-[17px] md:text-[19px] leading-relaxed text-gray-500; }
        .body-base { @apply font-sans text-[15px] leading-relaxed text-gray-500; }
        .label-sm { @apply font-sans text-[13px] font-medium uppercase tracking-[0.1em] text-jungle/60; }
    </style>
    @stack('styles')
</head>
<body class="bg-ivory text-ink font-sans antialiased min-h-screen flex flex-col" style="selection-background: #1B3A2D;">

    {{-- Navigation --}}
    <header class="fixed top-0 left-0 right-0 z-50 nav-clear" id="main-nav" aria-label="Navigasi utama">
        <div class="flex justify-between items-center px-gutter py-4 md:py-5 max-w-7xl mx-auto">

            {{-- Logo --}}
            <a href="{{ route('landing') }}" class="font-serif text-xl md:text-2xl text-white tracking-tight hover:opacity-70 transition-opacity flex-shrink-0">
                Bangkiang Jaran
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden md:flex items-center gap-8" aria-label="Menu desktop">
                <a href="{{ route('landing') }}" class="font-sans text-sm text-white/80 hover:text-white transition-colors">{{ __('messages.home') }}</a>
                <a href="{{ route('tiket.index') }}" class="font-sans text-sm text-white/80 hover:text-white transition-colors">{{ __('messages.tickets') }}</a>
                @auth
                <a href="{{ route('wisatawan.pemesanan.riwayat') }}" class="font-sans text-sm text-white/80 hover:text-white transition-colors">{{ __('messages.history') }}</a>
                <a href="{{ route('profile.edit') }}" class="font-sans text-sm text-white/80 hover:text-white transition-colors">{{ Auth::user()->username }}</a>
                <a href="{{ route('logout.get') }}" class="font-sans text-sm text-white border border-white/30 px-5 py-2 rounded-full hover:bg-white/10 transition-colors">{{ __('messages.logout') }}</a>
                @else
                <a href="{{ route('login') }}" class="font-sans text-sm text-white/80 hover:text-white transition-colors">{{ __('messages.login') }}</a>
                <a href="{{ route('register') }}" class="font-sans text-sm text-white border border-white/30 px-5 py-2 rounded-full hover:bg-white/10 transition-colors">{{ __('messages.register') }}</a>
                @endauth
                <a href="{{ route('lang.switch', app()->getLocale() === 'id' ? 'en' : 'id') }}" class="font-sans text-sm text-white/50 hover:text-white transition-colors">{{ app()->getLocale() === 'id' ? 'EN' : 'ID' }}</a>
            </nav>

            {{-- Hamburger (mobile) --}}
            <button id="mobileMenuBtn"
                    class="md:hidden text-white w-10 h-10 flex items-center justify-center rounded-lg hover:bg-white/10 transition-colors flex-shrink-0"
                    aria-label="{{ __('messages.open_menu') }}" aria-expanded="false" aria-controls="mobileMenu">
                <span class="material-symbols-outlined text-2xl" id="menuIcon">menu</span>
            </button>
        </div>

        {{-- Mobile menu --}}
        <div id="mobileMenu"
             class="menu-close md:hidden border-t border-white/10"
             style="background-color: rgba(20,28,38,0.97); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);"
             aria-label="Menu mobile">
            <div class="px-gutter py-5 space-y-1">
                <a href="{{ route('landing') }}" class="nav-mobile-link flex items-center gap-3 font-sans text-sm text-white/80 hover:text-white hover:bg-white/8 px-3 py-2.5 rounded-xl transition-all">
                    <span class="material-symbols-outlined text-base text-white/40">home</span> {{ __('messages.home') }}
                </a>
                <a href="{{ route('tiket.index') }}" class="nav-mobile-link flex items-center gap-3 font-sans text-sm text-white/80 hover:text-white hover:bg-white/8 px-3 py-2.5 rounded-xl transition-all">
                    <span class="material-symbols-outlined text-base text-white/40">confirmation_number</span> {{ __('messages.tickets') }}
                </a>
                @auth
                <a href="{{ route('wisatawan.pemesanan.riwayat') }}" class="nav-mobile-link flex items-center gap-3 font-sans text-sm text-white/80 hover:text-white hover:bg-white/8 px-3 py-2.5 rounded-xl transition-all">
                    <span class="material-symbols-outlined text-base text-white/40">history</span> {{ __('messages.history') }}
                </a>
                <a href="{{ route('profile.edit') }}" class="nav-mobile-link flex items-center gap-3 font-sans text-sm text-white/80 hover:text-white hover:bg-white/8 px-3 py-2.5 rounded-xl transition-all">
                    <span class="material-symbols-outlined text-base text-white/40">person</span> {{ Auth::user()->username }}
                </a>
                <div class="pt-2 mt-2 border-t border-white/10 flex items-center gap-3">
                    <a href="{{ route('logout.get') }}" class="nav-mobile-link flex-1 flex items-center justify-center gap-2 font-sans text-sm text-white border border-white/20 py-2.5 rounded-xl hover:bg-white/10 transition-all">
                        <span class="material-symbols-outlined text-base">logout</span> {{ __('messages.logout') }}
                    </a>
                    <a href="{{ route('lang.switch', app()->getLocale() === 'id' ? 'en' : 'id') }}" class="nav-mobile-link font-sans text-sm text-white/50 border border-white/10 px-4 py-2.5 rounded-xl hover:text-white hover:bg-white/8 transition-all">
                        {{ app()->getLocale() === 'id' ? 'EN' : 'ID' }}
                    </a>
                </div>
                @else
                <div class="pt-2 mt-2 border-t border-white/10 flex items-center gap-3">
                    <a href="{{ route('login') }}" class="nav-mobile-link flex-1 flex items-center justify-center gap-2 font-sans text-sm text-white/80 hover:text-white border border-white/20 py-2.5 rounded-xl hover:bg-white/10 transition-all">
                        {{ __('messages.login') }}
                    </a>
                    <a href="{{ route('register') }}" class="nav-mobile-link flex-1 flex items-center justify-center gap-2 font-sans text-sm text-white border border-white/30 py-2.5 rounded-xl hover:bg-white/10 transition-all">
                        {{ __('messages.register') }}
                    </a>
                    <a href="{{ route('lang.switch', app()->getLocale() === 'id' ? 'en' : 'id') }}" class="nav-mobile-link font-sans text-sm text-white/50 border border-white/10 px-4 py-2.5 rounded-xl hover:text-white hover:bg-white/8 transition-all">
                        {{ app()->getLocale() === 'id' ? 'EN' : 'ID' }}
                    </a>
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
                        <a href="#" target="_blank" rel="noopener"
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
                        <a href="mailto:info@bangkiangjaran.com"
                           class="w-9 h-9 rounded-full flex items-center justify-center transition-all hover:-translate-y-0.5"
                           style="background:rgba(255,255,255,0.07); color:rgba(247,243,238,0.6);"
                           aria-label="Email">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
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
        /* ═══════════════════════════════════════════════
           NAVBAR — scroll hide/show + background swap
           ─────────────────────────────────────────────
           Rules:
           · 0–40px          → transparent (nav-clear)
           · 40–120px        → semi-transparent (nav-tinted)
           · >120px scrolling DOWN → hide (nav-hidden)
           · >120px scrolling UP   → solid (nav-solid)

           Dead zone: ignore direction changes < 6px
           to prevent jitter on trackpads/momentum scroll.
        ════════════════════════════════════════════════ */

        const nav     = document.getElementById('main-nav');
        const TINTED  = 40;   // px – start tinting
        const SOLID   = 120;  // px – start hide/show logic
        const DEAD    = 6;    // px – direction dead zone

        let lastY     = window.scrollY;
        let ticking   = false;
        let hidden     = false;

        function setClass(cls) {
            nav.classList.remove('nav-clear', 'nav-tinted', 'nav-solid', 'nav-hidden');
            nav.classList.add(cls);
        }

        function update() {
            const curr = window.scrollY;
            const delta = curr - lastY;

            if (curr <= TINTED) {
                // Top zone — always visible, transparent
                hidden = false;
                setClass('nav-clear');
            } else if (curr <= SOLID) {
                // Mid zone — always visible, tinted
                hidden = false;
                setClass('nav-tinted');
            } else {
                // Deep zone — react to scroll direction
                if (Math.abs(delta) > DEAD) {
                    if (delta > 0 && !hidden) {
                        // scrolling DOWN → hide
                        hidden = true;
                        setClass('nav-hidden');
                    } else if (delta < 0 && hidden) {
                        // scrolling UP → show solid
                        hidden = false;
                        setClass('nav-solid');
                    } else if (!hidden) {
                        setClass('nav-solid');
                    }
                }
            }

            lastY = curr;
            ticking = false;
        }

        if (nav) {
            // Set initial state without waiting for scroll
            update();

            window.addEventListener('scroll', function () {
                if (!ticking) {
                    requestAnimationFrame(update);
                    ticking = true;
                }
            }, { passive: true });
        }

        /* ═══════════════════════════════════════════════
           MOBILE MENU — smooth open/close
        ════════════════════════════════════════════════ */
        const btn      = document.getElementById('mobileMenuBtn');
        const menu     = document.getElementById('mobileMenu');
        const menuIcon = document.getElementById('menuIcon');
        let menuOpen   = false;

        function openMenu() {
            menuOpen = true;
            menu.classList.remove('menu-close');
            menu.classList.add('menu-open');
            menuIcon.textContent = 'close';
            btn.setAttribute('aria-expanded', 'true');
            // If nav is at top, give it a solid bg so menu is readable
            if (window.scrollY <= TINTED) {
                nav.classList.remove('nav-clear');
                nav.classList.add('nav-tinted');
            }
        }

        function closeMenu() {
            menuOpen = false;
            menu.classList.remove('menu-open');
            menu.classList.add('menu-close');
            menuIcon.textContent = 'menu';
            btn.setAttribute('aria-expanded', 'false');
            // Restore transparent bg if at top
            if (window.scrollY <= TINTED) {
                nav.classList.remove('nav-tinted', 'nav-solid');
                nav.classList.add('nav-clear');
            }
        }

        if (btn && menu) {
            btn.addEventListener('click', function () {
                menuOpen ? closeMenu() : openMenu();
            });

            // Close on any link click inside mobile menu
            menu.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', closeMenu);
            });

            // Close on outside click
            document.addEventListener('click', function (e) {
                if (menuOpen && !nav.contains(e.target)) {
                    closeMenu();
                }
            });

            // Close on Escape
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && menuOpen) closeMenu();
            });

            // Close on resize to desktop
            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768 && menuOpen) closeMenu();
            });
        }
    })();

    /* ═══════════════════════════════════════════════
       AOS + GSAP init
    ════════════════════════════════════════════════ */
    gsap.registerPlugin(ScrollTrigger);
    AOS.init({ duration: 800, easing: 'ease-out', once: true, offset: 80 });

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) entry.target.classList.add('show');
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right').forEach(function (el) {
        observer.observe(el);
    });
    </script>
    @stack('scripts')
</body>
</html>
