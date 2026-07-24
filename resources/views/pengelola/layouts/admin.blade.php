<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Pengelola — ' . config('app.name', 'Bangkiang Jaran'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:opsz,wght@14..32,300..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,200&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['"Instrument Serif"', 'Georgia', 'serif'],
                        sans:  ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        [x-cloak]  { display: none !important; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0,'wght' 300,'GRAD' 0,'opsz' 24; font-size: 18px; vertical-align: middle; }

        /* ── Sidebar ── */
        #sidebar {
            transition: transform .28s cubic-bezier(.4,0,.2,1);
            background: #0c1512;
            border-right: 1px solid rgba(255,255,255,.04);
        }
        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 12px; border-radius: 10px;
            font-size: 12.5px; font-weight: 500;
            color: rgba(255,255,255,.38);
            transition: background .16s ease, color .16s ease;
            white-space: nowrap;
            letter-spacing: .01em;
        }
        .nav-link:hover   { background: rgba(255,255,255,.05); color: rgba(255,255,255,.8); }
        .nav-link.active  {
            background: rgba(255,255,255,.09);
            color: #fff;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.06);
        }
        .nav-link .material-symbols-outlined { color: inherit; font-size: 16px; }

        /* ── Scrollbar thin ── */
        ::-webkit-scrollbar       { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
    </style>
    @stack('styles')
</head>

<body class="font-sans antialiased bg-slate-50 text-slate-800 min-h-screen">
<div x-data="{ sidebarOpen: false }" class="flex min-h-screen">

    {{-- ── SIDEBAR ── --}}
    <aside id="sidebar"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-40 w-60 flex flex-col lg:translate-x-0 lg:static lg:inset-auto">

        {{-- Logo --}}
        <div class="flex items-center justify-between h-[60px] px-5 flex-shrink-0"
             style="border-bottom: 1px solid rgba(255,255,255,.06)">
            <a href="{{ route('pengelola.dashboard') }}" class="flex items-center gap-2.5">
                <span class="font-serif text-[17px] text-white tracking-tight leading-none">Bangkiang Jaran</span>
            </a>
            <button @click="sidebarOpen = false"
                    class="lg:hidden text-white/40 hover:text-white/80 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto py-5 px-3 space-y-0.5">

            <p class="px-3 mb-2 font-sans text-[9px] font-semibold uppercase tracking-[.18em] text-white/20">Utama</p>

            <a href="{{ route('pengelola.dashboard') }}"
               class="nav-link {{ Route::is('pengelola.dashboard') ? 'active' : '' }}">
                <span class="material-symbols-outlined">space_dashboard</span>
                Dashboard
            </a>
            <a href="{{ route('pengelola.tiket.index') }}"
               class="nav-link {{ Route::is('pengelola.tiket*') ? 'active' : '' }}">
                <span class="material-symbols-outlined">confirmation_number</span>
                Kelola Tiket
            </a>
            <a href="{{ route('pengelola.galeri.index') }}"
               class="nav-link {{ Route::is('pengelola.galeri*') ? 'active' : '' }}">
                <span class="material-symbols-outlined">photo_library</span>
                Galeri
            </a>
            <a href="{{ route('pengelola.konten.index') }}"
               class="nav-link {{ Route::is('pengelola.konten*') ? 'active' : '' }}">
                <span class="material-symbols-outlined">article</span>
                Konten
            </a>

            <div class="my-4" style="height:1px; background: rgba(255,255,255,.06)"></div>

            <p class="px-3 mb-2 font-sans text-[9px] font-semibold uppercase tracking-[.18em] text-white/20">Operasional</p>

            <a href="{{ route('pengelola.scan.index') }}"
               class="nav-link {{ Route::is('pengelola.scan*') ? 'active' : '' }}">
                <span class="material-symbols-outlined">qr_code_scanner</span>
                Scan Tiket
            </a>
            <a href="{{ route('pengelola.verifikasi.index') }}"
               class="nav-link {{ Route::is('pengelola.verifikasi*') ? 'active' : '' }}">
                <span class="material-symbols-outlined">verified_user</span>
                Verifikasi
            </a>
            <a href="{{ route('pengelola.laporan.index') }}"
               class="nav-link {{ Route::is('pengelola.laporan*') ? 'active' : '' }}">
                <span class="material-symbols-outlined">monitoring</span>
                Laporan
            </a>
            <a href="{{ route('pengelola.user.index') }}"
               class="nav-link {{ Route::is('pengelola.user*') ? 'active' : '' }}">
                <span class="material-symbols-outlined">group</span>
                Pengguna
            </a>
        </nav>

        {{-- Footer --}}
        <div class="px-3 py-4 flex-shrink-0" style="border-top: 1px solid rgba(255,255,255,.06)">
            <div class="flex items-center gap-2.5 px-3 py-2 mb-1">
                <div class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                    <span class="font-sans text-[11px] font-semibold text-white/70">
                        {{ strtoupper(substr(Auth::user()->name ?? 'P', 0, 1)) }}
                    </span>
                </div>
                <div class="min-w-0">
                    <p class="font-sans text-[12px] font-medium text-white/80 truncate leading-tight">{{ Auth::user()->name ?? 'Pengelola' }}</p>
                    <p class="font-sans text-[10px] text-white/30 truncate">Pengelola</p>
                </div>
            </div>
            <a href="{{ route('profile.edit') }}" class="nav-link text-xs">
                <span class="material-symbols-outlined">manage_accounts</span>
                Profil Saya
            </a>
            <a href="{{ route('logout.get') }}" class="nav-link text-xs mt-0.5 hover:!bg-red-900/20 hover:!text-red-400">
                <span class="material-symbols-outlined">logout</span>
                Keluar
            </a>
        </div>
    </aside>

    {{-- Overlay mobile --}}
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-cloak
         class="fixed inset-0 bg-black/50 z-30 lg:hidden backdrop-blur-sm"></div>

    {{-- ── MAIN ── --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Topbar --}}
        <header class="bg-white/95 backdrop-blur-sm border-b border-slate-100/80 h-[60px] flex items-center px-4 sm:px-6 gap-4 sticky top-0 z-20 flex-shrink-0" style="box-shadow: 0 1px 0 rgba(15,26,23,.04)">
            <button @click="sidebarOpen = true"
                    class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                <span class="material-symbols-outlined">menu</span>
            </button>

            {{-- Page title (injected per page) --}}
            <div class="flex-1 min-w-0">
                <p class="font-sans text-sm font-semibold text-slate-700 truncate hidden sm:block">
                    @yield('page_title', 'Dashboard')
                </p>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                {{-- Lang --}}
                <a href="{{ route('lang.switch', app()->getLocale() === 'id' ? 'en' : 'id') }}"
                   class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined" style="font-size:14px">language</span>
                    {{ app()->getLocale() === 'id' ? 'EN' : 'ID' }}
                </a>

                {{-- Profile dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                        <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center flex-shrink-0">
                            <span class="font-sans text-[10px] font-semibold text-slate-600">
                                {{ strtoupper(substr(Auth::user()->name ?? 'P', 0, 1)) }}
                            </span>
                        </div>
                        <span class="hidden sm:block font-sans text-xs font-medium text-slate-600 max-w-[120px] truncate">{{ Auth::user()->name ?? 'Pengelola' }}</span>
                        <span class="material-symbols-outlined text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="font-size:14px">expand_more</span>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak
                         class="absolute right-0 mt-1.5 w-44 bg-white rounded-xl shadow-lg border border-slate-100 py-1.5 z-50">
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">
                            <span class="material-symbols-outlined" style="font-size:15px">settings</span>
                            Profil Saya
                        </a>
                        <div class="my-1 border-t border-slate-100"></div>
                        <a href="{{ route('logout.get') }}"
                           class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors">
                            <span class="material-symbols-outlined" style="font-size:15px">logout</span>
                            Keluar
                        </a>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto bg-slate-50/60">
            @yield('content')
        </main>

    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>
