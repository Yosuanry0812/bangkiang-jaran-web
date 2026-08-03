<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Bangkiang Jaran'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: { extend: {
                colors: {
                    "on-primary-fixed-variant": "#005143","outline": "#6e7975","surface-container": "#f0eee9","tertiary": "#5b442a","on-background": "#1b1c19","on-surface-variant": "#3e4945","primary": "#005344","surface-container-highest": "#e4e2dd","surface": "#fbf9f4","on-secondary-fixed-variant": "#23501e","on-primary-fixed": "#00201a","surface-container-low": "#f5f3ee","tertiary-fixed-dim": "#e3c19f","primary-fixed": "#9df3dc","secondary": "#3b6934","primary-fixed-dim": "#81d6c0","on-tertiary": "#ffffff","secondary-fixed": "#bcf0ae","inverse-surface": "#30312e","on-secondary-fixed": "#002201","surface-dim": "#dbdad5","on-tertiary-fixed-variant": "#5a4229","outline-variant": "#bec9c4","secondary-container": "#b9eeab","on-tertiary-fixed": "#291803","error": "#ba1a1a","surface-variant": "#e4e2dd","on-primary": "#ffffff","inverse-on-surface": "#f2f1ec","background": "#fbf9f4","inverse-primary": "#81d6c0","surface-container-lowest": "#ffffff","on-error-container": "#93000a","tertiary-container": "#755b40","surface-container-high": "#eae8e3","on-primary-container": "#96ebd5","tertiary-fixed": "#ffddbb","on-secondary-container": "#3f6d38","surface-tint": "#006b59","on-tertiary-container": "#f8d5b2","on-error": "#ffffff","on-secondary": "#ffffff","on-surface": "#1b1c19","primary-container": "#006d5b","surface-bright": "#fbf9f4","secondary-fixed-dim": "#a1d494","error-container": "#ffdad6"
                },
                borderRadius: { "2xl": "1.5rem" },
                spacing: { "xl": "80px", "container-max": "1280px", "lg": "48px", "md": "24px", "sm": "12px", "base": "8px", "xs": "4px", "gutter": "24px" },
                fontFamily: { "display": ["Playfair Display", "serif"], "body": ["Plus Jakarta Sans", "sans-serif"] },
                fontSize: {
                    "display-mobile": ["40px", { lineHeight: "1.2", fontWeight: "700" }],
                    "headline-md": ["32px", { lineHeight: "1.3", fontWeight: "600" }],
                    "headline-sm": ["24px", { lineHeight: "1.4", fontWeight: "600" }],
                    "body-lg": ["18px", { lineHeight: "1.6", fontWeight: "400" }],
                    "body-md": ["16px", { lineHeight: "1.6", fontWeight: "400" }],
                    "label-md": ["14px", { lineHeight: "1.2", letterSpacing: "0.05em", fontWeight: "600" }],
                    "caption": ["12px", { lineHeight: "1.4", fontWeight: "500" }],
                }
            }}
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .card-shadow { box-shadow: 0px 4px 20px rgba(0, 50, 40, 0.04); }
    </style>
</head>
<body class="font-body antialiased min-h-screen flex flex-col selection:bg-emerald-100" style="background: #f0f2ef;">

    {{-- ── Subtle background texture ── --}}
    <div class="fixed inset-0 pointer-events-none" style="background: radial-gradient(ellipse 80% 60% at 20% 10%, rgba(0,100,70,0.06) 0%, transparent 60%), radial-gradient(ellipse 60% 50% at 80% 90%, rgba(0,80,60,0.05) 0%, transparent 55%);"></div>

    {{-- ── Minimal top nav ── --}}
    <nav class="relative z-10 py-5 px-8">
        <div class="flex justify-between items-center max-w-6xl mx-auto">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 group">
                <div class="w-7 h-7 rounded-lg bg-emerald-800 flex items-center justify-center shadow-sm group-hover:bg-emerald-700 transition-colors">
                    <span class="material-symbols-outlined text-white text-[13px]" style="font-variation-settings:'FILL' 1;">water</span>
                </div>
                <span class="font-display text-[15px] font-semibold text-emerald-900 tracking-wide">Bangkiang Jaran</span>
            </a>
            <div class="flex items-center gap-1">
                <a href="{{ route('lang.switch', app()->getLocale() === 'id' ? 'en' : 'id') }}"
                   class="flex items-center gap-1.5 font-body text-[13px] font-medium text-gray-500 hover:text-emerald-800 hover:bg-white/70 transition-all px-3 py-2 rounded-xl">
                    <span class="material-symbols-outlined text-[14px]">language</span>
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

    @stack('scripts')
</body>
</html>
