@extends('layouts.app')
@section('nav-mode', 'hero')

@section('content')

{{-- ══════════════════════════════════════════════════════
     1. HERO SECTION — Natural & Authentic Elegance
══════════════════════════════════════════════════════ --}}
<section id="hero-section" class="relative w-full min-h-screen min-h-svh flex flex-col justify-between overflow-hidden bg-[#0D1A12] text-white">

    {{-- Background Layer --}}
    <div class="absolute inset-0 z-0 pointer-events-none select-none overflow-hidden">
        {{-- Video --}}
        <video id="hero-video"
               class="absolute inset-0 w-full h-full object-cover scale-[1.02] transition-all duration-1000 ease-out opacity-0"
               autoplay muted loop playsinline
               poster="{{ asset('images/sejarah-bangkiang-waterfall.webp') }}"
               oncanplay="onVideoReady()"
               onerror="onVideoError()">
            <source src="{{ asset('videos/hero-bangkiang.mp4') }}" type="video/mp4">
        </video>

        {{-- High quality fallback image --}}
        <img id="hero-img"
             src="{{ asset('images/sejarah-bangkiang-waterfall.webp') }}"
             alt="Bangkiang Jaran Waterfall"
             fetchpriority="high"
             onload="onImgLoaded()"
             class="absolute inset-0 w-full h-full object-cover scale-[1.02] transition-all duration-1000 ease-out"
             style="opacity:1;">

        {{-- Soft Cinematic Overlays --}}
        <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/20 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#0D1A12] via-[#0D1A12]/80 to-transparent"></div>
    </div>

    {{-- Spacer for Fixed Navbar --}}
    <div class="pt-24 md:pt-32"></div>

    {{-- Hero Main Content Body --}}
    <div id="hero-content"
         class="relative z-10 w-full max-w-7xl mx-auto px-gutter py-8 md:py-12 opacity-0 translate-y-6 transition-all duration-1000 ease-out flex flex-col justify-end">

        {{-- Tagline --}}
        <p class="font-sans text-xs md:text-sm font-medium tracking-[0.2em] uppercase text-white/90 mb-4">
            {{ __('messages.hero_tag') }}
        </p>

        {{-- Hero Editorial Title --}}
        <h1 class="font-serif text-[clamp(2.8rem,6.5vw,5.5rem)] leading-[1.04] text-white max-w-4xl tracking-tight mb-6 text-balance">
            <span id="typing-heading" class="block min-h-[1.1em]"></span>
        </h1>

        {{-- Subtitle --}}
        <p id="typing-sub"
           class="font-sans text-[15px] sm:text-base md:text-lg text-white/80 max-w-2xl font-light leading-relaxed mb-8 min-h-[2.8em]">
        </p>

        {{-- Action Button --}}
        <div class="flex items-center gap-4 mb-12">
            @auth
            <a href="{{ route('wisatawan.pemesanan.create') }}"
               class="inline-flex items-center gap-2.5 bg-white text-forest hover:bg-gray-100 font-sans text-sm font-semibold px-8 py-3.5 rounded-full shadow-lg transition-all duration-300 hover:-translate-y-0.5">
                <span>{{ __('messages.hero_cta_book') }}</span>
                <span class="material-symbols-outlined text-base">arrow_forward</span>
            </a>
            @else
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2.5 bg-white text-forest hover:bg-gray-100 font-sans text-sm font-semibold px-8 py-3.5 rounded-full shadow-lg transition-all duration-300 hover:-translate-y-0.5">
                <span>{{ __('messages.hero_cta_book') }}</span>
                <span class="material-symbols-outlined text-base">arrow_forward</span>
            </a>
            @endauth
        </div>

        {{-- Clean Minimalist Spec Strip --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 pt-6 border-t border-white/15 max-w-4xl">
            <div>
                <p class="font-sans text-[11px] uppercase tracking-widest text-white/50 mb-1">{{ __('messages.stat_height') }}</p>
                <p class="font-sans text-sm sm:text-base text-white font-medium">{{ __('messages.stat_height_val') }}</p>
            </div>
            <div>
                <p class="font-sans text-[11px] uppercase tracking-widest text-white/50 mb-1">{{ __('messages.stat_source') }}</p>
                <p class="font-sans text-sm sm:text-base text-white font-medium">{{ __('messages.stat_source_val') }}</p>
            </div>
            <div>
                <p class="font-sans text-[11px] uppercase tracking-widest text-white/50 mb-1">{{ __('messages.location') }}</p>
                <p class="font-sans text-sm sm:text-base text-white font-medium">{{ __('messages.address') }}</p>
            </div>
            <div>
                <p class="font-sans text-[11px] uppercase tracking-widest text-white/50 mb-1">{{ __('messages.operating_hours_label') }}</p>
                <p class="font-sans text-sm sm:text-base text-white font-medium">{{ __('messages.operating_hours') }}</p>
            </div>
        </div>

    </div>

    {{-- Bottom Breathing Space --}}
    <div class="pb-6"></div>

</section>

{{-- ══════════════════════════════════════════════════════
     2. THE NARRATIVE & HERITAGE — Storytelling
══════════════════════════════════════════════════════ --}}
<section id="narrative" class="py-24 md:py-32 px-gutter bg-[#FAF8F5] text-ink relative">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">

            {{-- Left Column: Storytelling --}}
            <div class="lg:col-span-6" data-aos="fade-up">
                <span class="font-sans text-xs uppercase tracking-[0.2em] text-forest/70 font-semibold mb-3 block">
                    {{ __('messages.narrative_tag') }}
                </span>

                <h2 class="font-serif text-[clamp(2.2rem,4vw,3.4rem)] text-forest leading-[1.08] mb-6 text-balance">
                    {{ __('messages.narrative_title') }}
                </h2>

                <p class="font-serif italic text-lg sm:text-xl text-forest/90 leading-relaxed mb-6">
                    "{{ __('messages.narrative_quote') }}"
                </p>

                <div class="space-y-4 font-sans text-[15px] sm:text-base text-stone font-light leading-relaxed">
                    <p>{{ __('messages.narrative_p1') }}</p>
                    <p>{{ __('messages.narrative_p2') }}</p>
                </div>
            </div>

            {{-- Right Column: Single Clean Photography Frame --}}
            <div class="lg:col-span-6" data-aos="fade-up" data-aos-delay="100">
                <div class="rounded-2xl overflow-hidden shadow-xl border border-stone/10 bg-warm h-[380px] sm:h-[480px]">
                    <img src="{{ asset('images/sejarah-bangkiang-waterfall.webp') }}"
                         alt="Pemandangan Alam Bangkiang Jaran"
                         class="w-full h-full object-cover transition-transform duration-700 hover:scale-105">
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     3. EMPAT ELEMEN PENGALAMAN SUAKA — Professional Cards
══════════════════════════════════════════════════════ --}}
<section class="py-24 md:py-32 px-gutter bg-white relative text-ink">
    <div class="max-w-7xl mx-auto">

        {{-- Section Header --}}
        <div class="max-w-2xl mb-16" data-aos="fade-up">
            <span class="font-sans text-xs uppercase tracking-[0.2em] text-forest/70 font-semibold mb-2 block">
                {{ __('messages.pillar_tag') }}
            </span>
            <h2 class="font-serif text-[clamp(2.2rem,4vw,3.4rem)] text-forest leading-[1.08] mb-4 text-balance">
                {{ __('messages.pillar_title') }}
            </h2>
            <p class="font-sans text-stone text-sm sm:text-base font-light leading-relaxed">
                {{ __('messages.pillar_desc') }}
            </p>
        </div>

        {{-- 4 Refined Experience Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Card 1 --}}
            <div class="bg-[#FAF8F5] rounded-2xl p-7 border border-stone/10 hover:border-forest/20 hover:shadow-md transition-all duration-300 flex flex-col justify-between" data-aos="fade-up">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-forest/8 text-forest flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-2xl">water_drop</span>
                    </div>
                    <h3 class="font-serif text-xl sm:text-2xl text-forest mb-3">{{ __('messages.pillar_1_title') }}</h3>
                    <p class="font-sans text-stone text-xs sm:text-sm font-light leading-relaxed">{{ __('messages.pillar_1_desc') }}</p>
                </div>
            </div>

            {{-- Card 2 --}}
            <div class="bg-[#FAF8F5] rounded-2xl p-7 border border-stone/10 hover:border-forest/20 hover:shadow-md transition-all duration-300 flex flex-col justify-between" data-aos="fade-up" data-aos-delay="100">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-forest/8 text-forest flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-2xl">pool</span>
                    </div>
                    <h3 class="font-serif text-xl sm:text-2xl text-forest mb-3">{{ __('messages.pillar_2_title') }}</h3>
                    <p class="font-sans text-stone text-xs sm:text-sm font-light leading-relaxed">{{ __('messages.pillar_2_desc') }}</p>
                </div>
            </div>

            {{-- Card 3 --}}
            <div class="bg-[#FAF8F5] rounded-2xl p-7 border border-stone/10 hover:border-forest/20 hover:shadow-md transition-all duration-300 flex flex-col justify-between" data-aos="fade-up" data-aos-delay="200">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-forest/8 text-forest flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-2xl">forest</span>
                    </div>
                    <h3 class="font-serif text-xl sm:text-2xl text-forest mb-3">{{ __('messages.pillar_3_title') }}</h3>
                    <p class="font-sans text-stone text-xs sm:text-sm font-light leading-relaxed">{{ __('messages.pillar_3_desc') }}</p>
                </div>
            </div>

            {{-- Card 4 --}}
            <div class="bg-[#FAF8F5] rounded-2xl p-7 border border-stone/10 hover:border-forest/20 hover:shadow-md transition-all duration-300 flex flex-col justify-between" data-aos="fade-up" data-aos-delay="300">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-forest/8 text-forest flex items-center justify-center mb-6">
                        <span class="material-symbols-outlined text-2xl">restaurant</span>
                    </div>
                    <h3 class="font-serif text-xl sm:text-2xl text-forest mb-3">{{ __('messages.pillar_4_title') }}</h3>
                    <p class="font-sans text-stone text-xs sm:text-sm font-light leading-relaxed">{{ __('messages.pillar_4_desc') }}</p>
                </div>
            </div>

        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     4. ARSIP VISUAL — Dynamic Promotion Gallery
══════════════════════════════════════════════════════ --}}
@if(isset($galeri) && $galeri->count() > 0)
<section id="galeri" class="py-24 md:py-32 px-gutter bg-[#FAF8F5] relative text-ink" x-data="{ lightbox: false, activeSrc: '', activeCaption: '' }">
    <div class="max-w-7xl mx-auto">

        {{-- Section Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-14 gap-6" data-aos="fade-up">
            <div class="max-w-xl">
                <span class="font-sans text-xs uppercase tracking-[0.2em] text-forest/70 font-semibold mb-2 block">
                    {{ __('messages.gallery_sec_tag') }}
                </span>
                <h2 class="font-serif text-[clamp(2.2rem,4vw,3.4rem)] text-forest leading-[1.08] mb-3 text-balance">
                    {{ __('messages.gallery_sec_title') }}
                </h2>
                <p class="font-sans text-stone text-sm sm:text-base font-light leading-relaxed">
                    {{ __('messages.gallery_sec_desc') }}
                </p>
            </div>

            <p class="font-sans text-xs text-stone/70">
                {{ __('messages.gallery_zoom_hint') }}
            </p>
        </div>

        {{-- Gallery Grid (Dynamic & Responsive) --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($galeri as $index => $media)
            <div class="relative rounded-2xl overflow-hidden bg-warm group cursor-pointer border border-stone/10 h-48 sm:h-64 shadow-sm"
                 data-aos="fade-up" data-aos-delay="{{ ($index % 4) * 60 }}"
                 @click="lightbox = true; activeSrc = '{{ asset('storage/' . $media->file) }}'; activeCaption = '{{ addslashes($media->keterangan ?? 'Bangkiang Jaran Waterfall') }}'">

                <img src="{{ asset('storage/' . $media->file) }}"
                     alt="{{ $media->keterangan ?? 'Bangkiang Jaran Waterfall' }}"
                     loading="lazy"
                     class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">

                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4 text-white">
                    <p class="font-sans text-xs sm:text-sm font-medium text-white line-clamp-1">{{ $media->keterangan ?? 'Bangkiang Jaran' }}</p>
                    <span class="font-sans text-[10px] uppercase tracking-widest text-white/60 mt-0.5">Gianyar, Bali</span>
                </div>
            </div>
            @endforeach
        </div>

    </div>

    {{-- Clean Lightbox Modal --}}
    <div x-show="lightbox"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 backdrop-blur-none"
         x-transition:enter-end="opacity-100 backdrop-blur-md"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 backdrop-blur-md"
         x-transition:leave-end="opacity-0 backdrop-blur-none"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-8 bg-black/90 backdrop-blur-md"
         @keydown.escape.window="lightbox = false">

        <button @click="lightbox = false"
                class="absolute top-6 right-6 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer">
            <span class="material-symbols-outlined text-2xl">close</span>
        </button>

        <div class="max-w-5xl w-full flex flex-col items-center" @click.outside="lightbox = false">
            <img :src="activeSrc"
                 class="max-h-[80vh] w-auto max-w-full object-contain rounded-xl shadow-2xl"
                 alt="Preview">
            <p x-text="activeCaption" class="font-sans text-xs sm:text-sm text-white/90 mt-4 text-center max-w-lg font-light tracking-wide"></p>
        </div>
    </div>

</section>
@endif

{{-- ══════════════════════════════════════════════════════
     5. LOKASI & AKSESIBILITAS — Clean Directions & Map
══════════════════════════════════════════════════════ --}}
<section id="lokasi" class="py-24 md:py-32 px-gutter bg-white relative text-ink">
    <div class="max-w-7xl mx-auto">

        {{-- Section Header --}}
        <div class="max-w-2xl mb-14" data-aos="fade-up">
            <span class="font-sans text-xs uppercase tracking-[0.2em] text-forest/70 font-semibold mb-2 block">
                {{ __('messages.travel_sec_tag') }}
            </span>
            <h2 class="font-serif text-[clamp(2.2rem,4vw,3.4rem)] text-forest leading-[1.08] mb-3 text-balance">
                {{ __('messages.travel_sec_title') }}
            </h2>
            <p class="font-sans text-stone text-sm sm:text-base font-light leading-relaxed">
                {{ __('messages.travel_sec_desc') }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

            {{-- Google Map Embed Frame --}}
            <div class="lg:col-span-7 rounded-2xl overflow-hidden shadow-sm border border-stone/15 h-[380px] sm:h-[450px]" data-aos="fade-up">
                <iframe src="https://www.google.com/maps?q=-8.511979,115.328486&output=embed&z=15"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>

            {{-- Right Travel Guide & Directions --}}
            <div class="lg:col-span-5 space-y-6" data-aos="fade-up" data-aos-delay="100">

                {{-- Travel Times List --}}
                <div class="bg-[#FAF8F5] p-6 rounded-2xl border border-stone/10">
                    <p class="font-sans text-xs uppercase tracking-widest text-forest font-semibold mb-4">{{ __('messages.travel_time') }} Estimasi</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="pb-2 border-b border-stone/10">
                            <p class="font-sans text-xs text-stone">{{ __('messages.travel_ubud') }}</p>
                            <p class="font-sans text-sm font-semibold text-forest mt-0.5">{{ __('messages.travel_ubud_sub') }}</p>
                        </div>
                        <div class="pb-2 border-b border-stone/10">
                            <p class="font-sans text-xs text-stone">{{ __('messages.travel_sanur') }}</p>
                            <p class="font-sans text-sm font-semibold text-forest mt-0.5">{{ __('messages.travel_sanur_sub') }}</p>
                        </div>
                        <div class="pb-2 border-b border-stone/10">
                            <p class="font-sans text-xs text-stone">{{ __('messages.travel_kuta') }}</p>
                            <p class="font-sans text-sm font-semibold text-forest mt-0.5">{{ __('messages.travel_kuta_sub') }}</p>
                        </div>
                        <div class="pb-2 border-b border-stone/10">
                            <p class="font-sans text-xs text-stone">{{ __('messages.travel_airport') }}</p>
                            <p class="font-sans text-sm font-semibold text-forest mt-0.5">{{ __('messages.travel_airport_sub') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Practical Advice --}}
                <div class="bg-[#FAF8F5] p-6 rounded-2xl border border-stone/10">
                    <p class="font-sans text-xs uppercase tracking-widest text-forest font-semibold mb-3">{{ __('messages.etiquette_title') }}</p>
                    <ul class="space-y-2.5 font-sans text-xs sm:text-sm text-stone font-light">
                        <li class="flex items-start gap-2.5">
                            <span class="material-symbols-outlined text-forest text-base mt-0.5">check</span>
                            <span>{{ __('messages.etiquette_1') }}</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="material-symbols-outlined text-forest text-base mt-0.5">check</span>
                            <span>{{ __('messages.etiquette_2') }}</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="material-symbols-outlined text-forest text-base mt-0.5">check</span>
                            <span>{{ __('messages.etiquette_3') }}</span>
                        </li>
                    </ul>
                </div>

                {{-- Google Maps Direct Link --}}
                <a href="https://www.google.com/maps?q=-8.511979,115.328486" target="_blank"
                   class="inline-flex items-center justify-center gap-2 w-full bg-forest text-white font-sans text-sm font-medium py-3.5 rounded-full hover:bg-leaf transition-all duration-300 shadow-sm">
                    <span class="material-symbols-outlined text-base">near_me</span>
                    <span>{{ __('messages.open_in_gmaps') }}</span>
                </a>

            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     6. INFORMASI PENTING (FAQ) — Minimalist Accordion
══════════════════════════════════════════════════════ --}}
<section class="py-24 md:py-32 px-gutter bg-[#FAF8F5] relative text-ink" x-data="{ openFaq: 1 }">
    <div class="max-w-3xl mx-auto">

        {{-- Section Header --}}
        <div class="text-center mb-14" data-aos="fade-up">
            <span class="font-sans text-xs uppercase tracking-[0.2em] text-forest/70 font-semibold mb-2 block">
                {{ __('messages.faq_sec_tag') }}
            </span>
            <h2 class="font-serif text-[clamp(2.2rem,4vw,3.4rem)] text-forest leading-[1.08] mb-3">
                {{ __('messages.faq_sec_title') }}
            </h2>
        </div>

        {{-- Minimalist FAQ Items --}}
        <div class="divide-y divide-stone/20 border-y border-stone/20">

            {{-- Item 1 --}}
            <div class="py-5" data-aos="fade-up">
                <button @click="openFaq = (openFaq === 1 ? null : 1)"
                        class="w-full flex items-center justify-between text-left font-serif text-lg sm:text-xl text-forest hover:text-leaf transition-colors cursor-pointer">
                    <span>{{ __('messages.faq_q1') }}</span>
                    <span class="font-sans text-lg text-stone ml-4 transition-transform duration-300" :class="{ 'rotate-45': openFaq === 1 }">+</span>
                </button>
                <div x-show="openFaq === 1" x-collapse>
                    <p class="font-sans text-xs sm:text-sm text-stone font-light leading-relaxed pt-3 max-w-2xl">
                        {{ __('messages.faq_a1') }}
                    </p>
                </div>
            </div>

            {{-- Item 2 --}}
            <div class="py-5" data-aos="fade-up" data-aos-delay="60">
                <button @click="openFaq = (openFaq === 2 ? null : 2)"
                        class="w-full flex items-center justify-between text-left font-serif text-lg sm:text-xl text-forest hover:text-leaf transition-colors cursor-pointer">
                    <span>{{ __('messages.faq_q2') }}</span>
                    <span class="font-sans text-lg text-stone ml-4 transition-transform duration-300" :class="{ 'rotate-45': openFaq === 2 }">+</span>
                </button>
                <div x-show="openFaq === 2" x-collapse>
                    <p class="font-sans text-xs sm:text-sm text-stone font-light leading-relaxed pt-3 max-w-2xl">
                        {{ __('messages.faq_a2') }}
                    </p>
                </div>
            </div>

            {{-- Item 3 --}}
            <div class="py-5" data-aos="fade-up" data-aos-delay="120">
                <button @click="openFaq = (openFaq === 3 ? null : 3)"
                        class="w-full flex items-center justify-between text-left font-serif text-lg sm:text-xl text-forest hover:text-leaf transition-colors cursor-pointer">
                    <span>{{ __('messages.faq_q3') }}</span>
                    <span class="font-sans text-lg text-stone ml-4 transition-transform duration-300" :class="{ 'rotate-45': openFaq === 3 }">+</span>
                </button>
                <div x-show="openFaq === 3" x-collapse>
                    <p class="font-sans text-xs sm:text-sm text-stone font-light leading-relaxed pt-3 max-w-2xl">
                        {{ __('messages.faq_a3') }}
                    </p>
                </div>
            </div>

            {{-- Item 4 --}}
            <div class="py-5" data-aos="fade-up" data-aos-delay="180">
                <button @click="openFaq = (openFaq === 4 ? null : 4)"
                        class="w-full flex items-center justify-between text-left font-serif text-lg sm:text-xl text-forest hover:text-leaf transition-colors cursor-pointer">
                    <span>{{ __('messages.faq_q4') }}</span>
                    <span class="font-sans text-lg text-stone ml-4 transition-transform duration-300" :class="{ 'rotate-45': openFaq === 4 }">+</span>
                </button>
                <div x-show="openFaq === 4" x-collapse>
                    <p class="font-sans text-xs sm:text-sm text-stone font-light leading-relaxed pt-3 max-w-2xl">
                        {{ __('messages.faq_a4') }}
                    </p>
                </div>
            </div>

        </div>

    </div>
</section>

@endsection

@push('styles')
<style>
/* Hero Reveal Transitions */
#hero-img { transition: opacity 0.8s ease, transform 1.2s ease-out; }
#hero-video { transition: opacity 0.8s ease; }
#hero-content { transition: opacity 0.9s cubic-bezier(0.16,1,0.3,1), transform 0.9s cubic-bezier(0.16,1,0.3,1); }
</style>
@endpush

@push('scripts')
<script>
let heroRevealed = false;

function onImgLoaded() {
    revealHero();
}

function onVideoReady() {
    const img = document.getElementById('hero-img');
    const vid = document.getElementById('hero-video');
    if (vid) vid.style.opacity = '1';
    if (img) img.style.opacity = '0';
    revealHero();
}

function onVideoError() {
    const vid = document.getElementById('hero-video');
    if (vid) vid.style.display = 'none';
    revealHero();
}

function revealHero() {
    if (heroRevealed) return;
    heroRevealed = true;

    const content = document.getElementById('hero-content');
    if (content) {
        content.classList.remove('opacity-0', 'translate-y-6');
    }

    setTimeout(startTyping, 350);
}

function startTyping() {
    const heading = document.getElementById('typing-heading');
    const sub = document.getElementById('typing-sub');
    if (!heading || !sub) return;

    const text1 = @json(__("messages.hero_title_lead"));
    const text2 = @json(__("messages.hero_desc_lead"));
    let idx = 0, isHeading = true;

    function type() {
        const target = isHeading ? heading : sub;
        const text   = isHeading ? text1 : text2;
        if (idx < text.length) {
            target.textContent = text.slice(0, idx + 1);
            idx++;
            setTimeout(type, isHeading ? 35 + Math.random() * 20 : 15 + Math.random() * 10);
        } else if (isHeading) {
            isHeading = false; idx = 0;
            setTimeout(type, 200);
        }
    }
    type();
}

(function () {
    const img = document.getElementById('hero-img');
    const vid = document.getElementById('hero-video');
    if (img && img.complete) revealHero();
    if (vid && vid.readyState >= 3) onVideoReady();
})();
</script>
@endpush
