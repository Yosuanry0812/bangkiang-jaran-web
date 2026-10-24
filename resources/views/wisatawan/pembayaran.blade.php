@extends('layouts.app')
@section('nav-mode', 'light')

@section('title', __('messages.step_payment') . ' | Bangkiang Jaran')

@section('content')
{{-- ── Fixed top progress bar ───────────────────────── --}}
<div class="bj-payment-progress fixed top-12 md:top-[68px] left-0 right-0 z-40 bg-white border-b border-gray-100 shadow-sm">
    <div class="bj-payment-progress__inner max-w-6xl mx-auto px-gutter py-2.5 sm:py-3">
        <div class="bj-payment-booking">
            <span class="material-symbols-outlined" aria-hidden="true">confirmation_number</span>
            <div class="min-w-0">
                <span class="bj-payment-booking__label">{{ __('messages.booking_code') }}</span>
                <span class="bj-payment-booking__code" title="{{ $pemesanan->kode_booking ?? '-' }}">{{ $pemesanan->kode_booking ?? '-' }}</span>
            </div>
        </div>
        <ol class="bj-payment-steps" aria-label="Progres pembayaran">
            <li class="is-current" id="step-1-indicator"><span id="step-1-circle">1</span><strong>{{ __('messages.step_method') }}</strong></li>
            <li class="bj-payment-steps__line" aria-hidden="true"></li>
            <li id="step-2-indicator"><span id="step-2-circle">2</span><strong id="step-2-label">{{ __('messages.step_upload') }}</strong></li>
            <li class="bj-payment-steps__line" aria-hidden="true"></li>
            <li><span>3</span><strong>{{ __('messages.step_done') }}</strong></li>
        </ol>
    </div>
</div>

{{-- ── Page body ─────────────────────────────────────── --}}
<div class="bj-payment-flow min-h-screen pt-32 md:pt-36 pb-16 px-gutter bj-transaction-page" data-booking-watch="{{ route('wisatawan.pemesanan.status', $pemesanan->id_pemesanan) }}" data-status-token="{{ $pemesanan->statusToken() }}" data-booking-destination="{{ route('wisatawan.pemesanan.detail', $pemesanan->id_pemesanan) }}">
    <div class="bj-payment-shell max-w-5xl mx-auto">
        <header class="bj-payment-intro"><h1>{{ app()->getLocale() === 'en' ? 'One step closer to the waterfall.' : 'Selangkah lagi menuju air terjun.' }}</h1><p>{{ app()->getLocale() === 'en' ? 'Choose a payment method, then upload your receipt. The team will verify your payment before issuing your e-ticket.' : 'Pilih metode pembayaran, lalu unggah buktinya. Pengelola akan memverifikasi pembayaran sebelum e-ticket diterbitkan.' }}</p></header>
        <div class="bj-payment-layout grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- ── LEFT: Steps ─────────────────────────── --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Step 1: Pilih Metode --}}
                <div class="bj-payment-panel bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" id="panel-step-1">
                    <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100 bg-forest">
                        <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">1</div>
                        <h2 class="font-serif text-lg text-white">{{ __('messages.choose_payment_method') }}</h2>
                    </div>
                    <div class="p-6 space-y-6">

                        @if(session('error'))
                        <p role="alert" class="border border-red-200 bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</p>
                        @endif
                        @if($errors->any())
                        <div role="alert" class="border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                        </div>
                        @endif
                        @if($metodeBayar === [])
                        <div class="bj-payment-catalog" aria-label="{{ app()->getLocale() === 'en' ? 'Payment methods' : 'Pilihan metode pembayaran' }}">@foreach(config('payment.catalog', []) as $method)<div class="bj-payment-catalog__method" aria-disabled="true"><img src="{{ asset('images/payments/' . $method['logo']) }}" alt="" width="48" height="36"><span><strong>{{ $method['name'] }}</strong><small>{{ app()->getLocale() === 'en' ? 'Not activated yet' : 'Belum diaktifkan' }}</small></span></div>@endforeach</div>
                        <p role="status">{{ app()->getLocale() === 'en' ? 'Payment methods are not available yet.' : 'Metode pembayaran belum tersedia.' }}</p>
                        @else
                        @if(collect($metodeBayar)->contains(fn ($method) => $method['is_trial'] ?? false))
                        <p class="bj-payment-trial-note">{{ app()->getLocale() === 'en' ? 'Trial mode: choose a method and upload a sample receipt. No transfer is needed.' : 'Mode uji coba: pilih metode dan unggah bukti contoh. Tidak perlu melakukan transfer.' }}</p>
                        @endif

                        {{-- Bank Transfer --}}
                        @if(collect($metodeBayar)->contains('type', 'bank'))
                        <div class="bj-payment-method-group">
                            <p class="font-sans text-[10px] font-semibold text-stone uppercase tracking-[0.15em] mb-3">{{ __('messages.bank_transfer') }}</p>
                            <div class="space-y-2">
                                @foreach($metodeBayar as $m)
                                @if($m['type'] === 'bank')
                                <div class="bj-payment-method border border-gray-100 rounded-xl overflow-hidden transition-all duration-200" id="card-{{ $m['id'] }}">
                                    <button type="button"
                                            data-id="{{ $m['id'] }}" data-name="{{ $m['name'] }}" aria-expanded="false" aria-controls="detail-{{ $m['id'] }}"
                                            onclick="pilihMetode(this.dataset.id, this.dataset.name)"
                                            class="w-full flex items-center justify-between px-4 py-3.5 hover:bg-gray-50 transition-colors text-left">
                                        <div class="flex items-center gap-3">
                                            <div class="w-11 h-11 rounded-xl bg-white border border-gray-200 flex items-center justify-center flex-shrink-0 p-1.5">
                                                <img src="{{ asset('images/payments/' . $m['logo']) }}" alt="" class="w-full h-full object-contain">
                                            </div>
                                            <span class="font-sans text-sm font-medium text-ink">{{ $m['name'] }}</span>
                                        </div>
                                        <span class="material-symbols-outlined text-pebble text-base transition-transform duration-200" id="icon-{{ $m['id'] }}">expand_more</span>
                                    </button>
                                    <div class="hidden px-4 pb-4 pt-0 bg-gray-50 border-t border-gray-100" id="detail-{{ $m['id'] }}">
                                        <div class="pt-4 flex items-center justify-between">
                                            @include('partials.payment-recipient', ['method' => $m])
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- E-Wallet --}}
                        @if(collect($metodeBayar)->contains('type', 'ewallet'))
                        <div class="bj-payment-method-group">
                            <p class="font-sans text-[10px] font-semibold text-stone uppercase tracking-[0.15em] mb-3">{{ __('messages.e_wallet') }}</p>
                            <div class="space-y-2">
                                @foreach($metodeBayar as $m)
                                @if($m['type'] === 'ewallet')
                                <div class="bj-payment-method border border-gray-100 rounded-xl overflow-hidden transition-all duration-200" id="card-{{ $m['id'] }}">
                                    <button type="button"
                                            data-id="{{ $m['id'] }}" data-name="{{ $m['name'] }}" aria-expanded="false" aria-controls="detail-{{ $m['id'] }}"
                                            onclick="pilihMetode(this.dataset.id, this.dataset.name)"
                                            class="w-full flex items-center justify-between px-4 py-3.5 hover:bg-gray-50 transition-colors text-left">
                                        <div class="flex items-center gap-3">
                                            <div class="w-11 h-11 rounded-xl bg-white border border-gray-200 flex items-center justify-center flex-shrink-0 p-1.5">
                                                <img src="{{ asset('images/payments/' . $m['logo']) }}" alt="" class="w-full h-full object-contain">
                                            </div>
                                            <span class="font-sans text-sm font-medium text-ink">{{ $m['name'] }}</span>
                                        </div>
                                        <span class="material-symbols-outlined text-pebble text-base transition-transform duration-200" id="icon-{{ $m['id'] }}">expand_more</span>
                                    </button>
                                    <div class="hidden px-4 pb-4 pt-0 bg-gray-50 border-t border-gray-100" id="detail-{{ $m['id'] }}">
                                        <div class="pt-4 flex items-center justify-between">
                                            @include('partials.payment-recipient', ['method' => $m])
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Continue button --}}
                        <button type="button" id="btn-lanjutkan" disabled
                                class="bj-payment-next w-full flex items-center justify-center gap-2 bg-forest text-white font-sans text-sm font-semibold py-3.5 rounded-xl transition-all opacity-40 cursor-not-allowed">
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            {{ __('messages.continue_to_upload') }}
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Step 2: Upload --}}
                <div class="bj-payment-panel bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden transition-all" id="panel-step-2">
                    <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100" id="step-2-header" style="background:#1B3A2D;">
                        <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">2</div>
                        <h2 class="font-serif text-lg text-white">{{ __('messages.upload_payment') }}</h2>
                    </div>
                    <div class="p-6">
                        <form method="POST"
                              action="{{ route('wisatawan.pembayaran.store', $pemesanan->id_pemesanan) }}"
                              enctype="multipart/form-data"
                              id="form-pembayaran"
                              class="space-y-5">
                            @csrf
                            <input type="hidden" name="metode" id="input-metode" value="">

                            {{-- Selected method badge --}}
                            <div id="selected-method-badge" class="hidden items-center gap-3 bg-forest/6 border border-forest/15 rounded-xl px-4 py-3">
                                <div class="w-7 h-7 rounded-full bg-forest flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-white text-sm icon-fill">check</span>
                                </div>
                                <div>
                                    <p class="font-sans text-xs text-stone">{{ __('messages.method_selected') }}</p>
                                    <p class="font-sans text-sm font-semibold text-forest" id="badge-method-name"></p>
                                </div>
                            </div>

                            <div class="bj-payment-file-field">
                                <label for="bukti_bayar">{{ app()->getLocale() === 'en' ? 'Choose payment proof' : 'Pilih bukti pembayaran' }}</label>
                                <div class="bj-payment-file-picker">
                                    <span class="bj-payment-file-picker__button" aria-hidden="true">{{ app()->getLocale() === 'en' ? 'Choose File' : 'Pilih File' }}</span>
                                    <span id="payment-selected-file" class="bj-payment-file-picker__name" aria-hidden="true">{{ app()->getLocale() === 'en' ? 'No file selected' : 'Belum ada file dipilih' }}</span>
                                    <input type="file" name="bukti_bayar" id="bukti_bayar" aria-describedby="payment-file-help"
                                           accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf" required
                                           class="font-sans bj-payment-file-input">
                                </div>
                                <p id="payment-file-help">{{ app()->getLocale() === 'en' ? 'Select a JPG, PNG, or PDF from your device. Maximum size: 5 MB.' : 'Pilih JPG, PNG, atau PDF dari perangkat Anda. Ukuran maksimal: 5 MB.' }}</p>
                            </div>

                            <div class="bj-payment-upload relative border border-gray-200 rounded-2xl flex items-center justify-center overflow-hidden">
                                <div class="flex flex-col items-center text-center p-6 md:p-8" id="upload-prompt">
                                    <span class="material-symbols-outlined text-forest text-3xl mb-3" aria-hidden="true">description</span>
                                    <p class="font-sans text-sm font-semibold text-ink mb-1">{{ app()->getLocale() === 'en' ? 'No payment proof selected' : 'Bukti pembayaran belum dipilih' }}</p>
                                    <p class="font-sans text-sm text-stone">{{ app()->getLocale() === 'en' ? 'Your selected file will appear here.' : 'Pratinjau file yang Anda pilih akan tampil di sini.' }}</p>
                                </div>

                                {{-- Loading state --}}
                                <div class="hidden absolute inset-0 w-full h-full bg-white z-20 flex flex-col items-center justify-center p-6" id="upload-loading" role="status">
                                    <div class="w-16 h-16 rounded-full border-4 border-gray-100 border-t-forest animate-spin mb-4"></div>
                                    <p class="font-sans text-sm font-medium text-ink">{{ __('messages.processing_file') }}</p>
                                    <p class="font-sans text-xs text-stone mt-1">{{ __('messages.please_wait') }}</p>
                                </div>

                                {{-- Preview container --}}
                                <div class="hidden absolute inset-0 w-full h-full bg-gray-50 z-20 rounded-2xl overflow-hidden" id="upload-preview-container" aria-live="polite">
                                    {{-- Image preview (contain — full image visible) --}}
                                    <div class="relative w-full h-full flex items-center justify-center bg-gray-50" id="image-preview-wrapper" style="display:none;">
                                        <img alt="Preview"
                                             class="max-w-full max-h-full w-auto h-auto object-contain"
                                             id="upload-preview">

                                        {{-- Tombol action di pojok kanan atas --}}
                                        <div class="absolute top-3 right-3 flex items-center gap-2 z-30">
                                            <button class="flex items-center gap-1.5 bg-white/90 backdrop-blur-sm text-ink hover:bg-white border border-gray-200/60 px-3 py-2 rounded-xl text-xs font-medium transition-all shadow-sm hover:shadow-md"
                                                    id="change-file" type="button">
                                                <span class="material-symbols-outlined text-sm">edit</span>
                                                <span class="hidden sm:inline">{{ __('messages.change') }}</span>
                                            </button>
                                            <button class="flex items-center gap-1.5 bg-white/90 backdrop-blur-sm text-red-600 hover:bg-white border border-gray-200/60 px-3 py-2 rounded-xl text-xs font-medium transition-all shadow-sm hover:shadow-md"
                                                    id="remove-file" type="button">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                                <span class="hidden sm:inline">{{ __('messages.delete') }}</span>
                                            </button>
                                        </div>

                                        {{-- Info file di pojok kiri bawah --}}
                                        <div class="absolute bottom-0 left-0 right-0 p-3 md:p-4 z-30">
                                            <div class="inline-flex items-center gap-2.5 bg-white/90 backdrop-blur-sm rounded-xl px-3.5 py-2.5 shadow-sm border border-gray-100/60">
                                                <span class="material-symbols-outlined text-forest text-sm flex-shrink-0">check_circle</span>
                                                <div class="min-w-0">
                                                    <p class="font-sans text-xs font-medium text-ink truncate max-w-[180px] sm:max-w-[260px]" id="file-name"></p>
                                                    <p class="font-sans text-[11px] text-stone" id="file-size"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- PDF preview --}}
                                    <div class="hidden w-full h-full flex-col items-center justify-center bg-gradient-to-br from-red-50 to-white p-6" id="pdf-preview-wrapper">
                                        <div class="w-20 h-20 rounded-2xl bg-red-100 flex items-center justify-center mb-4 shadow-inner">
                                            <span class="material-symbols-outlined text-red-500" style="font-size:48px;">picture_as_pdf</span>
                                        </div>
                                        <p class="font-sans text-xs font-medium text-stone mb-3">{{ __('messages.pdf_document') }}</p>
                                        <div class="flex items-center gap-2.5 bg-white border border-gray-200 rounded-xl px-4 py-3 shadow-sm w-full max-w-xs">
                                            <span class="material-symbols-outlined text-forest text-sm flex-shrink-0">check_circle</span>
                                            <div class="min-w-0 flex-1">
                                                <p class="font-sans text-xs font-semibold text-ink truncate" id="pdf-file-name"></p>
                                                <p class="font-sans text-[11px] text-stone" id="pdf-file-size"></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 mt-4">
                                            <button class="flex items-center gap-1.5 text-stone bg-white border border-gray-200 hover:border-gray-300 hover:bg-gray-50 px-3.5 py-2 rounded-xl text-xs font-medium transition-colors shadow-sm"
                                                    id="change-file-pdf" type="button">
                                                <span class="material-symbols-outlined text-sm">edit</span>
                                                {{ __('messages.change') }}
                                            </button>
                                            <button class="flex items-center gap-1.5 text-red-600 bg-white border border-red-200 hover:bg-red-50 px-3.5 py-2 rounded-xl text-xs font-medium transition-colors shadow-sm"
                                                    id="remove-file-pdf" type="button">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                                {{ __('messages.delete') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Error state --}}
                                <div class="hidden absolute inset-0 w-full h-full bg-red-50 z-20 flex flex-col items-center justify-center p-6 text-center" id="upload-error" role="alert">
                                    <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mb-4">
                                        <span class="material-symbols-outlined text-red-500 text-3xl">error</span>
                                    </div>
                                    <p class="font-sans text-sm font-semibold text-red-700 mb-1" id="error-title">{{ __('messages.invalid_file') }}</p>
                                    <p class="font-sans text-xs text-red-600 mb-4 max-w-xs" id="error-message"></p>
                                    <button class="flex items-center gap-1.5 text-red-700 border border-red-300 hover:bg-red-100 px-4 py-2 rounded-xl text-xs font-medium transition-colors"
                                            id="retry-upload" type="button">
                                        <span class="material-symbols-outlined text-sm">refresh</span>
                                        {{ __('messages.retry') }}
                                    </button>
                                </div>
                            </div>

                            <button type="submit" id="submit-btn" disabled
                                    class="bj-payment-submit w-full flex items-center justify-center gap-2 bg-forest text-white font-sans text-sm font-semibold py-3.5 rounded-xl hover:bg-leaf transition-colors">
                                <span class="material-symbols-outlined text-sm">send</span>
                                {{ __('messages.submit_payment_proof') }}
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Info verifikasi --}}
                <div class="flex items-start gap-3 bg-amber-50 border border-amber-200/60 rounded-xl px-4 py-3.5">
                    <span class="material-symbols-outlined text-amber-500 text-base mt-0.5 flex-shrink-0">info</span>
                    <div>
                        <p class="font-sans text-sm font-medium text-ink">{{ __('messages.verification_info') }}</p>
                        <p class="font-sans text-xs text-stone mt-0.5 leading-relaxed">{{ __('messages.verification_info_desc') }}</p>
                    </div>
                </div>

            </div>

            {{-- ── RIGHT: Summary sidebar ───────────────── --}}
            <div class="bj-payment-aside lg:col-span-1">
                <div class="sticky top-20 space-y-4">

                    {{-- Order summary card --}}
                    <div class="bj-payment-summary bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="bg-forest px-5 py-5">
                            <p class="font-sans text-[10px] uppercase tracking-[0.15em] text-white/40 mb-1">{{ __('messages.total_payment') }}</p>
                            <p class="font-serif text-3xl text-white">
                                Rp{{ number_format($pemesanan->total_harga ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="px-5 py-5 space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <span class="font-sans text-xs text-stone flex-shrink-0">{{ __('messages.booking_code') }}</span>
                                <span class="font-sans text-xs font-semibold text-forest text-right break-all">{{ $pemesanan->kode_booking ?? '-' }}</span>
                            </div>
                            <div class="h-px bg-gray-100"></div>
                            <div class="flex items-start justify-between gap-3">
                                <span class="font-sans text-xs text-stone flex-shrink-0">{{ __('messages.ticket') }}</span>
                                <span class="font-sans text-xs font-semibold text-ink text-right text-right">
                                    @php
                                        $tSummary = $pemesanan->detailPemesanan->groupBy('nama_tiket')->map(fn($g) => $g->count() . ' ' . $g->first()->nama_tiket)->implode(', ');
                                    @endphp
                                    {{ $tSummary ?: ($pemesanan->tiket->nama_tiket ?? '-') }}
                                </span>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <span class="font-sans text-xs text-stone flex-shrink-0">{{ __('messages.quantity') }}</span>
                                <span class="font-sans text-xs font-semibold text-ink">{{ $pemesanan->detailPemesanan->count() }} {{ __('messages.ticket_unit') }}</span>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <span class="font-sans text-xs text-stone flex-shrink-0">{{ __('messages.date') }}</span>
                                <span class="font-sans text-xs font-semibold text-ink text-right">
                                    {{ isset($pemesanan->tgl_kunjungan) ? \Carbon\Carbon::parse($pemesanan->tgl_kunjungan)->isoFormat('D MMMM YYYY') : '-' }}
                                </span>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <span class="font-sans text-xs text-stone flex-shrink-0">{{ __('messages.th_status') }}</span>
                                @include('partials.booking-status', ['booking' => $pemesanan])
                            </div>
                        </div>
                    </div>

                    {{-- Help card --}}
                    <div class="bj-payment-help bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-5">
                        <div class="flex items-center gap-2.5 mb-3">
                            <div class="w-8 h-8 rounded-lg bg-forest/8 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-forest text-sm">support_agent</span>
                            </div>
                            <p class="font-sans text-sm font-medium text-ink">{{ __('messages.need_help') }}</p>
                        </div>
                        <p class="font-sans text-xs text-stone leading-relaxed mb-4">{{ __('messages.help_payment_desc') }}</p>
                        @if(config('payment.contact_whatsapp'))
                        <a href="https://wa.me/{{ config('payment.contact_whatsapp') }}" target="_blank" rel="noopener noreferrer"
                           class="flex items-center justify-center gap-2 w-full font-sans text-xs font-semibold text-white bg-[#25D366] hover:bg-[#1dbd5a] transition-colors px-4 py-2.5 rounded-xl">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            {{ __('messages.whatsapp_contact') }}
                        </a>
                        @else
                        <a href="{{ route('wisatawan.pemesanan.detail', $pemesanan->id_pemesanan) }}" class="bj-text-link">Kembali ke detail pesanan</a>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>
@endpush

@push('scripts')
<script>
    let activeMetode = null;

    function pilihMetode(id, nama, nomor, atasNama) {
        document.querySelectorAll('.bj-payment-method > button').forEach(button => button.setAttribute('aria-expanded', 'false'));
        document.querySelectorAll('[id^="detail-"]').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('[id^="card-"]').forEach(el => {
            el.classList.remove('border-forest', 'border-2', 'bg-forest/3');
            el.classList.add('border-gray-100');
        });
        document.querySelectorAll('[id^="icon-"]').forEach(el => { el.style.transform = ''; });

        if (activeMetode === id) {
            activeMetode = null;
            document.getElementById('selected-method-badge')?.classList.add('hidden');
            document.getElementById('selected-method-badge')?.classList.remove('flex');
            document.getElementById('input-metode').value = '';
            updateLanjutkanBtn();
            return;
        }

        activeMetode = id;
        document.querySelector('#card-' + id + ' > button')?.setAttribute('aria-expanded', 'true');
        const detail = document.getElementById('detail-' + id);
        const card   = document.getElementById('card-' + id);
        const icon   = document.getElementById('icon-' + id);
        if (detail) detail.classList.remove('hidden');
        if (card)   { card.classList.remove('border-gray-100'); card.classList.add('border-forest', 'border-2'); }
        if (icon)   icon.style.transform = 'rotate(180deg)';

        document.getElementById('input-metode').value = id;

        const badge = document.getElementById('selected-method-badge');
        const badgeName = document.getElementById('badge-method-name');
        if (badge && badgeName) {
            badge.classList.remove('hidden');
            badge.classList.add('flex');
            badgeName.textContent = nama;
        }
        updateLanjutkanBtn();
    }

    function salin(text, iconId) {
        if (!text) return;
        navigator.clipboard.writeText(text).then(() => {
            const icon = document.getElementById(iconId);
            if (icon) { icon.textContent = 'check'; setTimeout(() => { icon.textContent = 'content_copy'; }, 2000); }
        }).catch(() => {
            const el = document.createElement('textarea');
            el.value = text; document.body.appendChild(el); el.select(); document.execCommand('copy'); document.body.removeChild(el);
        });
    }

    function updateLanjutkanBtn() {
        const submitBtn = document.getElementById('submit-btn');
        if (submitBtn) submitBtn.disabled = !activeMetode;
        const btn = document.getElementById('btn-lanjutkan');
        if (!btn) return;
        if (activeMetode) {
            btn.disabled = false;
            btn.classList.remove('opacity-40', 'cursor-not-allowed');
        } else {
            btn.disabled = true;
            btn.classList.add('opacity-40', 'cursor-not-allowed');
        }
    }

    const btnLanjutkan = document.getElementById('btn-lanjutkan');
    if (btnLanjutkan) {
        btnLanjutkan.addEventListener('click', () => {
            if (!activeMetode) return;
            const panel2  = document.getElementById('panel-step-2');
            const header2 = document.getElementById('step-2-header');
            const step1 = document.getElementById('step-1-indicator');
            const step2 = document.getElementById('step-2-indicator');
            header2.style.background = '#1B3A2D';
            const c2 = document.getElementById('step-2-circle');
            const l2 = document.getElementById('step-2-label');
            if (step1) { step1.classList.remove('is-current'); step1.classList.add('is-complete'); }
            if (step2) step2.classList.add('is-current');
            if (c2) { c2.classList.remove('bg-gray-100', 'text-gray-400'); c2.classList.add('bg-forest', 'text-white'); }
            if (l2) { l2.classList.remove('text-gray-400'); l2.classList.add('font-semibold', 'text-forest'); }
            panel2.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'start' });
            document.getElementById('bukti_bayar')?.focus({ preventScroll: true });
        });
    }

    // ── Upload zone elements ───────────────────────────────────────────────────
    const fileInput     = document.getElementById('bukti_bayar');
    const promptEl      = document.getElementById('upload-prompt');
    const loadingEl     = document.getElementById('upload-loading');
    const previewBox    = document.getElementById('upload-preview-container');
    const previewImg    = document.getElementById('upload-preview');
    const imgWrapper    = document.getElementById('image-preview-wrapper');
    const pdfWrapper    = document.getElementById('pdf-preview-wrapper');
    const fileNameEl    = document.getElementById('file-name');
    const fileSizeEl    = document.getElementById('file-size');
    const errorBox      = document.getElementById('upload-error');
    const errorTitle    = document.getElementById('error-title');
    const errorMsg      = document.getElementById('error-message');
    const removeBtn     = document.getElementById('remove-file');
    const changeBtn     = document.getElementById('change-file');
    const retryBtn      = document.getElementById('retry-upload');
    const pdfFileNameEl = document.getElementById('pdf-file-name');
    const pdfFileSizeEl = document.getElementById('pdf-file-size');
    const changePdfBtn  = document.getElementById('change-file-pdf');
    const removePdfBtn  = document.getElementById('remove-file-pdf');

    const MAX_SIZE_MB   = 5;
    const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];

    const selectedFileLabel = document.getElementById('payment-selected-file');
    const emptyFileLabel = selectedFileLabel?.textContent;

    // The primary picker receives native clicks directly, without interception.
    function changePaymentFile() {
        fileInput?.click();
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function showState(state) {
        const all = [promptEl, loadingEl, previewBox, errorBox];
        all.forEach(el => { if (el) { el.classList.add('hidden'); el.classList.remove('flex'); } });
        const target = { prompt: promptEl, loading: loadingEl, preview: previewBox, error: errorBox }[state];
        if (target) { target.classList.remove('hidden'); target.classList.add('flex'); }
    }

    function resetUpload() {
        fileInput.value = '';
        if (selectedFileLabel) selectedFileLabel.textContent = emptyFileLabel;
        if (previewImg) previewImg.removeAttribute('src');
        if (imgWrapper) imgWrapper.style.display = 'none';
        if (pdfWrapper) { pdfWrapper.classList.add('hidden'); pdfWrapper.classList.remove('flex'); }
        if (pdfFileNameEl) pdfFileNameEl.textContent = '';
        if (pdfFileSizeEl) pdfFileSizeEl.textContent = '';
        showState('prompt');
        fileInput.focus({ preventScroll: true });
    }

    function showError(title, message) {
        if (errorTitle) errorTitle.textContent = title;
        if (errorMsg)   errorMsg.textContent   = message;
        showState('error');
        fileInput.value = '';
        if (selectedFileLabel) selectedFileLabel.textContent = emptyFileLabel;
    }

    function handleFiles(files) {
        if (!files || files.length === 0) { resetUpload(); return; }
        const file = files[0];

        // Some device file providers return an empty MIME type.
        const fileType = file.type || (/\.jpe?g$/i.test(file.name) ? 'image/jpeg'
            : /\.png$/i.test(file.name) ? 'image/png'
            : /\.pdf$/i.test(file.name) ? 'application/pdf' : '');
        if (!ALLOWED_TYPES.includes(fileType)) {
            showError('{{ __('messages.err_format') }}', '{{ __('messages.err_format_desc') }}');
            return;
        }
        // Validate size
        if (file.size > MAX_SIZE_MB * 1024 * 1024) {
            showError('{{ __('messages.err_size') }}', `{{ __('messages.err_size_desc') }}${formatFileSize(file.size)}.`.replace(':mb', MAX_SIZE_MB));
            return;
        }

        showState('loading');
        if (selectedFileLabel) selectedFileLabel.textContent = file.name;

        const reader = new FileReader();
        reader.onload = (e) => {
            if (fileInput.files[0] !== file) return;
            // Populate meta
            if (fileNameEl) fileNameEl.textContent = file.name;
            if (fileSizeEl) fileSizeEl.textContent  = formatFileSize(file.size);

            if (fileType.startsWith('image/')) {
                // Image preview — full-bleed
                previewImg.src = e.target.result;
                if (imgWrapper) imgWrapper.style.display = '';
                if (pdfWrapper) { pdfWrapper.classList.add('hidden'); pdfWrapper.classList.remove('flex'); }
            } else {
                // PDF preview
                if (imgWrapper) imgWrapper.style.display = 'none';
                if (pdfWrapper) { pdfWrapper.classList.remove('hidden'); pdfWrapper.classList.add('flex'); }
                if (pdfFileNameEl) pdfFileNameEl.textContent = file.name;
                if (pdfFileSizeEl) pdfFileSizeEl.textContent = formatFileSize(file.size);
            }

            // Small delay to let the browser render
            showState('preview');
        };
        reader.onerror = () => {
            if (fileInput.files[0] === file) showError('{{ __('messages.err_read') }}', '{{ __('messages.err_read_desc') }}');
        };
        reader.readAsDataURL(file);
    }

    // Restore file input clickability from inside the change button
    if (changeBtn) {
        changeBtn.addEventListener('click', e => {
            e.stopPropagation();
            changePaymentFile();
        });
    }
    if (removeBtn) {
        removeBtn.addEventListener('click', e => { e.stopPropagation(); resetUpload(); });
    }
    if (retryBtn) {
        retryBtn.addEventListener('click', e => { e.stopPropagation(); changePaymentFile(); });
    }
    // PDF action buttons
    if (changePdfBtn) {
        changePdfBtn.addEventListener('click', e => {
            e.stopPropagation();
            changePaymentFile();
        });
    }
    if (removePdfBtn) {
        removePdfBtn.addEventListener('click', e => { e.stopPropagation(); resetUpload(); });
    }
    if (fileInput) fileInput.addEventListener('change', e => handleFiles(e.target.files));

</script>
@endpush
@endsection
