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
        <div><p class="bj-nature-label">{{ $isEnglish ? 'Part of a day outdoors' : 'Bagian dari hari di alam' }}</p><h2 id="resto-information-title">{{ $isEnglish ? 'From the trail to the table.' : 'Dari jalur setapak ke meja makan.' }}</h2><p>{{ $isEnglish ? 'A dining stop at Bangkiang Jaran, in Bakbakan Village. Ask the team about the menu and seating before you arrive.' : 'Jeda bersantap di Bangkiang Jaran, Desa Bakbakan. Tanyakan pilihan menu dan ketersediaan tempat kepada pengelola sebelum datang.' }}</p></div>
        <dl><div><dt>{{ $isEnglish ? 'Find us' : 'Temukan kami' }}</dt><dd>Bangkiang Jaran<br>Desa Bakbakan, Gianyar, Bali</dd></div><div><dt>{{ $isEnglish ? 'Menu & prices' : 'Menu & harga' }}</dt><dd>{{ $isEnglish ? 'Ask the team for the latest menu.' : 'Tanyakan menu terbaru kepada pengelola.' }}</dd></div><div><dt>{{ $isEnglish ? 'Coming with a group?' : 'Datang bersama rombongan?' }}</dt><dd>{{ $isEnglish ? 'Confirm seating before your visit.' : 'Konfirmasikan tempat sebelum berkunjung.' }}</dd></div></dl>
        </div>
    </section>
    </div>
</div>
@endsection
