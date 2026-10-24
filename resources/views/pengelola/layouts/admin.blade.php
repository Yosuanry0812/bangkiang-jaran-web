<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Pengelola — ' . config('app.name', 'Bangkiang Jaran'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,200&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['DM Sans', 'system-ui', 'sans-serif'],
                        sans:  ['DM Sans', 'system-ui', 'sans-serif'],
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
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <link rel="stylesheet" href="{{ asset('css/visual-refresh.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-workspace.css') . '?v=' . filemtime(public_path('css/admin-workspace.css')) }}">
</head>

<body class="bj-admin font-sans antialiased bg-slate-50 text-slate-800 min-h-screen overflow-x-clip">
<a class="admin-skip" href="#admin-content">Lewati ke konten</a>
<div x-data="{ sidebarOpen: false, isDesktop: window.innerWidth >= 1024 }"
     x-effect="document.body.style.overflow = sidebarOpen ? 'hidden' : ''"
     @keydown.escape.window="if (sidebarOpen) { sidebarOpen = false; $nextTick(() => $refs.sidebarToggle.focus()); }"
     @keydown.tab.window="if (sidebarOpen) { const links = [...document.querySelectorAll('#sidebar a, #sidebar button')].filter(el => el.getClientRects().length); const first = links[0]; const last = links[links.length - 1]; if ($event.shiftKey && document.activeElement === first) { $event.preventDefault(); last.focus(); } else if (!$event.shiftKey && document.activeElement === last) { $event.preventDefault(); first.focus(); } }"
     @resize.window="isDesktop = window.innerWidth >= 1024; if (isDesktop) sidebarOpen = false"
     class="flex min-h-screen">

    {{-- ── SIDEBAR ── --}}
    <aside id="sidebar" :inert="!isDesktop && !sidebarOpen" :aria-hidden="!isDesktop && !sidebarOpen" aria-label="Menu pengelola"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-40 w-60 flex flex-col lg:translate-x-0 lg:static lg:inset-auto">

        {{-- Logo --}}
        <div class="admin-brand flex items-center justify-between flex-shrink-0"
             style="border-bottom: 1px solid rgba(255,255,255,.06)">
            <a href="{{ route('pengelola.dashboard') }}" class="flex items-center gap-2.5">
                <span class="admin-brand-mark material-symbols-outlined" aria-hidden="true">landscape</span>
                <span><span class="admin-brand-name">Bangkiang Jaran</span><span class="admin-brand-sub">Ruang pengelola</span></span>
            </a>
            <button x-ref="sidebarClose" @click="sidebarOpen = false; $nextTick(() => $refs.sidebarToggle.focus())" aria-label="Tutup navigasi"
                    class="lg:hidden text-white/80 hover:text-white transition-colors min-w-[44px] min-h-[44px]">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        {{-- Nav --}}
        <nav aria-label="Navigasi pengelola" class="flex-1 overflow-y-auto overscroll-contain py-5 px-3 space-y-0.5">

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

            <div class="my-4" style="height:1px; background: rgba(255,255,255,.06)"></div>

            <p class="px-3 mb-2 font-sans text-[9px] font-semibold uppercase tracking-[.18em] text-white/20">Operasional</p>

            <a href="{{ route('pengelola.pemesanan-offline.create') }}"
               class="nav-link {{ Route::is('pengelola.pemesanan-offline*') ? 'active' : '' }}">
                <span class="material-symbols-outlined">point_of_sale</span>
                Pemesanan Offline
            </a>
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
            <a href="{{ route('profile.edit') }}" class="nav-link text-xs {{ Route::is('profile.*') ? 'active' : '' }}">
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
    <div :inert="sidebarOpen && !isDesktop" class="admin-workspace flex-1 flex flex-col min-w-0">

        {{-- Topbar --}}
        <header class="admin-topbar flex items-center sticky top-0 z-20 flex-shrink-0">
            <button x-ref="sidebarToggle" @click="sidebarOpen = true; $nextTick(() => $refs.sidebarClose.focus())" :aria-expanded="sidebarOpen" aria-controls="sidebar" aria-label="Buka navigasi"
                    class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                <span class="material-symbols-outlined">menu</span>
            </button>

            {{-- Page title (injected per page) --}}
            <div class="flex-1 min-w-0">
                <p class="admin-breadcrumb"><span>Pengelola</span><span aria-hidden="true">/</span><strong>@yield('page_title', View::getSection('title') ?: 'Dashboard')</strong></p>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                {{-- Lang --}}
                <a href="{{ route('lang.switch', app()->getLocale() === 'id' ? 'en' : 'id') }}"
                   class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                   title="{{ app()->getLocale() === 'id' ? 'Switch to English' : 'Ganti ke Bahasa Indonesia' }}">
                    <span class="material-symbols-outlined" style="font-size:15px">translate</span>
                    {{ app()->getLocale() === 'id' ? 'EN' : 'ID' }}
                </a>

                {{-- Notifikasi: polling dan status berasal dari server --}}
                <div class="relative" x-data="notifikasi()" @keydown.escape.stop="open = false; $refs.notificationToggle.focus()">
                    <button x-ref="notificationToggle" @click="open = !open" :aria-expanded="open" aria-controls="admin-notifications" :aria-label="'Notifikasi, ' + count + ' pemberitahuan'" class="relative p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                        <span class="material-symbols-outlined" aria-hidden="true">notifications</span>
                        <span x-show="count > 0" x-cloak class="absolute top-0 right-0 min-w-[17px] h-[17px] px-1 rounded-full bg-[#a96829] text-white text-[10px] font-semibold flex items-center justify-center" x-text="count"></span>
                    </button>
                    <section id="admin-notifications" aria-label="Notifikasi" x-show="open" @click.outside="open = false" x-cloak
                        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                        class="admin-notification-panel absolute right-0 mt-3 z-50">
                        <div class="admin-notification-head"><div><strong>Notifikasi</strong><p x-text="loading ? 'Memuat pemberitahuan…' : count + ' pemberitahuan operasional'"></p></div><a href="{{ route('pengelola.verifikasi.index') }}">Lihat semua</a></div>
                        <div x-show="failed" class="px-5 py-4" role="status"><p class="admin-note">Pemberitahuan belum dapat dimuat.</p><button type="button" @click="load()" class="admin-text-action">Coba lagi</button></div>
                        <template x-if="verifikasi.length">
                            <div class="admin-notification-group"><p class="admin-notification-label"><span class="material-symbols-outlined" aria-hidden="true">pending_actions</span>Menunggu verifikasi <span class="admin-count" x-text="verifikasi.length"></span></p>
                                <template x-for="v in verifikasi" :key="'v-' + v.id_pemesanan">
                                    <a :href="detailUrl(v.id_pemesanan)" class="admin-notification-item">
                                        <span class="admin-notification-icon material-symbols-outlined" aria-hidden="true">receipt_long</span>
                                        <div><strong x-text="v.nama_user"></strong><small x-text="v.kode_booking"></small><small x-text="v.metode"></small></div>
                                        <div class="admin-notification-amount"><strong x-text="rp(v.total)"></strong><small x-text="v.waktu"></small></div>
                                    </a>
                                </template>
                            </div>
                        </template>
                        <template x-if="pesananBaru.length">
                            <div class="admin-notification-group"><p class="admin-notification-label"><span class="material-symbols-outlined" aria-hidden="true">confirmation_number</span>Pesanan baru <span class="admin-count" x-text="pesananBaru.length"></span></p>
                                <template x-for="p in pesananBaru" :key="'p-' + p.id_pemesanan">
                                    <a href="{{ route('pengelola.verifikasi.index') }}" class="admin-notification-item">
                                        <span class="admin-notification-icon admin-notification-icon--order material-symbols-outlined" aria-hidden="true">confirmation_number</span>
                                        <div><strong x-text="p.nama_user"></strong><small x-text="p.kode_booking"></small></div>
                                        <div class="admin-notification-amount"><strong x-text="rp(p.total)"></strong><small x-text="p.waktu"></small></div>
                                    </a>
                                </template>
                            </div>
                        </template>
                        <div x-show="!loading && !failed && count === 0" class="admin-empty"><span class="material-symbols-outlined" aria-hidden="true">notifications_none</span><h3>Semua sudah terpantau</h3><p>Pemberitahuan baru akan muncul di sini.</p></div>
                        <p class="admin-notification-footer">Diperbarui otomatis setiap 20 detik.</p>
                    </section>
                </div>

                {{-- Profile dropdown --}}
                <div class="relative" x-data="{ open: false }" @keydown.escape.stop="open = false; $refs.profileToggle.focus()">
                    <button x-ref="profileToggle" @click="open = !open" :aria-expanded="open" aria-controls="admin-profile-menu" aria-label="Menu akun"
                            class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                        <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center flex-shrink-0">
                            <span class="font-sans text-[10px] font-semibold text-slate-600">
                                {{ strtoupper(substr(Auth::user()->name ?? 'P', 0, 1)) }}
                            </span>
                        </div>
                        <span class="hidden sm:block font-sans text-xs font-medium text-slate-600 max-w-[120px] truncate">{{ Auth::user()->name ?? 'Pengelola' }}</span>
                        <span class="material-symbols-outlined text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="font-size:14px">expand_more</span>
                    </button>
                    <div id="admin-profile-menu" x-show="open" @click.away="open = false" x-cloak
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
        <main id="admin-content" tabindex="-1" class="admin-content flex-1">
            @if(session('success'))<div class="admin-alert" role="status">{{ session('success') }}</div>@endif
            @yield('content')
        </main>

    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
function notifikasi() {
    return {
        open: false,
        loading: true,
        failed: false,
        count: 0,
        verifikasi: [],
        pesananBaru: [],
        prevCount: null,
        timer: null,
        init() {
            this.load();
            this.timer = setInterval(() => this.load(), 20000);
        },
        destroy() { clearInterval(this.timer); },
        detailUrl(id) { return @js(route('pengelola.verifikasi.show', '__id__')).replace('__id__', encodeURIComponent(id)); },
        async load() {
            try {
                const r = await fetch('{{ route('pengelola.notifikasi') }}', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!r.ok) throw new Error('Notification request failed');
                const d = await r.json();
                // Popup otomatis hanya saat ADA data BARU (bukan saat pertama buka halaman)
                if (this.prevCount !== null && d.count > this.prevCount) {
                    this.$refs.notificationToggle.animate([{ transform: 'rotate(-12deg)' }, { transform: 'rotate(12deg)' }, { transform: 'rotate(0)' }], { duration: matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 240 });
                }
                this.count = d.count;
                this.verifikasi = d.verifikasi;
                this.pesananBaru = d.pesanan_baru;
                this.prevCount = d.count;
                this.failed = false;
            } catch (e) { this.failed = true; }
            finally { this.loading = false; }
        },
        rp(n) {
            return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
        },
    };
}
</script>
<script defer src="{{ asset('js/site.js') }}"></script>
@stack('scripts')
</body>
</html>
