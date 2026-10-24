@extends('layouts.app')

@section('title', 'Bangkiang Jaran | Air Terjun di Desa Bakbakan, Gianyar')
@section('nav-mode', 'hero')

@section('content')
@php
    $isEnglish = app()->getLocale() === 'en';
    $hargaMulai = $tiket->where('kategori', 'perorangan')->min('harga') ?? $tiket->min('harga');
    $fallbackPhoto = asset('images/sejarah-bangkiang-waterfall.webp');
    $heroVideoPath = ltrim((string) config('tourism.hero_video'), '/');
    $hasHeroVideo = $heroVideoPath && ! str_contains($heroVideoPath, '..') && preg_match('/\.(mp4|webm)$/i', $heroVideoPath) && is_file(public_path($heroVideoPath));
    $usableGallery = $galeri->filter(fn ($item) => ! preg_match('/(?:^|\/)(?:galeri-\d+|placeholder\d*)\.(?:jpe?g|png|webp)$/i', (string) $item->file))->values();
    $photoAt = function (int $index) use ($usableGallery, $fallbackPhoto) {
        if ($usableGallery->isEmpty()) return ['url' => $fallbackPhoto, 'caption' => 'Air Terjun Bangkiang Jaran'];
        $media = $usableGallery->get($index % $usableGallery->count());
        return ['url' => asset('storage/' . ltrim((string) $media->file, '/')), 'caption' => $media->keterangan ?: 'Air Terjun Bangkiang Jaran'];
    };
    $stories = $isEnglish ? [
        ['Waterfall', 'Water, rock, and a canopy of green. Take a closer look at the landscape that gives Bangkiang Jaran its character.', 'The sound of the valley'],
        ['Hidden Canyon', 'Another side of Bangkiang Jaran to ask about before you visit. Confirm access and current conditions with the team on site.', 'Before you set off'],
    ] : [
        ['Air terjun', 'Air yang jatuh, bebatuan, dan pepohonan yang memenuhi pandangan. Kenali lanskap yang menjadi wajah Bangkiang Jaran.', 'Dengarkan lembahnya'],
        ['Hidden Canyon', 'Sisi lain Bangkiang Jaran untuk Anda kenali sebelum berkunjung. Tanyakan akses dan kondisi terkini kepada pengelola di lokasi.', 'Sebelum melangkah'],
    ];
    $facilities = $isEnglish ? [
        ['local_parking', 'Parking area', 'A practical starting point for cars and motorbikes.'],
        ['confirmation_number', 'Digital tickets', 'Keep your verified ticket ready on your phone.'],
        ['deck', 'Rest area', 'Pause before or after walking to the waterfall.'],
        ['restaurant', 'Dining space', 'A simple place to rest and enjoy the village atmosphere.'],
        ['shower', 'Changing area', 'Freshen up after spending time near the water.'],
        ['support_agent', 'Local team', 'Ask the team on site whenever you need assistance.'],
    ] : [
        ['local_parking', 'Area parkir', 'Titik awal yang praktis untuk mobil dan sepeda motor.'],
        ['confirmation_number', 'Tiket digital', 'Simpan tiket terverifikasi agar mudah dibuka dari ponsel.'],
        ['deck', 'Area istirahat', 'Beristirahat sebelum atau setelah berjalan menuju air terjun.'],
        ['restaurant', 'Ruang bersantap', 'Tempat sederhana untuk beristirahat sambil menikmati suasana desa.'],
        ['shower', 'Area ganti', 'Bersihkan diri setelah menikmati waktu di dekat air.'],
        ['support_agent', 'Tim lokal', 'Hubungi tim di lokasi saat Anda membutuhkan bantuan.'],
    ];
    $visitSteps = $isEnglish ? [
        ['Choose a visit date', 'Check the available ticket options and choose the day that suits you.'],
        ['Complete your booking', 'Enter visitor details and follow the payment instructions shown.'],
        ['Wait for verification', 'Your e-ticket becomes active after the manager verifies the payment.'],
        ['Prepare for the trail', 'Wear suitable footwear and keep your active ticket easy to open.'],
        ['Enjoy Bangkiang Jaran', 'Show your ticket at the entrance and follow directions from the local team.'],
    ] : [
        ['Pilih tanggal kunjungan', 'Lihat tiket yang tersedia dan tentukan hari yang paling sesuai.'],
        ['Selesaikan pemesanan', 'Isi data pengunjung lalu ikuti petunjuk pembayaran yang ditampilkan.'],
        ['Tunggu verifikasi', 'E-tiket akan aktif setelah pembayaran dikonfirmasi oleh pengelola.'],
        ['Siapkan perjalanan', 'Gunakan alas kaki yang sesuai dan simpan tiket aktif agar mudah dibuka.'],
        ['Nikmati Bangkiang Jaran', 'Tunjukkan tiket di pintu masuk lalu ikuti arahan tim setempat.'],
    ];
@endphp

<div class="bj-home bj-home--journey">
    <section id="hero-section" class="bj-home-hero bj-home-hero--cinema" data-bj-hero aria-labelledby="home-hero-title">
        <div class="bj-home-hero__media swiper" data-bj-hero-swiper aria-hidden="true">
            <div class="swiper-wrapper">
            @if($usableGallery->isEmpty())
                <div class="bj-home-hero__slide swiper-slide is-active" data-bj-hero-slide data-caption="Air Terjun Bangkiang Jaran"><img src="{{ $fallbackPhoto }}" alt="" fetchpriority="high"></div>
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
        <video class="bj-hero-film" data-hero-film muted loop playsinline preload="none" poster="{{ $fallbackPhoto }}" aria-hidden="true"><source data-video-src="{{ asset($heroVideoPath) }}" type="{{ str_ends_with(strtolower($heroVideoPath), '.webm') ? 'video/webm' : 'video/mp4' }}"></video>
        @endif
        <div class="bj-home-hero__shade" aria-hidden="true"></div>
        <button class="bj-title-motion-toggle" type="button" data-hero-title-toggle hidden aria-pressed="false" aria-label="{{ $isEnglish ? 'Pause heading motion' : 'Jeda gerak judul' }}" data-play-label="{{ $isEnglish ? 'Play heading motion' : 'Putar gerak judul' }}" data-pause-label="{{ $isEnglish ? 'Pause heading motion' : 'Jeda gerak judul' }}"><span class="material-symbols-outlined" aria-hidden="true">pause</span></button>
        <div class="bj-home-wrap bj-home-hero__content">
            <div class="bj-home-hero__copy">
                <p class="bj-home-kicker bj-reveal">{{ $isEnglish ? 'Bakbakan Village · Gianyar, Bali' : 'Desa Bakbakan · Gianyar, Bali' }}</p>
                <div class="bj-hero-title-motion" data-hero-title-motion>
                <h1 id="home-hero-title" class="bj-home-title bj-reveal" data-bj-split>
                    @if($isEnglish)
                        A little closer<br>to the waterfall.
                    @else
                        Sejenak di luar.<br>Dekat dengan alam.
                    @endif
                </h1>
                </div>
                <p class="bj-editorial-destination">Bangkiang Jaran Waterfall</p>
                <p class="bj-home-hero__lead bj-reveal">{{ $isEnglish ? 'A waterfall, a green valley, and a reason to linger in Bakbakan, Gianyar.' : 'Air terjun, lembah yang rimbun, dan alasan untuk tinggal sedikit lebih lama di Bakbakan, Gianyar.' }}</p>
                <div class="bj-home-hero__actions bj-reveal">
                    <a class="bj-home-button bj-home-button--gold" href="{{ route('tiket.index') }}">{{ $isEnglish ? 'Choose your ticket' : 'Pilih tiket kunjungan' }}<span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
                    <a class="bj-home-button bj-home-button--ghost" href="#kisah">{{ $isEnglish ? 'Meet Bangkiang Jaran' : 'Kenali Bangkiang Jaran' }}</a>
                </div>
            </div>

            <div class="bj-home-hero__bottom bj-reveal">
                @if($hasHeroVideo)
                    <button type="button" class="bj-film-toggle" data-film-toggle aria-label="{{ $isEnglish ? 'Play background video' : 'Putar video latar' }}" data-play-label="{{ $isEnglish ? 'Play background video' : 'Putar video latar' }}" data-pause-label="{{ $isEnglish ? 'Pause background video' : 'Jeda video latar' }}" aria-pressed="false"><span class="material-symbols-outlined" aria-hidden="true">play_arrow</span><span data-film-label>{{ $isEnglish ? 'Play video' : 'Putar video' }}</span></button>
                @elseif($usableGallery->count() > 1)
                    <div class="bj-home-hero__controls ml-auto" aria-label="{{ $isEnglish ? 'Hero photos' : 'Foto pembuka' }}">
                        <span class="bj-home-hero__caption" data-bj-hero-caption>{{ $usableGallery->first()->keterangan ?: 'Air Terjun Bangkiang Jaran' }}</span>
                        <span class="bj-home-hero__count"><strong data-bj-hero-current>01</strong> / {{ str_pad(min($usableGallery->count(), 6), 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="bj-home-hero__progress" aria-hidden="true"><i data-bj-hero-progress></i></span>
                        <button type="button" data-bj-hero-prev aria-label="{{ $isEnglish ? 'Previous photo' : 'Foto sebelumnya' }}"><span class="material-symbols-outlined" aria-hidden="true">arrow_back</span></button>
                        <button type="button" data-bj-hero-next aria-label="{{ $isEnglish ? 'Next photo' : 'Foto berikutnya' }}"><span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></button>
                    </div>
                @endif
            </div>
        </div>
        <a class="bj-home-hero__scroll" href="#kisah" aria-label="{{ $isEnglish ? 'Scroll to explore' : 'Gulir untuk menjelajah' }}"><span>{{ $isEnglish ? 'Scroll' : 'Gulir' }}</span><i aria-hidden="true"></i></a>
    </section>

    <div class="bj-home-paper">

        <div class="bj-home-overlap" data-overlap-stack>
        <section id="kisah" class="bj-home-intro" data-overlap-panel aria-labelledby="home-intro-title">
            <div class="bj-home-wrap bj-home-intro__grid">
                <div class="bj-home-intro__copy bj-reveal">
                    <p class="bj-home-kicker">{{ $isEnglish ? 'Welcome to Bangkiang Jaran' : 'Selamat datang di Bangkiang Jaran' }}</p>
                    <h2 id="home-intro-title">{{ $isEnglish ? 'Let the water ' : 'Biarkan suara air ' }}<em>{{ $isEnglish ? 'set the pace.' : 'mengubah ritme hari.' }}</em></h2>
                    <p>{{ $isEnglish ? 'In Bakbakan, Gianyar, Bangkiang Jaran brings falling water and a green landscape into one view. Pause by the valley, look closer, and make room for a day outdoors.' : 'Di Desa Bakbakan, Gianyar, Bangkiang Jaran mempertemukan aliran air dan lanskap hijau dalam satu pandangan. Berhenti sejenak di tepian lembah, perhatikan sekeliling, dan sisakan waktu untuk menikmati alam.' }}</p>
                    <a class="bj-home-text-link" href="#perjalanan">{{ $isEnglish ? 'Follow the journey' : 'Ikuti perjalanannya' }}<span class="material-symbols-outlined" aria-hidden="true">south_east</span></a>
                </div>
                @include('partials.photo-collage', ['collagePhotos' => $usableGallery->isNotEmpty() ? $usableGallery->map(fn ($media) => ['url' => asset('storage/' . ltrim((string) $media->file, '/')), 'caption' => $media->keterangan ?: 'Bangkiang Jaran']) : [$photoAt(0)]])
            </div>
        </section>

        <section id="perjalanan" class="bj-home-stories" data-overlap-panel aria-labelledby="stories-title">
            <div class="bj-home-wrap bj-home-section-head bj-home-section-head--wide bj-reveal">
                <div><p class="bj-home-kicker">{{ $isEnglish ? 'A slower journey' : 'Perjalanan yang lebih pelan' }}</p><h2 id="stories-title">{{ $isEnglish ? 'Follow the water ' : 'Ikuti aliran air ' }}<em>{{ $isEnglish ? 'into the green' : 'menuju hijau' }}</em></h2></div>
                <p>{{ $isEnglish ? 'Move through Bangkiang Jaran one moment at a time, from the village path to the cool edge of the water.' : 'Nikmati Bangkiang Jaran satu momen demi satu momen, dari jalur desa hingga tepian air yang sejuk.' }}</p>
            </div>
            <div class="bj-home-story-list">
                @foreach($stories as $index => [$title, $copy, $eyebrow])
                    @php($photo = $photoAt($index + 4))
                    <article class="bj-home-story {{ $index % 2 ? 'bj-home-story--reverse' : '' }}">
                        @if($index === 0 || $usableGallery->count() > 1)
                        <figure class="bj-home-story__media bj-reveal" data-bj-parallax><img src="{{ $photo['url'] }}" alt="{{ $photo['caption'] }}" loading="lazy"><span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span></figure>
                        @endif
                        <div class="bj-home-story__copy bj-reveal"><p class="bj-home-kicker">{{ $eyebrow }}</p><h3>{{ $title }}</h3><p>{{ $copy }}</p><a class="bj-home-text-link" href="#lokasi">{{ $isEnglish ? 'Prepare your visit' : 'Siapkan perjalanan Anda' }}</a></div>
                    </article>
                @endforeach
            </div>
        </section>

        <section id="galeri" class="bj-editorial-gallery" data-overlap-panel aria-labelledby="home-gallery-title">
            <div class="bj-home-wrap">
                <div class="bj-home-section-head"><div><p class="bj-home-kicker">Bangkiang Jaran, Bali</p><h2 id="home-gallery-title">{{ $isEnglish ? 'Find your favourite view.' : 'Temukan sudut favoritmu.' }}</h2></div><p>{{ $isEnglish ? 'A look at the water, the greenery, and the moments in between.' : 'Lihat airnya, hijaunya, dan momen di antaranya.' }}</p></div>
                @include('partials.photo-columns', ['columnPhotos' => $usableGallery->isNotEmpty() ? $usableGallery->map(fn ($media) => ['url' => asset('storage/' . ltrim((string) $media->file, '/')), 'caption' => $media->keterangan ?: 'Bangkiang Jaran']) : [$photoAt(0)]])
            </div>
        </section>

        <section class="bj-home-facilities" data-overlap-panel aria-labelledby="facilities-title">
            <div class="bj-home-wrap">
                <div class="bj-home-section-head bj-reveal"><div><p class="bj-home-kicker">{{ $isEnglish ? 'At the destination' : 'Di lokasi wisata' }}</p><h2 id="facilities-title">{{ $isEnglish ? 'A comfortable day outdoors.' : 'Nyaman menikmati alam.' }}</h2></div></div>
                <div class="bj-home-facilities__layout">
                    <div class="bj-home-facilities__grid">@foreach($facilities as [$icon, $title, $copy])<article class="bj-home-facility bj-reveal"><span class="material-symbols-outlined" aria-hidden="true">{{ $icon }}</span><h3>{{ $title }}</h3><p>{{ $copy }}</p></article>@endforeach</div>
                </div>
            </div>
        </section>

        </div>
        <section class="bj-home-pledge" aria-labelledby="pledge-title"><div class="bj-home-wrap bj-pledge-layout"><span class="material-symbols-outlined" aria-hidden="true">eco</span><div><h2 id="pledge-title">{{ $isEnglish ? 'Keep this corner of Bali green.' : 'Jaga sudut Bali ini tetap hijau.' }}</h2><p>{{ $isEnglish ? 'Take your rubbish with you, respect the trail, and follow the local team’s directions.' : 'Bawa kembali sampahmu, jaga jalur setapak, dan ikuti arahan tim di lokasi.' }}</p></div><a href="#lokasi">{{ $isEnglish ? 'Before you arrive' : 'Sebelum berkunjung' }}<span class="material-symbols-outlined" aria-hidden="true">south_east</span></a></div></section>

        <section class="bj-home-steps" aria-labelledby="steps-title">
            <div class="bj-home-wrap bj-home-steps__layout">
                <div class="bj-home-steps__intro bj-reveal"><p class="bj-home-kicker">{{ $isEnglish ? 'Plan with ease' : 'Rencanakan dengan mudah' }}</p><h2 id="steps-title">{{ $isEnglish ? 'Five steps from plan to ' : 'Lima langkah dari rencana menuju ' }}<em>{{ $isEnglish ? 'waterfall' : 'air terjun' }}</em></h2><p>{{ $isEnglish ? 'Everything is clearer when the practical details are prepared before you arrive.' : 'Perjalanan terasa lebih tenang ketika kebutuhan praktis sudah disiapkan sebelum tiba.' }}</p><a class="bj-home-button bj-home-button--forest" href="{{ route('tiket.index') }}">{{ $isEnglish ? 'Plan your visit' : 'Rencanakan kunjungan' }}</a></div>
                <ol class="bj-home-steps__list">@foreach($visitSteps as $index => [$title, $copy])<li class="bj-reveal"><span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span><div><h3>{{ $title }}</h3><p>{{ $copy }}</p></div></li>@endforeach</ol>
            </div>
        </section>

        <section class="bj-home-faq" aria-labelledby="home-faq-title">
            <div class="bj-home-wrap bj-home-faq__layout">
                <div class="bj-home-faq__intro bj-reveal"><p class="bj-home-kicker">{{ __('messages.promo_faq_label') }}</p><h2 id="home-faq-title">{{ $isEnglish ? 'Good to ' : 'Baik untuk ' }}<em>{{ $isEnglish ? 'know' : 'diketahui' }}</em></h2><p>{{ $isEnglish ? 'Practical answers for a calmer visit to Bangkiang Jaran.' : 'Jawaban praktis untuk kunjungan yang lebih tenang ke Bangkiang Jaran.' }}</p></div>
                <div class="bj-home-faq__items">@for($i = 1; $i <= 4; $i++)<details class="bj-reveal" {{ $i === 1 ? 'open' : '' }}><summary>{{ __('messages.promo_faq_q' . $i) }}<span class="material-symbols-outlined" aria-hidden="true">add</span></summary><p>{{ __('messages.promo_faq_a' . $i) }}</p></details>@endfor</div>
            </div>
        </section>

        <section id="lokasi" class="bj-home-route" data-bj-route-planner data-destination="{{ config('tourism.destination') }}" data-place-id="{{ config('tourism.place_id') }}" aria-labelledby="route-title">
            <div class="bj-home-wrap">
                <header class="bj-home-route__head bj-reveal">
                    <div><p class="bj-home-kicker">{{ $isEnglish ? 'Route planner' : 'Perencana rute' }}</p><h2 id="route-title">{{ $isEnglish ? 'Where are you ' : 'Anda berangkat ' }}<em>{{ $isEnglish ? 'starting from?' : 'dari mana?' }}</em></h2></div>
                    <p>{{ $isEnglish ? 'Choose your starting point. Open directions to Bangkiang Jaran in Google Maps.' : 'Pilih titik keberangkatan. Buka rute menuju Bangkiang Jaran di Google Maps.' }}</p>
                </header>
                <div class="bj-home-route__layout">
                    <div class="bj-home-route__origins" role="group" aria-label="{{ $isEnglish ? 'Choose starting point' : 'Pilih titik awal' }}">
                        <button type="button" data-route-origin="" data-route-label="{{ $isEnglish ? 'My location' : 'Lokasi saya' }}" data-route-current aria-pressed="false"><span class="material-symbols-outlined" aria-hidden="true">my_location</span><span><strong>{{ $isEnglish ? 'My location' : 'Lokasi saya' }}</strong><small>{{ $isEnglish ? 'Allow location when asked' : 'Izinkan lokasi saat diminta' }}</small></span><i class="material-symbols-outlined" aria-hidden="true">radio_button_unchecked</i></button>
                        <button type="button" data-route-origin="Denpasar, Bali" data-route-label="Denpasar" data-route-subtitle="{{ $isEnglish ? 'From Denpasar City' : 'Dari Kota Denpasar' }}" aria-pressed="false"><span class="material-symbols-outlined" aria-hidden="true">location_city</span><span><strong>Denpasar</strong><small>{{ $isEnglish ? 'From Denpasar City' : 'Dari Kota Denpasar' }}</small></span><i class="material-symbols-outlined" aria-hidden="true">radio_button_unchecked</i></button>
                        <button type="button" data-route-origin="Tabanan, Bali" data-route-label="Tabanan" data-route-subtitle="{{ $isEnglish ? 'From Tabanan' : 'Dari Tabanan' }}" aria-pressed="false"><span class="material-symbols-outlined" aria-hidden="true">directions_car</span><span><strong>Tabanan</strong><small>{{ $isEnglish ? 'From Tabanan' : 'Dari Tabanan' }}</small></span><i class="material-symbols-outlined" aria-hidden="true">radio_button_unchecked</i></button>
                        <button type="button" data-route-origin="I Gusti Ngurah Rai International Airport, Bali" data-route-label="Ngurah Rai Airport" data-route-subtitle="{{ $isEnglish ? 'From I Gusti Ngurah Rai Airport' : 'Dari Bandara I Gusti Ngurah Rai' }}" aria-pressed="false"><span class="material-symbols-outlined" aria-hidden="true">flight</span><span><strong>{{ $isEnglish ? 'Ngurah Rai Airport' : 'Bandara Ngurah Rai' }}</strong><small>{{ $isEnglish ? 'From I Gusti Ngurah Rai Airport' : 'Dari Bandara I Gusti Ngurah Rai' }}</small></span><i class="material-symbols-outlined" aria-hidden="true">radio_button_unchecked</i></button>
                        <button type="button" data-route-origin="Ubud, Gianyar, Bali" data-route-label="Ubud" data-route-subtitle="{{ $isEnglish ? 'From Ubud, Gianyar' : 'Dari Ubud, Gianyar' }}" aria-pressed="false"><span class="material-symbols-outlined" aria-hidden="true">spa</span><span><strong>Ubud</strong><small>{{ $isEnglish ? 'From Ubud, Gianyar' : 'Dari Ubud, Gianyar' }}</small></span><i class="material-symbols-outlined" aria-hidden="true">radio_button_unchecked</i></button>
                        <button type="button" data-route-origin="Canggu, Bali" data-route-label="Canggu" data-route-subtitle="{{ $isEnglish ? 'From Canggu' : 'Dari Canggu' }}" aria-pressed="false"><span class="material-symbols-outlined" aria-hidden="true">surfing</span><span><strong>Canggu</strong><small>{{ $isEnglish ? 'From Canggu' : 'Dari Canggu' }}</small></span><i class="material-symbols-outlined" aria-hidden="true">radio_button_unchecked</i></button>
                    </div>
                    <div class="bj-home-route__map bj-reveal">
                        <div class="bj-home-route__map-frame"><iframe title="{{ $isEnglish ? 'Route to Bangkiang Jaran Waterfall' : 'Rute menuju Air Terjun Bangkiang Jaran' }}" data-bj-route-map loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="{{ config('tourism.maps_embed') }}"></iframe><div class="bj-home-route__destination"><span class="material-symbols-outlined" aria-hidden="true">location_on</span><span><small>{{ $isEnglish ? 'Destination' : 'Tujuan' }}</small><strong>Bangkiang Jaran Waterfall</strong></span></div></div>
                        <div class="bj-home-route__map-foot"><p><span>{{ $isEnglish ? 'Starting point' : 'Titik awal' }}</span><strong data-route-selection>{{ $isEnglish ? 'Choose a starting point' : 'Pilih titik awal' }}</strong></p><a data-route-open href="{{ config('tourism.maps_url') }}" target="_blank" rel="noopener noreferrer">{{ $isEnglish ? 'Open in Google Maps' : 'Buka di Google Maps' }}<span class="material-symbols-outlined" aria-hidden="true">north_east</span></a></div>
                    </div>
                </div>
                <p class="bj-route-status" data-route-status role="status" aria-live="polite">{{ $isEnglish ? 'Your location is requested only when you choose My location.' : 'Lokasi perangkat hanya diminta ketika Anda memilih Lokasi saya.' }}</p>
            </div>
        </section>
    </div>

</div>
@endsection
