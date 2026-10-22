@extends('layouts.app')

@section('title', 'Bangkiang Jaran — Air Terjun di Desa Bakbakan, Gianyar')
@section('nav-mode', 'hero')

@section('content')
@php
    $isEnglish = app()->getLocale() === 'en';
    $hargaMulai = $tiket->where('kategori', 'perorangan')->min('harga') ?? $tiket->min('harga');
    $fallbackPhoto = asset('images/sejarah-bangkiang-waterfall.webp');
    $usableGallery = $galeri->filter(fn ($item) => ! preg_match('/(?:^|\/)(?:galeri-\d+|placeholder\d*)\.(?:jpe?g|png|webp)$/i', (string) $item->file))->values();
    $photoAt = function (int $index) use ($usableGallery, $fallbackPhoto) {
        if ($usableGallery->isEmpty()) return ['url' => $fallbackPhoto, 'caption' => 'Air Terjun Bangkiang Jaran'];
        $media = $usableGallery->get($index % $usableGallery->count());
        return ['url' => asset('storage/' . ltrim((string) $media->file, '/')), 'caption' => $media->keterangan ?: 'Air Terjun Bangkiang Jaran'];
    };
    $stories = $isEnglish ? [
        ['The first sound of water', 'Begin with a short walk from the village and let the cooler air lead you towards the falls.', 'An easy beginning'],
        ['A quiet natural pool', 'Pause at the water, enjoy the green surroundings, and follow every safety direction from the local team.', 'Time to slow down'],
        ['A valley cared for locally', 'Your visit supports the people who welcome visitors and care for Bangkiang Jaran every day.', 'Community managed'],
    ] : [
        ['Suara air yang pertama', 'Mulai dari perjalanan singkat melewati desa, lalu biarkan udara yang lebih sejuk menuntun Anda menuju air terjun.', 'Awal yang ringan'],
        ['Kolam alami yang tenang', 'Berhenti sejenak di tepi air, nikmati suasana hijau, dan selalu ikuti arahan keselamatan dari tim setempat.', 'Waktu untuk melambat'],
        ['Lembah yang dijaga bersama', 'Setiap kunjungan ikut mendukung masyarakat yang menyambut wisatawan dan merawat Bangkiang Jaran setiap hari.', 'Dikelola masyarakat'],
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
    <section id="hero-section" class="bj-home-hero" data-bj-hero aria-labelledby="home-hero-title">
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
        <div class="bj-home-hero__shade" aria-hidden="true"></div>
        <div class="bj-home-wrap bj-home-hero__content">
            <div class="bj-home-hero__copy">
                <p class="bj-home-kicker bj-reveal">{{ $isEnglish ? 'Bakbakan Village · Gianyar, Bali' : 'Desa Bakbakan · Gianyar, Bali' }}</p>
                <h1 id="home-hero-title" class="bj-home-title bj-reveal" data-bj-split>
                    @if($isEnglish)
                        Where the forest <em>finds its light.</em>
                    @else
                        Ketika rimba <em>menemukan cahayanya.</em>
                    @endif
                </h1>
                <p class="bj-home-hero__lead bj-reveal">{{ $isEnglish ? 'Follow the sound of water into a greener, quieter side of Gianyar—welcomed and cared for by the people of Bakbakan.' : 'Ikuti suara air menuju sisi Gianyar yang lebih hijau dan tenang—disambut serta dirawat oleh masyarakat Bakbakan.' }}</p>
                <div class="bj-home-hero__actions bj-reveal">
                    <a class="bj-home-button bj-home-button--gold" href="{{ route('tiket.index') }}">{{ $isEnglish ? 'See ticket prices' : 'Lihat harga tiket' }}<span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
                    <a class="bj-home-button bj-home-button--ghost" href="#kisah">{{ $isEnglish ? 'Explore the waterfall' : 'Jelajahi air terjun' }}</a>
                </div>
            </div>

            <div class="bj-home-hero__bottom bj-reveal">
                @if($usableGallery->count() > 1)
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

    <main class="bj-home-paper">
        <div class="bj-home-facts-band">
            <div class="bj-home-wrap bj-home-facts bj-reveal">
                <div><strong>5–10</strong><span>{{ $isEnglish ? 'minutes of relaxed walking' : 'menit berjalan santai' }}</span></div>
                <div><strong>07.00</strong><span>{{ $isEnglish ? 'opening time every day' : 'mulai buka setiap hari' }}</span></div>
                <div><strong data-bj-count="{{ $tiket->count() }}">{{ $tiket->count() }}</strong><span>{{ $isEnglish ? 'active ticket options' : 'pilihan tiket aktif' }}</span></div>
                <div><strong>100%</strong><span>{{ $isEnglish ? 'digital booking flow' : 'alur pemesanan digital' }}</span></div>
            </div>
        </div>

        <section id="kisah" class="bj-home-intro" aria-labelledby="home-intro-title">
            <div class="bj-home-wrap bj-home-intro__grid">
                <div class="bj-home-intro__copy bj-reveal">
                    <p class="bj-home-kicker">{{ $isEnglish ? 'Welcome to Bangkiang Jaran' : 'Selamat datang di Bangkiang Jaran' }}</p>
                    <h2 id="home-intro-title">{{ $isEnglish ? 'More than a waterfall, ' : 'Bukan sekadar air terjun, ' }}<em>{{ $isEnglish ? 'a haven in the green.' : 'ruang hijau untuk kembali tenang.' }}</em></h2>
                    <p>{{ $isEnglish ? 'Bangkiang Jaran brings together falling water, a natural pool, and the everyday warmth of a Balinese village. The walk is short, but the atmosphere invites you to stay a little longer.' : 'Bangkiang Jaran mempertemukan aliran air, kolam alami, dan kehangatan keseharian desa Bali. Perjalanannya singkat, tetapi suasananya mengajak Anda tinggal sedikit lebih lama.' }}</p>
                    <a class="bj-home-text-link" href="#perjalanan">{{ $isEnglish ? 'Follow the journey' : 'Ikuti perjalanannya' }}<span class="material-symbols-outlined" aria-hidden="true">south_east</span></a>
                </div>
                <div class="bj-home-intro__photos">
                    @for($i = 0; $i < 3; $i++)
                        @php($photo = $photoAt($i + 1))
                        <figure class="bj-home-intro__photo bj-home-intro__photo--{{ $i + 1 }} bj-reveal" data-bj-parallax><img src="{{ $photo['url'] }}" alt="{{ $photo['caption'] }}" loading="lazy"></figure>
                    @endfor
                </div>
            </div>
        </section>

        <section class="bj-home-ticket bj-stitch-tickets" aria-labelledby="home-ticket-title">
            <div class="bj-home-wrap">
                <div class="bj-home-section-head bj-reveal"><div><p class="bj-home-kicker">{{ $isEnglish ? 'Plan your visit' : 'Rencanakan kunjungan' }}</p><h2 id="home-ticket-title">{{ $isEnglish ? 'Choose your experience' : 'Pilih pengalaman kunjungan Anda' }}</h2></div><p>{{ $isEnglish ? 'Explore the available tickets and plan a quiet day in Bakbakan.' : 'Temukan pilihan tiket yang tersedia dan rencanakan hari yang tenang di Bakbakan.' }}</p></div>
                <div class="bj-stitch-ticket-grid">
                    @forelse($tiket->take(3) as $index => $ticket)
                        <article class="bj-stitch-ticket bj-reveal">
                            <p class="bj-home-kicker">{{ $isEnglish ? 'Official ticket' : 'Tiket resmi' }} - {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</p>
                            <h3>{{ $ticket->nama_tiket }}</h3>
                            <p class="bj-stitch-ticket__description">{{ $isEnglish ? 'Discover Bangkiang Jaran and enjoy the natural surroundings of Bakbakan.' : 'Nikmati suasana alami Bangkiang Jaran dan kesejukan Desa Bakbakan.' }}</p>
                            <p class="bj-stitch-ticket__price">Rp {{ number_format($ticket->harga, 0, ',', '.') }}</p>
                            <p class="bj-stitch-ticket__note">{{ $isEnglish ? 'See ticket details and availability before booking.' : 'Lihat rincian tiket dan ketersediaan sebelum memesan.' }}</p>
                            <a class="bj-home-button {{ $index === 1 ? 'bj-home-button--gold' : 'bj-home-button--forest' }}" href="{{ route('tiket.index') }}">{{ $isEnglish ? 'View ticket' : 'Lihat tiket' }} <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
                        </article>
                    @empty
                        <p>{{ $isEnglish ? 'Ticket information will be available soon.' : 'Informasi tiket akan segera tersedia.' }}</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section id="perjalanan" class="bj-home-stories" aria-labelledby="stories-title">
            <div class="bj-home-wrap bj-home-section-head bj-home-section-head--wide bj-reveal">
                <div><p class="bj-home-kicker">{{ $isEnglish ? 'A slower journey' : 'Perjalanan yang lebih pelan' }}</p><h2 id="stories-title">{{ $isEnglish ? 'Follow the water ' : 'Ikuti aliran air ' }}<em>{{ $isEnglish ? 'into the green' : 'menuju hijau' }}</em></h2></div>
                <p>{{ $isEnglish ? 'Move through Bangkiang Jaran one moment at a time, from the village path to the cool edge of the water.' : 'Nikmati Bangkiang Jaran satu momen demi satu momen, dari jalur desa hingga tepian air yang sejuk.' }}</p>
            </div>
            <div class="bj-home-story-list">
                @foreach($stories as $index => [$title, $copy, $eyebrow])
                    @php($photo = $photoAt($index + 4))
                    <article class="bj-home-story {{ $index % 2 ? 'bj-home-story--reverse' : '' }}">
                        <figure class="bj-home-story__media bj-reveal" data-bj-parallax><img src="{{ $photo['url'] }}" alt="{{ $photo['caption'] }}" loading="lazy"><span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span></figure>
                        <div class="bj-home-story__copy bj-reveal"><p class="bj-home-kicker">{{ $eyebrow }}</p><h3>{{ $title }}</h3><p>{{ $copy }}</p></div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="bj-home-facilities" aria-labelledby="facilities-title">
            <div class="bj-home-wrap">
                <div class="bj-home-section-head bj-reveal"><div><p class="bj-home-kicker">{{ $isEnglish ? 'Facilities' : 'Fasilitas' }}</p><h2 id="facilities-title">{{ $isEnglish ? 'Everything you need, ' : 'Semua yang dibutuhkan, ' }}<em>{{ $isEnglish ? 'close at hand' : 'tetap dekat' }}</em></h2></div></div>
                <div class="bj-home-facilities__layout">
                    @php($facilityPhoto = $photoAt(10))
                    <figure class="bj-home-facilities__photo bj-reveal" data-bj-parallax><img src="{{ $facilityPhoto['url'] }}" alt="{{ $facilityPhoto['caption'] }}" loading="lazy"><figcaption>{{ $isEnglish ? 'Your visit begins in Bakbakan village' : 'Perjalanan Anda dimulai dari Desa Bakbakan' }}</figcaption></figure>
                    <div class="bj-home-facilities__grid">@foreach($facilities as [$icon, $title, $copy])<article class="bj-home-facility bj-reveal"><span class="material-symbols-outlined" aria-hidden="true">{{ $icon }}</span><h3>{{ $title }}</h3><p>{{ $copy }}</p></article>@endforeach</div>
                </div>
            </div>
        </section>

        <section class="bj-home-pledge" aria-label="{{ $isEnglish ? 'Responsible visit reminder' : 'Pengingat kunjungan bertanggung jawab' }}">@php($pledgePhoto = $photoAt(5))<img src="{{ $pledgePhoto['url'] }}" alt="" loading="lazy"><div class="bj-home-pledge__shade" aria-hidden="true"></div><blockquote class="bj-reveal">{{ $isEnglish ? 'Take nothing but photos. Leave nothing but footprints.' : 'Bawa pulang hanya foto. Tinggalkan hanya jejak langkah.' }}</blockquote></section>

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

        <section id="lokasi" class="bj-home-route" data-bj-route-planner aria-labelledby="route-title">
            <div class="bj-home-wrap">
                <header class="bj-home-route__head bj-reveal">
                    <div><p class="bj-home-kicker">{{ $isEnglish ? 'Route planner' : 'Perencana rute' }}</p><h2 id="route-title">{{ $isEnglish ? 'Where are you ' : 'Anda berangkat ' }}<em>{{ $isEnglish ? 'starting from?' : 'dari mana?' }}</em></h2></div>
                    <p>{{ $isEnglish ? 'Choose a starting point and open the clearest route to Bangkiang Jaran Waterfall in Google Maps.' : 'Pilih titik awal perjalanan dan buka rute paling jelas menuju Air Terjun Bangkiang Jaran di Google Maps.' }}</p>
                </header>
                <div class="bj-home-route__layout">
                    <div class="bj-home-route__origins" role="group" aria-label="{{ $isEnglish ? 'Choose starting point' : 'Pilih titik awal' }}">
                        <button type="button" class="is-active" data-route-origin="My location" data-route-label="{{ $isEnglish ? 'My location' : 'Lokasi saya' }}" data-route-subtitle="{{ $isEnglish ? 'Use your current location' : 'Gunakan lokasi Anda saat ini' }}" data-route-current aria-pressed="true"><span class="material-symbols-outlined" aria-hidden="true">my_location</span><span><strong>{{ $isEnglish ? 'My location' : 'Lokasi saya' }}</strong><small>{{ $isEnglish ? 'Use your current location' : 'Gunakan lokasi Anda saat ini' }}</small></span><i class="material-symbols-outlined" aria-hidden="true">check</i></button>
                        <button type="button" data-route-origin="Denpasar, Bali" data-route-label="Denpasar" data-route-subtitle="{{ $isEnglish ? 'From Denpasar City' : 'Dari Kota Denpasar' }}" aria-pressed="false"><span class="material-symbols-outlined" aria-hidden="true">location_city</span><span><strong>Denpasar</strong><small>{{ $isEnglish ? 'From Denpasar City' : 'Dari Kota Denpasar' }}</small></span><i class="material-symbols-outlined" aria-hidden="true">radio_button_unchecked</i></button>
                        <button type="button" data-route-origin="Tabanan, Bali" data-route-label="Tabanan" data-route-subtitle="{{ $isEnglish ? 'From Tabanan' : 'Dari Tabanan' }}" aria-pressed="false"><span class="material-symbols-outlined" aria-hidden="true">directions_car</span><span><strong>Tabanan</strong><small>{{ $isEnglish ? 'From Tabanan' : 'Dari Tabanan' }}</small></span><i class="material-symbols-outlined" aria-hidden="true">radio_button_unchecked</i></button>
                        <button type="button" data-route-origin="I Gusti Ngurah Rai International Airport, Bali" data-route-label="Ngurah Rai Airport" data-route-subtitle="{{ $isEnglish ? 'From I Gusti Ngurah Rai Airport' : 'Dari Bandara I Gusti Ngurah Rai' }}" aria-pressed="false"><span class="material-symbols-outlined" aria-hidden="true">flight</span><span><strong>{{ $isEnglish ? 'Ngurah Rai Airport' : 'Bandara Ngurah Rai' }}</strong><small>{{ $isEnglish ? 'From I Gusti Ngurah Rai Airport' : 'Dari Bandara I Gusti Ngurah Rai' }}</small></span><i class="material-symbols-outlined" aria-hidden="true">radio_button_unchecked</i></button>
                        <button type="button" data-route-origin="Ubud, Gianyar, Bali" data-route-label="Ubud" data-route-subtitle="{{ $isEnglish ? 'From Ubud, Gianyar' : 'Dari Ubud, Gianyar' }}" aria-pressed="false"><span class="material-symbols-outlined" aria-hidden="true">spa</span><span><strong>Ubud</strong><small>{{ $isEnglish ? 'From Ubud, Gianyar' : 'Dari Ubud, Gianyar' }}</small></span><i class="material-symbols-outlined" aria-hidden="true">radio_button_unchecked</i></button>
                        <button type="button" data-route-origin="Canggu, Bali" data-route-label="Canggu" data-route-subtitle="{{ $isEnglish ? 'From Canggu' : 'Dari Canggu' }}" aria-pressed="false"><span class="material-symbols-outlined" aria-hidden="true">surfing</span><span><strong>Canggu</strong><small>{{ $isEnglish ? 'From Canggu' : 'Dari Canggu' }}</small></span><i class="material-symbols-outlined" aria-hidden="true">radio_button_unchecked</i></button>
                    </div>
                    <div class="bj-home-route__map bj-reveal">
                        <div class="bj-home-route__map-frame"><iframe title="{{ $isEnglish ? 'Route to Bangkiang Jaran Waterfall' : 'Rute menuju Air Terjun Bangkiang Jaran' }}" data-bj-route-map loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps?q=-8.511979,115.328486&amp;output=embed&amp;z=14"></iframe><div class="bj-home-route__destination"><span class="material-symbols-outlined" aria-hidden="true">location_on</span><span><small>{{ $isEnglish ? 'Destination' : 'Tujuan' }}</small><strong>Bangkiang Jaran Waterfall</strong></span></div></div>
                        <div class="bj-home-route__map-foot"><p><span>{{ $isEnglish ? 'Route from' : 'Rute dari' }}</span><strong data-route-selection>{{ $isEnglish ? 'My location' : 'Lokasi saya' }}</strong></p><a data-route-open href="https://www.google.com/maps/dir/?api=1&amp;destination=-8.511979%2C115.328486" target="_blank" rel="noopener noreferrer">{{ $isEnglish ? 'Open in Google Maps' : 'Buka di Google Maps' }}<span class="material-symbols-outlined" aria-hidden="true">north_east</span></a></div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <section class="bj-home-cta" aria-labelledby="home-cta-title">
        <div class="bj-home-wrap bj-home-cta__content bj-reveal"><p class="bj-home-kicker">{{ $isEnglish ? 'Plan your visit' : 'Rencanakan kunjungan' }}</p><h2 id="home-cta-title">{{ $isEnglish ? 'Ready to hear the ' : 'Siap mendengar ' }}<em>{{ $isEnglish ? 'roar?' : 'derunya?' }}</em></h2><p>{{ $isEnglish ? 'Choose your date, prepare your journey, and let the valley welcome you.' : 'Pilih tanggal kunjungan, siapkan perjalanan, dan biarkan lembah menyambut Anda.' }}</p><div class="bj-home-cta__actions">@if(config('payment.contact_whatsapp'))<a class="bj-home-button bj-home-button--whatsapp" href="https://wa.me/{{ config('payment.contact_whatsapp') }}" target="_blank" rel="noopener noreferrer"><span class="material-symbols-outlined" aria-hidden="true">chat</span>{{ $isEnglish ? 'Chat on WhatsApp' : 'Chat via WhatsApp' }}</a>@endif<a class="bj-home-button bj-home-button--ghost" href="{{ route('tiket.index') }}">{{ $isEnglish ? 'Check ticket prices' : 'Lihat harga tiket' }}<span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a></div></div>
        <div class="bj-home-ribbons" aria-hidden="true">@foreach(['left', 'right'] as $row => $direction)<div class="bj-home-ribbon" data-bj-marquee data-direction="{{ $direction }}">@for($copy = 0; $copy < 2; $copy++)<div class="bj-home-ribbon__set">@for($i = 0; $i < 6; $i++)@php($photo = $photoAt($i + 2 + ($row * 5)))<img src="{{ $photo['url'] }}" alt="" loading="lazy">@endfor</div>@endfor</div>@endforeach</div>
    </section>
</div>
@endsection
