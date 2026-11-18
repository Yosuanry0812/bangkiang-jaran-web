@extends('layouts.app')

@section('title', __('ui.t_landing'))
@section('nav-mode', 'hero')

@section('content')
@php
    $isEnglish = app()->getLocale() === 'en';
    $hargaMulai = $tiket->where('kategori', 'perorangan')->min('harga') ?? $tiket->min('harga');
    $fallbackPhoto = asset('images/sejarah-bangkiang-waterfall.webp');
    $heroVideoPath = ltrim((string) config('tourism.hero_video'), '/');
    $hasHeroVideo = $heroVideoPath && ! str_contains($heroVideoPath, '..') && preg_match('/\.(mp4|webm)$/i', $heroVideoPath) && is_file(public_path($heroVideoPath));
    $heroPoster = $heroVideoPath === 'videos/hero-bangkiang.mp4' && is_file(public_path('videos/hero-bangkiang.webp')) ? asset('videos/hero-bangkiang.webp') : $fallbackPhoto;
    $heroPlaybackPath = $heroVideoPath === 'videos/hero-bangkiang.mp4' && is_file(public_path('videos/hero-bangkiang-web.mp4')) ? 'videos/hero-bangkiang-web.mp4' : $heroVideoPath;
    $heroHdPath = $hasHeroVideo && is_file(public_path('videos/hero-bangkiang-hd.mp4')) ? 'videos/hero-bangkiang-hd.mp4' : null;
    $usableGallery = $galeri->filter(fn ($item) => ! preg_match('/(?:^|\/)(?:galeri-\d+|placeholder\d*)\.(?:jpe?g|png|webp)$/i', (string) $item->file))->values();
    $mediaPhoto = function ($media) {
        $file = ltrim((string) $media->file, '/');
        $photo = ['url' => asset('storage/' . $file), 'caption' => $media->keterangan ?: 'Bangkiang Jaran'];
        if (str_starts_with($file, 'galeri/')) {
            $photo['srcset'] = collect([480, 800, 1280, 1920])->map(fn ($w) => url("img/{$w}/{$file}") . " {$w}w")->implode(', ');
            $photo['w1920'] = url("img/1920/{$file}");
        }
        return $photo;
    };
    $photoAt = function (int $index) use ($usableGallery, $fallbackPhoto) {
        if ($usableGallery->isEmpty()) return ['url' => $fallbackPhoto, 'caption' => 'Air Terjun Bangkiang Jaran'];
        $media = $usableGallery->get($index % $usableGallery->count());
        return $mediaPhoto($media);
    };
    $sectionPhotos = $usableGallery->map($mediaPhoto)->unique('url')->take(12)->values();
    if ($sectionPhotos->isEmpty()) {
        $sectionPhotos->push($photoAt(0));
        foreach ([12, 36, 62] as $second) {
            if (is_file(public_path("images/bangkiang-film-{$second}.webp"))) {
                $sectionPhotos->push(['url' => asset("images/bangkiang-film-{$second}.webp"), 'caption' => $isEnglish ? 'Bangkiang Jaran valley' : 'Lembah Bangkiang Jaran']);
            }
        }
    }
    $stories = $isEnglish ? [
        ['Waterfall', 'Water drops from the cliff into the pool below, surrounded by thick greenery.', 'First stop'],
        ['Hidden Canyon', 'A small canyon on the other side of Bangkiang Jaran. Ask the staff whether the path is open that day.', 'Ask the staff first'],
    ] : [
        ['Air terjun', 'Air jatuh dari tebing ke kolam di bawahnya, dikelilingi tanaman yang rapat.', 'Yang pertama dilihat'],
        ['Hidden Canyon', 'Ngarai kecil di sisi lain Bangkiang Jaran. Tanyakan kepada petugas apakah jalurnya bisa dilewati hari itu.', 'Tanya petugas dulu'],
    ];
    $facilities = $isEnglish ? [
        ['local_parking', 'Parking', 'Space for cars and motorbikes.'],
        ['confirmation_number', 'Digital tickets', 'Show your e-ticket from your phone at the gate.'],
        ['deck', 'Rest area', 'Somewhere to sit before or after the walk to the waterfall.'],
        ['restaurant', 'Dining area', 'Eat and drink while you rest.'],
        ['shower', 'Changing area', 'For changing after getting wet.'],
        ['support_agent', 'Staff on site', 'Ask the staff if you have any questions.'],
    ] : [
        ['local_parking', 'Parkir', 'Tersedia untuk mobil dan sepeda motor.'],
        ['confirmation_number', 'Tiket digital', 'Tunjukkan e-tiket dari ponsel di pintu masuk.'],
        ['deck', 'Area istirahat', 'Tempat duduk sebelum atau sesudah berjalan ke air terjun.'],
        ['restaurant', 'Ruang makan', 'Makan dan minum sambil beristirahat.'],
        ['shower', 'Area ganti', 'Untuk berganti pakaian setelah bermain air.'],
        ['support_agent', 'Petugas di lokasi', 'Tanyakan kepada petugas kalau ada yang kurang jelas.'],
    ];
    $visitSteps = $isEnglish ? [
        ['Pick a date', 'Choose a ticket type and the day you want to visit.'],
        ['Fill in visitor details', 'Then pay following the instructions on the payment page.'],
        ['Wait for confirmation', 'Your e-ticket becomes active once the manager has checked your payment.'],
        ['Get ready', 'Wear non-slip footwear and keep your e-ticket on your phone.'],
        ['Show your ticket', 'Present the e-ticket at the entrance.'],
    ] : [
        ['Pilih tanggal', 'Pilih jenis tiket dan hari kunjungan.'],
        ['Isi data pengunjung', 'Lalu bayar sesuai petunjuk di halaman pembayaran.'],
        ['Tunggu konfirmasi', 'E-tiket aktif setelah pengelola memeriksa pembayaran Anda.'],
        ['Siapkan diri', 'Pakai alas kaki yang tidak licin dan simpan e-tiket di ponsel.'],
        ['Tunjukkan tiket', 'Tunjukkan e-tiket di pintu masuk.'],
    ];
    $destValues = $destination->values();
    $hours = $destination->localized('operating_hours');
    $address = $destination->localized('address');
    $pick = fn (int $i) => $sectionPhotos->get($i % $sectionPhotos->count());
    $facts = array_values(array_filter([
        [$isEnglish ? 'Open' : 'Buka', $hours],
        [$isEnglish ? 'Address' : 'Alamat', $address],
        $hargaMulai ? [$isEnglish ? 'Tickets from' : 'Tiket mulai', 'Rp ' . number_format($hargaMulai, 0, ',', '.')] : null,
    ]));
    $journey = $isEnglish ? [
        ['Parking', 'Leave your vehicle in the parking area. From here you continue on foot.'],
        ['The footpath', 'A shaded footpath leads to the waterfall. Wear non-slip footwear.'],
        ['The waterfall', 'Water drops from the cliff into the pool below, surrounded by thick greenery.'],
        ['Hidden Canyon', 'A small canyon on the other side of Bangkiang Jaran. Ask the staff whether the path is open that day.'],
        ['Rest area', 'There is a rest area and a dining space where you can sit down before heading home.'],
    ] : [
        ['Parkir', 'Titipkan kendaraan di area parkir. Dari sini perjalanan dilanjutkan dengan berjalan kaki.'],
        ['Jalan setapak', 'Jalan setapak yang teduh menuju air terjun. Pakai alas kaki yang tidak licin.'],
        ['Air terjun', 'Air jatuh dari tebing ke kolam di bawahnya, dikelilingi tanaman yang rapat.'],
        ['Hidden Canyon', 'Ngarai kecil di sisi lain Bangkiang Jaran. Tanyakan kepada petugas apakah jalurnya bisa dilewati hari itu.'],
        ['Area istirahat', 'Tersedia area istirahat dan ruang makan untuk duduk sebelum pulang.'],
    ];
    $rules = $isEnglish
        ? ['Take your rubbish home', 'Stay on the footpath', 'Follow the staff\'s directions']
        : ['Bawa pulang sampah Anda', 'Tetap di jalan setapak', 'Ikuti arahan petugas'];
    $visitSteps = $isEnglish ? [
        ['Pick a date', 'Choose a ticket type and the day you want to visit.'],
        ['Fill in visitor details', 'Then pay following the instructions on the payment page.'],
        ['Wait for confirmation', 'Your e-ticket becomes active once the manager has checked your payment.'],
        ['Get ready', 'Wear non-slip footwear and keep your e-ticket on your phone.'],
        ['Show your ticket', 'Present the e-ticket at the entrance.'],
    ] : [
        ['Pilih tanggal', 'Pilih jenis tiket dan hari kunjungan.'],
        ['Isi data pengunjung', 'Lalu bayar sesuai petunjuk di halaman pembayaran.'],
        ['Tunggu konfirmasi', 'E-tiket aktif setelah pengelola memeriksa pembayaran Anda.'],
        ['Siapkan diri', 'Pakai alas kaki yang tidak licin dan simpan e-tiket di ponsel.'],
        ['Tunjukkan tiket', 'Tunjukkan e-tiket di pintu masuk.'],
    ];
    $origins = [
        ['my_location', '', $isEnglish ? 'My location' : 'Lokasi saya', $isEnglish ? 'Allow location when asked' : 'Izinkan lokasi saat diminta', true],
        ['location_city', 'Denpasar, Bali', 'Denpasar', $isEnglish ? 'From Denpasar City' : 'Dari Kota Denpasar', false],
        ['directions_car', 'Tabanan, Bali', 'Tabanan', $isEnglish ? 'From Tabanan' : 'Dari Tabanan', false],
        ['flight', 'I Gusti Ngurah Rai International Airport, Bali', $isEnglish ? 'Ngurah Rai Airport' : 'Bandara Ngurah Rai', $isEnglish ? 'From I Gusti Ngurah Rai Airport' : 'Dari Bandara I Gusti Ngurah Rai', false],
        ['spa', 'Ubud, Gianyar, Bali', 'Ubud', $isEnglish ? 'From Ubud, Gianyar' : 'Dari Ubud, Gianyar', false],
        ['surfing', 'Canggu, Bali', 'Canggu', $isEnglish ? 'From Canggu' : 'Dari Canggu', false],
    ];
    $galleryPhotos = $usableGallery->isNotEmpty() ? $usableGallery->map($mediaPhoto) : [$photoAt(0)];
@endphp
@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home-revamp.css') . '?v=' . filemtime(public_path('css/home-revamp.css')) }}">
@endpush
@push('scripts')
    <script defer src="{{ asset('js/home-revamp.js') . '?v=' . filemtime(public_path('js/home-revamp.js')) }}"></script>
@endpush

<div class="bj-home bj-home--journey">
    <section id="hero-section" class="bj-home-hero bj-home-hero--cinema {{ $hasHeroVideo ? 'bj-home-hero--film' : '' }}" data-bj-hero @if($hasHeroVideo) data-hero-intro @endif aria-labelledby="home-hero-title">
        @if($hasHeroVideo)
        <script>
            // Hide the copy before first paint; keep it available if the enhancement fails.
            (() => {
                const hero = document.currentScript.parentElement;
                if (matchMedia('(prefers-reduced-motion: reduce)').matches || navigator.connection?.saveData) return;
                hero.classList.add('is-intro-pending');
                setTimeout(() => {
                    if (!hero.hasAttribute('data-intro-managed')) hero.classList.remove('is-intro-pending');
                }, 12000);
            })();
        </script>
        @endif
        <div class="bj-home-hero__media swiper" data-bj-hero-swiper aria-hidden="true">
            <div class="swiper-wrapper">
            @if($hasHeroVideo || $usableGallery->isEmpty())
                <div class="bj-home-hero__slide swiper-slide is-active" data-bj-hero-slide data-caption="Air Terjun Bangkiang Jaran"><img src="{{ $hasHeroVideo ? $heroPoster : $fallbackPhoto }}" alt="" fetchpriority="high"></div>
            @else
                @foreach($usableGallery->take(6) as $index => $media)
                    <div class="bj-home-hero__slide swiper-slide {{ $index === 0 ? 'is-active' : '' }}" data-bj-hero-slide data-caption="{{ $media->keterangan ?: 'Air Terjun Bangkiang Jaran' }}">
                        <img src="{{ asset('storage/' . ltrim((string) $media->file, '/')) }}" alt="" {{ $index === 0 ? 'fetchpriority=high' : 'loading=lazy' }}>
                    </div>
                @endforeach
            @endif
            </div>
        </div>
        @if($hasHeroVideo)
        <div class="bj-hero-film-frame" aria-hidden="true">
            <video class="bj-hero-film" data-hero-film autoplay muted loop playsinline preload="none" poster="{{ $heroPoster }}" tabindex="-1" disablepictureinpicture disableremoteplayback><source data-video-src="{{ asset($heroPlaybackPath) }}" @if($heroHdPath) data-video-src-hd="{{ asset($heroHdPath) }}" @endif type="{{ str_ends_with(strtolower($heroPlaybackPath), '.webm') ? 'video/webm' : 'video/mp4' }}"></video>
        </div>
        @endif
        <div class="bj-home-hero__shade" aria-hidden="true"></div>
        <div class="bj-home-wrap bj-home-hero__content">
            <div class="bj-home-hero__copy">
                <p class="bj-home-kicker bj-reveal">Bangkiang Jaran · Bali</p>
                <div class="bj-hero-title-motion">
                <h1 id="home-hero-title" class="bj-home-title bj-reveal" @if($hasHeroVideo) data-hero-typewriter @else data-bj-split @endif>
                    @if($isEnglish)
                        Let the day<br>flow a little slower.
                    @else
                        Di sini, waktu<br>mengalir pelan.
                    @endif
                </h1>
                </div>
                <p class="bj-home-hero__lead bj-reveal">{{ $isEnglish ? 'Discover the waterfall and the quiet greenery of Bakbakan.' : 'Nikmati air terjun dan teduhnya alam Bakbakan.' }}</p>
                <div class="bj-home-hero__actions bj-reveal">
                    <a class="bj-home-button bj-home-button--gold" href="{{ route('tiket.index') }}">{{ $isEnglish ? 'Book tickets' : 'Pesan tiket' }}<span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
                    <a class="bj-home-button bj-home-button--ghost" href="#perjalanan">{{ $isEnglish ? 'Explore' : 'Jelajahi' }}</a>
                </div>
            </div>

            @if(! $hasHeroVideo && $usableGallery->count() > 1)
            <div class="bj-home-hero__bottom bj-reveal">
                    <div class="bj-home-hero__controls ml-auto" aria-label="{{ $isEnglish ? 'Hero photos' : 'Foto pembuka' }}">
                        <span class="bj-home-hero__caption" data-bj-hero-caption>{{ $usableGallery->first()->keterangan ?: 'Air Terjun Bangkiang Jaran' }}</span>
                        <span class="bj-home-hero__count"><strong data-bj-hero-current>01</strong> / {{ str_pad(min($usableGallery->count(), 6), 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="bj-home-hero__progress" aria-hidden="true"><i data-bj-hero-progress></i></span>
                        <button type="button" data-bj-hero-prev aria-label="{{ $isEnglish ? 'Previous photo' : 'Foto sebelumnya' }}"><span class="material-symbols-outlined" aria-hidden="true">arrow_back</span></button>
                        <button type="button" data-bj-hero-next aria-label="{{ $isEnglish ? 'Next photo' : 'Foto berikutnya' }}"><span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></button>
                    </div>
            </div>
            @endif
        </div>
        <a class="bj-home-hero__scroll" href="#perjalanan" aria-label="{{ $isEnglish ? 'Scroll to explore' : 'Gulir untuk menjelajah' }}"><span>{{ $isEnglish ? 'Scroll' : 'Gulir' }}</span><i aria-hidden="true"></i></a>
    </section>
</div>

<div class="bj-rv">
    {{-- 2. Jalur: foto-foto berjajar menyamping, digulir lewat scroll vertikal --}}
    <section id="perjalanan" class="bj-rv-trail" data-rv-trail aria-labelledby="rv-trail-title">
        <div class="bj-rv-trail__pin">
            <div class="bj-rv-trail__track" data-rv-track>
                <div class="bj-rv-trail__intro">
                    <h2 id="rv-trail-title" data-rv-weight="200" data-rv-weight-to="600">{{ $isEnglish ? 'From the parking area to the water' : 'Dari area parkir sampai ke air' }}</h2>
                    <p>{{ $isEnglish ? 'Five stops, from the parking area to the rest area.' : 'Lima titik, dari parkir sampai area istirahat.' }}</p>
                </div>
                @foreach($journey as $ji => [$jTitle, $jCopy])
                    @php($jp = $pick($ji))
                    <article class="bj-rv-panel bj-rv-panel--{{ $ji % 2 ? 'low' : 'high' }}" data-rv-panel>
                        <figure><img src="{{ $jp['url'] }}" @if(! empty($jp['srcset'])) srcset="{{ $jp['srcset'] }}" sizes="(min-width: 901px) 40vw, (min-width: 600px) 46vw, 100vw" @endif alt="{{ $jp['caption'] }}" loading="lazy" decoding="async"></figure>
                        <div class="bj-rv-panel__text">
                            <span>{{ str_pad($ji + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $jTitle }}</h3>
                            <p>{{ $jCopy }}</p>
                        </div>
                    </article>
                @endforeach
                <a class="bj-rv-trail__end" href="{{ route('tiket.index') }}">{{ $isEnglish ? 'Book your ticket' : 'Pesan tiketnya' }}<span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
            </div>
        </div>
    </section>

    {{-- 3. Dinding foto --}}
    <section id="galeri" class="bj-rv-wall" aria-labelledby="rv-wall-title">
        <h2 id="rv-wall-title" class="bj-rv-wall__title">{{ $isEnglish ? 'Photos from the site' : 'Foto-foto dari lokasi' }}<small>{{ $isEnglish ? 'Tap a photo to enlarge it.' : 'Ketuk foto untuk memperbesar.' }}</small></h2>
        @include('partials.photo-columns', ['columnPhotos' => $galleryPhotos])
    </section>

    {{-- 5. Tiket, berbentuk karcis --}}
    <section id="tiket-promo" class="bj-rv-tickets" data-rv-cta aria-labelledby="rv-ticket-title">
        <div class="bj-rv-tickets__bg" data-rv-parallax style="background-image:url('{{ $pick(1)['w1920'] ?? $pick(1)['url'] }}')" aria-hidden="true"></div>
        <div class="bj-rv-wrap bj-rv-tickets__layout">
            <div class="bj-rv-tickets__copy">
                <h2 id="rv-ticket-title" data-rv-weight="200" data-rv-weight-to="650">{{ $isEnglish ? 'Book first, show up later' : 'Pesan dulu, datang kemudian' }}</h2>
                <p>{{ $isEnglish ? 'Once the manager confirms your payment, your e-ticket is ready to use at the entrance.' : 'Setelah pembayaran dikonfirmasi pengelola, e-tiket langsung bisa dipakai di pintu masuk.' }}</p>
                <p class="bj-rv-tickets__meta">{{ $isEnglish ? 'Open' : 'Buka' }} {{ $hours }} · {{ $address }}</p>
                <a class="bj-rv-btn" href="{{ route('tiket.index') }}">{{ $isEnglish ? 'Book tickets' : 'Pesan tiket' }}<span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
            </div>
            @if($tiket->isNotEmpty())
            <ul class="bj-rv-stubs" aria-label="{{ $isEnglish ? 'Ticket prices' : 'Harga tiket' }}">
                @foreach($tiket->take(4) as $t)
                    <li class="bj-rv-stub"><span class="bj-rv-stub__name">{{ $t->nama_tiket }}</span><strong class="bj-rv-stub__price">Rp {{ number_format($t->harga, 0, ',', '.') }}</strong></li>
                @endforeach
            </ul>
            @endif
        </div>
    </section>

    {{-- 6. Cara pesan, seperti titik-titik di jalur --}}
    <section class="bj-rv-steps" aria-labelledby="rv-steps-title">
        <div class="bj-rv-wrap">
            <h2 id="rv-steps-title" class="bj-rv-steps__title" data-rv-weight="200" data-rv-weight-to="600">{{ $isEnglish ? 'Book in five steps' : 'Pesan tiket dalam lima langkah' }}</h2>
            <ol class="bj-rv-steps__path">
                @foreach($visitSteps as $si => [$sTitle, $sCopy])
                    <li class="bj-reveal"><span class="bj-rv-steps__num">{{ $si + 1 }}</span><div><h3>{{ $sTitle }}</h3><p>{{ $sCopy }}</p></div></li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- 7. Tanya jawab --}}
    <section class="bj-rv-faq" aria-labelledby="rv-faq-title">
        <div class="bj-rv-wrap">
            <h2 id="rv-faq-title" class="bj-rv-faq__title">{{ $isEnglish ? 'Questions' : 'Tanya jawab' }}</h2>
            <div class="bj-rv-faq__items">
                @for($i = 1; $i <= 12; $i++)
                    <details {{ $i === 1 ? 'open' : '' }}><summary><span class="bj-rv-faq__n">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</span>{{ __('messages.promo_faq_q' . $i) }}<span class="material-symbols-outlined" aria-hidden="true">add</span></summary><p>{{ trim(__('messages.promo_faq_a' . $i, ['hours' => $hours, 'from' => $hargaMulai ? ($isEnglish ? 'Prices start from Rp ' : 'Harga mulai dari Rp ') . number_format($hargaMulai, 0, ',', '.') . '.' : ''])) }}</p></details>
                @endfor
            </div>
        </div>
    </section>

    {{-- 8. Rute: peta penuh, panel pilihan di atasnya --}}
    <section id="lokasi" class="bj-rv-route" data-bj-route-planner data-destination="{{ config('tourism.destination') }}" data-place-id="{{ config('tourism.place_id') }}" aria-labelledby="rv-route-title">
        <div class="bj-rv-route__map"><iframe title="{{ $isEnglish ? 'Route to Bangkiang Jaran Waterfall' : 'Rute menuju Air Terjun Bangkiang Jaran' }}" data-bj-route-map loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="{{ $destValues['maps_embed'] }}"></iframe></div>
        <div class="bj-rv-route__panel">
            <h2 id="rv-route-title">{{ $isEnglish ? 'Where are you starting from?' : 'Berangkat dari mana?' }}</h2>
            <p>{{ $isEnglish ? 'Choose your starting point. Then open directions to Bangkiang Jaran in Google Maps.' : 'Pilih titik awal, lalu buka rute ke Bangkiang Jaran di Google Maps.' }}</p>
            <div class="bj-rv-route__origins" role="group" aria-label="{{ $isEnglish ? 'Choose starting point' : 'Pilih titik awal' }}">
                @foreach($origins as [$oIcon, $oQuery, $oLabel, $oSub, $oCurrent])
                    <button type="button" data-route-origin="{{ $oQuery }}" data-route-label="{{ $oLabel }}" @if($oCurrent) data-route-current @else data-route-subtitle="{{ $oSub }}" @endif aria-pressed="false"><span class="material-symbols-outlined" aria-hidden="true">{{ $oIcon }}</span><span><strong>{{ $oLabel }}</strong><small>{{ $oSub }}</small></span><i class="material-symbols-outlined" aria-hidden="true">radio_button_unchecked</i></button>
                @endforeach
            </div>
            <div class="bj-rv-route__foot"><p><span>{{ $isEnglish ? 'Starting point' : 'Titik awal' }}</span><strong data-route-selection>{{ $isEnglish ? 'Choose a starting point' : 'Pilih titik awal' }}</strong></p><a data-route-open href="{{ $destValues['maps_url'] }}" target="_blank" rel="noopener noreferrer">{{ $isEnglish ? 'Open in Google Maps' : 'Buka di Google Maps' }}<span class="material-symbols-outlined" aria-hidden="true">north_east</span></a></div>
            <p class="bj-rv-route__status" data-route-status role="status" aria-live="polite">{{ $isEnglish ? 'Your location is requested only when you choose My location.' : 'Lokasi perangkat hanya diminta ketika Anda memilih Lokasi saya.' }}</p>
        </div>
    </section>

    {{-- 9. Penutup --}}
    <section class="bj-rv-close" aria-labelledby="rv-close-title">
        <h2 id="rv-close-title" data-rv-weight="150" data-rv-weight-to="600">{{ $isEnglish ? 'See you at Bangkiang Jaran.' : 'Sampai jumpa di Bangkiang Jaran.' }}</h2>
        <div class="bj-rv-note">
            <p>{{ $isEnglish ? 'Please keep it clean' : 'Mohon jaga kebersihannya' }}</p>
            <ul>@foreach($rules as $rule)<li>{{ $rule }}</li>@endforeach</ul>
            @if($destination->localized('rules'))<p class="bj-rv-note__extra bj-preserve-lines">{{ $destination->localized('rules') }}</p>@endif
        </div>
        <a class="bj-rv-btn" href="{{ route('tiket.index') }}">{{ $isEnglish ? 'Book tickets' : 'Pesan tiket' }}<span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
    </section>

    <a class="bj-rv-fab" href="{{ route('tiket.index') }}" data-rv-fab><span class="material-symbols-outlined" aria-hidden="true">confirmation_number</span>{{ $isEnglish ? 'Book tickets' : 'Pesan tiket' }}@if($hargaMulai)<span class="bj-rv-fab__price">&nbsp;· {{ $isEnglish ? 'from' : 'mulai' }} Rp {{ number_format($hargaMulai, 0, ',', '.') }}</span>@endif</a>
</div>
@endsection
