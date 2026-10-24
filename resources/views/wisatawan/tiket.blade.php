@extends('layouts.app')
@section('title', __('messages.tickets_page_label') . ' | Bangkiang Jaran')
@section('nav-mode', 'light')
@section('content')
@php
    $isEnglish = app()->getLocale() === 'en';
    $firstTicket = $tikets->first();
    $photos = ($galeriWisata ?? collect())->filter(fn ($item) => ! preg_match('/(?:^|\/)(?:galeri-\d+|placeholder\d*)\.(?:jpe?g|png|webp)$/i', (string) $item->file))->values();
    $ticketPhoto = $photos->isNotEmpty() ? asset('storage/' . $photos->first()->file) : asset('images/sejarah-bangkiang-waterfall.webp');
    $bookingUrlFor = fn ($ticket) => route('wisatawan.pemesanan.create', ['id_tiket' => $ticket->id_tiket]);
    $faqs = $isEnglish ? [
        ['When can I use my ticket?', 'Your ticket is valid for the visit date selected during booking, after the team verifies your payment.'],
        ['How do I receive my e-ticket?', 'Complete your visitor details and follow the payment instructions. Check your booking history for payment verification and your e-ticket.'],
        ['Which ticket should I choose for a child?', 'Check the ticket names and confirm the current age conditions with the team before booking.'],
        ['Can I book for a group?', 'Yes. Enter the quantity you need for each ticket type, then complete the visitor details. There is no per-type booking quota.'],
        ['How do I pay?', 'Choose an available bank or e-wallet method on the payment page, then upload your receipt. This website uses manual verification by the team.'],
        ['Does uploading a receipt confirm my payment?', 'No. Your booking remains awaiting verification until the team approves your receipt.'],
        ['What if my receipt is declined?', 'Open the booking details, check the payment method and upload a corrected receipt.'],
        ['Can I use the same ticket twice?', 'Each visitor or vehicle receives a ticket code. A ticket that has already been checked in cannot be used for another admission.'],
    ] : [
        ['Kapan tiket dapat digunakan?', 'Tiket berlaku pada tanggal kunjungan yang dipilih saat pemesanan, setelah pembayaran diverifikasi pengelola.'],
        ['Bagaimana mendapatkan e-ticket?', 'Lengkapi data pengunjung dan ikuti petunjuk pembayaran. Periksa verifikasi pembayaran dan e-ticket melalui riwayat pemesanan.'],
        ['Tiket apa yang dipilih untuk anak?', 'Periksa nama tiket dan konfirmasikan ketentuan usia terbaru kepada pengelola sebelum memesan.'],
        ['Bisa memesan untuk rombongan?', 'Bisa. Masukkan jumlah yang dibutuhkan pada setiap jenis tiket, lalu lengkapi data pengunjung. Tidak ada kuota pemesanan per jenis tiket.'],
        ['Bagaimana cara membayar?', 'Pilih bank atau e-wallet yang tersedia di halaman pembayaran, lalu unggah bukti transfer. Pembayaran diperiksa secara manual oleh pengelola.'],
        ['Apakah unggah bukti langsung membuat pembayaran berhasil?', 'Tidak. Pesanan tetap menunggu verifikasi sampai pengelola menyetujui bukti pembayaran.'],
        ['Bagaimana jika bukti pembayaran ditolak?', 'Buka detail pesanan, periksa metode pembayaran dan unggah kembali bukti yang benar.'],
        ['Bisa memakai tiket yang sama dua kali?', 'Setiap pengunjung atau kendaraan mendapat kode tiket. Tiket yang sudah dipakai untuk masuk tidak dapat dipakai untuk masuk kembali.'],
    ];
@endphp
<div class="bj-ticket-editorial bj-ticket-shop">
    <header class="bj-shop-heading bj-nature-wrap">
        <div><p class="bj-nature-label">Bangkiang Jaran Waterfall</p><h1>{{ $isEnglish ? 'A day worth stepping outside for.' : 'Saatnya keluar, menikmati alam.' }}</h1><p>{{ $isEnglish ? 'Choose your ticket and plan a visit to Bakbakan, Gianyar.' : 'Pilih tiketmu dan rencanakan kunjungan ke Bakbakan, Gianyar.' }}</p></div>
    </header>
    @if($firstTicket)
    <section class="bj-ticket-picker" aria-labelledby="ticket-picker-title" data-ticket-estimator>
        <div class="bj-ticket-wrap">
            <div class="bj-ticket-picker__intro"><div><h2 id="ticket-picker-title">{{ $isEnglish ? 'Find your way in.' : 'Pilih akses kunjunganmu.' }}</h2></div><p>{{ $isEnglish ? 'Prices are per ticket. Select a category to see your estimate.' : 'Harga berlaku per tiket. Pilih kategori untuk melihat estimasi biaya.' }}</p></div>
            <div class="bj-ticket-picker__grid">
                <fieldset class="bj-ticket-picker__choices">
                    <legend class="sr-only">{{ $isEnglish ? 'Choose a ticket' : 'Pilih tiket' }}</legend>
                    @foreach($tikets as $index => $ticket)
                    <div class="bj-pass-option" id="ticket-{{ $ticket->id_tiket }}">
                        <label class="bj-ticket-choice">
                            <input type="radio" name="ticket_choice" value="{{ $ticket->id_tiket }}" data-ticket-choice data-price="{{ $ticket->harga }}" data-name="{{ $ticket->nama_tiket }}" data-booking-url="{{ $bookingUrlFor($ticket) }}" @checked($index === 0)>
                            <span class="bj-ticket-choice__photo"><img src="{{ $ticketPhoto }}" alt="" loading="lazy" width="706" height="1020"></span>
                            <span class="bj-ticket-choice__body"><small>Bangkiang Jaran</small><strong>{{ $ticket->nama_tiket }}</strong><span>{{ $ticket->kategori === 'kendaraan' ? ($isEnglish ? 'Vehicle admission' : 'Akses kendaraan') : ($isEnglish ? 'Visitor admission' : 'Akses pengunjung') }}</span></span>
                            <span class="bj-ticket-choice__price">Rp {{ number_format($ticket->harga, 0, ',', '.') }}<small>/ {{ $ticket->kategori === 'kendaraan' ? ($isEnglish ? 'vehicle' : 'kendaraan') : ($isEnglish ? 'person' : 'orang') }}</small><span class="bj-choice-check material-symbols-outlined" aria-hidden="true">check</span></span>
                        </label>
                        <a class="bj-pass-detail" href="{{ route('tiket.detail', $ticket->id_tiket) }}">{{ $isEnglish ? 'View details' : 'Lihat detail' }}<span class="sr-only"> {{ $ticket->nama_tiket }}</span><span class="material-symbols-outlined" aria-hidden="true">north_east</span></a>
                    </div>
                    @endforeach
                </fieldset>
                <aside class="bj-ticket-estimate" aria-labelledby="ticket-estimate-title">
                    <p class="bj-nature-label">{{ $isEnglish ? 'Your visit' : 'Kunjunganmu' }}</p>
                    <h3 id="ticket-estimate-title" data-ticket-estimate-name>{{ $firstTicket->nama_tiket }}</h3>
                    <p class="bj-estimate-location"><span class="material-symbols-outlined" aria-hidden="true">location_on</span>Bakbakan, Gianyar, Bali</p>
                    <label><span>{{ $isEnglish ? 'Ticket quantity' : 'Jumlah tiket' }}</span><input type="number" min="1" step="1" value="1" inputmode="numeric" data-ticket-quantity></label>
                    <div class="bj-ticket-estimate__total"><span>{{ $isEnglish ? 'Estimated total' : 'Estimasi total' }}</span><strong data-ticket-total aria-live="polite">Rp {{ number_format($firstTicket->harga, 0, ',', '.') }}</strong></div>
                    <a class="bj-ticket-button bj-ticket-button--dark" href="{{ $bookingUrlFor($firstTicket) }}" data-ticket-booking-link>{{ $isEnglish ? 'Continue to booking' : 'Lanjut ke pemesanan' }}<span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
                    <p class="bj-ticket-estimate__note">{{ $isEnglish ? 'Choose your date and confirm quantities in the next step. E-tickets follow payment verification.' : 'Tentukan tanggal dan pastikan jumlah di langkah berikutnya. E-ticket terbit setelah verifikasi pembayaran.' }}</p>
                </aside>
            </div>
        </div>
    </section>
    @else
    <section class="bj-nature-wrap bj-ticket-empty"><h2>{{ __('messages.no_tickets') }}</h2><p>{{ __('messages.no_tickets_desc') }}</p><a class="bj-nature-button" href="{{ route('landing') }}">{{ $isEnglish ? 'Back to home' : 'Kembali ke beranda' }}</a></section>
    @endif
    <section class="bj-ticket-faq" aria-labelledby="ticket-faq-title"><div class="bj-ticket-wrap bj-ticket-faq__grid"><div class="bj-ticket-faq__intro"><h2 id="ticket-faq-title">{{ $isEnglish ? 'Before you book.' : 'Sebelum memesan.' }}</h2><p>{{ $isEnglish ? 'A few things to help you plan.' : 'Beberapa hal untuk membantu rencana kunjunganmu.' }}</p></div><div class="bj-ticket-faq__list">@foreach($faqs as [$question, $answer])<details><summary>{{ $question }}<span aria-hidden="true">+</span></summary><p>{{ $answer }}</p></details>@endforeach</div></div></section>
</div>
@endsection
