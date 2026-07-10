<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Pengelola - ' . config('app.name', 'Bangkiang Jaran'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,200&display=swap" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        heading: ['Playfair Display', 'serif'],
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        teal: { 50: '#f0fdfa', 100: '#ccfbf1', 200: '#99f6e4', 300: '#5eead4', 400: '#2dd4bf', 500: '#14b8a6', 600: '#0d9488', 700: '#0f766e', 800: '#115e59', 900: '#134e4a' },
                        emerald: { 50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0', 300: '#6ee7b7', 400: '#34d399', 500: '#10b981', 600: '#059669', 700: '#047857', 800: '#065f46', 900: '#064e3b' },
                        amber: { 50: '#fffbeb', 100: '#fef3c7', 200: '#fde68a', 300: '#fcd34d', 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706', 700: '#b45309', 800: '#92400e', 900: '#78350f' },
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-transition { transition: transform 0.3s ease, opacity 0.2s ease; }
        .material-symbols-outlined { font-size: 1.25rem; vertical-align: middle; }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-stone-50 text-stone-800">
    <div class="min-h-screen flex">
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-emerald-950 text-white transform -translate-x-full lg:translate-x-0 sidebar-transition lg:static lg:inset-auto flex flex-col">
            <div class="flex items-center justify-between h-16 px-6 border-b border-emerald-800/50">
                <a href="{{ route('pengelola.dashboard') }}" class="flex items-center gap-3 text-white">
                    <span class="material-symbols-outlined text-3xl text-emerald-400">nature</span>
                    <div>
                        <span class="font-heading font-bold text-lg leading-tight block">Bangkiang Jaran</span>
                        <span class="text-[10px] text-emerald-300/80 uppercase tracking-widest font-medium">Panel Pengelola</span>
                    </div>
                </a>
                <button id="closeSidebar" class="lg:hidden p-1.5 rounded-lg hover:bg-emerald-800/50 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
                <a href="{{ route('pengelola.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(Route::is('pengelola.dashboard')) bg-emerald-700 text-white shadow-lg shadow-emerald-900/30 @else text-emerald-200 hover:bg-emerald-800/50 hover:text-white @endif">
                    <span class="material-symbols-outlined @if(Route::is('pengelola.dashboard')) text-emerald-300 @endif">dashboard</span>
                    Dashboard
                </a>
                <a href="{{ route('pengelola.tiket.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(Route::is('pengelola.tiket*')) bg-emerald-700 text-white shadow-lg shadow-emerald-900/30 @else text-emerald-200 hover:bg-emerald-800/50 hover:text-white @endif">
                    <span class="material-symbols-outlined @if(Route::is('pengelola.tiket*')) text-emerald-300 @endif">confirmation_number</span>
                    Kelola Tiket
                </a>
                <a href="{{ route('pengelola.galeri.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(Route::is('pengelola.galeri*')) bg-emerald-700 text-white shadow-lg shadow-emerald-900/30 @else text-emerald-200 hover:bg-emerald-800/50 hover:text-white @endif">
                    <span class="material-symbols-outlined @if(Route::is('pengelola.galeri*')) text-emerald-300 @endif">photo_library</span>
                    Kelola Galeri
                </a>
                <a href="{{ route('pengelola.konten.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(Route::is('pengelola.konten*')) bg-emerald-700 text-white shadow-lg shadow-emerald-900/30 @else text-emerald-200 hover:bg-emerald-800/50 hover:text-white @endif">
                    <span class="material-symbols-outlined @if(Route::is('pengelola.konten*')) text-emerald-300 @endif">article</span>
                    Kelola Konten
                </a>
                <div class="border-t border-emerald-800/30 my-3"></div>
                <a href="{{ route('pengelola.verifikasi.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(Route::is('pengelola.verifikasi*')) bg-emerald-700 text-white shadow-lg shadow-emerald-900/30 @else text-emerald-200 hover:bg-emerald-800/50 hover:text-white @endif">
                    <span class="material-symbols-outlined @if(Route::is('pengelola.verifikasi*')) text-emerald-300 @endif">verified_user</span>
                    Verifikasi
                </a>
                <a href="{{ route('pengelola.laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(Route::is('pengelola.laporan*')) bg-emerald-700 text-white shadow-lg shadow-emerald-900/30 @else text-emerald-200 hover:bg-emerald-800/50 hover:text-white @endif">
                    <span class="material-symbols-outlined @if(Route::is('pengelola.laporan*')) text-emerald-300 @endif">monitoring</span>
                    Laporan
                </a>
                <a href="{{ route('pengelola.user.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 @if(Route::is('pengelola.user*')) bg-emerald-700 text-white shadow-lg shadow-emerald-900/30 @else text-emerald-200 hover:bg-emerald-800/50 hover:text-white @endif">
                    <span class="material-symbols-outlined @if(Route::is('pengelola.user*')) text-emerald-300 @endif">group</span>
                    User
                </a>
            </nav>

            <div class="px-3 py-4 border-t border-emerald-800/30">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-sm font-medium text-emerald-300 hover:bg-red-900/30 hover:text-red-300 transition-all duration-200">
                        <span class="material-symbols-outlined">logout</span>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden backdrop-blur-sm"></div>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-stone-200 shadow-sm sticky top-0 z-20">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <button id="openSidebar" class="lg:hidden p-2 rounded-xl text-stone-500 hover:text-emerald-700 hover:bg-emerald-50 transition-colors">
                            <span class="material-symbols-outlined">menu</span>
                        </button>
                        <h1 class="text-sm font-semibold text-stone-700 hidden sm:block">
                            @yield('title', 'Dashboard')
                        </h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-stone-500 hidden sm:block">{{ Auth::user()->name ?? 'Pengelola' }}</span>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium text-stone-600 hover:text-emerald-700 hover:bg-emerald-50 transition-colors">
                                <span class="material-symbols-outlined">account_circle</span>
                                <span class="material-symbols-outlined text-base transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-lg border border-stone-200 py-1 z-50 overflow-hidden">
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-stone-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                    <span class="material-symbols-outlined text-base">settings</span>
                                    Profil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 w-full text-left px-4 py-2.5 text-sm text-stone-700 hover:bg-red-50 hover:text-red-600 transition-colors">
                                        <span class="material-symbols-outlined text-base">logout</span>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        document.getElementById('openSidebar')?.addEventListener('click', () => {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        });
        document.getElementById('closeSidebar')?.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
        overlay?.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
</body>
</html>