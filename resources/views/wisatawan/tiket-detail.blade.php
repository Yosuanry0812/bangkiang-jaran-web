@extends('layouts.app')
@section('nav-mode', 'light')
@section('title', $tiket->nama_tiket . ' | Bangkiang Jaran')
@section('content')
@php($isEnglish = app()->getLocale() === 'en')
<div class="bj-ticket-detail bj-nature-wrap">
    <div class="bj-detail-layout">
        <article class="bj-detail-story">
            <figure class="bj-detail-photo"><img src="{{ asset('images/sejarah-bangkiang-waterfall.webp') }}" alt="{{ $isEnglish ? 'Bangkiang Jaran Waterfall, Bakbakan' : 'Air Terjun Bangkiang Jaran, Bakbakan' }}" width="706" height="1020" fetchpriority="high"><figcaption>Bangkiang Jaran · Gianyar, Bali</figcaption></figure>
            <div class="bj-detail-description"><h2>{{ $isEnglish ? 'Your visit, at a glance.' : 'Tentang kunjunganmu.' }}</h2><p>{{ $tiket->deskripsi ?: ($isEnglish ? 'Spend a day at Bangkiang Jaran Waterfall in Bakbakan, Gianyar. Select your visit date and complete each visitor’s details during booking.' : 'Nikmati satu hari di Air Terjun Bangkiang Jaran, Bakbakan, Gianyar. Pilih tanggal kunjungan dan lengkapi data setiap pengunjung saat memesan.') }}</p><ul><li>{{ __('messages.facility_access_entry') }}</li><li>{{ __('messages.facility_valid_date') }}</li><li>{{ __('messages.facility_free_access') }}</li></ul></div>
        </article>
        <aside class="bj-detail-reservation" aria-labelledby="ticket-detail-title"><p class="bj-nature-label">{{ $isEnglish ? 'Your pass to the waterfall' : 'Akses menuju air terjun' }}</p><h1 id="ticket-detail-title">{{ $tiket->nama_tiket }}</h1><p class="bj-detail-price">Rp {{ number_format($tiket->harga, 0, ',', '.') }}<small>/ {{ $tiket->kategori === 'kendaraan' ? ($isEnglish ? 'vehicle' : 'kendaraan') : ($isEnglish ? 'person' : 'orang') }}</small></p><dl><div><dt>{{ __('messages.operating_hours_label') }}</dt><dd>{{ __('messages.operating_hours') }}</dd></div><div><dt>{{ __('messages.location') }}</dt><dd>{{ __('messages.address') }}</dd></div><div><dt>{{ $isEnglish ? 'Valid for' : 'Berlaku untuk' }}</dt><dd>{{ $isEnglish ? 'Your selected visit date' : 'Tanggal kunjungan pilihan' }}</dd></div></dl>
            @if($tiket->status === 'aktif')
                @auth
                    @if(Auth::user()->role === 'wisatawan')
                    <a class="bj-nature-button" href="{{ route('wisatawan.pemesanan.create', ['id_tiket' => $tiket->id_tiket]) }}">{{ __('messages.book_now_btn') }}<span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></a>
                    @else
                    <a class="bj-nature-button" href="{{ route('pengelola.dashboard') }}">{{ $isEnglish ? 'Open dashboard' : 'Buka dashboard pengelola' }}</a>
                    @endif
                @else
                <a class="bj-nature-button" href="{{ route('wisatawan.pemesanan.create', ['id_tiket' => $tiket->id_tiket]) }}">{{ __('messages.login_to_book') }}</a>
                @endauth
            @else
            <button type="button" class="bj-nature-button" disabled>{{ __('messages.ticket_unavailable') }}</button>
            @endif
            <p class="bj-detail-note">{{ $isEnglish ? 'E-tickets become available after the team verifies your payment.' : 'E-ticket tersedia setelah pembayaran diverifikasi oleh pengelola.' }}</p>
            <a class="bj-detail-back" href="{{ route('tiket.index') }}">{{ __('messages.view_other_tickets') }}</a>
        </aside>
    </div>
</div>
@endsection
