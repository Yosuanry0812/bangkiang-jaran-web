@extends('layouts.app')

@section('title', 'Tiket Masuk & Reservasi — Bangkiang Jaran')
@section('nav-mode', 'hero')

@section('content')

{{-- ══════════════════════════════════════════════════════
     1. HERO SECTION — Cinematic & Natural Ambiance
══════════════════════════════════════════════════════ --}}
<section id="tiket-hero" class="relative w-full min-h-[75vh] sm:min-h-[80vh] flex flex-col justify-between overflow-hidden bg-[#0D1A12] text-white">

    {{-- Background Layer with Slow Zoom --}}
    <div class="absolute inset-0 z-0 select-none pointer-events-none overflow-hidden">
        <img src="{{ asset('images/sejarah-bangkiang-waterfall.webp') }}"
             alt="Bangkiang Jaran Waterfall Admission"
             class="w-full h-full object-cover scale-100 animate-slow-zoom transition-all duration-1000 ease-out"
             style="opacity: 0.82;">
        <div class="absolute inset-0 bg-gradient-to-b from-black/55 via-black/25 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#0D1A12] via-[#0D1A12]/80 to-transparent"></div>
    </div>

    {{-- Top Spacer for Fixed Navbar --}}
    <div class="pt-28 md:pt-36"></div>

    {{-- Hero Content Body --}}
    <div class="relative z-10 w-full max-w-7xl mx-auto px-gutter pb-14 pt-8">

        {{-- Tagline --}}
        <p class="font-sans text-xs md:text-sm font-medium tracking-[0.2em] uppercase text-white/90 mb-3 block"
           data-aos="fade-up" data-aos-duration="700">
            {{ __('messages.online_booking') }}
        </p>

        {{-- Hero Title --}}
        <h1 class="font-serif text-[clamp(2.6rem,5.8vw,4.8rem)] leading-[1.05] text-white max-w-3xl tracking-tight mb-4 text-balance"
            data-aos="fade-up" data-aos-delay="100" data-aos-duration="800">
            {{ __('messages.plan_your_journey') }}
        </h1>

        {{-- Subtitle --}}
        <p class="font-sans text-[15px] sm:text-base md:text-lg text-white/80 max-w-2xl font-light leading-relaxed mb-8"
           data-aos="fade-up" data-aos-delay="200" data-aos-duration="800">
            {{ __('messages.plan_your_journey_desc') }}
        </p>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap items-center gap-4 mb-10" data-aos="fade-up" data-aos-delay="300" data-aos-duration="800">
            <a href="#katalog-tiket"
               class="inline-flex items-center gap-2.5 bg-white text-forest hover:bg-gray-100 font-sans text-sm font-semibold px-8 py-3.5 rounded-full shadow-lg transition-all duration-300 hover:-translate-y-0.5">
                <span>{{ __('messages.select_ticket') }}</span>
                <span class="material-symbols-outlined text-base">arrow_downward</span>
            </a>

            <a href="#panduan-pesan"
               class="inline-flex items-center gap-2.5 font-sans text-sm text-white bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 hover:border-white/40 px-7 py-3.5 rounded-full transition-all duration-300 hover:-translate-y-0.5">
                <span>{{ __('messages.how_to_book') }}</span>
            </a>
        </div>

        {{-- Quick Spec Strip --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 pt-6 border-t border-white/15 max-w-4xl"
             data-aos="fade-up" data-aos-delay="400" data-aos-duration="800">
            <div>
                <p class="font-sans text-[11px] uppercase tracking-widest text-white/50 mb-1">{{ __('messages.operating_hours_label') }}</p>
                <p class="font-sans text-sm sm:text-base text-white font-medium">{{ __('messages.operating_hours') }}</p>
            </div>
            <div>
                <p class="font-sans text-[11px] uppercase tracking-widest text-white/50 mb-1">{{ __('messages.location') }}</p>
                <p class="font-sans text-sm sm:text-base text-white font-medium">{{ __('messages.address') }}</p>
            </div>
            <div>
                <p class="font-sans text-[11px] uppercase tracking-widest text-white/50 mb-1">Konfirmasi</p>
                <p class="font-sans text-sm sm:text-base text-white font-medium">E-Ticket Instan</p>
            </div>
            <div>
                <p class="font-sans text-[11px] uppercase tracking-widest text-white/50 mb-1">Pembayaran</p>
                <p class="font-sans text-sm sm:text-base text-white font-medium">QRIS & Bank</p>
            </div>
        </div>

    </div>

    <div class="pb-4"></div>

</section>

{{-- ══════════════════════════════════════════════════════
     2. KATALOG TIKET MASUK — Clean Admission Passes
══════════════════════════════════════════════════════ --}}
<section id="katalog-tiket" class="py-24 md:py-32 px-gutter bg-[#FAF8F5] text-ink relative">
    <div class="max-w-7xl mx-auto">

        {{-- Section Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6" data-aos="fade-up">
            <div class="max-w-xl">
                <span class="font-sans text-xs uppercase tracking-[0.2em] text-forest/70 font-semibold mb-2 block">
                    {{ __('messages.ticket_sec_tag') }}
                </span>
                <h2 class="font-serif text-[clamp(2.2rem,4vw,3.4rem)] text-forest leading-[1.08] mb-3 text-balance">
                    {{ __('messages.ticket_sec_title') }}
                </h2>
                <p class="font-sans text-stone text-sm sm:text-base font-light leading-relaxed">
                    {{ __('messages.ticket_sec_desc') }}
                </p>
            </div>

            <div class="flex items-center gap-2 text-xs font-sans text-stone/70">
                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                <span>Semua tiket berlaku untuk 1 hari kunjungan penuh</span>
            </div>
        </div>

        {{-- Tickets Grid --}}
        @if(isset($tikets) && $tikets->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($tikets as $index => $t)
            <div class="group bg-white rounded-3xl p-8 sm:p-9 border border-stone/15 hover:border-forest/40 hover:shadow-xl hover:-translate-y-1.5 transition-all duration-500 flex flex-col justify-between"
                 data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">

                <div>
                    {{-- Pass Top Meta --}}
                    <div class="flex items-center justify-between pb-5 mb-6 border-b border-stone/10">
                        <span class="font-sans text-[11px] uppercase tracking-widest text-forest font-semibold">
                            {{ $t->kategori ?? 'Tiket Masuk' }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 font-sans text-xs text-stone/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ __('messages.available') }}
                        </span>
                    </div>

                    {{-- Pass Title --}}
                    <h3 class="font-serif text-2xl sm:text-3xl text-forest mb-2">{{ $t->nama_tiket }}</h3>
                    <p class="font-sans text-xs text-stone font-light mb-7">{{ __('messages.ticket_entry_one_day') }}</p>

                    {{-- Price Presentation --}}
                    <div class="flex items-baseline gap-1.5 mb-7 pb-7 border-b border-stone/10">
                        <span class="font-sans text-xs text-stone font-light">IDR</span>
                        <span class="font-serif text-4xl sm:text-5xl text-forest font-normal tracking-tight">{{ number_format($t->harga, 0, ',', '.') }}</span>
                        <span class="font-sans text-xs text-stone/70">/ {{ __('messages.per_person_unit') }}</span>
                    </div>

                    {{-- Inclusions Checklist --}}
                    <ul class="space-y-3 font-sans text-xs sm:text-sm text-stone font-light mb-8">
                        <li class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-forest text-base">check</span>
                            <span>{{ __('messages.facility_access_entry') }}</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-forest text-base">check</span>
                            <span>{{ __('messages.facility_free_access') }}</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-forest text-base">check</span>
                            <span>{{ __('messages.instant_confirmation_email') }}</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-forest text-base">check</span>
                            <span>Jalur cepat scan QR Code di gerbang masuk</span>
                        </li>
                    </ul>
                </div>

                {{-- Action Buttons --}}
                <div class="space-y-2.5 pt-2">
                    @auth
                    <a href="{{ route('wisatawan.pemesanan.create', ['id_tiket' => $t->id_tiket]) }}"
                       class="w-full inline-flex items-center justify-center gap-2 text-center py-3.5 rounded-full font-sans text-xs uppercase tracking-widest font-semibold bg-forest text-white hover:bg-leaf transition-all duration-300 shadow-sm">
                        <span>{{ __('messages.book_this_ticket') }}</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                    @else
                    <a href="{{ route('login') }}"
                       class="w-full inline-flex items-center justify-center gap-2 text-center py-3.5 rounded-full font-sans text-xs uppercase tracking-widest font-semibold bg-forest text-white hover:bg-leaf transition-all duration-300 shadow-sm">
                        <span>{{ __('messages.login_and_book') }}</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                    @endauth

                    <a href="{{ route('tiket.detail', $t->id_tiket) }}"
                       class="w-full text-center block py-2.5 rounded-full font-sans text-xs text-stone/80 hover:text-forest transition-colors">
                        {{ __('messages.breadcrumb_detail') }}
                    </a>
                </div>

            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-20 bg-white rounded-3xl border border-stone/15" data-aos="fade-up">
            <span class="material-symbols-outlined text-4xl text-stone/40 block mb-3">confirmation_number</span>
            <p class="font-serif text-2xl text-forest mb-2">{{ __('messages.no_tickets') }}</p>
            <p class="font-sans text-sm text-stone font-light">{{ __('messages.no_tickets_desc') }}</p>
        </div>
        @endif

    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     3. KEUNGGULAN PESAN ONLINE — 3 Core Values
══════════════════════════════════════════════════════ --}}
<section class="py-24 md:py-32 px-gutter bg-white text-ink relative">
    <div class="max-w-7xl mx-auto">

        {{-- Section Header --}}
        <div class="max-w-2xl mb-16" data-aos="fade-up">
            <span class="font-sans text-xs uppercase tracking-[0.2em] text-forest/70 font-semibold mb-2 block">
                {{ __('messages.advantages') }}
            </span>
            <h2 class="font-serif text-[clamp(2.2rem,4vw,3.4rem)] text-forest leading-[1.08] mb-3 text-balance">
                {{ __('messages.why_book_online') }}
            </h2>
        </div>

        {{-- 3 Column Values --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- Value 1 --}}
            <div class="p-8 rounded-3xl bg-[#FAF8F5] border border-stone/10" data-aos="fade-up">
                <span class="font-serif text-3xl text-stone/40 block mb-4">01</span>
                <h3 class="font-serif text-xl sm:text-2xl text-forest mb-3">{{ __('messages.fast_easy_process') }}</h3>
                <p class="font-sans text-stone text-xs sm:text-sm font-light leading-relaxed">{{ __('messages.fast_easy_desc') }}</p>
            </div>

            {{-- Value 2 --}}
            <div class="p-8 rounded-3xl bg-[#FAF8F5] border border-stone/10" data-aos="fade-up" data-aos-delay="100">
                <span class="font-serif text-3xl text-stone/40 block mb-4">02</span>
                <h3 class="font-serif text-xl sm:text-2xl text-forest mb-3">{{ __('messages.secure_reliable') }}</h3>
                <p class="font-sans text-stone text-xs sm:text-sm font-light leading-relaxed">{{ __('messages.secure_reliable_desc') }}</p>
            </div>

            {{-- Value 3 --}}
            <div class="p-8 rounded-3xl bg-[#FAF8F5] border border-stone/10" data-aos="fade-up" data-aos-delay="200">
                <span class="font-serif text-3xl text-stone/40 block mb-4">03</span>
                <h3 class="font-serif text-xl sm:text-2xl text-forest mb-3">{{ __('messages.anytime_support') }}</h3>
                <p class="font-sans text-stone text-xs sm:text-sm font-light leading-relaxed">{{ __('messages.anytime_support_desc') }}</p>
            </div>

        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     4. PANDUAN CARA PESAN — 3 Easy Steps
══════════════════════════════════════════════════════ --}}
<section id="panduan-pesan" class="py-24 md:py-32 px-gutter bg-[#FAF8F5] text-ink relative">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">

            {{-- Left Steps Timeline --}}
            <div class="lg:col-span-6" data-aos="fade-right">
                <span class="font-sans text-xs uppercase tracking-[0.2em] text-forest/70 font-semibold mb-2 block">
                    {{ __('messages.how_to_book') }}
                </span>
                <h2 class="font-serif text-[clamp(2.2rem,4vw,3.4rem)] text-forest leading-[1.08] mb-10 text-balance">
                    {{ __('messages.three_steps_title') }}
                </h2>

                <div class="space-y-8">
                    {{-- Step 1 --}}
                    <div class="flex items-start gap-5">
                        <span class="w-10 h-10 rounded-full bg-forest text-white font-serif text-lg flex items-center justify-center flex-shrink-0">1</span>
                        <div>
                            <h4 class="font-serif text-xl text-forest mb-1.5">{{ __('messages.step_one_title') }}</h4>
                            <p class="font-sans text-xs sm:text-sm text-stone font-light leading-relaxed">{{ __('messages.step_one_desc') }}</p>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="flex items-start gap-5">
                        <span class="w-10 h-10 rounded-full bg-forest text-white font-serif text-lg flex items-center justify-center flex-shrink-0">2</span>
                        <div>
                            <h4 class="font-serif text-xl text-forest mb-1.5">{{ __('messages.step_two_title') }}</h4>
                            <p class="font-sans text-xs sm:text-sm text-stone font-light leading-relaxed">{{ __('messages.step_two_desc') }}</p>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="flex items-start gap-5">
                        <span class="w-10 h-10 rounded-full bg-forest text-white font-serif text-lg flex items-center justify-center flex-shrink-0">3</span>
                        <div>
                            <h4 class="font-serif text-xl text-forest mb-1.5">{{ __('messages.step_three_title') }}</h4>
                            <p class="font-sans text-xs sm:text-sm text-stone font-light leading-relaxed">{{ __('messages.step_three_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Photo Visual Frame --}}
            <div class="lg:col-span-6" data-aos="fade-left">
                <div class="rounded-3xl overflow-hidden shadow-xl border border-stone/10 bg-warm h-[380px] sm:h-[480px]">
                    <img src="{{ asset('images/sejarah-bangkiang-waterfall.webp') }}"
                         alt="Pemesanan Tiket Bangkiang Jaran"
                         class="w-full h-full object-cover transition-transform duration-700 hover:scale-105">
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════
     5. TANYA JAWAB SEPUTAR TIKET (FAQ)
══════════════════════════════════════════════════════ --}}
<section class="py-24 md:py-32 px-gutter bg-white text-ink relative" x-data="{ openFaq: 1 }">
    <div class="max-w-3xl mx-auto">

        {{-- Section Header --}}
        <div class="text-center mb-14" data-aos="fade-up">
            <span class="font-sans text-xs uppercase tracking-[0.2em] text-forest/70 font-semibold mb-2 block">
                FAQ
            </span>
            <h2 class="font-serif text-[clamp(2.2rem,4vw,3.4rem)] text-forest leading-[1.08] mb-3">
                {{ __('messages.faq_title') }}
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
@keyframes slowZoom {
    0% { transform: scale(1.0); }
    50% { transform: scale(1.04); }
    100% { transform: scale(1.0); }
}
.animate-slow-zoom {
    animation: slowZoom 20s ease-in-out infinite alternate;
}
</style>
@endpush
