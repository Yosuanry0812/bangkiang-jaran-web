@extends('layouts.app')

@section('content')

{{-- ══════════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════════ --}}
<section class="relative w-full min-h-screen min-h-svh flex items-end overflow-hidden">
    {{-- Background image --}}
    <div class="absolute inset-0 z-0">
        <img id="hero-img"
             src="{{ asset('images/sejarah-bangkiang-waterfall.webp') }}"
             alt="Bangkiang Jaran Waterfall"
             fetchpriority="high"
             onload="revealHero()"
             onerror="revealHero()"
             class="w-full h-full object-cover">
        <div class="absolute inset-0" style="background:linear-gradient(to top, rgba(10,20,14,0.85) 0%, rgba(10,20,14,0.35) 55%, transparent 100%);"></div>
    </div>

    {{-- Hero content --}}
    <div id="hero-content"
         class="relative z-10 w-full max-w-7xl mx-auto px-gutter pb-20 md:pb-28 opacity-0 translate-y-6 transition-all duration-1000 ease-out">
        <div class="max-w-2xl">
            <p class="font-sans text-[11px] tracking-[0.2em] uppercase text-white/40 mb-6">Bangkiang Jaran Waterfall · Gianyar, Bali</p>
            <h1 id="typing-heading" class="font-serif text-[clamp(2.8rem,6vw,5rem)] leading-[1.05] text-white mb-5 min-h-[1.1em]"></h1>
            <p id="typing-sub" class="font-sans text-[15px] text-white/55 mb-10 leading-relaxed max-w-md min-h-[2em]"></p>
            <div class="flex flex-wrap items-center gap-3">
                @auth
                <a href="{{ route('wisatawan.pemesanan.create') }}"
                   class="inline-flex items-center gap-2 bg-white text-forest font-sans text-sm font-semibold px-7 py-3.5 rounded-full hover:bg-ivory transition-colors">
                    {{ __('messages.book_ticket') }}
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
                @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 bg-white text-forest font-sans text-sm font-semibold px-7 py-3.5 rounded-full hover:bg-ivory transition-colors">
                    {{ __('messages.book_now_btn') }}
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
                @endauth
                <a href="#about"
                   class="inline-flex items-center gap-2 font-sans text-sm text-white/70 border border-white/20 px-7 py-3.5 rounded-full hover:border-white/40 hover:text-white transition-colors">
                    {{ __('messages.learn_more') }}
                </a>
            </div>
        </div>

        {{-- Quick info strip --}}
        <div class="flex flex-wrap gap-8 mt-16 pt-8 border-t border-white/10">
            <div>
                <p class="font-sans text-[10px] tracking-widest uppercase text-white/30 mb-1">{{ __('messages.operating_hours_label') }}</p>
                <p class="font-sans text-sm text-white/70">{{ __('messages.operating_hours') }}</p>
            </div>
            <div class="w-px bg-white/10"></div>
            <div>
                <p class="font-sans text-[10px] tracking-widest uppercase text-white/30 mb-1">{{ __('messages.location') }}</p>
                <p class="font-sans text-sm text-white/70">{{ __('messages.address') }}</p>
            </div>
            <div class="w-px bg-white/10"></div>
            <div>
                <p class="font-sans text-[10px] tracking-widest uppercase text-white/30 mb-1">{{ __('messages.online_ticket') }}</p>
                <p class="font-sans text-sm text-white/70">{{ __('messages.start_from', ['price' => '15.000']) }}</p>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     ABOUT / SEJARAH
══════════════════════════════════════════════════════ --}}
@if(isset($kontenList) && $kontenList->count() > 0)
<section id="about" class="py-24 md:py-32 px-gutter" style="background:#F8F7F5;">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            {{-- Text --}}
            <div class="lg:col-span-5" data-aos="fade-right">
                <p class="font-sans text-[11px] tracking-[0.18em] uppercase text-stone mb-5">{{ __('messages.about') }}</p>
                @foreach($kontenList->take(1) as $item)
                <h2 class="font-serif text-[clamp(2rem,4vw,3.2rem)] text-forest leading-[1.1] mb-6">{{ $item->judul }}</h2>
                <div class="space-y-4">
                    @foreach(array_filter(array_map('trim', explode("\n", $item->isi))) as $p)
                    <p class="font-sans text-[15px] text-stone leading-relaxed">{{ $p }}</p>
                    @endforeach
                </div>
                @endforeach
                <a href="{{ route('tiket.index') }}"
                   class="inline-flex items-center gap-2 mt-8 font-sans text-sm font-semibold text-forest border-b border-forest/30 pb-0.5 hover:border-forest transition-colors">
                    {{ __('messages.view_entry_tickets') }}
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>

            {{-- Images --}}
            <div class="lg:col-span-7" data-aos="fade-left" data-aos-delay="100">
                <div class="grid grid-cols-12 grid-rows-2 gap-3 h-[320px] sm:h-[420px] md:h-[480px]">
                    <div class="col-span-7 row-span-2 rounded-2xl overflow-hidden">
                        <img src="{{ asset('images/sejarah-bangkiang-waterfall.webp') }}"
                             alt="Bangkiang Jaran"
                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                    </div>
                    <div class="col-span-5 row-span-1 rounded-2xl overflow-hidden bg-forest/10">
                        @if(isset($galeri) && $galeri->count() > 0)
                        <img src="{{ asset('storage/' . $galeri->first()->file) }}"
                             alt="Galeri"
                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                        @else
                        <div class="w-full h-full bg-gradient-to-br from-forest/20 to-moss/20 flex items-center justify-center">
                            <span class="material-symbols-outlined text-4xl text-forest/30">forest</span>
                        </div>
                        @endif
                    </div>
                    <div class="col-span-5 row-span-1 rounded-2xl overflow-hidden bg-gold/10 flex items-center justify-center p-5">
                        <div class="text-center">
                            <p class="font-serif text-4xl text-forest mb-1">5K+</p>
                            <p class="font-sans text-xs text-stone">{{ __('messages.visitors_per_month') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════
     HIGHLIGHTS / USP
══════════════════════════════════════════════════════ --}}
<section class="py-20 px-gutter bg-white border-y border-gray-100">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
            <div class="py-10 md:py-0 md:pr-12" data-aos="fade-up">
                <div class="w-10 h-10 rounded-xl bg-forest/8 flex items-center justify-center mb-5">
                    <span class="material-symbols-outlined text-forest text-lg">nature</span>
                </div>
                <h3 class="font-serif text-xl text-forest mb-2">{{ __('messages.natural_beauty') }}</h3>
                <p class="font-sans text-sm text-stone leading-relaxed">{{ __('messages.natural_beauty_desc') }}</p>
            </div>
            <div class="py-10 md:py-0 md:px-12" data-aos="fade-up" data-aos-delay="60">
                <div class="w-10 h-10 rounded-xl bg-forest/8 flex items-center justify-center mb-5">
                    <span class="material-symbols-outlined text-forest text-lg">directions_walk</span>
                </div>
                <h3 class="font-serif text-xl text-forest mb-2">{{ __('messages.easy_access') }}</h3>
                <p class="font-sans text-sm text-stone leading-relaxed">{{ __('messages.easy_access_desc') }}</p>
            </div>
            <div class="py-10 md:py-0 md:pl-12" data-aos="fade-up" data-aos-delay="120">
                <div class="w-10 h-10 rounded-xl bg-forest/8 flex items-center justify-center mb-5">
                    <span class="material-symbols-outlined text-forest text-lg">spa</span>
                </div>
                <h3 class="font-serif text-xl text-forest mb-2">{{ __('messages.peaceful_atmosphere') }}</h3>
                <p class="font-sans text-sm text-stone leading-relaxed">{{ __('messages.peaceful_atmosphere_desc') }}</p>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     GALLERY — Stacked Scroll Cards
══════════════════════════════════════════════════════ --}}
@if(isset($galeri) && $galeri->count() > 0)
<section id="galeri" class="relative bg-white">

    {{-- ── Slide stage ─────────────────────────────────── --}}
    <div class="relative w-full" id="gallery-carousel">

        {{-- ── Section header ── --}}
        <div class="max-w-7xl mx-auto px-gutter pt-20 pb-16 flex items-end justify-between">
            <div data-aos="fade-up">
                <p class="font-sans text-[11px] tracking-[0.18em] uppercase text-stone mb-3">{{ __('messages.gallery_tab') }}</p>
                <h2 class="font-serif text-[clamp(1.8rem,3.5vw,2.8rem)] text-forest leading-tight">{{ __('messages.visual_journey') }}</h2>
                <p class="font-sans text-sm text-stone mt-2 max-w-sm">{{ __('messages.visual_journey_desc') }}</p>
            </div>
            <p class="font-sans text-xs text-pebble hidden md:block" data-aos="fade-up">
                <span class="material-symbols-outlined text-sm align-middle mr-1">mouse</span>
                {{ __('messages.scroll_for_more') }}
            </p>
        </div>

        {{-- ── Stacked scroll container ── --}}
        {{-- Height = number of cards × step so each card gets full scroll segment --}}
        <div id="stack-scroll-root"
             style="height: {{ ($galeri->count() * 100) + 50 }}vh; position: relative;">

            {{-- Sticky viewport --}}
            <div id="stack-sticky"
                 style="position: sticky; top: 0; height: 100vh; overflow: hidden; display: flex; align-items: center; justify-content: center;">

                {{-- Card stack wrapper — centered --}}
                <div id="stack-wrapper"
                     class="relative w-full max-w-4xl mx-auto px-6 md:px-10"
                     style="height: 72vh; min-height: 460px; max-height: 680px;">

                    @foreach($galeri as $index => $media)
                    @php
                        $total   = $galeri->count();
                        // Initial stagger: cards below the top card peek out
                        $peek    = min($index, 4) * 14;   // px downward offset
                        $scaleInit = 1 - (min($index, 4) * 0.04);
                    @endphp
                    <div class="stack-card absolute inset-0 rounded-3xl overflow-hidden shadow-2xl will-change-transform"
                         data-index="{{ $index }}"
                         style="transform: translateY({{ $peek }}px) scale({{ $scaleInit }});
                                transform-origin: top center;
                                z-index: {{ $total - $index }};
                                top: 0; left: 0; right: 0; bottom: 0;">

                        {{-- Photo --}}
                        <img src="{{ asset('storage/' . $media->file) }}"
                             alt="{{ $media->keterangan ?? 'Bangkiang Jaran' }}"
                             class="w-full h-full object-cover"
                             loading="{{ $index === 0 ? 'eager' : 'lazy' }}">

                        {{-- Bottom gradient --}}
                        <div class="absolute inset-0"
                             style="background: linear-gradient(to top, rgba(0,0,0,0.60) 0%, transparent 55%);"></div>

                        {{-- Card label --}}
                        <div class="absolute bottom-0 left-0 right-0 p-7 md:p-10 flex items-end justify-between">
                            <div>
                                @if($media->keterangan)
                                <p class="font-serif text-xl md:text-2xl text-white leading-snug mb-1">{{ $media->keterangan }}</p>
                                @endif
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-px bg-white/40"></span>
                                    <span class="font-sans text-xs text-white/50 uppercase tracking-widest">Bangkiang Jaran</span>
                                </div>
                            </div>
                            {{-- Index badge --}}
                            <span class="font-serif text-5xl md:text-6xl text-white/15 tabular-nums leading-none select-none">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>

                    </div>
                    @endforeach

                </div>{{-- /wrapper --}}

                {{-- Scroll hint arrow (fades out when scroll starts) --}}
                <div id="stack-hint"
                     class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1 transition-opacity duration-500">
                    <span class="font-sans text-xs text-stone uppercase tracking-widest">Scroll</span>
                    <span class="material-symbols-outlined text-stone text-base animate-bounce">keyboard_arrow_down</span>
                </div>

            </div>{{-- /sticky --}}

        </div>{{-- /scroll root --}}

    </div>{{-- /gallery-carousel --}}

</section>
@endif

{{-- ══════════════════════════════════════════════════════
     RESTAURANT PROMO
══════════════════════════════════════════════════════ --}}
<section class="py-24 md:py-32 px-gutter bg-[#FAF8F5] overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
            {{-- Images grid --}}
            <div class="lg:col-span-7" data-aos="fade-right">
                <div class="grid grid-cols-4 grid-rows-2 gap-3 h-[260px] sm:h-[420px] md:h-[500px]">
                    <div class="col-span-2 row-span-2 rounded-2xl overflow-hidden shadow-lg">
                        <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600&q=80"
                             alt="Resto interior"
                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                    </div>
                    <div class="col-span-2 row-span-1 rounded-2xl overflow-hidden shadow-md">
                        <img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?w=400&q=80"
                             alt="Makanan khas"
                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                    </div>
                    <div class="col-span-1 row-span-1 rounded-2xl overflow-hidden shadow-md">
                        <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=300&q=80"
                             alt="Minuman segar"
                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                    </div>
                    <div class="col-span-1 row-span-1 rounded-2xl overflow-hidden shadow-md">
                        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=300&q=80"
                             alt="Hidangan istimewa"
                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                    </div>
                </div>
            </div>

            {{-- Text --}}
            <div class="lg:col-span-5" data-aos="fade-left" data-aos-delay="80">
                <p class="font-sans text-[11px] tracking-[0.18em] uppercase text-stone mb-4">Restoran & Kuliner</p>
                <h2 class="font-serif text-[clamp(1.8rem,3.5vw,2.8rem)] text-forest leading-[1.1] mb-5">
                    Nikmati Hidangan<br>di <span class="text-leaf">Kepulauan Rasa</span>
                </h2>
                <p class="font-sans text-[15px] text-stone leading-relaxed mb-6">
                    Setelah puas menikmati air terjun, mampirlah ke restoran kami yang menyajikan
                    beragam masakan khas Nusantara dan minuman segar. Suasana tropis yang asri
                    menemani setiap santapan Anda.
                </p>
                <div class="space-y-3 mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-forest/8 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-forest text-sm">restaurant</span>
                        </div>
                        <div>
                            <p class="font-sans text-sm font-semibold text-ink">Masakan Khas Nusantara</p>
                            <p class="font-sans text-xs text-stone">Aneka lauk & sambal tradisional</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-forest/8 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-forest text-sm">local_cafe</span>
                        </div>
                        <div>
                            <p class="font-sans text-sm font-semibold text-ink">Minuman Tropis Segar</p>
                            <p class="font-sans text-xs text-stone">Kelapa muda, es campur, jus buah</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-forest/8 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-forest text-sm">deck</span>
                        </div>
                        <div>
                            <p class="font-sans text-sm font-semibold text-ink">Gazebo & Area Santai</p>
                            <p class="font-sans text-xs text-stone">Duduk santai dengan pemandangan hijau</p>
                        </div>
                    </div>
                </div>
                <a href="#lokasi"
                   class="inline-flex items-center gap-2 bg-forest text-white font-sans text-sm font-semibold px-7 py-3.5 rounded-full hover:bg-leaf transition-colors shadow-md">
                    Jelajahi Sekarang
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     RESTAURANT GALLERY — Stacked Scroll Cards
══════════════════════════════════════════════════════ --}}
@if(isset($galeriRestoran) && $galeriRestoran->count() > 0)
<section class="relative bg-[#FAF8F5]">

    {{-- ── Slide stage ─────────────────────────────────── --}}
    <div class="relative w-full" id="resto-gallery-carousel">

        {{-- ── Section header ── --}}
        <div class="max-w-7xl mx-auto px-gutter pt-20 pb-16 flex items-end justify-between">
            <div data-aos="fade-up">
                <p class="font-sans text-[11px] tracking-[0.18em] uppercase text-stone mb-3">Galeri Restoran</p>
                <h2 class="font-serif text-[clamp(1.8rem,3.5vw,2.8rem)] text-forest leading-tight">Kuliner di Kepulauan Rasa</h2>
                <p class="font-sans text-sm text-stone mt-2 max-w-sm">Suasana dan hidangan terbaik di restoran kami.</p>
            </div>
            <p class="font-sans text-xs text-pebble hidden md:block" data-aos="fade-up">
                <span class="material-symbols-outlined text-sm align-middle mr-1">mouse</span>
                Scroll untuk lihat lainnya
            </p>
        </div>

        {{-- ── Stacked scroll container ── --}}
        <div id="resto-stack-root"
             style="height: {{ ($galeriRestoran->count() * 100) + 50 }}vh; position: relative;">

            {{-- Sticky viewport --}}
            <div id="resto-stack-sticky"
                 style="position: sticky; top: 0; height: 100vh; overflow: hidden; display: flex; align-items: center; justify-content: center;">

                {{-- Card stack wrapper --}}
                <div id="resto-stack-wrapper"
                     class="relative w-full max-w-4xl mx-auto px-6 md:px-10"
                     style="height: 72vh; min-height: 460px; max-height: 680px;">

                    @foreach($galeriRestoran as $index => $media)
                    @php
                        $total   = $galeriRestoran->count();
                        $peek    = min($index, 4) * 14;
                        $scaleInit = 1 - (min($index, 4) * 0.04);
                    @endphp
                    <div class="resto-stack-card absolute inset-0 rounded-3xl overflow-hidden shadow-2xl will-change-transform"
                         data-index="{{ $index }}"
                         style="transform: translateY({{ $peek }}px) scale({{ $scaleInit }});
                                transform-origin: top center;
                                z-index: {{ $total - $index }};
                                top: 0; left: 0; right: 0; bottom: 0;">

                        <img src="{{ asset('storage/' . $media->file) }}"
                             alt="{{ $media->keterangan ?? 'Restoran Bangkiang Jaran' }}"
                             class="w-full h-full object-cover"
                             loading="{{ $index === 0 ? 'eager' : 'lazy' }}">

                        <div class="absolute inset-0"
                             style="background: linear-gradient(to top, rgba(0,0,0,0.60) 0%, transparent 55%);"></div>

                        <div class="absolute bottom-0 left-0 right-0 p-7 md:p-10 flex items-end justify-between">
                            <div>
                                @if($media->keterangan)
                                <p class="font-serif text-xl md:text-2xl text-white leading-snug mb-1">{{ $media->keterangan }}</p>
                                @endif
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-px bg-white/40"></span>
                                    <span class="font-sans text-xs text-white/50 uppercase tracking-widest">Restoran Bangkiang Jaran</span>
                                </div>
                            </div>
                            <span class="font-serif text-5xl md:text-6xl text-white/15 tabular-nums leading-none select-none">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>

                    </div>
                    @endforeach

                </div>{{-- /wrapper --}}

                {{-- Scroll hint --}}
                <div id="resto-stack-hint"
                     class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1 transition-opacity duration-500">
                    <span class="font-sans text-xs text-stone uppercase tracking-widest">Scroll</span>
                    <span class="material-symbols-outlined text-stone text-base animate-bounce">keyboard_arrow_down</span>
                </div>

            </div>{{-- /sticky --}}

        </div>{{-- /scroll root --}}

    </div>{{-- /carousel --}}

</section>
@endif

{{-- ══════════════════════════════════════════════════════
     LOCATION
══════════════════════════════════════════════════════ --}}
<section id="lokasi" class="py-24 md:py-32 px-gutter bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="mb-12" data-aos="fade-up">
            <p class="font-sans text-[11px] tracking-[0.18em] uppercase text-stone mb-3">Lokasi</p>
            <h2 class="font-serif text-[clamp(1.8rem,3.5vw,2.8rem)] text-forest">{{ __('messages.location_title') }}</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-8 rounded-2xl overflow-hidden h-[400px] md:h-[480px]" data-aos="fade-right">
                <iframe src="https://www.google.com/maps?q=-8.511979,115.328486&output=embed&z=15"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
            <div class="lg:col-span-4 space-y-3" data-aos="fade-left" data-aos-delay="80">
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                    <div class="w-9 h-9 rounded-xl bg-forest/8 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-forest text-base">location_on</span>
                    </div>
                    <p class="font-sans text-xs text-stone uppercase tracking-widest mb-1">{{ __('messages.address_label') }}</p>
                    <p class="font-sans text-sm text-forest font-medium">{{ __('messages.location_desc') }}</p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                    <div class="w-9 h-9 rounded-xl bg-forest/8 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-forest text-base">route</span>
                    </div>
                    <p class="font-sans text-xs text-stone uppercase tracking-widest mb-1">{{ __('messages.distance') }}</p>
                    <p class="font-sans text-sm text-forest font-medium">{{ __('messages.distance_value') }}</p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                    <div class="w-9 h-9 rounded-xl bg-forest/8 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-forest text-base">schedule</span>
                    </div>
                    <p class="font-sans text-xs text-stone uppercase tracking-widest mb-1">{{ __('messages.travel_time') }}</p>
                    <p class="font-sans text-sm text-forest font-medium">{{ __('messages.travel_time_value') }}</p>
                </div>
                <a href="https://www.google.com/maps?q=-8.511979,115.328486" target="_blank"
                   class="flex items-center justify-center gap-2 w-full bg-forest text-white font-sans text-sm font-medium py-3.5 rounded-xl hover:bg-leaf transition-colors">
                    <span class="material-symbols-outlined text-sm">map</span>
                    {{ __('messages.open_in_gmaps') }}
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
/* ── Stacked cards — NO CSS transition, lerp handles smoothing ── */
.stack-card {
    will-change: transform, opacity;
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
}

/* ── Resto stack cards ── */
.resto-stack-card {
    will-change: transform, opacity;
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
}

/* ── Bounce for scroll hint ── */
@keyframes bounce-soft {
    0%, 100% { transform: translateX(-50%) translateY(0); }
    50%       { transform: translateX(-50%) translateY(6px); }
}
#stack-hint { animation: bounce-soft 1.8s ease-in-out infinite; }
#stack-hint.hide { opacity: 0 !important; pointer-events: none; }
#resto-stack-hint { animation: bounce-soft 1.8s ease-in-out infinite; }
#resto-stack-hint.hide { opacity: 0 !important; pointer-events: none; }
</style>
@endpush

@push('scripts')
<script>
/* ───────── hero reveal: media dulu, konten setelah ───────── */
let heroRevealed = false;

function revealHero() {
    if (heroRevealed) return;
    heroRevealed = true;

    const content = document.getElementById('hero-content');
    if (!content) return;

    // Fade-in + slide up konten
    content.classList.remove('opacity-0', 'translate-y-6');

    // Typing animation mulai setelah reveal selesai
    setTimeout(startTyping, 500);
}

/* ───────── typing animation ───────── */
function startTyping() {
    const heading = document.getElementById('typing-heading');
    const sub = document.getElementById('typing-sub');
    if (!heading || !sub) return;

    const text1 = '{{ __("messages.typing_heading") }}';
    const text2 = '{{ __("messages.typing_sub") }}';
    let idx = 0, isHeading = true;

    function type() {
        const target = isHeading ? heading : sub;
        const text   = isHeading ? text1 : text2;
        if (idx < text.length) {
            target.textContent = text.slice(0, idx + 1);
            idx++;
            setTimeout(type, isHeading ? 55 + Math.random() * 35 : 22 + Math.random() * 18);
        } else if (isHeading) {
            isHeading = false; idx = 0;
            setTimeout(type, 350);
        }
    }
    type();
}

/* ───────── fallback kalo image udah cached ───────── */
(function () {
    const img = document.getElementById('hero-img');
    if (img && img.complete) revealHero();
})();

/* ═══════════════════════════════════════════════════════
   GALLERY — Stacked Scroll Effect  (smooth lerp version)
════════════════════════════════════════════════════════ */
(function () {
    const root   = document.getElementById('stack-scroll-root');
    const cards  = Array.from(document.querySelectorAll('.stack-card'));
    const hint   = document.getElementById('stack-hint');

    if (!root || !cards.length) return;

    const N        = cards.length;
    const SEGMENT  = window.innerHeight;   // px of scroll per card
    const EASE     = 0.10;                 // lerp factor 0..1  (lower = smoother)

    // Per-card interpolated state
    const state = cards.map(() => ({
        ty:  0,      // current translateY  (%)
        sc:  1,      // current scale
        op:  1,      // current opacity
        tty: 0,      // target translateY
        tsc: 1,      // target scale
        top: 1,      // target opacity
    }));

    let hintHidden = false;
    let rafId      = null;
    let lastScroll = -1;

    /* ── compute target values from raw scrollY ── */
    function computeTargets() {
        const rootTop  = root.getBoundingClientRect().top + window.scrollY;
        const scrolled = Math.max(0, window.scrollY - rootTop);

        if (!hintHidden && scrolled > 40) {
            hintHidden = true;
            if (hint) hint.classList.add('hide');
        }

        cards.forEach((_, i) => {
            const start    = i * SEGMENT;
            const progress = Math.min(1, Math.max(0, (scrolled - start) / SEGMENT));

            const peekPx  = Math.min(i, 4) * 14;
            const peekSc  = 1 - Math.min(i, 4) * 0.04;

            if (scrolled < start) {
                // Not yet: stacked resting position
                state[i].tty = 0;                // use px via translateY(px), handled below
                state[i]._peekPx = peekPx;
                state[i]._usePx  = true;
                state[i].tsc     = peekSc;
                state[i].top     = 1;
            } else if (progress >= 1) {
                // Fully gone — above the viewport
                state[i]._usePx  = false;
                state[i].tty     = -108;        // translateY in %
                state[i].tsc     = 0.93;
                state[i].top     = 0;
            } else {
                // Animating: lift from peekPx → -108%
                state[i]._usePx  = false;
                // Convert peekPx to a rough % equivalent at animation start
                const startPct   = 0;           // we'll blend from 0% and add a small px offset separately
                state[i].tty     = startPct + progress * (-108 - startPct);
                state[i].tsc     = peekSc   + progress * (0.93 - peekSc);
                state[i].top     = progress > 0.75
                                   ? 1 - ((progress - 0.75) / 0.25)
                                   : 1;

                // Compress cards below while this one rises
                for (let j = i + 1; j < Math.min(i + 5, N); j++) {
                    if (scrolled < j * SEGMENT) {
                        const d     = j - i;
                        const fromP = Math.min(d, 4) * 14;
                        const toP   = Math.min(d - 1, 4) * 14;
                        const fromS = 1 - Math.min(d, 4) * 0.04;
                        const toS   = 1 - Math.min(d - 1, 4) * 0.04;
                        state[j]._usePx  = true;
                        state[j]._peekPx = fromP + progress * (toP - fromP);
                        state[j].tsc     = fromS + progress * (toS   - fromS);
                        state[j].top     = 1;
                    }
                }
            }
        });
    }

    /* ── lerp helper ── */
    const lerp = (a, b, t) => a + (b - a) * t;

    /* ── animation loop ── */
    function tick() {
        rafId = requestAnimationFrame(tick);

        const scrollNow = window.scrollY;
        const moved     = Math.abs(scrollNow - lastScroll) > 0.2;
        lastScroll      = scrollNow;

        computeTargets();

        let anyDirty = false;

        cards.forEach((card, i) => {
            const s = state[i];

            // Lerp scale and opacity always
            s.sc = lerp(s.sc, s.tsc, EASE);
            s.op = lerp(s.op, s.top, EASE);

            // Lerp position
            if (s._usePx) {
                // resting / compressing — use px
                if (s._peekPxPrev === undefined) s._peekPxPrev = s._peekPx;
                s._peekPxPrev = lerp(s._peekPxPrev, s._peekPx, EASE);
                card.style.transform = `translateY(${s._peekPxPrev.toFixed(2)}px) scale(${s.sc.toFixed(4)})`;
            } else {
                // lifting — use %
                if (s.tyPrev === undefined) s.tyPrev = s._usePx ? 0 : s.tty;
                s.tyPrev = lerp(s.tyPrev, s.tty, EASE);
                card.style.transform = `translateY(${s.tyPrev.toFixed(2)}%) scale(${s.sc.toFixed(4)})`;
            }

            card.style.opacity = s.op.toFixed(4);

            // Check if anything is still moving
            const dScale = Math.abs(s.sc  - s.tsc);
            const dOp    = Math.abs(s.op  - s.top);
            if (dScale > 0.0005 || dOp > 0.0005 || moved) anyDirty = true;
        });

        // Stop RAF when nothing is moving and page isn't scrolling
        if (!anyDirty && !moved) {
            cancelAnimationFrame(rafId);
            rafId = null;
        }
    }

    /* ── kick RAF on scroll ── */
    function onScroll() {
        if (!rafId) rafId = requestAnimationFrame(tick);
    }

    window.addEventListener('scroll',  onScroll, { passive: true });
    window.addEventListener('resize',  onScroll, { passive: true });

    // Start the loop once to set initial positions
    computeTargets();
    tick();
})();

/* ═══════════════════════════════════════════════════════
   RESTO GALLERY — Stacked Scroll Effect (same as main)
════════════════════════════════════════════════════════ */
(function () {
    const root   = document.getElementById('resto-stack-root');
    const cards  = Array.from(document.querySelectorAll('.resto-stack-card'));
    const hint   = document.getElementById('resto-stack-hint');

    if (!root || !cards.length) return;

    const N        = cards.length;
    const SEGMENT  = window.innerHeight;
    const EASE     = 0.10;

    const state = cards.map(() => ({
        ty: 0, sc: 1, op: 1, tty: 0, tsc: 1, top: 1
    }));

    let hintHidden = false;
    let rafId      = null;
    let lastScroll = -1;

    function computeTargets() {
        const rootTop  = root.getBoundingClientRect().top + window.scrollY;
        const scrolled = Math.max(0, window.scrollY - rootTop);

        if (!hintHidden && scrolled > 40) {
            hintHidden = true;
            if (hint) hint.classList.add('hide');
        }

        cards.forEach((_, i) => {
            const start    = i * SEGMENT;
            const progress = Math.min(1, Math.max(0, (scrolled - start) / SEGMENT));
            const peekPx  = Math.min(i, 4) * 14;
            const peekSc  = 1 - Math.min(i, 4) * 0.04;

            if (scrolled < start) {
                state[i].tty = 0;
                state[i]._peekPx = peekPx;
                state[i]._usePx  = true;
                state[i].tsc     = peekSc;
                state[i].top     = 1;
            } else if (progress >= 1) {
                state[i]._usePx  = false;
                state[i].tty     = -108;
                state[i].tsc     = 0.93;
                state[i].top     = 0;
            } else {
                state[i]._usePx  = false;
                state[i].tty     = 0 + progress * (-108 - 0);
                state[i].tsc     = peekSc + progress * (0.93 - peekSc);
                state[i].top     = progress > 0.75 ? 1 - ((progress - 0.75) / 0.25) : 1;

                for (let j = i + 1; j < Math.min(i + 5, N); j++) {
                    if (scrolled < j * SEGMENT) {
                        const d     = j - i;
                        const fromP = Math.min(d, 4) * 14;
                        const toP   = Math.min(d - 1, 4) * 14;
                        const fromS = 1 - Math.min(d, 4) * 0.04;
                        const toS   = 1 - Math.min(d - 1, 4) * 0.04;
                        state[j]._usePx  = true;
                        state[j]._peekPx = fromP + progress * (toP - fromP);
                        state[j].tsc     = fromS + progress * (toS - fromS);
                        state[j].top     = 1;
                    }
                }
            }
        });
    }

    const lerp = (a, b, t) => a + (b - a) * t;

    function tick() {
        rafId = requestAnimationFrame(tick);
        const scrollNow = window.scrollY;
        const moved     = Math.abs(scrollNow - lastScroll) > 0.2;
        lastScroll      = scrollNow;
        computeTargets();

        let anyDirty = false;
        cards.forEach((card, i) => {
            const s = state[i];
            s.sc = lerp(s.sc, s.tsc, EASE);
            s.op = lerp(s.op, s.top, EASE);

            if (s._usePx) {
                if (s._peekPxPrev === undefined) s._peekPxPrev = s._peekPx;
                s._peekPxPrev = lerp(s._peekPxPrev, s._peekPx, EASE);
                card.style.transform = `translateY(${s._peekPxPrev.toFixed(2)}px) scale(${s.sc.toFixed(4)})`;
            } else {
                if (s.tyPrev === undefined) s.tyPrev = s._usePx ? 0 : s.tty;
                s.tyPrev = lerp(s.tyPrev, s.tty, EASE);
                card.style.transform = `translateY(${s.tyPrev.toFixed(2)}%) scale(${s.sc.toFixed(4)})`;
            }

            card.style.opacity = s.op.toFixed(4);
            const dScale = Math.abs(s.sc  - s.tsc);
            const dOp    = Math.abs(s.op  - s.top);
            if (dScale > 0.0005 || dOp > 0.0005 || moved) anyDirty = true;
        });

        if (!anyDirty && !moved) {
            cancelAnimationFrame(rafId);
            rafId = null;
        }
    }

    function onScroll() {
        if (!rafId) rafId = requestAnimationFrame(tick);
    }

    window.addEventListener('scroll',  onScroll, { passive: true });
    window.addEventListener('resize',  onScroll, { passive: true });

    computeTargets();
    tick();
})();
</script>
@endpush
