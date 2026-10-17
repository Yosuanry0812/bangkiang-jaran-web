@extends('layouts.app')

@section('title', __('messages.dining_label') . ' — Bangkiang Jaran')
@section('nav-mode', 'hero')

@section('content')
@php
    $isEnglish = app()->getLocale() === 'en';
    $restaurantPhotos = $galeriRestoran->values();
    $photoAt = function (int $index) use ($restaurantPhotos) {
        if ($restaurantPhotos->isEmpty()) {
            return null;
        }

        $photo = $restaurantPhotos[$index % $restaurantPhotos->count()];

        return [
            'url' => asset('storage/' . $photo->file),
            'caption' => $photo->keterangan ?: 'Restoran Bangkiang Jaran',
        ];
    };
    $heroPhoto = $photoAt(0);
    $featuredPhoto = $photoAt(1);
    $guideCards = [
        [
            'title' => $isEnglish ? 'A calm table after the trail' : 'Meja yang tenang seusai perjalanan',
            'copy' => $isEnglish
                ? 'A comfortable place to pause, gather, and enjoy the atmosphere of Bakbakan village.'
                : 'Ruang nyaman untuk beristirahat, berkumpul, dan menikmati suasana Desa Bakbakan.',
            'meta' => $isEnglish ? 'A place to pause' : 'Tempat untuk beristirahat',
        ],
        [
            'title' => $isEnglish ? 'Local flavours, served simply' : 'Rasa lokal, disajikan dengan sederhana',
            'copy' => $isEnglish
                ? 'Ask the on-site team about the latest dishes, availability, and group arrangements.'
                : 'Tanyakan menu terbaru, ketersediaan, dan kebutuhan rombongan kepada tim di lokasi.',
            'meta' => $isEnglish ? 'Dining information' : 'Informasi bersantap',
        ],
    ];
@endphp

<div class="bj-restaurant-page">
    <svg class="bj-restaurant-icon-sprite" aria-hidden="true">
        <symbol id="bj-resto-icon-restaurant" viewBox="0 0 24 24"><path d="M7 3v7M4.5 3v4.5A2.5 2.5 0 0 0 7 10h0a2.5 2.5 0 0 0 2.5-2.5V3M7 10v11M15 3v18M15 3c3.1 1.5 4.5 4.1 4.5 7.5H15"/></symbol>
        <symbol id="bj-resto-icon-image" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8.5" cy="9" r="1.5"/><path d="m5.5 17 4.2-4.2 3.1 3.1 2.1-2.1 3.6 3.2M17.5 2v4M15.5 4h4"/></symbol>
        <symbol id="bj-resto-icon-expand" viewBox="0 0 24 24"><path d="M8 3H3v5M16 3h5v5M8 21H3v-5M16 21h5v-5"/></symbol>
        <symbol id="bj-resto-icon-arrow-ne" viewBox="0 0 24 24"><path d="M7 17 17 7M8 7h9v9"/></symbol>
        <symbol id="bj-resto-icon-arrow" viewBox="0 0 24 24"><path d="M5 12h14M14 7l5 5-5 5"/></symbol>
        <symbol id="bj-resto-icon-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
        <symbol id="bj-resto-icon-chat" viewBox="0 0 24 24"><path d="M20 11.5a8 8 0 0 1-11.7 7.1L4 20l1.4-4.1A8 8 0 1 1 20 11.5Z"/><path d="M9 9.2c.7 2 1.8 3.1 3.8 3.8"/></symbol>
    </svg>
    <header id="hero-section" class="bj-restaurant-hero">
        <div class="bj-restaurant-hero__media" @if($heroPhoto) data-bj-parallax @endif aria-hidden="true">
            @if($heroPhoto)
                <img src="{{ $heroPhoto['url'] }}" alt="" fetchpriority="high">
            @else
                <div class="bj-restaurant-placeholder bj-restaurant-placeholder--hero">
                    <svg class="bj-restaurant-icon"><use href="#bj-resto-icon-restaurant"/></svg>
                </div>
            @endif
        </div>
        <div class="bj-restaurant-hero__shade" aria-hidden="true"></div>

        <div class="bj-restaurant-wrap bj-restaurant-hero__content">
            <nav class="bj-restaurant-breadcrumb bj-reveal" aria-label="Breadcrumb">
                <a href="{{ route('landing') }}">{{ $isEnglish ? 'Home' : 'Beranda' }}</a>
                <span aria-hidden="true">/</span>
                <span>{{ $isEnglish ? 'Restaurant' : 'Restoran' }}</span>
            </nav>

            <div class="bj-restaurant-hero__copy">
                <p class="bj-restaurant-kicker bj-reveal">
                    <span aria-hidden="true"></span>{{ $isEnglish ? 'Dining at Bangkiang Jaran' : 'Bersantap di Bangkiang Jaran' }}
                </p>
                <h1 class="bj-reveal" data-bj-split>
                    {{ $isEnglish ? 'Flavours from the ' : 'Rasa yang tumbuh dari ' }}<em>{{ $isEnglish ? 'valley' : 'lembah' }}</em>
                </h1>
                <p class="bj-restaurant-hero__lead bj-reveal">
                    {{ $isEnglish
                        ? 'Take a quiet pause after the waterfall trail. Discover a simple dining space surrounded by the calm of Bakbakan village.'
                        : 'Ambil jeda yang tenang setelah menyusuri air terjun. Temukan ruang bersantap sederhana di tengah suasana Desa Bakbakan.' }}
                </p>
            </div>
        </div>
    </header>

    <main class="bj-restaurant-paper">
        <section id="restoran-galeri" class="bj-restaurant-feature" aria-labelledby="restaurant-feature-title">
            <div class="bj-restaurant-wrap bj-restaurant-feature__grid">
                <div class="bj-restaurant-feature__media bj-reveal" @if($featuredPhoto) data-bj-parallax @endif>
                    @if($featuredPhoto)
                        <button type="button" data-gallery-open data-gallery-src="{{ $featuredPhoto['url'] }}" data-gallery-caption="{{ $featuredPhoto['caption'] }}" aria-label="{{ $isEnglish ? 'Enlarge restaurant photo' : 'Perbesar foto restoran' }}: {{ $featuredPhoto['caption'] }}">
                            <img src="{{ $featuredPhoto['url'] }}" alt="{{ $featuredPhoto['caption'] }}" loading="lazy">
                            <span class="bj-restaurant-zoom" aria-hidden="true"><svg class="bj-restaurant-icon"><use href="#bj-resto-icon-expand"/></svg></span>
                        </button>
                    @else
                        <div class="bj-restaurant-placeholder">
                            <svg class="bj-restaurant-icon"><use href="#bj-resto-icon-image"/></svg>
                            <small>{{ $isEnglish ? 'Main restaurant photo' : 'Foto utama restoran' }}</small>
                        </div>
                    @endif
                </div>

                <div class="bj-restaurant-feature__copy bj-reveal">
                    <p class="bj-restaurant-kicker"><span aria-hidden="true"></span>{{ $isEnglish ? 'Start here' : 'Mulai dari sini' }}</p>
                    <h2 id="restaurant-feature-title">{{ $isEnglish ? 'A pleasant pause by the waterfall' : 'Jeda yang menyenangkan di dekat air terjun' }}</h2>
                    <p>{{ __('messages.dining_about_copy') }}</p>
                    <a class="bj-restaurant-text-link" href="{{ route('landing') }}#lokasi">
                        {{ $isEnglish ? 'See the location' : 'Lihat lokasi' }}
                        <svg class="bj-restaurant-icon" aria-hidden="true"><use href="#bj-resto-icon-arrow"/></svg>
                    </a>
                </div>
            </div>
        </section>

        <section class="bj-restaurant-guides" aria-labelledby="restaurant-guides-title">
            <div class="bj-restaurant-wrap">
                <div class="bj-restaurant-guides__heading bj-reveal">
                    <p class="bj-restaurant-kicker"><span aria-hidden="true"></span>{{ $isEnglish ? 'Discover more' : 'Kenali lebih dekat' }}</p>
                    <h2 id="restaurant-guides-title" class="sr-only">{{ $isEnglish ? 'Restaurant highlights' : 'Pilihan restoran' }}</h2>
                </div>

                <div class="bj-restaurant-guides__grid">
                    @foreach($guideCards as $index => $card)
                    @php($guidePhoto = $photoAt($index + 2))
                    <article class="bj-restaurant-card bj-reveal">
                        <div class="bj-restaurant-card__media" @if($guidePhoto) data-bj-parallax @endif>
                            @if($guidePhoto)
                                <button type="button" data-gallery-open data-gallery-src="{{ $guidePhoto['url'] }}" data-gallery-caption="{{ $guidePhoto['caption'] }}" aria-label="{{ $isEnglish ? 'Enlarge restaurant photo' : 'Perbesar foto restoran' }}: {{ $guidePhoto['caption'] }}">
                                    <img src="{{ $guidePhoto['url'] }}" alt="{{ $guidePhoto['caption'] }}" loading="lazy">
                                    <span class="bj-restaurant-card__arrow" aria-hidden="true"><svg class="bj-restaurant-icon"><use href="#bj-resto-icon-arrow-ne"/></svg></span>
                                </button>
                            @else
                                <div class="bj-restaurant-placeholder">
                                    <svg class="bj-restaurant-icon"><use href="#bj-resto-icon-image"/></svg>
                                    <small>{{ $isEnglish ? 'Restaurant photo' : 'Foto restoran' }} {{ $index + 2 }}</small>
                                </div>
                            @endif
                        </div>
                        <div class="bj-restaurant-card__body">
                            <p class="bj-restaurant-card__meta"><svg class="bj-restaurant-icon" aria-hidden="true"><use href="#bj-resto-icon-clock"/></svg>{{ $card['meta'] }}</p>
                            <h3>{{ $card['title'] }}</h3>
                            <p>{{ $card['copy'] }}</p>
                        </div>
                    </article>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <section class="bj-restaurant-cta" aria-labelledby="restaurant-cta-title">
        <div class="bj-restaurant-wrap bj-restaurant-cta__content bj-reveal">
            <p class="bj-restaurant-kicker"><span aria-hidden="true"></span>{{ $isEnglish ? 'Plan your visit' : 'Rencanakan kunjungan' }}</p>
            <h2 id="restaurant-cta-title">{{ $isEnglish ? 'Ready to enjoy the ' : 'Siap menikmati ' }}<em>{{ $isEnglish ? 'pause?' : 'jedanya?' }}</em></h2>
            <p>{{ $isEnglish
                ? 'Ask our team about today’s menu, seating availability, and arrangements for your group.'
                : 'Tanyakan menu hari ini, ketersediaan tempat, dan pengaturan untuk rombongan Anda kepada tim kami.' }}</p>
            <div class="bj-restaurant-cta__actions">
                @if(config('payment.contact_whatsapp'))
                <a class="bj-home-button bj-home-button--whatsapp" href="https://wa.me/{{ config('payment.contact_whatsapp') }}" target="_blank" rel="noopener noreferrer">
                    <svg class="bj-restaurant-icon" aria-hidden="true"><use href="#bj-resto-icon-chat"/></svg>{{ $isEnglish ? 'Chat on WhatsApp' : 'Chat via WhatsApp' }}
                </a>
                @endif
                <a class="bj-home-button bj-home-button--ghost" href="{{ route('tiket.index') }}">
                    {{ $isEnglish ? 'Check ticket prices' : 'Lihat harga tiket' }}<svg class="bj-restaurant-icon" aria-hidden="true"><use href="#bj-resto-icon-arrow"/></svg>
                </a>
            </div>
        </div>

        <div class="bj-restaurant-ribbons" aria-hidden="true">
            @foreach(['left', 'right'] as $rowIndex => $direction)
            <div class="bj-home-ribbon bj-restaurant-ribbon" data-bj-marquee data-direction="{{ $direction }}">
                @for($copy = 0; $copy < 2; $copy++)
                <div class="bj-home-ribbon__set">
                    @for($i = 0; $i < 6; $i++)
                    @php($ribbonPhoto = $photoAt($i + ($rowIndex * 6)))
                    @if($ribbonPhoto)
                        <img src="{{ $ribbonPhoto['url'] }}" alt="" loading="lazy">
                    @else
                        <div class="bj-restaurant-ribbon__placeholder">
                            <svg class="bj-restaurant-icon"><use href="#bj-resto-icon-restaurant"/></svg>
                        </div>
                    @endif
                    @endfor
                </div>
                @endfor
            </div>
            @endforeach
        </div>
    </section>
</div>

<dialog id="bj-gallery-dialog" class="bj-gallery-dialog" aria-label="{{ __('messages.dining_gallery_title') }}">
    <button type="button" data-gallery-close aria-label="{{ $isEnglish ? 'Close gallery' : 'Tutup galeri' }}">×</button>
    <img src="" alt="">
    <p></p>
</dialog>
@endsection
