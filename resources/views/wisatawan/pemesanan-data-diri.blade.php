@extends('layouts.app')
@section('nav-mode', 'light')
@section('title', __('messages.visitor_data_title') . ' — Bangkiang Jaran')

@section('content')
@php
    $isEnglish = app()->getLocale() === 'en';
@endphp
<div class="bj-visitor" x-data="{ submitting: false }">
    <header class="bj-visitor-intro" aria-labelledby="visitor-title">
        <div class="bj-visitor-wrap bj-visitor-intro__inner">
            <div class="bj-visitor-intro__copy">
                <p class="bj-visitor-kicker bj-reveal"><span aria-hidden="true"></span>{{ $isEnglish ? 'Visitor details' : 'Data pengunjung' }}</p>
                <h1 id="visitor-title" class="bj-reveal" data-bj-split>{{ $isEnglish ? 'Complete visitor details.' : 'Lengkapi data pengunjung.' }}</h1>
                <p class="bj-visitor-intro__lead bj-reveal">{{ $isEnglish ? 'Each visitor receives an individual entry ticket. Complete the details below so your arrival is smooth and personal.' : 'Setiap pengunjung akan menerima tiket masuk pribadi. Lengkapi data berikut agar kedatangan Anda berjalan lancar.' }}</p>
            </div>
            <div class="bj-visitor-intro__folio bj-reveal" aria-label="{{ $isEnglish ? 'Reservation details' : 'Detail reservasi' }}">
                <span>{{ $isEnglish ? 'Visit date' : 'Tanggal kunjungan' }}</span>
                <strong>{{ \Carbon\Carbon::parse($flow['tgl_kunjungan'])->isoFormat('D MMMM YYYY') }}</strong>
                <small>{{ $isEnglish ? 'Booking reference' : 'Referensi booking' }} · {{ $flow['kode_booking'] }}</small>
            </div>
        </div>
    </header>

    <main class="bj-visitor-main">
        <div class="bj-visitor-wrap">
            @if ($errors->any())
                <div class="bj-visitor-alert bj-reveal" role="alert"><span class="material-symbols-outlined" aria-hidden="true">error</span><div><strong>{{ $isEnglish ? 'Please review the marked details.' : 'Periksa kembali data yang ditandai.' }}</strong><p>{{ $isEnglish ? 'Complete each required field before continuing to payment.' : 'Lengkapi setiap kolom wajib sebelum melanjutkan ke pembayaran.' }}</p></div></div>
            @endif

            <div class="bj-visitor-layout">
                <form method="POST" action="{{ route('wisatawan.pemesanan.store-data-diri') }}" class="bj-visitor-form" @submit="submitting = true">
                    @csrf
                    <section class="bj-visitor-manifest bj-reveal" aria-labelledby="manifest-title">
                        <header class="bj-visitor-manifest__head"><div><p>{{ $isEnglish ? 'Step 02 · visitor details' : 'Langkah 02 · data pengunjung' }}</p><h2 id="manifest-title">{{ __('messages.visitor_data_title') }}</h2></div><span class="material-symbols-outlined" aria-hidden="true">badge</span></header>
                        <p class="bj-visitor-manifest__intro">{{ __('messages.visitor_data_desc') }}</p>
                        <div class="bj-visitor-entries">
                            @php($currentGroup = null)
                            @foreach($flow['detailRows'] as $i => $row)
                                @php($groupKey = $row['nama_tiket'] . '_' . $row['id_tiket'])
                                @if($currentGroup !== $groupKey)
                                    @php($currentGroup = $groupKey)
                                    <div class="bj-visitor-group"><span class="material-symbols-outlined" aria-hidden="true">{{ $row['kategori'] === 'kendaraan' ? 'directions_car' : 'confirmation_number' }}</span><p><strong>{{ $row['nama_tiket'] }}</strong><small>{{ $row['kategori'] === 'kendaraan' ? __('messages.input_vehicle_plate') : __('messages.input_full_name') }}</small></p></div>
                                @endif
                                <fieldset class="bj-visitor-entry">
                                    <legend><span>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>{{ $row['kategori'] === 'kendaraan' ? ($isEnglish ? 'Vehicle access' : 'Akses kendaraan') : ($isEnglish ? 'Visitor details' : 'Data pengunjung') }}</legend>
                                    @if($row['kategori'] === 'kendaraan')
                                        <div class="bj-visitor-field"><label for="plat_{{ $i }}">{{ __('messages.vehicle_plate') }} <b aria-hidden="true">*</b></label><input id="plat_{{ $i }}" type="text" name="plat_{{ $i }}" value="{{ old('plat_' . $i) }}" placeholder="{{ __('messages.plate_placeholder') }}" autocomplete="off" class="uppercase" aria-invalid="@error('plat_' . $i) true @else false @enderror" @error('plat_' . $i) aria-describedby="plat_{{ $i }}_error" @enderror required>@error('plat_' . $i)<p id="plat_{{ $i }}_error" class="bj-visitor-field__error"><span class="material-symbols-outlined" aria-hidden="true">error</span>{{ $message }}</p>@enderror</div>
                                    @else
                                        <div class="bj-visitor-fields">
                                            <div class="bj-visitor-field"><label for="nama_{{ $i }}">{{ __('messages.full_name') }} <b aria-hidden="true">*</b></label><input id="nama_{{ $i }}" type="text" name="nama_{{ $i }}" value="{{ old('nama_' . $i) }}" placeholder="{{ __('messages.name_placeholder') }}" autocomplete="name" aria-invalid="@error('nama_' . $i) true @else false @enderror" @error('nama_' . $i) aria-describedby="nama_{{ $i }}_error" @enderror required>@error('nama_' . $i)<p id="nama_{{ $i }}_error" class="bj-visitor-field__error"><span class="material-symbols-outlined" aria-hidden="true">error</span>{{ $message }}</p>@enderror</div>
                                            <div class="bj-visitor-field"><label for="gender_{{ $i }}">{{ __('messages.gender') }} <b aria-hidden="true">*</b></label><select id="gender_{{ $i }}" name="gender_{{ $i }}" autocomplete="sex" aria-invalid="@error('gender_' . $i) true @else false @enderror" @error('gender_' . $i) aria-describedby="gender_{{ $i }}_error" @enderror required><option value="" disabled {{ old('gender_' . $i) ? '' : 'selected' }}>{{ __('messages.select_gender') }}</option><option value="L" {{ old('gender_' . $i) === 'L' ? 'selected' : '' }}>{{ __('messages.male') }}</option><option value="P" {{ old('gender_' . $i) === 'P' ? 'selected' : '' }}>{{ __('messages.female') }}</option></select>@error('gender_' . $i)<p id="gender_{{ $i }}_error" class="bj-visitor-field__error"><span class="material-symbols-outlined" aria-hidden="true">error</span>{{ $message }}</p>@enderror</div>
                                        </div>
                                    @endif
                                </fieldset>
                            @endforeach
                        </div>
                    </section>
                    <div class="bj-visitor-actions bj-reveal"><a href="{{ route('wisatawan.pemesanan.create') }}" class="bj-visitor-back"><span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>{{ __('messages.back') }}</a><button type="submit" class="bj-visitor-submit" :disabled="submitting" :aria-busy="submitting"><span><small>{{ $isEnglish ? 'Final step' : 'Langkah terakhir' }}</small><strong x-show="!submitting">{{ __('messages.continue_to_payment') }}</strong><strong x-show="submitting">{{ $isEnglish ? 'Preparing payment...' : 'Menyiapkan pembayaran...' }}</strong></span><span class="material-symbols-outlined" aria-hidden="true" x-text="submitting ? 'progress_activity' : 'arrow_forward'">arrow_forward</span></button></div>
                </form>
                <aside class="bj-visitor-aside">
                    <section class="bj-visitor-summary bj-reveal" aria-labelledby="summary-title"><header><p>{{ __('messages.total') }}</p><strong>Rp{{ number_format($flow['total_harga'], 0, ',', '.') }}</strong></header><div class="bj-visitor-summary__body"><div class="bj-visitor-summary__title"><p id="summary-title">{{ __('messages.ticket_summary') }}</p><span>{{ count($flow['detailRows']) }} {{ $isEnglish ? 'entries' : 'entri' }}</span></div><ul>@foreach($flow['detail'] as $d)<li><p><strong>{{ $d['nama'] }}</strong><small>×{{ $d['jumlah'] }}</small></p><span>Rp{{ number_format($d['subtotal'], 0, ',', '.') }}</span></li>@endforeach</ul><dl><div><dt><span class="material-symbols-outlined" aria-hidden="true">calendar_month</span>{{ __('messages.visit_date') }}</dt><dd>{{ \Carbon\Carbon::parse($flow['tgl_kunjungan'])->isoFormat('D MMMM YYYY') }}</dd></div><div><dt><span class="material-symbols-outlined" aria-hidden="true">confirmation_number</span>{{ __('messages.booking_code') }}</dt><dd>{{ $flow['kode_booking'] }}</dd></div></dl></div></section>
                    <div class="bj-visitor-note bj-reveal"><span class="material-symbols-outlined" aria-hidden="true">info</span><p>{{ __('messages.visitor_data_note') }}</p></div>
                </aside>
            </div>
        </div>
    </main>
</div>
@endsection

