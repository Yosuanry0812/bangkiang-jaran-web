@extends('layouts.app')

@section('title', __('messages.tickets_page_label') . ' — Bangkiang Jaran')
@section('nav-mode', 'hero')

@section('content')
@php
    $isEnglish = app()->getLocale() === 'en';
    $photos = ($galeriWisata ?? collect())->values();
    $fallbackPhoto = [
        'url' => asset('images/sejarah-bangkiang-waterfall.webp'),
        'caption' => 'Air Terjun Bangkiang Jaran',
    ];
    $photoAt = function (int $index) use ($photos, $fallbackPhoto) {
        if ($photos->isEmpty()) {
            return $fallbackPhoto;
        }

        $photo = $photos[$index % $photos->count()];
        return [
            'url' => asset('storage/' . $photo->file),
            'caption' => $photo->keterangan ?: 'Air Terjun Bangkiang Jaran',
        ];
    };
    $ticketProfile = function ($ticket) use ($isEnglish) {
        $rawName = mb_strtolower(($ticket->getRawOriginal('nama_tiket') ?? '') . ' ' . ($ticket->getRawOriginal('nama_tiket_en') ?? ''));

        if (str_contains($rawName, 'anak') || str_contains($rawName, 'child')) {
            return [
                'key' => 'child',
                'name' => $isEnglish ? 'Child Admission' : 'Tiket Anak',
                'eyebrow' => $isEnglish ? 'Ages 3–12' : 'Usia 3–12 tahun',
                'summary' => $isEnglish ? 'A lighter admission price for children visiting with an adult companion.' : 'Harga masuk khusus anak yang berkunjung bersama pendamping dewasa.',
                'description' => $isEnglish
                    ? 'Designed for young visitors aged 3–12. Children must remain with a responsible adult throughout the visit, especially around paths, stairs, and wet areas.'
                    : 'Ditujukan untuk pengunjung usia 3–12 tahun. Anak wajib bersama pendamping dewasa selama kunjungan, terutama di jalur, tangga, dan area yang basah.',
                'recommended' => $isEnglish ? 'Families with children' : 'Keluarga dengan anak',
                'features' => $isEnglish
                    ? ['One-time attraction entry', 'Valid on the selected visit date', 'Digital ticket after confirmation', 'Adult supervision required']
                    : ['Satu kali akses masuk kawasan', 'Berlaku pada tanggal yang dipilih', 'E-ticket setelah pembayaran dikonfirmasi', 'Wajib didampingi orang dewasa'],
            ];
        }

        if (str_contains($rawName, 'wna') || str_contains($rawName, 'asing') || str_contains($rawName, 'mancanegara') || str_contains($rawName, 'foreign') || str_contains($rawName, 'international')) {
            return [
                'key' => 'international',
                'name' => $isEnglish ? 'International Visitor' : 'Tiket Wisatawan Mancanegara',
                'eyebrow' => $isEnglish ? 'International guest' : 'Pengunjung internasional',
                'summary' => $isEnglish ? 'Admission for international visitors exploring Bangkiang Jaran Waterfall.' : 'Tiket masuk bagi wisatawan mancanegara yang mengunjungi Air Terjun Bangkiang Jaran.',
                'description' => $isEnglish
                    ? 'A straightforward digital admission for international guests. Choose your date, complete payment, and keep the confirmed e-ticket ready on your phone at the entrance.'
                    : 'Tiket digital yang praktis untuk wisatawan mancanegara. Pilih tanggal, selesaikan pembayaran, lalu siapkan e-ticket yang telah dikonfirmasi di ponsel saat tiba.',
                'recommended' => $isEnglish ? 'International travellers' : 'Wisatawan mancanegara',
                'features' => $isEnglish
                    ? ['One-time attraction entry', 'Valid on the selected visit date', 'Digital ticket after confirmation', 'Show the ticket from your phone']
                    : ['Satu kali akses masuk kawasan', 'Berlaku pada tanggal yang dipilih', 'E-ticket setelah pembayaran dikonfirmasi', 'Cukup tunjukkan tiket dari ponsel'],
            ];
        }

        if (str_contains($rawName, 'dewasa') || str_contains($rawName, 'adult')) {
            return [
                'key' => 'adult',
                'name' => $isEnglish ? 'Adult Admission' : 'Tiket Dewasa',
                'eyebrow' => $isEnglish ? 'Ages 13+' : 'Usia 13 tahun ke atas',
                'summary' => $isEnglish ? 'Standard admission for adult visitors and guests aged 13 or older.' : 'Tiket masuk standar untuk pengunjung dewasa dan tamu berusia 13 tahun ke atas.',
                'description' => $isEnglish
                    ? 'The standard choice for visitors aged 13 and above. Your ticket gives one-time entry on the selected date and is delivered digitally after payment is confirmed.'
                    : 'Pilihan standar untuk pengunjung berusia 13 tahun ke atas. Tiket berlaku untuk satu kali masuk pada tanggal pilihan dan dikirim secara digital setelah pembayaran dikonfirmasi.',
                'recommended' => $isEnglish ? 'Adults and teenagers' : 'Dewasa dan remaja',
                'features' => $isEnglish
                    ? ['One-time attraction entry', 'Valid on the selected visit date', 'Digital ticket after confirmation', 'No printed ticket required']
                    : ['Satu kali akses masuk kawasan', 'Berlaku pada tanggal yang dipilih', 'E-ticket setelah pembayaran dikonfirmasi', 'Tidak perlu mencetak tiket'],
            ];
        }

        return [
            'key' => $ticket->kategori === 'kendaraan' ? 'vehicle' : 'general',
            'name' => $ticket->nama_tiket,
            'eyebrow' => $ticket->kategori === 'kendaraan' ? ($isEnglish ? 'Vehicle ticket' : 'Tiket kendaraan') : ($isEnglish ? 'Visitor admission' : 'Tiket pengunjung'),
            'summary' => $isEnglish ? 'Choose this ticket according to the category shown before continuing to booking.' : 'Pilih tiket ini sesuai kategori yang tertera sebelum melanjutkan pemesanan.',
            'description' => $isEnglish ? 'This ticket is valid for one use on the visit date selected during booking.' : 'Tiket ini berlaku satu kali pada tanggal kunjungan yang dipilih saat pemesanan.',
            'recommended' => $isEnglish ? 'Visitors in this category' : 'Pengunjung sesuai kategori',
            'features' => $isEnglish
                ? ['One-time use', 'Valid on the selected date', 'Digital confirmation', 'Official Bangkiang Jaran ticket']
                : ['Berlaku satu kali', 'Valid pada tanggal pilihan', 'Konfirmasi digital', 'Tiket resmi Bangkiang Jaran'],
        ];
    };
    $profiles = $tikets->mapWithKeys(fn ($ticket) => [$ticket->id_tiket => $ticketProfile($ticket)]);
    $heroPhoto = $photoAt(0);
    $firstTicket = $tikets->first();
    $faqItems = $isEnglish ? [
        ['Where is Bangkiang Jaran Waterfall?', 'Bangkiang Jaran Waterfall is located in Bakbakan Village, Gianyar, Bali. Open the location link on the homepage for the latest route guidance.'],
        ['How does online booking work?', 'Choose a ticket and visit date, enter visitor details, then follow the payment instructions. Your e-ticket is issued after the payment is verified by the team.'],
        ['When will my e-ticket become active?', 'The e-ticket becomes usable after payment verification. Verification may take up to 1×24 hours on working days, so booking ahead is recommended.'],
        ['Do young children need a ticket?', 'Children aged 3–12 use the child admission. For infants or current on-site policies, confirm with the team before visiting.'],
        ['How do I show the ticket at the entrance?', 'Open the confirmed e-ticket or a clear screenshot on your phone. Make sure its code can be read by the entrance staff.'],
    ] : [
        ['Di mana lokasi Air Terjun Bangkiang Jaran?', 'Air Terjun Bangkiang Jaran berada di Desa Bakbakan, Gianyar, Bali. Buka tautan lokasi pada halaman beranda untuk panduan rute terbaru.'],
        ['Bagaimana proses pemesanan tiket online?', 'Pilih tiket dan tanggal kunjungan, lengkapi data pengunjung, lalu ikuti petunjuk pembayaran. E-ticket diterbitkan setelah pembayaran diverifikasi oleh pengelola.'],
        ['Kapan e-ticket saya dapat digunakan?', 'E-ticket dapat digunakan setelah pembayaran terverifikasi. Proses verifikasi dapat memerlukan waktu hingga 1×24 jam pada hari kerja, jadi sebaiknya pesan lebih awal.'],
        ['Apakah anak kecil memerlukan tiket?', 'Anak usia 3–12 tahun menggunakan Tiket Anak. Untuk bayi atau kebijakan terbaru di lokasi, konfirmasikan kepada pengelola sebelum berkunjung.'],
        ['Bagaimana menunjukkan tiket di pintu masuk?', 'Buka e-ticket yang telah dikonfirmasi atau screenshot yang jelas di ponsel. Pastikan kode tiket dapat dibaca oleh petugas pintu masuk.'],
    ];
@endphp

<div class="bj-ticket-editorial">
    <svg class="bj-ticket-icon-sprite" aria-hidden="true">
        <symbol id="bj-ticket-icon-arrow" viewBox="0 0 24 24"><path d="M5 12h14M14 7l5 5-5 5"/></symbol>
        <symbol id="bj-ticket-icon-check" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.6 2.6L16.5 9"/></symbol>
        <symbol id="bj-ticket-icon-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
        <symbol id="bj-ticket-icon-ticket" viewBox="0 0 24 24"><path d="M4 6h16v4a2 2 0 0 0 0 4v4H4v-4a2 2 0 0 0 0-4Z"/><path d="M9 8.5v7"/></symbol>
        <symbol id="bj-ticket-icon-calendar" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/></symbol>
        <symbol id="bj-ticket-icon-users" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M3.5 19a5.5 5.5 0 0 1 11 0M16 6.5a2.5 2.5 0 0 1 0 5M17 14a4.5 4.5 0 0 1 3.5 4.4"/></symbol>
    </svg>

    <header id="hero-section" class="bj-ticket-hero">
        <div class="bj-ticket-hero__media" data-bj-parallax aria-hidden="true">
            <img src="{{ $heroPhoto['url'] }}" alt="" fetchpriority="high">
        </div>
        <div class="bj-ticket-hero__shade" aria-hidden="true"></div>
        <div class="bj-ticket-wrap bj-ticket-hero__content">
            <nav class="bj-ticket-breadcrumb bj-reveal" aria-label="Breadcrumb">
                <a href="{{ route('landing') }}">{{ $isEnglish ? 'Home' : 'Beranda' }}</a><span aria-hidden="true">/</span><span>{{ $isEnglish ? 'Tickets' : 'Tiket' }}</span>
            </nav>
            <div class="bj-ticket-hero__copy">
                <p class="bj-ticket-kicker bj-reveal"><span aria-hidden="true"></span>{{ $isEnglish ? 'Plan your visit' : 'Rencanakan kunjungan' }}</p>
                <h1 class="bj-reveal" data-bj-split>{{ $isEnglish ? 'Choose the visit that ' : 'Pilih kunjungan yang ' }}<em>{{ $isEnglish ? 'fits you' : 'pas untukmu' }}</em></h1>
                <p class="bj-ticket-hero__lead bj-reveal">{{ $isEnglish
                    ? 'Compare every available ticket, understand what it includes, and continue to a clear digital booking process.'
                    : 'Bandingkan setiap tiket yang tersedia, pahami manfaatnya, lalu lanjutkan ke proses pemesanan digital yang jelas.' }}</p>
            </div>
        </div>
    </header>

    <main class="bj-ticket-paper">
        <section class="bj-ticket-snapshot" aria-labelledby="ticket-options-title">
            <div class="bj-ticket-wrap">
                <h2 id="ticket-options-title" class="sr-only">{{ $isEnglish ? 'Available ticket options' : 'Pilihan tiket tersedia' }}</h2>
                @if($tikets->isNotEmpty())
                <div class="bj-ticket-snapshot__grid">
                    @foreach($tikets as $index => $ticket)
                    @php
                        $profile = $profiles[$ticket->id_tiket];
                    @endphp
                    <a href="#ticket-{{ $ticket->id_tiket }}" class="bj-ticket-snapshot__item bj-reveal">
                        <span class="bj-ticket-snapshot__number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <small>{{ $profile['eyebrow'] }}</small>
                        <h3>{{ $profile['name'] }}</h3>
                        <p><svg class="bj-ticket-icon" aria-hidden="true"><use href="#bj-ticket-icon-clock"/></svg>{{ $isEnglish ? 'One visit' : 'Satu kali kunjungan' }}</p>
                        <strong>Rp {{ number_format($ticket->harga, 0, ',', '.') }} <span>/ {{ $ticket->kategori === 'kendaraan' ? ($isEnglish ? 'vehicle' : 'kendaraan') : ($isEnglish ? 'person' : 'orang') }}</span></strong>
                        <span class="bj-ticket-snapshot__link">{{ $isEnglish ? 'See package' : 'Lihat paket' }} <svg class="bj-ticket-icon" aria-hidden="true"><use href="#bj-ticket-icon-arrow"/></svg></span>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="bj-ticket-empty">{{ __('messages.no_tickets_desc') }}</div>
                @endif
            </div>
        </section>

        @foreach($tikets as $index => $ticket)
        @php
            $profile = $profiles[$ticket->id_tiket];
            $detailPhoto = $photoAt($index + 1);
        @endphp
        <section id="ticket-{{ $ticket->id_tiket }}" class="bj-ticket-package {{ $index % 2 ? 'bj-ticket-package--reverse' : '' }}" aria-labelledby="ticket-title-{{ $ticket->id_tiket }}">
            <div class="bj-ticket-wrap bj-ticket-package__grid">
                <div class="bj-ticket-package__media bj-reveal" data-bj-parallax>
                    <img src="{{ $detailPhoto['url'] }}" alt="{{ $detailPhoto['caption'] }}" loading="lazy">
                    <span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }} / {{ str_pad(max(1, $tikets->count()), 2, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="bj-ticket-package__copy bj-reveal">
                    <p class="bj-ticket-kicker"><span aria-hidden="true"></span>{{ $isEnglish ? 'Package' : 'Paket' }} {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</p>
                    <h2 id="ticket-title-{{ $ticket->id_tiket }}">{{ $profile['name'] }}</h2>
                    <div class="bj-ticket-package__facts">
                        <p><strong>Rp {{ number_format($ticket->harga, 0, ',', '.') }}</strong><span>/ {{ $ticket->kategori === 'kendaraan' ? ($isEnglish ? 'vehicle' : 'kendaraan') : ($isEnglish ? 'person' : 'orang') }}</span></p>
                        <p><strong>{{ $isEnglish ? '1 visit' : '1 kunjungan' }}</strong><span>{{ $isEnglish ? 'Selected date' : 'Tanggal pilihan' }}</span></p>
                    </div>
                    <p class="bj-ticket-package__description">{{ $profile['description'] }}</p>
                    <ul>
                        @foreach($profile['features'] as $feature)
                        <li><svg class="bj-ticket-icon" aria-hidden="true"><use href="#bj-ticket-icon-check"/></svg>{{ $feature }}</li>
                        @endforeach
                    </ul>
                    <div class="bj-ticket-package__actions">
                        @auth
                            @if(Auth::user()->role === 'wisatawan')
                            <a class="bj-ticket-button bj-ticket-button--gold" href="{{ route('wisatawan.pemesanan.create', ['id_tiket' => $ticket->id_tiket]) }}">{{ $isEnglish ? 'Choose this ticket' : 'Pilih tiket ini' }} <svg class="bj-ticket-icon" aria-hidden="true"><use href="#bj-ticket-icon-arrow"/></svg></a>
                            @else
                            <a class="bj-ticket-button bj-ticket-button--gold" href="{{ route('pengelola.dashboard') }}">{{ $isEnglish ? 'Open dashboard' : 'Buka dashboard' }}</a>
                            @endif
                        @else
                        <a class="bj-ticket-button bj-ticket-button--gold" href="{{ route('login') }}">{{ $isEnglish ? 'Log in to book' : 'Masuk untuk memesan' }} <svg class="bj-ticket-icon" aria-hidden="true"><use href="#bj-ticket-icon-arrow"/></svg></a>
                        @endauth
                        <a class="bj-ticket-text-link" href="{{ route('tiket.detail', $ticket->id_tiket) }}">{{ $isEnglish ? 'Full details' : 'Detail lengkap' }} <svg class="bj-ticket-icon" aria-hidden="true"><use href="#bj-ticket-icon-arrow"/></svg></a>
                    </div>
                    <p class="bj-ticket-package__recommended">{{ $isEnglish ? 'Recommended for:' : 'Cocok untuk:' }} <strong>{{ $profile['recommended'] }}</strong></p>
                </div>
            </div>
        </section>
        @endforeach

        @if($tikets->isNotEmpty())
        <section class="bj-ticket-compare" aria-labelledby="ticket-compare-title">
            <div class="bj-ticket-wrap">
                <div class="bj-ticket-section-head bj-reveal">
                    <div><p class="bj-ticket-kicker"><span aria-hidden="true"></span>{{ $isEnglish ? 'Compare' : 'Bandingkan' }}</p><h2 id="ticket-compare-title">{{ $isEnglish ? 'Everything side by ' : 'Semua pilihan, ' }}<em>{{ $isEnglish ? 'side' : 'sekilas' }}</em></h2></div>
                    <p>{{ $isEnglish ? 'Prices and visitor categories are shown together so you can choose without second-guessing.' : 'Harga dan kategori pengunjung disajikan berdampingan agar Anda dapat memilih tanpa ragu.' }}</p>
                </div>
                <div class="bj-ticket-compare__scroll bj-reveal" tabindex="0" role="region" aria-label="{{ $isEnglish ? 'Ticket comparison table' : 'Tabel perbandingan tiket' }}">
                    <table>
                        <thead><tr><th scope="col">{{ $isEnglish ? 'Information' : 'Informasi' }}</th>@foreach($tikets as $ticket)<th scope="col">{{ $profiles[$ticket->id_tiket]['name'] }}<small>Rp {{ number_format($ticket->harga, 0, ',', '.') }}</small></th>@endforeach</tr></thead>
                        <tbody>
                            <tr><th scope="row">{{ $isEnglish ? 'Attraction entry' : 'Akses kawasan' }}</th>@foreach($tikets as $ticket)<td><span class="bj-ticket-compare__yes"><svg class="bj-ticket-icon" aria-hidden="true"><use href="#bj-ticket-icon-check"/></svg><span class="sr-only">{{ $isEnglish ? 'Included' : 'Termasuk' }}</span></span></td>@endforeach</tr>
                            <tr><th scope="row">{{ $isEnglish ? 'Selected-date validity' : 'Berlaku pada tanggal pilihan' }}</th>@foreach($tikets as $ticket)<td><span class="bj-ticket-compare__yes"><svg class="bj-ticket-icon" aria-hidden="true"><use href="#bj-ticket-icon-check"/></svg><span class="sr-only">{{ $isEnglish ? 'Included' : 'Termasuk' }}</span></span></td>@endforeach</tr>
                            <tr><th scope="row">{{ $isEnglish ? 'Digital e-ticket' : 'E-ticket digital' }}</th>@foreach($tikets as $ticket)<td><span class="bj-ticket-compare__yes"><svg class="bj-ticket-icon" aria-hidden="true"><use href="#bj-ticket-icon-check"/></svg><span class="sr-only">{{ $isEnglish ? 'Included' : 'Termasuk' }}</span></span></td>@endforeach</tr>
                            <tr><th scope="row">{{ $isEnglish ? 'Adult supervision' : 'Pendamping dewasa' }}</th>@foreach($tikets as $ticket)<td>{{ $profiles[$ticket->id_tiket]['key'] === 'child' ? ($isEnglish ? 'Required' : 'Wajib') : '—' }}</td>@endforeach</tr>
                            <tr><th scope="row">{{ $isEnglish ? 'Best suited for' : 'Cocok untuk' }}</th>@foreach($tikets as $ticket)<td>{{ $profiles[$ticket->id_tiket]['recommended'] }}</td>@endforeach</tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        @endif
    </main>

    @if($tikets->isNotEmpty())
    <section class="bj-ticket-picker" aria-labelledby="ticket-picker-title" data-ticket-estimator>
        <div class="bj-ticket-wrap">
            <div class="bj-ticket-picker__intro bj-reveal">
                <div><p class="bj-ticket-kicker"><span aria-hidden="true"></span>{{ $isEnglish ? 'Tickets & booking' : 'Tiket & pemesanan' }}</p><h2 id="ticket-picker-title">{{ $isEnglish ? 'Choose your ticket, ' : 'Pilih tiketmu, ' }}<em>{{ $isEnglish ? 'plan your day' : 'rencanakan harimu' }}</em></h2></div>
                <p>{{ $isEnglish ? 'Select a visitor category and quantity to see an estimate before continuing to the secure booking flow.' : 'Pilih kategori pengunjung dan jumlah tiket untuk melihat estimasi sebelum melanjutkan ke proses pemesanan.' }}</p>
            </div>
            <div class="bj-ticket-picker__grid">
                <fieldset class="bj-ticket-picker__choices bj-reveal">
                    <legend class="sr-only">{{ $isEnglish ? 'Choose a ticket' : 'Pilih tiket' }}</legend>
                    @foreach($tikets as $index => $ticket)
                    @php
                        $profile = $profiles[$ticket->id_tiket];
                        if (auth()->check() && auth()->user()->role === 'wisatawan') {
                            $bookingUrl = route('wisatawan.pemesanan.create', ['id_tiket' => $ticket->id_tiket]);
                        } elseif (auth()->check()) {
                            $bookingUrl = route('pengelola.dashboard');
                        } else {
                            $bookingUrl = route('login');
                        }
                    @endphp
                    <label class="bj-ticket-choice">
                        <input type="radio" name="ticket_choice" value="{{ $ticket->id_tiket }}" data-ticket-choice data-price="{{ $ticket->harga }}" data-name="{{ $profile['name'] }}" data-booking-url="{{ $bookingUrl }}" @checked($index === 0)>
                        <span class="bj-ticket-choice__photo"><img src="{{ $photoAt($index + 1)['url'] }}" alt="" loading="lazy"></span>
                        <span class="bj-ticket-choice__body"><strong>{{ $profile['name'] }}</strong><small>{{ $profile['eyebrow'] }}</small><span>{{ $profile['summary'] }}</span></span>
                        <span class="bj-ticket-choice__price">Rp {{ number_format($ticket->harga, 0, ',', '.') }}<small>/ {{ $ticket->kategori === 'kendaraan' ? ($isEnglish ? 'vehicle' : 'kendaraan') : ($isEnglish ? 'person' : 'orang') }}</small></span>
                    </label>
                    @endforeach
                </fieldset>

                <aside class="bj-ticket-estimate bj-reveal" aria-labelledby="ticket-estimate-title">
                    <p class="bj-ticket-kicker"><span aria-hidden="true"></span>{{ $isEnglish ? 'Your estimate' : 'Estimasi kunjungan' }}</p>
                    <h3 id="ticket-estimate-title" data-ticket-estimate-name>{{ $profiles[$firstTicket->id_tiket]['name'] }}</h3>
                    <label><span>{{ $isEnglish ? 'Visit date' : 'Tanggal kunjungan' }}</span><input type="date" min="{{ now()->toDateString() }}" data-ticket-date></label>
                    <label><span>{{ $isEnglish ? 'Ticket quantity' : 'Jumlah tiket' }}</span><input type="number" min="1" max="30" value="1" inputmode="numeric" data-ticket-quantity></label>
                    <div class="bj-ticket-estimate__total"><span>{{ $isEnglish ? 'Estimated total' : 'Estimasi total' }}</span><strong data-ticket-total aria-live="polite">Rp {{ number_format($firstTicket->harga, 0, ',', '.') }}</strong><small>{{ $isEnglish ? 'Final total is confirmed during booking.' : 'Total akhir dikonfirmasi saat pemesanan.' }}</small></div>
                    <a class="bj-ticket-button bj-ticket-button--dark" href="{{ auth()->check() && auth()->user()->role === 'wisatawan' ? route('wisatawan.pemesanan.create', ['id_tiket' => $firstTicket->id_tiket]) : (auth()->check() ? route('pengelola.dashboard') : route('login')) }}" data-ticket-booking-link>{{ $isEnglish ? 'Continue to booking' : 'Lanjut ke pemesanan' }} <svg class="bj-ticket-icon" aria-hidden="true"><use href="#bj-ticket-icon-arrow"/></svg></a>
                    <p class="bj-ticket-estimate__note"><svg class="bj-ticket-icon" aria-hidden="true"><use href="#bj-ticket-icon-ticket"/></svg>{{ $isEnglish ? 'The visit date and visitor details are completed in the next step.' : 'Tanggal dan data pengunjung dilengkapi pada langkah berikutnya.' }}</p>
                </aside>
            </div>
        </div>
    </section>
    @endif

    <section class="bj-ticket-faq" aria-labelledby="ticket-faq-title">
        <div class="bj-ticket-wrap bj-ticket-faq__grid">
            <div class="bj-ticket-faq__intro bj-reveal"><p class="bj-ticket-kicker"><span aria-hidden="true"></span>FAQ</p><h2 id="ticket-faq-title">{{ $isEnglish ? 'Questions before you ' : 'Pertanyaan sebelum ' }}<em>{{ $isEnglish ? 'book?' : 'memesan?' }}</em></h2><p>{{ $isEnglish ? 'Still unsure? Read the most important information before choosing a visit date.' : 'Masih ragu? Baca informasi terpenting sebelum memilih tanggal kunjungan.' }}</p></div>
            <div class="bj-ticket-faq__list bj-reveal">
                @foreach($faqItems as $index => [$question, $answer])
                <details @if($index === 0) open @endif><summary>{{ $question }}<span aria-hidden="true">+</span></summary><p>{{ $answer }}</p></details>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bj-ticket-finale" aria-labelledby="ticket-finale-title">
        <div class="bj-ticket-wrap bj-ticket-finale__content bj-reveal">
            <p class="bj-ticket-kicker"><span aria-hidden="true"></span>{{ $isEnglish ? 'Plan your visit' : 'Rencanakan kunjungan' }}</p>
            <h2 id="ticket-finale-title">{{ $isEnglish ? 'Ready to hear the ' : 'Siap mendengar ' }}<em>{{ $isEnglish ? 'roar?' : 'derunya?' }}</em></h2>
            <p>{{ $isEnglish ? 'Choose the right ticket, prepare your visit date, and let the valley welcome you.' : 'Pilih tiket yang tepat, siapkan tanggal kunjungan, dan biarkan lembah menyambut Anda.' }}</p>
            <div class="bj-ticket-finale__actions">
                @if(config('payment.contact_whatsapp'))<a class="bj-home-button bj-home-button--whatsapp" href="https://wa.me/{{ config('payment.contact_whatsapp') }}" target="_blank" rel="noopener noreferrer">{{ $isEnglish ? 'Chat on WhatsApp' : 'Chat via WhatsApp' }}</a>@endif
                @if($firstTicket)<a class="bj-home-button bj-home-button--ghost" href="#ticket-picker-title">{{ $isEnglish ? 'Choose a ticket' : 'Pilih tiket' }} <svg class="bj-ticket-icon" aria-hidden="true"><use href="#bj-ticket-icon-arrow"/></svg></a>@endif
            </div>
        </div>
        <div class="bj-ticket-ribbons" aria-hidden="true">
            @foreach(['left', 'right'] as $rowIndex => $direction)
            <div class="bj-home-ribbon bj-ticket-ribbon" data-bj-marquee data-direction="{{ $direction }}">
                @for($copy = 0; $copy < 2; $copy++)<div class="bj-home-ribbon__set">@for($i = 0; $i < 6; $i++)<img src="{{ $photoAt($i + ($rowIndex * 5))['url'] }}" alt="" loading="lazy">@endfor</div>@endfor
            </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
