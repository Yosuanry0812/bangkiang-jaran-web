@extends('layouts.app')

@section('content')
<div class="bg-background min-h-screen">
    {{-- ═══════════════════════════════════════════════════════════════
         HERO
    ════════════════════════════════════════════════════════════════ --}}
    <section class="relative w-full h-screen min-h-[600px] flex items-center justify-center overflow-hidden bg-[#1a3a32]">
        {{-- Media (image/video) — tampil duluan --}}
        <div class="absolute inset-0 z-0">
            <img class="w-full h-full object-cover"
                 src="{{ asset('images/sejarah-bangkiang-waterfall.webp') }}"
                 alt="Bangkiang Jaran Waterfall"
                 fetchpriority="high"
                 id="hero-img"
                 onload="revealHero()"
                 onerror="revealHero()">
            <div class="absolute inset-0 bg-black/30"></div>
        </div>
        {{-- Content — muncul setelah media ready --}}
        <div id="hero-content"
             class="relative z-10 max-w-container-max mx-auto px-gutter text-center flex flex-col items-center w-full
                    opacity-0 translate-y-8 transition-all duration-1000 ease-out">
            <h1 id="typing-heading" class="font-display text-display-mobile md:text-display-lg text-white mb-md text-shadow max-w-4xl min-h-[1.2em]"></h1>
            <p id="typing-sub" class="font-body text-body-lg text-white/90 mb-xl max-w-2xl text-shadow min-h-[1.6em]"></p>
            <a href="{{ route('tiket.index') }}"
               class="bg-primary-container text-white font-body text-label-md px-8 py-4 rounded-xl hover:-translate-y-1 hover:shadow-lg transition-all duration-300 flex items-center gap-sm">
                {{ __('messages.book_now') }}
                <span class="material-symbols-outlined">arrow_forward</span>
            </a>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════
         GALERI — PREVIEW
    ════════════════════════════════════════════════════════════════ --}}
    @if(isset($galeri) && $galeri->count() > 0)
    <section class="py-xl px-gutter bg-surface">
        <div class="max-w-container-max mx-auto">
            <div class="flex justify-between items-end mb-lg">
                <div>
                    <h2 class="font-display text-headline-md text-primary mb-sm">{{ __('messages.visual_journey') }}</h2>
                    <p class="font-body text-body-md text-on-surface-variant">{{ __('messages.visual_journey_desc') }}</p>
                </div>

            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-sm">
                @foreach($galeri->take(6) as $i => $media)
                <div class="{{ $i == 0 ? 'col-span-2 row-span-2' : '' }} rounded-2xl overflow-hidden relative group {{ $i > 0 ? 'aspect-square' : '' }}">
                    <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                         src="{{ asset('storage/' . $media->file) }}"
                         alt="{{ $media->keterangan ?? 'Foto Bangkiang Jaran' }}">
                    <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors duration-300"></div>
                </div>
                @endforeach
            </div>

        </div>
    </section>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════
         SEJARAH & INFORMASI — NARRATIVE BENTO GRID
    ════════════════════════════════════════════════════════════════ --}}
    @if(isset($kontenList) && $kontenList->count() > 0)
    <section id="sejarah" class="py-xl px-gutter bg-surface-container-low">
        <div class="max-w-container-max mx-auto">
            <div class="text-center mb-lg">
                <h2 class="font-display text-headline-md text-primary mb-sm">{{ __('messages.history_info') }}</h2>
                <p class="font-body text-body-md text-on-surface-variant">{{ __('messages.history_info_desc') }}</p>
            </div>
            <div class="space-y-lg">
                @foreach($kontenList->take(2) as $item)
                @php $isSejarah = $item->jenis == 'sejarah'; $isSecond = $loop->index == 1; @endphp

                <div class="grid grid-cols-1 md:grid-cols-12 gap-lg">
                    {{-- Image block first (only for sejarah, second item) --}}
                    @if($isSejarah && $isSecond)
                    <div class="md:col-span-7 h-[400px] md:h-auto rounded-2xl overflow-hidden card-shadow relative">
                        <img class="absolute inset-0 w-full h-full object-cover"
                             src="{{ asset('images/sejarah-bangkiang-waterfall.webp') }}"
                             alt="Sejarah Bangkiang Waterfall">
                    </div>
                    @endif

                    {{-- Text block --}}
                    <div class="@if($isSejarah && !$isSecond) md:col-span-7 @elseif($isSejarah && $isSecond) md:col-span-5 @else md:col-span-12 @endif bg-surface-container-lowest rounded-2xl p-lg card-shadow flex flex-col justify-center">
                        <h2 class="font-display text-headline-md text-on-background mb-md">
                            {{ $item->judul }}
                        </h2>
                        <div class="font-body text-body-md text-on-surface-variant space-y-md">
                            @php $paras = array_filter(array_map('trim', explode("\n", $item->isi))); @endphp
                            @foreach($paras as $p)
                            <p>{{ $p }}</p>
                            @endforeach
                        </div>
                    </div>

                    {{-- Image block (only for sejarah, NOT second item) --}}
                    @if($isSejarah && !$isSecond)
                    <div class="md:col-span-5 h-[400px] md:h-auto rounded-2xl overflow-hidden card-shadow relative">
                        <img class="absolute inset-0 w-full h-full object-cover"
                             src="{{ asset('images/sejarah-bangkiang-waterfall.webp') }}"
                             alt="Bangkiang Jaran Waterfall">
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════
         USP
    ════════════════════════════════════════════════════════════════ --}}
    <section class="py-xl px-gutter bg-surface-container-low">
        <div class="max-w-container-max mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-md">
                <div class="bg-surface-container-lowest p-lg rounded-2xl card-shadow hover:card-shadow-hover transition-shadow duration-300 text-center">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-md">
                        <span class="material-symbols-outlined text-primary text-3xl">nature</span>
                    </div>
                    <h3 class="font-display text-headline-sm text-primary mb-sm">{{ __('messages.natural_beauty') }}</h3>
                    <p class="font-body text-body-md text-on-surface-variant">{{ __('messages.natural_beauty_desc') }}</p>
                </div>
                <div class="bg-surface-container-lowest p-lg rounded-2xl card-shadow hover:card-shadow-hover transition-shadow duration-300 text-center">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-md">
                        <span class="material-symbols-outlined text-primary text-3xl">directions_walk</span>
                    </div>
                    <h3 class="font-display text-headline-sm text-primary mb-sm">{{ __('messages.easy_access') }}</h3>
                    <p class="font-body text-body-md text-on-surface-variant">{{ __('messages.easy_access_desc') }}</p>
                </div>
                <div class="bg-surface-container-lowest p-lg rounded-2xl card-shadow hover:card-shadow-hover transition-shadow duration-300 text-center">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-md">
                        <span class="material-symbols-outlined text-primary text-3xl">spa</span>
                    </div>
                    <h3 class="font-display text-headline-sm text-primary mb-sm">{{ __('messages.peaceful_atmosphere') }}</h3>
                    <p class="font-body text-body-md text-on-surface-variant">{{ __('messages.peaceful_atmosphere_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════
         LOKASI
    ════════════════════════════════════════════════════════════════ --}}
    <section id="lokasi" class="py-xl px-gutter bg-surface">
        <div class="max-w-container-max mx-auto">
            <div class="text-center mb-lg">
                <h2 class="font-display text-headline-md text-primary mb-sm">{{ __('messages.location_title') }}</h2>
                <p class="font-body text-body-md text-on-surface-variant">{{ __('messages.location_desc') }}</p>
            </div>
            <div class="rounded-2xl overflow-hidden card-shadow h-[350px]">
                <iframe
                    src="https://www.google.com/maps?q=-8.511979,115.328486&output=embed&z=15"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy">
                </iframe>
            </div>
            <div class="mt-md grid grid-cols-1 sm:grid-cols-3 gap-sm text-center">
                <div class="bg-surface-container-lowest rounded-xl p-md card-shadow">
                    <span class="material-symbols-outlined text-primary block mb-xs">route</span>
                    <p class="font-body text-label-md text-outline">{{ __('messages.distance') }}</p>
                    <p class="font-body text-body-md text-on-surface font-medium">{{ __('messages.distance_value') }}</p>
                </div>
                <div class="bg-surface-container-lowest rounded-xl p-md card-shadow">
                    <span class="material-symbols-outlined text-primary block mb-xs">schedule</span>
                    <p class="font-body text-label-md text-outline">{{ __('messages.travel_time') }}</p>
                    <p class="font-body text-body-md text-on-surface font-medium">{{ __('messages.travel_time_value') }}</p>
                </div>
                <div class="bg-surface-container-lowest rounded-xl p-md card-shadow">
                    <span class="material-symbols-outlined text-primary block mb-xs">directions_car</span>
                    <p class="font-body text-label-md text-outline">{{ __('messages.access_label') }}</p>
                    <p class="font-body text-body-md text-on-surface font-medium">{{ __('messages.access_value') }}</p>
                </div>
            </div>
        </div>
    </section>


</div>
@endsection

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
    content.classList.remove('opacity-0', 'translate-y-8');

    // Typing animation mulai setelah reveal selesai
    setTimeout(startTyping, 600);
}

/* ───────── typing animation ───────── */
function startTyping() {
    const heading = document.getElementById('typing-heading');
    const sub = document.getElementById('typing-sub');
    if (!heading || !sub) return;

    const text1 = '{{ __("messages.typing_heading") }}';
    const text2 = '{{ __("messages.typing_sub") }}';
    let idx = 0;
    let isHeading = true;
    let timer;

    function type() {
        const target = isHeading ? heading : sub;
        const text = isHeading ? text1 : text2;

        if (idx < text.length) {
            target.textContent = text.slice(0, idx + 1);
            idx++;
            const delay = isHeading ? 60 + Math.random() * 40 : 25 + Math.random() * 20;
            timer = setTimeout(type, delay);
        } else if (isHeading) {
            isHeading = false;
            idx = 0;
            timer = setTimeout(type, 400);
        }
    }

    type();
}

/* ───────── fallback kalo image udah cached ───────── */
(function() {
    const img = document.getElementById('hero-img');
    if (img && img.complete) {
        revealHero();
    }
})();
</script>
@endpush
