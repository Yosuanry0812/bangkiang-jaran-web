@extends('layouts.app')

@section('title', __('messages.dining_label') . ' — Bangkiang Jaran')
@section('nav-mode', 'light')

@section('content')
@php
    $isEnglish = app()->getLocale() === 'en';
    $restaurantPhotos = $galeriRestoran->values();
    $photoAt = function (int $index) use ($restaurantPhotos) {
        if ($restaurantPhotos->isEmpty()) return null;
        $photo = $restaurantPhotos[$index % $restaurantPhotos->count()];

        return [
            'url' => asset('storage/' . $photo->file),
            'caption' => $photo->keterangan ?: 'Restoran Bangkiang Jaran',
        ];
    };
    $heroPhoto = $photoAt(0);
    $collectionCards = $isEnglish ? [
        ['eyebrow' => 'After the trail', 'title' => 'A table for a slower pause', 'copy' => 'Rest, gather, and let the atmosphere of Bakbakan accompany your meal.', 'meta' => 'A calm place to pause'],
        ['eyebrow' => 'Local flavours', 'title' => 'Warm dishes, served simply', 'copy' => 'Ask the on-site team about today’s menu and the latest serving availability.', 'meta' => 'Daily information on site'],
        ['eyebrow' => 'Valley setting', 'title' => 'Dining close to nature', 'copy' => 'A relaxed dining space that completes a visit to Bangkiang Jaran Waterfall.', 'meta' => 'Part of your visit'],
    ] : [
        ['eyebrow' => 'Seusai perjalanan', 'title' => 'Meja untuk jeda yang lebih pelan', 'copy' => 'Beristirahat, berkumpul, dan biarkan suasana Desa Bakbakan menemani waktu makan Anda.', 'meta' => 'Tempat untuk beristirahat'],
        ['eyebrow' => 'Rasa lokal', 'title' => 'Sajian hangat, disajikan sederhana', 'copy' => 'Tanyakan menu hari ini dan informasi penyajian terbaru kepada tim pengelola di lokasi.', 'meta' => 'Informasi harian di lokasi'],
        ['eyebrow' => 'Suasana lembah', 'title' => 'Bersantap dekat dengan alam', 'copy' => 'Ruang bersantap yang santai untuk melengkapi kunjungan ke Air Terjun Bangkiang Jaran.', 'meta' => 'Bagian dari kunjungan'],
    ];
    $experienceCards = $isEnglish ? [
        ['icon' => 'leaf', 'number' => '01', 'label' => 'A natural pause', 'title' => 'A village atmosphere', 'copy' => 'A quiet transition from the waterfall trail to time shared around the table.'],
        ['icon' => 'shelter', 'number' => '02', 'label' => 'A grounded space', 'title' => 'Simple and welcoming', 'copy' => 'A dining area designed for resting, talking, and enjoying the surroundings.'],
        ['icon' => 'sound', 'number' => '03', 'label' => 'Valley rhythm', 'title' => 'Close to the waterfall', 'copy' => 'Keep the memory of the landscape close while taking a comfortable break.'],
        ['icon' => 'people', 'number' => '04', 'label' => 'Shared moments', 'title' => 'For friends and family', 'copy' => 'Ask the team about current seating and arrangements for your group.'],
    ] : [
        ['icon' => 'leaf', 'number' => '01', 'label' => 'Jeda alami', 'title' => 'Suasana desa', 'copy' => 'Peralihan yang tenang dari jalur air terjun menuju waktu bersama di meja makan.'],
        ['icon' => 'shelter', 'number' => '02', 'label' => 'Ruang membumi', 'title' => 'Sederhana dan hangat', 'copy' => 'Area bersantap untuk beristirahat, berbincang, dan menikmati lingkungan sekitar.'],
        ['icon' => 'sound', 'number' => '03', 'label' => 'Irama lembah', 'title' => 'Dekat air terjun', 'copy' => 'Tetap dekat dengan lanskap Bangkiang Jaran sambil mengambil jeda yang nyaman.'],
        ['icon' => 'people', 'number' => '04', 'label' => 'Momen bersama', 'title' => 'Untuk keluarga dan sahabat', 'copy' => 'Tanyakan ketersediaan tempat dan kebutuhan rombongan kepada tim pengelola.'],
    ];
@endphp

<div class="bj-resto-stitch" data-layout="bj-restaurant-hero">
    <svg class="bj-resto-stitch__sprite" aria-hidden="true">
        <symbol id="resto-icon-arrow" viewBox="0 0 24 24"><path d="M5 12h14M14 7l5 5-5 5"/></symbol>
        <symbol id="resto-icon-arrow-ne" viewBox="0 0 24 24"><path d="M7 17 17 7M8 7h9v9"/></symbol>
        <symbol id="resto-icon-calendar" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18"/></symbol>
        <symbol id="resto-icon-chat" viewBox="0 0 24 24"><path d="M20 11.5a8 8 0 0 1-11.7 7.1L4 20l1.4-4.1A8 8 0 1 1 20 11.5Z"/><path d="M9 9.2c.7 2 1.8 3.1 3.8 3.8"/></symbol>
        <symbol id="resto-icon-expand" viewBox="0 0 24 24"><path d="M8 3H3v5M16 3h5v5M8 21H3v-5M16 21h5v-5"/></symbol>
        <symbol id="resto-icon-image" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8.5" cy="9" r="1.5"/><path d="m5.5 17 4.2-4.2 3.1 3.1 2.1-2.1 3.6 3.2"/></symbol>
        <symbol id="resto-icon-leaf" viewBox="0 0 24 24"><path d="M20 4C12 4 6 8.5 6 15c0 3 2.1 5 5 5 6.5 0 9-7.2 9-16Z"/><path d="M4 21c2.5-5.5 6.5-9 12-12"/></symbol>
        <symbol id="resto-icon-shelter" viewBox="0 0 24 24"><path d="m3 10 9-7 9 7M5 9v12h14V9M8 21v-7h8v7"/></symbol>
        <symbol id="resto-icon-sound" viewBox="0 0 24 24"><path d="M4 10v4M8 7v10M12 4v16M16 7v10M20 10v4"/></symbol>
        <symbol id="resto-icon-people" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2"/><path d="M3 20c0-4 2.4-6 6-6s6 2 6 6M15 15c3.5 0 5 1.7 5 5"/></symbol>
    </svg>

    <main id="main-content">
        <section class="bj-resto-stitch__hero" aria-labelledby="resto-hero-title">
            <div class="bj-resto-stitch__wrap">
                <div class="bj-resto-stitch__intro">
                    <div class="bj-resto-stitch__intro-title">
                        <p class="bj-resto-stitch__eyebrow bj-reveal"><span aria-hidden="true"></span>{{ $isEnglish ? 'Authentic moments after the waterfall' : 'Jeda otentik selepas air terjun' }}</p>
                        <h1 id="resto-hero-title" class="bj-reveal" data-bj-split>{{ $isEnglish ? 'Flavours that grow from ' : 'Rasa yang tumbuh dari ' }}<em>{{ $isEnglish ? 'Gianyar’s valley.' : 'lembah Gianyar.' }}</em></h1>
                    </div>
                    <div class="bj-resto-stitch__intro-copy bj-reveal">
                        <p>{{ $isEnglish ? 'Sit close to the landscape and take an unhurried break. A warm dining experience in the atmosphere of Bakbakan village.' : 'Duduk dekat dengan lanskap dan nikmati jeda tanpa tergesa. Pengalaman bersantap yang hangat dalam suasana Desa Bakbakan.' }}</p>
                        <div class="bj-resto-stitch__actions">
                            @if(config('payment.contact_whatsapp'))
                            <a class="bj-resto-stitch__button bj-resto-stitch__button--gold" href="https://wa.me/{{ config('payment.contact_whatsapp') }}" target="_blank" rel="noopener noreferrer">{{ $isEnglish ? 'Ask for a table' : 'Tanyakan ketersediaan' }}<svg aria-hidden="true"><use href="#resto-icon-arrow"/></svg></a>
                            @endif
                            <a class="bj-resto-stitch__button bj-resto-stitch__button--soft" href="#koleksi-rasa">{{ $isEnglish ? 'Explore the experience' : 'Jelajahi pengalaman' }}</a>
                        </div>
                    </div>
                </div>

                <div class="bj-resto-stitch__mosaic">
                    <article class="bj-resto-stitch__feature-media bj-reveal">
                        @if($heroPhoto)
                        <button type="button" data-gallery-open data-gallery-src="{{ $heroPhoto['url'] }}" data-gallery-caption="{{ $heroPhoto['caption'] }}" aria-label="{{ $isEnglish ? 'Enlarge restaurant photo' : 'Perbesar foto restoran' }}: {{ $heroPhoto['caption'] }}"><img src="{{ $heroPhoto['url'] }}" alt="{{ $heroPhoto['caption'] }}" fetchpriority="high" width="1120" height="760"></button>
                        @else
                        <div class="bj-resto-stitch__placeholder"><svg aria-hidden="true"><use href="#resto-icon-image"/></svg><span>{{ __('messages.dining_gallery_empty') }}</span></div>
                        @endif
                        <div class="bj-resto-stitch__feature-badge">{{ $isEnglish ? 'Dining space · Bakbakan' : 'Ruang bersantap · Bakbakan' }}</div>
                        <div class="bj-resto-stitch__feature-caption"><div><span>{{ $isEnglish ? 'A pause by the valley' : 'Jeda di tepi lembah' }}</span><strong>{{ $isEnglish ? 'Warm company, calmer moments.' : 'Teman yang hangat, waktu yang lebih tenang.' }}</strong></div><svg aria-hidden="true"><use href="#resto-icon-arrow-ne"/></svg></div>
                    </article>

                    <div class="bj-resto-stitch__mini-grid">
                        @foreach(array_slice($collectionCards, 0, 2) as $index => $card)
                        @php($photo = $photoAt($index + 1))
                        <article class="bj-resto-stitch__mini-card bj-reveal">
                            <div class="bj-resto-stitch__mini-media">
                                @if($photo)
                                <button type="button" data-gallery-open data-gallery-src="{{ $photo['url'] }}" data-gallery-caption="{{ $photo['caption'] }}" aria-label="{{ $isEnglish ? 'Enlarge restaurant photo' : 'Perbesar foto restoran' }}: {{ $photo['caption'] }}"><img src="{{ $photo['url'] }}" alt="{{ $photo['caption'] }}" loading="lazy" width="640" height="420"></button>
                                @else
                                <div class="bj-resto-stitch__placeholder"><svg aria-hidden="true"><use href="#resto-icon-image"/></svg></div>
                                @endif
                            </div>
                            <div class="bj-resto-stitch__mini-copy"><div><span>{{ $card['eyebrow'] }}</span><small>{{ $card['meta'] }}</small></div><h2>{{ $card['title'] }}</h2><p>{{ $card['copy'] }}</p></div>
                        </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section id="koleksi-rasa" class="bj-resto-stitch__collection" aria-labelledby="resto-collection-title">
            <div class="bj-resto-stitch__wrap">
                <div class="bj-resto-stitch__section-heading">
                    <div><p class="bj-resto-stitch__eyebrow bj-reveal">{{ $isEnglish ? 'Flavour & atmosphere' : 'Rasa & suasana' }}</p><h2 id="resto-collection-title" class="bj-reveal">{{ $isEnglish ? 'A simple meal, ' : 'Jeda sederhana, ' }}<em>{{ $isEnglish ? 'remembered warmly.' : 'dikenang hangat.' }}</em></h2></div>
                    <p class="bj-reveal">{{ __('messages.dining_about_copy') }}</p>
                </div>
                <div class="bj-resto-stitch__cards">
                    @foreach($collectionCards as $index => $card)
                    @php($photo = $photoAt($index + 1))
                    <article class="bj-resto-stitch__card bj-reveal">
                        <div class="bj-resto-stitch__card-media">
                            @if($photo)
                            <button type="button" data-gallery-open data-gallery-src="{{ $photo['url'] }}" data-gallery-caption="{{ $photo['caption'] }}" aria-label="{{ $isEnglish ? 'Enlarge restaurant photo' : 'Perbesar foto restoran' }}: {{ $photo['caption'] }}"><img src="{{ $photo['url'] }}" alt="{{ $photo['caption'] }}" loading="lazy" width="720" height="900"></button>
                            @else
                            <div class="bj-resto-stitch__placeholder"><svg aria-hidden="true"><use href="#resto-icon-image"/></svg></div>
                            @endif
                            <span class="bj-resto-stitch__card-tag">{{ $card['eyebrow'] }}</span>
                            <div class="bj-resto-stitch__card-title"><small>{{ $card['meta'] }}</small><h3>{{ $card['title'] }}</h3></div>
                        </div>
                        <div class="bj-resto-stitch__card-copy"><p>{{ $card['copy'] }}</p><span>{{ $isEnglish ? 'Ask the team for current details' : 'Tanyakan informasi terbaru di lokasi' }}</span></div>
                    </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bj-resto-stitch__story" aria-labelledby="resto-story-title">
            <div class="bj-resto-stitch__wrap">
                <div class="bj-resto-stitch__quote bj-reveal">
                    <div><p class="bj-resto-stitch__eyebrow">{{ $isEnglish ? 'Our dining philosophy' : 'Filosofi ruang makan kami' }}</p><h2 id="resto-story-title">“{{ $isEnglish ? 'The best pause lets the journey linger a little longer.' : 'Jeda terbaik membuat perjalanan tinggal sedikit lebih lama.' }}”</h2><p>{{ $isEnglish ? 'The restaurant is a place to slow down after exploring the waterfall—to share stories, regain energy, and enjoy the village atmosphere.' : 'Restoran menjadi ruang untuk melambat setelah menjelajahi air terjun—berbagi cerita, memulihkan tenaga, dan menikmati suasana desa.' }}</p></div>
                    <dl><div><dt>{{ $isEnglish ? 'Location' : 'Lokasi' }}</dt><dd>{{ $isEnglish ? 'Bakbakan Village, Gianyar' : 'Desa Bakbakan, Gianyar' }}</dd></div><div><dt>{{ $isEnglish ? 'Setting' : 'Suasana' }}</dt><dd>{{ $isEnglish ? 'Near the waterfall area' : 'Dekat area air terjun' }}</dd></div><div><dt>{{ $isEnglish ? 'Latest information' : 'Informasi terbaru' }}</dt><dd>{{ $isEnglish ? 'Ask the on-site team' : 'Tanyakan kepada pengelola' }}</dd></div></dl>
                </div>
                <div class="bj-resto-stitch__experience-grid">
                    @foreach($experienceCards as $item)
                    <article class="bj-resto-stitch__experience bj-reveal"><span class="bj-resto-stitch__experience-icon"><svg aria-hidden="true"><use href="#resto-icon-{{ $item['icon'] }}"/></svg></span><small>{{ $item['number'] }} · {{ $item['label'] }}</small><h3>{{ $item['title'] }}</h3><p>{{ $item['copy'] }}</p></article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="reservasi-restoran" class="bj-resto-stitch__reserve" aria-labelledby="resto-reserve-title">
            <div class="bj-resto-stitch__wrap">
                <div class="bj-resto-stitch__reserve-panel bj-reveal">
                    <p class="bj-resto-stitch__eyebrow">{{ $isEnglish ? 'Plan a comfortable visit' : 'Rencanakan kunjungan dengan nyaman' }}</p>
                    <h2 id="resto-reserve-title">{{ $isEnglish ? 'Ready to enjoy a ' : 'Siap menikmati ' }}<em>{{ $isEnglish ? 'slower pause?' : 'jeda yang lebih pelan?' }}</em></h2>
                    <p>{{ $isEnglish ? 'Contact the team for today’s menu, seating availability, and group arrangements before arriving.' : 'Hubungi pengelola untuk menanyakan menu hari ini, ketersediaan tempat, dan kebutuhan rombongan sebelum datang.' }}</p>
                    <div class="bj-resto-stitch__facts"><div><small>{{ $isEnglish ? 'Destination' : 'Destinasi' }}</small><strong>Bangkiang Jaran</strong><span>{{ $isEnglish ? 'Waterfall & dining' : 'Air terjun & restoran' }}</span></div><div><small>{{ $isEnglish ? 'Valley location' : 'Lokasi lembah' }}</small><strong>{{ $isEnglish ? 'Bakbakan Village' : 'Desa Bakbakan' }}</strong><span>Gianyar, Bali</span></div><div><small>{{ $isEnglish ? 'Before you visit' : 'Sebelum berkunjung' }}</small><strong>{{ $isEnglish ? 'Confirm availability' : 'Konfirmasi ketersediaan' }}</strong><span>{{ $isEnglish ? 'Menu, seating, and groups' : 'Menu, tempat, dan rombongan' }}</span></div></div>
                    <div class="bj-resto-stitch__reserve-actions">
                        @if(config('payment.contact_whatsapp'))
                        <a class="bj-resto-stitch__button bj-resto-stitch__button--gold" href="https://wa.me/{{ config('payment.contact_whatsapp') }}" target="_blank" rel="noopener noreferrer"><svg aria-hidden="true"><use href="#resto-icon-chat"/></svg>{{ $isEnglish ? 'Contact the team' : 'Hubungi pengelola' }}</a>
                        @endif
                        <a class="bj-resto-stitch__button bj-resto-stitch__button--dark" href="{{ route('tiket.index') }}"><svg aria-hidden="true"><use href="#resto-icon-calendar"/></svg>{{ $isEnglish ? 'Plan your waterfall visit' : 'Rencanakan kunjungan wisata' }}</a>
                    </div>
                </div>
            </div>
            @if($restaurantPhotos->isNotEmpty())
            <div class="bj-resto-stitch__ribbon" aria-hidden="true"><div data-bj-marquee data-direction="left">@for($copy = 0; $copy < 2; $copy++)<div class="bj-home-ribbon__set">@for($index = 0; $index < max(4, min(8, $restaurantPhotos->count())); $index++)@php($photo = $photoAt($index))<img src="{{ $photo['url'] }}" alt="" loading="lazy" width="520" height="320">@endfor</div>@endfor</div></div>
            @endif
        </section>
    </main>
</div>

<dialog id="bj-gallery-dialog" class="bj-gallery-dialog" aria-label="{{ __('messages.dining_gallery_title') }}">
    <button type="button" data-gallery-close aria-label="{{ $isEnglish ? 'Close gallery' : 'Tutup galeri' }}">×</button><img src="" alt=""><p></p>
</dialog>
@endsection
