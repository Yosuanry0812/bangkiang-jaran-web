@extends('layouts.app')
@section('title', __('messages.dining_label') . ' | Bangkiang Jaran')
@section('nav-mode', 'hero')
@section('content')
@php
    $isEnglish = app()->getLocale() === 'en';
    $restaurantPhotos = $galeriRestoran->filter(fn ($item) => ! preg_match('/(?:^|\/)(?:galeri-\d+|placeholder\d*)\.(?:jpe?g|png|webp)$/i', (string) $item->file))->values();
    $deckPhotos = $restaurantPhotos->map(fn ($item) => ['url' => asset('storage/' . $item->file), 'caption' => $item->keterangan ?: 'Restoran Bangkiang Jaran']);
    $heroFallback = ['url' => asset('images/sejarah-bangkiang-waterfall.webp'), 'caption' => $isEnglish ? 'The landscape at Bangkiang Jaran Waterfall' : 'Lanskap Air Terjun Bangkiang Jaran'];
    $heroPhotos = $deckPhotos->unique('url')->values();
    if ($heroPhotos->isEmpty()) $heroPhotos = collect([$heroFallback]);
@endphp
<div class="bj-resto-stitch bj-restaurant-fresh" data-layout="bj-restaurant-hero">
    <header id="hero-section" class="bj-resto-heading bj-resto-heading--landscape bj-resto-full-hero" data-hero-intro aria-labelledby="resto-hero-title">
        <script>
            (() => {
                const hero = document.currentScript.parentElement;
                if (matchMedia('(prefers-reduced-motion: reduce)').matches || navigator.connection?.saveData) return;
                hero.classList.add('is-intro-pending');
                setTimeout(() => {
                    if (hero.hasAttribute('data-intro-managed')) return;
                    hero.classList.remove('is-intro-pending');
                    hero.classList.add('is-intro-complete');
                    hero.querySelector('.bj-resto-hero-copy').inert = false;
                }, 12000);
            })();
        </script>
        <div class="bj-resto-heading__grid">
            <div class="bj-resto-hero-copy">
                <p class="bj-nature-label bj-resto-kicker">{{ $isEnglish ? 'Restaurant at Bangkiang Jaran' : 'Restoran di Bangkiang Jaran' }}</p>
                <h1 id="resto-hero-title" data-hero-typewriter>{{ $isEnglish ? 'After the waterfall, a seat at the table.' : 'Seusai air terjun, mampir ke meja makan.' }}</h1>
                <p class="bj-resto-lead">{{ $isEnglish ? 'Take a break from the trail. Sit down, share a meal, and enjoy your time in Bakbakan.' : 'Beristirahat dari perjalanan. Duduk bersama, nikmati waktu makan, dan lanjutkan hari di Bakbakan.' }}</p>
                <a class="bj-nature-text-link bj-resto-actions" href="#suasana-restoran">{{ $isEnglish ? 'See the atmosphere' : 'Lihat suasananya' }}<span class="material-symbols-outlined" aria-hidden="true">south_east</span></a>
            </div>
        </div>
            <figure class="bj-resto-hero-photo bj-resto-film" data-resto-film data-resto-photos="{{ $heroPhotos->toJson() }}" data-resto-fallback="{{ json_encode($heroFallback) }}">
                <img class="bj-resto-film__frame" data-resto-frame src="{{ $heroPhotos->first()['url'] }}" alt="{{ $heroPhotos->first()['caption'] }}" width="1600" height="1000" fetchpriority="high" decoding="async">
                <img class="bj-resto-film__frame" data-resto-frame alt="" width="1600" height="1000" decoding="async" aria-hidden="true">
            </figure>
    </header>
    <div class="bj-home-overlap bj-restaurant-overlap" data-overlap-stack>
    <section id="suasana-restoran" class="bj-resto-gallery" data-overlap-panel aria-labelledby="resto-gallery-title">
        <div class="bj-nature-wrap">
        <div class="bj-stream-heading"><h2 id="resto-gallery-title">{{ $isEnglish ? 'Around the table.' : 'Di sekitar meja makan.' }}</h2><p>{{ $isEnglish ? 'A closer look at the dining space and the food.' : 'Lihat ruang bersantap dan sajian dari dekat.' }}</p></div>
        @include('partials.photo-columns', ['columnPhotos' => $deckPhotos, 'columnEmptyMessage' => $isEnglish ? 'Restaurant photographs have not been added yet.' : 'Dokumentasi restoran belum tersedia.'])
        </div>
    </section>
    <section class="bj-resto-overlap-visit" data-overlap-panel aria-labelledby="resto-information-title">
        <div class="bj-resto-visit bj-nature-wrap">
        <div>
            <p class="bj-nature-label">{{ $isEnglish ? 'Part of a day outdoors' : 'Bagian dari hari di alam' }}</p>
            <h2 id="resto-information-title">{{ $isEnglish ? 'From the trail to the table.' : 'Dari jalur setapak ke meja makan.' }}</h2>
            <p>{{ $isEnglish ? 'A dining stop at Bangkiang Jaran, in Bakbakan Village. Ask the team about the menu and seating before you arrive.' : 'Jeda bersantap di Bangkiang Jaran, Desa Bakbakan. Tanyakan pilihan menu dan ketersediaan tempat kepada pengelola sebelum datang.' }}</p>
            @if(config('payment.contact_whatsapp'))
            <div class="mt-4">
                <a href="https://wa.me/{{ config('payment.contact_whatsapp') }}?text={{ urlencode($isEnglish ? 'Hello, I would like to ask about restaurant reservation at Bangkiang Jaran.' : 'Halo, saya ingin menanyakan reservasi restoran di Bangkiang Jaran.') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 bg-[#25D366] hover:bg-[#20bd5a] text-white px-5 py-2.5 rounded-full font-medium text-sm transition-all shadow-sm">
                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    <span>{{ $isEnglish ? 'Contact via WhatsApp: 0821-1145-4935' : 'Hubungi via WhatsApp: 0821-1145-4935' }}</span>
                </a>
            </div>
            @endif
        </div>
        <dl><div><dt>{{ $isEnglish ? 'Find us' : 'Temukan kami' }}</dt><dd>Bangkiang Jaran<br>Desa Bakbakan, Gianyar, Bali</dd></div><div><dt>{{ $isEnglish ? 'Menu & prices' : 'Menu & harga' }}</dt><dd>{{ $isEnglish ? 'Ask the team for the latest menu.' : 'Tanyakan menu terbaru kepada pengelola.' }}</dd></div><div><dt>{{ $isEnglish ? 'Coming with a group?' : 'Datang bersama rombongan?' }}</dt><dd>{{ $isEnglish ? 'Confirm seating before your visit.' : 'Konfirmasikan tempat sebelum berkunjung.' }}</dd></div></dl>
        </div>
    </section>
    </div>
</div>
@endsection
