@extends('layouts.app')
@section('title', 'Restoran Kepulauan Rasa — Bangkiang Jaran')
@section('nav-mode', 'hero')

@section('content')

{{-- ══════════════════════════════════════════════════════
     1. HERO SECTION — Cinematic & Natural Ambiance
══════════════════════════════════════════════════════ --}}
<section id="resto-hero" class="relative w-full min-h-[85vh] flex flex-col justify-between overflow-hidden bg-[#0D1A12] text-white">

    {{-- Background Layer with Subtle Ken-Burns Zoom — dinamis: pakai foto restoran pertama dari pengelola jika ada --}}
    @php $heroResto = isset($galeriRestoran) && $galeriRestoran->count() > 0 ? $galeriRestoran->first() : null; @endphp
    <div class="absolute inset-0 z-0 select-none pointer-events-none overflow-hidden">
        <img src="{{ $heroResto ? asset('storage/' . $heroResto->file) : 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1920&q=80' }}"
             alt="{{ $heroResto->keterangan ?? 'Restoran Kepulauan Rasa Bangkiang Jaran' }}"
             class="w-full h-full object-cover scale-100 animate-slow-zoom transition-all duration-1000 ease-out"
             style="opacity: 0.85;">
        <div class="absolute inset-0 bg-gradient-to-b from-black/55 via-black/20 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#0D1A12] via-[#0D1A12]/80 to-transparent"></div>
    </div>

    {{-- Top Spacer for Fixed Navbar --}}
    <div class="pt-28 md:pt-36"></div>

    {{-- Hero Content Body --}}
    <div class="relative z-10 w-full max-w-7xl mx-auto px-gutter pb-16 pt-8">

        {{-- Tagline --}}
        <p class="font-sans text-xs md:text-sm font-medium tracking-[0.2em] uppercase text-white/90 mb-3 block"
           data-aos="fade-up" data-aos-duration="700">
            {{ __('messages.resto_label') }}
        </p>

        {{-- Hero Serif Title --}}
        <h1 class="font-serif text-[clamp(2.8rem,6vw,5.2rem)] leading-[1.04] text-white max-w-4xl tracking-tight mb-4 text-balance"
            data-aos="fade-up" data-aos-delay="100" data-aos-duration="800">
            {{ __('messages.resto_name') }}
        </h1>

        {{-- Subtitle --}}
        <p class="font-sans text-[15px] sm:text-base md:text-lg text-white/80 max-w-2xl font-light leading-relaxed mb-8"
           data-aos="fade-up" data-aos-delay="200" data-aos-duration="800">
            {{ __('messages.resto_desc') }}
        </p>

        {{-- Action Button --}}
        <div class="flex items-center gap-4 mb-10" data-aos="fade-up" data-aos-delay="300" data-aos-duration="800">
            <a href="#filosofi"
               class="inline-flex items-center gap-2.5 bg-white text-forest hover:bg-gray-100 font-sans text-sm font-semibold px-8 py-3.5 rounded-full shadow-lg transition-all duration-300 hover:-translate-y-0.5">
                <span>{{ __('messages.learn_more') }}</span>
                <span class="material-symbols-outlined text-base">arrow_downward</span>
            </a>
        </div>

        {{-- Operating Hours Strip --}}
        <div class="pt-6 border-t border-white/15 max-w-xl flex items-center gap-3 text-white/70 font-sans text-xs sm:text-sm"
             data-aos="fade-up" data-aos-delay="400" data-aos-duration="800">
            <span class="material-symbols-outlined text-base text-white/80">schedule</span>
            <span>{{ __('messages.resto_hours_detail') }}</span>
        </div>

    </div>

    <div class="pb-4"></div>

</section>

{{-- ══════════════════════════════════════════════════════
     2. THE RESTAURANT PHILOSOPHY — Story & Ingredients
══════════════════════════════════════════════════════ --}}
<section id="filosofi" class="py-24 md:py-32 px-gutter bg-[#FAF8F5] text-ink relative overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">

            {{-- Left Story Column --}}
            <div class="lg:col-span-6" data-aos="fade-right" data-aos-duration="800">
                <span class="font-sans text-xs uppercase tracking-[0.2em] text-forest/70 font-semibold mb-3 block">
                    {{ __('messages.resto_phil_tag') }}
                </span>

                <h2 class="font-serif text-[clamp(2.2rem,4vw,3.4rem)] text-forest leading-[1.08] mb-6 text-balance">
                    {{ __('messages.resto_phil_title') }}
                </h2>

                <p class="font-serif italic text-lg sm:text-xl text-forest/90 leading-relaxed mb-6">
                    "{{ __('messages.resto_phil_quote') }}"
                </p>

                <div class="space-y-4 font-sans text-[15px] sm:text-base text-stone font-light leading-relaxed mb-8">
                    <p>{{ __('messages.resto_phil_p1') }}</p>
                    <p>{{ __('messages.resto_phil_p2') }}</p>
                </div>

                {{-- Highlights List --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6 border-t border-stone/15">
                    <div>
                        <span class="material-symbols-outlined text-forest text-2xl mb-1">local_dining</span>
                        <p class="font-sans text-xs font-semibold text-forest">{{ __('messages.resto_feat_1') }}</p>
                    </div>
                    <div>
                        <span class="material-symbols-outlined text-forest text-2xl mb-1">eco</span>
                        <p class="font-sans text-xs font-semibold text-forest">{{ __('messages.resto_feat_2') }}</p>
                    </div>
                    <div>
                        <span class="material-symbols-outlined text-forest text-2xl mb-1">deck</span>
                        <p class="font-sans text-xs font-semibold text-forest">{{ __('messages.resto_feat_3') }}</p>
                    </div>
                </div>
            </div>

            {{-- Right Photo Grid — dinamis: ambil foto restoran ke-2 & ke-3 dari pengelola bila ada, fallback curated --}}
            @php
                $filoA = isset($galeriRestoran) && $galeriRestoran->count() > 1 ? $galeriRestoran[1] : null;
                $filoB = isset($galeriRestoran) && $galeriRestoran->count() > 2 ? $galeriRestoran[2] : null;
            @endphp
            <div class="lg:col-span-6" data-aos="fade-left" data-aos-duration="800">
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-2xl overflow-hidden shadow-sm h-64 sm:h-80 border border-stone/10 bg-warm group">
                        <img src="{{ $filoA ? asset('storage/' . $filoA->file) : 'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=800&q=80' }}"
                             alt="{{ $filoA->keterangan ?? 'Hidangan Tradisional Bali' }}"
                             class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-108">
                    </div>
                    <div class="rounded-2xl overflow-hidden shadow-sm h-64 sm:h-80 border border-stone/10 bg-warm mt-8 group">
                        <img src="{{ $filoB ? asset('storage/' . $filoB->file) : 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&q=80' }}"
                             alt="{{ $filoB->keterangan ?? 'Minuman Tropis Segar' }}"
                             class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-108">
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     3. DINING AMBIANCE & SEATING AREAS — Visual Showcase
══════════════════════════════════════════════════════ --}}
<section class="py-24 md:py-32 px-gutter bg-white text-ink relative">
    <div class="max-w-7xl mx-auto">

        {{-- Section Header --}}
        <div class="max-w-2xl mb-16" data-aos="fade-up" data-aos-duration="800">
            <span class="font-sans text-xs uppercase tracking-[0.2em] text-forest/70 font-semibold mb-2 block">
                {{ __('messages.resto_ambiance_tag') }}
            </span>
            <h2 class="font-serif text-[clamp(2.2rem,4vw,3.4rem)] text-forest leading-[1.08] mb-3 text-balance">
                {{ __('messages.resto_ambiance_title') }}
            </h2>
        </div>

        {{-- 3 Area Cards with Refined Luxury Editorial Styling --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- Area 1: Gazebo Tepi Sungai --}}
            <div class="group bg-[#FAF8F5] rounded-3xl p-8 sm:p-9 border border-stone/15 hover:border-forest/30 hover:shadow-xl hover:-translate-y-1.5 transition-all duration-500 flex flex-col justify-between"
                 data-aos="fade-up" data-aos-duration="800">
                <div>
                    {{-- Header with Elegant Monoline Emblem & Index --}}
                    <div class="flex items-center justify-between mb-8 pb-6 border-b border-stone/10">
                        <div class="w-12 h-12 rounded-full bg-[#EDE8E1] group-hover:bg-forest text-forest group-hover:text-white transition-colors duration-500 flex items-center justify-center p-2.5">
                            <svg class="w-full h-full" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9L12 3L21 9V10H3V9Z"/>
                                <path d="M6 10V18"/>
                                <path d="M12 10V18"/>
                                <path d="M18 10V18"/>
                                <path d="M4 18H20"/>
                                <path d="M2 21C6 20 8 22 12 21C16 20 18 22 22 21"/>
                            </svg>
                        </div>
                        <span class="font-serif text-2xl text-stone/40 group-hover:text-forest transition-colors duration-300">01</span>
                    </div>

                    <h3 class="font-serif text-2xl text-forest mb-3">{{ __('messages.resto_amb_1_title') }}</h3>
                    <p class="font-sans text-stone text-xs sm:text-sm font-light leading-relaxed mb-6">{{ __('messages.resto_amb_1_desc') }}</p>
                </div>
                <div class="pt-5 border-t border-stone/10 flex items-center gap-2 text-xs font-sans text-stone/70">
                    <span class="w-1.5 h-1.5 rounded-full bg-forest"></span>
                    <span>Suasana Tepi Aliran Sungai</span>
                </div>
            </div>

            {{-- Area 2: Bale Utama Terbuka --}}
            <div class="group bg-[#FAF8F5] rounded-3xl p-8 sm:p-9 border border-stone/15 hover:border-forest/30 hover:shadow-xl hover:-translate-y-1.5 transition-all duration-500 flex flex-col justify-between"
                 data-aos="fade-up" data-aos-delay="100" data-aos-duration="800">
                <div>
                    {{-- Header with Elegant Monoline Emblem & Index --}}
                    <div class="flex items-center justify-between mb-8 pb-6 border-b border-stone/10">
                        <div class="w-12 h-12 rounded-full bg-[#EDE8E1] group-hover:bg-forest text-forest group-hover:text-white transition-colors duration-500 flex items-center justify-center p-2.5">
                            <svg class="w-full h-full" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 9L12 2L22 9"/>
                                <path d="M4 9H20"/>
                                <path d="M5 9V19"/>
                                <path d="M19 9V19"/>
                                <path d="M9 19V13H15V19"/>
                                <path d="M3 19H21"/>
                            </svg>
                        </div>
                        <span class="font-serif text-2xl text-stone/40 group-hover:text-forest transition-colors duration-300">02</span>
                    </div>

                    <h3 class="font-serif text-2xl text-forest mb-3">{{ __('messages.resto_amb_2_title') }}</h3>
                    <p class="font-sans text-stone text-xs sm:text-sm font-light leading-relaxed mb-6">{{ __('messages.resto_amb_2_desc') }}</p>
                </div>
                <div class="pt-5 border-t border-stone/10 flex items-center gap-2 text-xs font-sans text-stone/70">
                    <span class="w-1.5 h-1.5 rounded-full bg-forest"></span>
                    <span>Kapasitas Rombongan & Keluarga</span>
                </div>
            </div>

            {{-- Area 3: Lesehan Rumpun Bambu --}}
            <div class="group bg-[#FAF8F5] rounded-3xl p-8 sm:p-9 border border-stone/15 hover:border-forest/30 hover:shadow-xl hover:-translate-y-1.5 transition-all duration-500 flex flex-col justify-between"
                 data-aos="fade-up" data-aos-delay="200" data-aos-duration="800">
                <div>
                    {{-- Header with Elegant Monoline Emblem & Index --}}
                    <div class="flex items-center justify-between mb-8 pb-6 border-b border-stone/10">
                        <div class="w-12 h-12 rounded-full bg-[#EDE8E1] group-hover:bg-forest text-forest group-hover:text-white transition-colors duration-500 flex items-center justify-center p-2.5">
                            <svg class="w-full h-full" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8 3V21"/>
                                <path d="M16 3V21"/>
                                <path d="M6 8H10"/>
                                <path d="M6 14H10"/>
                                <path d="M14 10H18"/>
                                <path d="M14 16H18"/>
                                <path d="M8 8C12 7 14 5 13 3C11 4 9 6 8 8Z"/>
                                <path d="M16 12C20 11 22 9 21 7C19 8 17 10 16 12Z"/>
                            </svg>
                        </div>
                        <span class="font-serif text-2xl text-stone/40 group-hover:text-forest transition-colors duration-300">03</span>
                    </div>

                    <h3 class="font-serif text-2xl text-forest mb-3">{{ __('messages.resto_amb_3_title') }}</h3>
                    <p class="font-sans text-stone text-xs sm:text-sm font-light leading-relaxed mb-6">{{ __('messages.resto_amb_3_desc') }}</p>
                </div>
                <div class="pt-5 border-t border-stone/10 flex items-center gap-2 text-xs font-sans text-stone/70">
                    <span class="w-1.5 h-1.5 rounded-full bg-forest"></span>
                    <span>Relaksasi Lesehan Rindang</span>
                </div>
            </div>

        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     4. DOKUMENTASI & GALERI RESTORAN (Dinamis + Lightbox)
══════════════════════════════════════════════════════ --}}
@php
    $curatedRestoPhotos = [
        ['src' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&q=80', 'title' => 'Suasana Gazebo Utama Restoran'],
        ['src' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=800&q=80', 'title' => 'Hidangan Tradisional Bali Berempah'],
        ['src' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&q=80', 'title' => 'Minuman Tropis & Kelapa Segar'],
        ['src' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=800&q=80', 'title' => 'Sajian Ikan Bakar Sambal Matah'],
    ];
@endphp

<section class="py-24 md:py-32 px-gutter bg-[#FAF8F5] text-ink relative" x-data="{ lightbox: false, activeSrc: '', activeCaption: '' }">
    <div class="max-w-7xl mx-auto">

        {{-- Section Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-14 gap-6" data-aos="fade-up" data-aos-duration="800">
            <div class="max-w-xl">
                <span class="font-sans text-xs uppercase tracking-[0.2em] text-forest/70 font-semibold mb-2 block">
                    {{ __('messages.resto_gallery_label') }}
                </span>
                <h2 class="font-serif text-[clamp(2.2rem,4vw,3.4rem)] text-forest leading-[1.08] mb-3 text-balance">
                    {{ __('messages.resto_gallery_heading') }}
                </h2>
                <p class="font-sans text-stone text-sm sm:text-base font-light leading-relaxed">
                    {{ __('messages.resto_gallery_desc') }}
                </p>
            </div>

            <p class="font-sans text-xs text-stone/70">
                {{ __('messages.gallery_zoom_hint') }}
            </p>
        </div>

        {{-- Gallery Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
            @if(isset($galeriRestoran) && $galeriRestoran->count() > 0)
                @foreach($galeriRestoran as $index => $g)
                <div class="relative rounded-2xl overflow-hidden bg-warm group cursor-pointer border border-stone/10 h-48 sm:h-64 shadow-sm"
                     data-aos="fade-up" data-aos-delay="{{ ($index % 4) * 60 }}" data-aos-duration="700"
                     @click="lightbox = true; activeSrc = '{{ asset('storage/' . $g->file) }}'; activeCaption = '{{ addslashes($g->keterangan ?? 'Restoran Kepulauan Rasa') }}'">

                    <img src="{{ asset('storage/' . $g->file) }}"
                         alt="{{ $g->keterangan ?? 'Restoran' }}"
                         loading="lazy"
                         class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-108">

                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4 text-white">
                        <p class="font-sans text-xs sm:text-sm font-medium text-white line-clamp-1">{{ $g->keterangan ?? 'Restoran Kepulauan Rasa' }}</p>
                    </div>
                </div>
                @endforeach
            @else
                @foreach($curatedRestoPhotos as $index => $item)
                <div class="relative rounded-2xl overflow-hidden bg-warm group cursor-pointer border border-stone/10 h-48 sm:h-64 shadow-sm"
                     data-aos="fade-up" data-aos-delay="{{ $index * 80 }}" data-aos-duration="700"
                     @click="lightbox = true; activeSrc = '{{ $item['src'] }}'; activeCaption = '{{ addslashes($item['title']) }}'">

                    <img src="{{ $item['src'] }}"
                         alt="{{ $item['title'] }}"
                         loading="lazy"
                         class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-108">

                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4 text-white">
                        <p class="font-sans text-xs sm:text-sm font-medium text-white line-clamp-1">{{ $item['title'] }}</p>
                    </div>
                </div>
                @endforeach
            @endif
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

@endsection

@push('styles')
<style>
@keyframes slowZoom {
    0% { transform: scale(1.0); }
    50% { transform: scale(1.04); }
    100% { transform: scale(1.0); }
}
.animate-slow-zoom {
    animation: slowZoom 20s ease-in-out infinite alternate;
}
.hover\:scale-108:hover {
    transform: scale(1.08);
}
</style>
@endpush
