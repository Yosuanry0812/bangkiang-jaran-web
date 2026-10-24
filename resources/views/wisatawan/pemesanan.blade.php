@extends('layouts.app')
@section('title', __('messages.book_ticket') . ' | Bangkiang Jaran')
@section('nav-mode', 'light')

@section('content')
@php
    $isEnglish = app()->getLocale() === 'en';
    $pricesJson = json_encode($tiketList->pluck('harga', 'id_tiket')->toArray());
    $namesJson = json_encode($tiketList->pluck('nama_tiket', 'id_tiket')->toArray());
    $initialTicketsJson = json_encode(old('tickets', []));
    $initialDate = old('tgl_kunjungan', $tanggal ?? date('Y-m-d'));
    $preSelectedId = $selectedId ?? null;
@endphp

<div class="bj-booking bj-transaction-page" x-data="ticketBooking({{ $pricesJson }}, {{ $namesJson }}, '{{ $initialDate }}', {{ $preSelectedId ? (int) $preSelectedId : 'null' }}, {{ $initialTicketsJson }}, '{{ $isEnglish ? 'en-US' : 'id-ID' }}')">
    <header id="hero-section" class="bj-booking-hero" aria-labelledby="booking-title">
        <div class="bj-booking-hero__media" data-bj-hero-slide aria-hidden="true"><img src="{{ asset('images/sejarah-bangkiang-waterfall.webp') }}" alt="" fetchpriority="high"></div>
        <div class="bj-booking-hero__shade" aria-hidden="true"></div>
        <div class="bj-booking-wrap bj-booking-hero__content">
            <div class="bj-booking-hero__grid">
                <div class="bj-booking-hero__copy">
                    <p class="bj-booking-kicker bj-reveal"><span aria-hidden="true"></span>{{ $isEnglish ? 'Your visit begins here' : 'Perjalanan Anda dimulai di sini' }}</p>
                    <h1 id="booking-title" class="bj-reveal" data-bj-split>{{ $isEnglish ? 'Plan your visit.' : 'Rencanakan kunjungan Anda.' }}</h1>
                    <p class="bj-booking-hero__lead bj-reveal">{{ $isEnglish ? 'Choose your visit date and the right ticket for every guest. The next step will collect visitor details.' : 'Pilih tanggal kunjungan dan tiket yang sesuai untuk setiap pengunjung. Data pengunjung akan dilengkapi pada langkah berikutnya.' }}</p>
                </div>
                <ol class="bj-booking-progress bj-reveal" aria-label="{{ $isEnglish ? 'Booking progress' : 'Progres pemesanan' }}">
                    <li class="is-current" aria-current="step"><span>01</span><div><strong>{{ $isEnglish ? 'Tickets' : 'Pilih tiket' }}</strong><small>{{ $isEnglish ? 'Date & quantity' : 'Tanggal & jumlah' }}</small></div></li>
                    <li><span>02</span><div><strong>{{ $isEnglish ? 'Visitors' : 'Data pengunjung' }}</strong><small>{{ $isEnglish ? 'Guest details' : 'Detail setiap tamu' }}</small></div></li>
                    <li><span>03</span><div><strong>{{ $isEnglish ? 'Payment' : 'Pembayaran' }}</strong><small>{{ $isEnglish ? 'Confirm booking' : 'Konfirmasi pesanan' }}</small></div></li>
                </ol>
            </div>
        </div>
    </header>

    @if(session('error') || (isset($errors) && $errors->any()))
        <div class="bj-booking-wrap bj-booking-alert bj-reveal" role="alert">
            <span class="material-symbols-outlined" aria-hidden="true">error</span>
            <div>@if(session('error'))<p>{{ session('error') }}</p>@endif @if(isset($errors) && $errors->any())<ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif</div>
        </div>
    @endif

    <div class="bj-booking-main">
        <form method="POST" action="{{ route('wisatawan.pemesanan.store') }}" class="bj-booking-wrap bj-booking-layout" @submit="submitting = true">
            @csrf
            <div class="bj-booking-form">
                <section class="bj-booking-section" aria-labelledby="booking-date-title">
                    <div class="bj-booking-section__head bj-reveal">
                        <div><span>01</span><div><p>{{ $isEnglish ? 'Set your arrival' : 'Tentukan kedatangan' }}</p><h2 id="booking-date-title">{{ __('messages.visit_date') }}</h2></div></div>
                        <small><span class="material-symbols-outlined" aria-hidden="true">schedule</span>07.00–18.00 WITA</small>
                    </div>
                    <div class="bj-booking-date bj-reveal">
                        <div class="bj-booking-date__icon"><span class="material-symbols-outlined" aria-hidden="true">calendar_month</span></div>
                        <div class="bj-booking-date__copy"><small>{{ $isEnglish ? 'Selected visit' : 'Kunjungan terpilih' }}</small><strong x-text="formattedDisplayDate()"></strong><p>{{ $isEnglish ? 'Your ticket will only be valid on this date.' : 'Tiket hanya berlaku pada tanggal kunjungan ini.' }}</p></div>
                        <label class="bj-booking-date__input"><span>{{ $isEnglish ? 'Change date' : 'Ubah tanggal' }}</span><input type="date" name="tgl_kunjungan" x-model="tglKunjungan" min="{{ date('Y-m-d') }}" required></label>
                    </div>
                </section>

                <section class="bj-booking-section" aria-labelledby="booking-ticket-title">
                    <div class="bj-booking-section__head bj-reveal">
                        <div><span>02</span><div><p>{{ $isEnglish ? 'Choose your access' : 'Pilih akses kunjungan' }}</p><h2 id="booking-ticket-title">{{ __('messages.select_ticket') }}</h2></div></div>
                    </div>
                    <div class="bj-booking-tickets">
                        @forelse($tiketList as $index => $ticket)
                            @php($isVehicle = $ticket->kategori === 'kendaraan')
                            <article class="bj-booking-ticket bj-reveal" :class="qty({{ $ticket->id_tiket }}) > 0 ? 'is-selected' : ''">
                                <div class="bj-booking-ticket__stub"><span class="material-symbols-outlined" aria-hidden="true">{{ $isVehicle ? 'directions_car' : 'confirmation_number' }}</span><small>Bangkiang<br>Jaran</small></div>
                                <div class="bj-booking-ticket__copy">
                                    <div class="bj-booking-ticket__title"><h3>{{ $ticket->nama_tiket }}</h3><span>{{ $ticket->kategori ?? ($isEnglish ? 'Individual' : 'Perorangan') }}</span></div>
                                    <p>{{ $isVehicle ? ($isEnglish ? 'Official vehicle access for your selected visit date.' : 'Akses kendaraan resmi pada tanggal kunjungan terpilih.') : ($isEnglish ? 'Waterfall, natural pool, and public facility access included.' : 'Termasuk akses air terjun, kolam alami, dan fasilitas umum.') }}</p>
                                    <strong>Rp {{ number_format($ticket->harga, 0, ',', '.') }} <small>/ {{ $isVehicle ? ($isEnglish ? 'vehicle' : 'kendaraan') : ($isEnglish ? 'person' : 'orang') }}</small></strong>
                                </div>
                                <div class="bj-booking-stepper" role="group" aria-label="{{ $isEnglish ? 'Quantity for' : 'Jumlah untuk' }} {{ $ticket->nama_tiket }}">
                                    <button type="button" @click="changeQuantity({{ $ticket->id_tiket }}, -1, $el)" :disabled="qty({{ $ticket->id_tiket }}) === 0" aria-label="{{ $isEnglish ? 'Reduce' : 'Kurangi' }} {{ $ticket->nama_tiket }}"><span class="material-symbols-outlined" aria-hidden="true">remove</span></button>
                                    <input type="number" min="0" step="1" inputmode="numeric" name="tickets[{{ $ticket->id_tiket }}]" :value="qty({{ $ticket->id_tiket }})" @input="setQuantity({{ $ticket->id_tiket }}, $event.target.value)" aria-label="{{ $isEnglish ? 'Quantity for ' : 'Jumlah ' }}{{ $ticket->nama_tiket }}">
                                    <button type="button" @click="changeQuantity({{ $ticket->id_tiket }}, 1, $el)" aria-label="{{ $isEnglish ? 'Add' : 'Tambah' }} {{ $ticket->nama_tiket }}"><span class="material-symbols-outlined" aria-hidden="true">add</span></button>
                                </div>
                            </article>
                        @empty
                            <div class="bj-booking-empty bj-reveal"><span class="material-symbols-outlined" aria-hidden="true">confirmation_number</span><p>{{ __('messages.no_tickets') }}</p></div>
                        @endforelse
                    </div>
                </section>

            </div>

            <aside class="bj-booking-summary-wrap">
                <div class="bj-booking-summary bj-reveal">
                    <div class="bj-booking-summary__eyebrow"><span>{{ $isEnglish ? 'Your visit' : 'Kunjunganmu' }}</span><small>{{ $isEnglish ? 'Step 1 of 3' : 'Langkah 1 dari 3' }}</small></div>
                    <h2>{{ $isEnglish ? 'Let’s make a plan.' : 'Rencana kunjunganmu.' }}</h2>
                    <dl class="bj-booking-summary__meta">
                        <div><dt><span class="material-symbols-outlined" aria-hidden="true">location_on</span>{{ $isEnglish ? 'Location' : 'Lokasi' }}</dt><dd>Bangkiang Jaran, Gianyar</dd></div>
                        <div><dt><span class="material-symbols-outlined" aria-hidden="true">event</span>{{ $isEnglish ? 'Visit date' : 'Tanggal kunjungan' }}</dt><dd x-text="formattedDisplayDate()"></dd></div>
                    </dl>
                    <div class="bj-booking-summary__empty" x-show="selectedTickets().length === 0" x-cloak x-transition.opacity.duration.250ms><span class="material-symbols-outlined" aria-hidden="true">confirmation_number</span><p>{{ __('messages.no_tickets_selected') }}</p><small>{{ $isEnglish ? 'Choose at least one ticket to continue.' : 'Pilih sedikitnya satu tiket untuk melanjutkan.' }}</small></div>
                    <div class="bj-booking-summary__items" x-show="selectedTickets().length > 0" x-cloak x-transition.opacity.duration.250ms>
                        <template x-for="item in selectedTickets()" :key="item.id"><div><p><strong x-text="item.nama"></strong><small x-text="item.qty + ' × Rp ' + item.harga.toLocaleString('id-ID')"></small></p><span x-text="'Rp ' + (item.qty * item.harga).toLocaleString('id-ID')"></span></div></template>
                    </div>
                    <div class="bj-booking-summary__total"><p><small>{{ $isEnglish ? 'Total payment' : 'Total pembayaran' }}</small><span>{{ $isEnglish ? 'Total for the tickets selected.' : 'Total tiket yang dipilih.' }}</span></p><strong x-text="'Rp ' + total().toLocaleString('id-ID')">Rp 0</strong></div>
                    <button type="submit" class="bj-booking-submit" :disabled="selectedTickets().length === 0 || submitting" :aria-busy="submitting"><span><small>{{ $isEnglish ? 'Next step' : 'Langkah berikutnya' }}</small><strong x-show="selectedTickets().length > 0 && !submitting">{{ $isEnglish ? 'Visitor details' : 'Data pengunjung' }}</strong><strong x-show="selectedTickets().length === 0 && !submitting">{{ __('messages.select_tickets_first') }}</strong><strong x-show="submitting">{{ $isEnglish ? 'Preparing reservation...' : 'Menyiapkan reservasi...' }}</strong></span><span class="bj-booking-submit__icon material-symbols-outlined" aria-hidden="true" x-text="submitting ? 'progress_activity' : 'arrow_forward'">arrow_forward</span></button>
                    <p class="bj-booking-summary__note"><span class="material-symbols-outlined" aria-hidden="true">lock</span>{{ __('messages.no_charge_yet') }}</p>
                </div>
                @if(config('payment.contact_whatsapp'))
                    <a class="bj-booking-help bj-reveal" href="https://wa.me/{{ config('payment.contact_whatsapp') }}" target="_blank" rel="noopener noreferrer"><span class="material-symbols-outlined" aria-hidden="true">support_agent</span><span><strong>{{ $isEnglish ? 'Need booking help?' : 'Butuh bantuan pemesanan?' }}</strong><small>{{ $isEnglish ? 'Chat with the local team' : 'Hubungi tim pengelola' }}</small></span><span class="material-symbols-outlined" aria-hidden="true">north_east</span></a>
                @endif
            </aside>
        </form>
    </div>
</div>

@push('scripts')
<script>
function ticketBooking(prices, names, initialDate, preSelectedId, initialTickets, locale) {
    const tickets = {};
    Object.keys(prices).forEach(id => {
        const restored = Number.parseInt(initialTickets?.[id] || 0, 10) || 0;
        tickets[id] = restored || (preSelectedId && Number.parseInt(id, 10) === preSelectedId ? 1 : 0);
    });
    return {
        tglKunjungan: initialDate, tickets, prices, names, locale, submitting: false,
        qty(id) { return this.tickets[id] || 0; },
        setQuantity(id, value) { this.tickets[id] = Math.max(0, Math.floor(Number(value) || 0)); },
        changeQuantity(id, delta, trigger) {
            const current = this.qty(id);
            this.setQuantity(id, current + delta);
            const stepper = trigger?.closest('.bj-booking-stepper');
            if (!stepper || this.tickets[id] === current) return;
            stepper.classList.remove('is-changing');
            requestAnimationFrame(() => stepper.classList.add('is-changing'));
            window.setTimeout(() => stepper.classList.remove('is-changing'), 240);
        },
        selectedTickets() { return Object.entries(this.tickets).filter(([, quantity]) => quantity > 0).map(([id, quantity]) => ({ id: Number.parseInt(id, 10), nama: this.names[id] || 'Tiket', qty: quantity, harga: Number(this.prices[id]) || 0 })); },
        total() { return this.selectedTickets().reduce((sum, ticket) => sum + ticket.qty * ticket.harga, 0); },
        formattedDisplayDate() {
            if (!this.tglKunjungan) return '-';
            const parts = this.tglKunjungan.split('-');
            if (parts.length !== 3) return this.tglKunjungan;
            return new Date(parts[0], parts[1] - 1, parts[2]).toLocaleDateString(this.locale, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        },
    };
}
</script>
@endpush
@endsection

