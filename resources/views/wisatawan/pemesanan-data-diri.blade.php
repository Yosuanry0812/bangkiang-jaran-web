@extends('layouts.app')
@section('nav-mode', 'light')

@section('title', __('messages.visitor_data_title') . ' — Bangkiang Jaran')

@section('content')
<div class="min-h-screen pt-24 sm:pt-32 pb-16 px-gutter bg-[#F8F7F5]">
    <div class="max-w-4xl mx-auto">

        {{-- Progress steps --}}
        <div class="flex items-center gap-3 mb-8 font-sans text-sm" data-aos="fade-up">
            <div class="flex items-center gap-1.5">
                <div class="w-7 h-7 rounded-full bg-forest text-white flex items-center justify-center text-xs font-bold">✓</div>
                <span class="text-forest font-medium hidden sm:inline">{{ __('messages.step_choose_ticket') }}</span>
            </div>
            <div class="w-8 h-px bg-forest"></div>
            <div class="flex items-center gap-1.5">
                <div class="w-7 h-7 rounded-full bg-forest text-white flex items-center justify-center text-xs font-bold">2</div>
                <span class="text-forest font-medium hidden sm:inline">{{ __('messages.step_visitor_data') }}</span>
            </div>
            <div class="w-8 h-px bg-gray-200"></div>
            <div class="flex items-center gap-1.5">
                <div class="w-7 h-7 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center text-xs font-bold">3</div>
                <span class="text-gray-400 hidden sm:inline">{{ __('messages.step_payment') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- LEFT: form data diri --}}
            <div class="lg:col-span-7" data-aos="fade-up">
                <form method="POST" action="{{ route('wisatawan.pemesanan.store-data-diri') }}" id="form-data-diri">
                    @csrf

                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="bg-forest px-6 py-5">
                            <h2 class="font-serif text-lg text-white">{{ __('messages.visitor_data_title') }}</h2>
                            <p class="font-sans text-xs text-white/60 mt-1">{{ __('messages.visitor_data_desc') }}</p>
                        </div>

                        <div class="p-6 space-y-6">
                            @php $currentGroup = null; @endphp

                            @foreach($flow['detailRows'] as $i => $row)
                                @php
                                    $groupKey = $row['nama_tiket'] . '_' . $row['id_tiket'];
                                @endphp

                                {{-- Section header per tipe tiket --}}
                                @if($currentGroup !== $groupKey)
                                    @php $currentGroup = $groupKey; @endphp
                                    @if($i > 0)
                                        <hr class="border-gray-100">
                                    @endif
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-forest/8 flex items-center justify-center flex-shrink-0">
                                            @if($row['kategori'] === 'kendaraan')
                                                <span class="material-symbols-outlined text-forest text-sm">directions_car</span>
                                            @else
                                                <span class="material-symbols-outlined text-forest text-sm">person</span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-sans text-sm font-semibold text-ink">{{ $row['nama_tiket'] }}</p>
                                            <p class="font-sans text-xs text-stone">
                                                @if($row['kategori'] === 'kendaraan')
                                                    {{ __('messages.input_vehicle_plate') }}
                                                @else
                                                    {{ __('messages.input_full_name') }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                {{-- Input field --}}
                                <div class="ml-11">
                                    @if($row['kategori'] === 'kendaraan')
                                        <div>
                                            <label class="block font-sans text-xs text-stone mb-1.5">
                                                {{ __('messages.vehicle_plate') }}
                                                <span class="text-red-400">*</span>
                                            </label>
                                            <input type="text"
                                                   name="plat_{{ $i }}"
                                                   value="{{ old('plat_' . $i) }}"
                                                   placeholder="{{ __('messages.plate_placeholder') }}"
                                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-ink uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-forest/30 focus:border-forest transition-all bg-white">
                                            @error('plat_' . $i)
                                                <p class="font-sans text-xs text-red-500 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @else
                                        <div class="space-y-3">
                                            <div>
                                                <label class="block font-sans text-xs text-stone mb-1.5">
                                                    {{ __('messages.full_name') }}
                                                    <span class="text-red-400">*</span>
                                                </label>
                                                <input type="text"
                                                       name="nama_{{ $i }}"
                                                       value="{{ old('nama_' . $i) }}"
                                                       placeholder="{{ __('messages.name_placeholder') }}"
                                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-forest/30 focus:border-forest transition-all bg-white">
                                                @error('nama_' . $i)
                                                    <p class="font-sans text-xs text-red-500 mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="block font-sans text-xs text-stone mb-1.5">
                                                    {{ __('messages.gender') }}
                                                    <span class="text-red-400">*</span>
                                                </label>
                                                <select name="gender_{{ $i }}"
                                                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-forest/30 focus:border-forest transition-all bg-white">
                                                    <option value="" disabled {{ old('gender_' . $i) ? '' : 'selected' }}>{{ __('messages.select_gender') }}</option>
                                                    <option value="L" {{ old('gender_' . $i) === 'L' ? 'selected' : '' }}>{{ __('messages.male') }}</option>
                                                    <option value="P" {{ old('gender_' . $i) === 'P' ? 'selected' : '' }}>{{ __('messages.female') }}</option>
                                                </select>
                                                @error('gender_' . $i)
                                                    <p class="font-sans text-xs text-red-500 mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div class="flex flex-col-reverse sm:flex-row gap-3 mt-5 sm:items-center sm:justify-between">
                        <a href="{{ route('wisatawan.pemesanan.create') }}"
                           class="inline-flex items-center justify-center gap-1.5 font-sans text-sm text-stone hover:text-ink border border-gray-200 hover:border-gray-300 px-5 py-3 rounded-xl transition-colors w-full sm:w-auto">
                            <span class="material-symbols-outlined text-sm">arrow_back</span>
                            {{ __('messages.back') }}
                        </a>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 bg-forest text-white font-sans text-sm font-semibold px-6 py-3 rounded-xl hover:bg-leaf transition-colors w-full sm:w-auto">
                            {{ __('messages.continue_to_payment') }}
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- RIGHT: summary --}}
            <div class="lg:col-span-5" data-aos="fade-left" data-aos-delay="50">
                <div class="sticky top-24 space-y-4">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="bg-forest px-5 py-4">
                            <p class="font-sans text-[10px] uppercase tracking-[0.15em] text-white/40 mb-0.5">{{ __('messages.total') }}</p>
                            <p class="font-serif text-2xl text-white">
                                Rp{{ number_format($flow['total_harga'], 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="px-5 py-4 space-y-3">
                            <p class="font-sans text-xs font-semibold text-stone uppercase tracking-wider mb-2">{{ __('messages.ticket_summary') }}</p>
                            @foreach($flow['detail'] as $d)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="font-sans text-xs text-ink">{{ $d['nama'] }}</span>
                                    <span class="font-sans text-[10px] text-pebble bg-gray-100 px-1.5 py-0.5 rounded-full">×{{ $d['jumlah'] }}</span>
                                </div>
                                <span class="font-sans text-xs text-stone tabular-nums">
                                    Rp{{ number_format($d['subtotal'], 0, ',', '.') }}
                                </span>
                            </div>
                            @endforeach
                            <div class="h-px bg-gray-100"></div>
                            <div class="flex items-center justify-between">
                                <span class="font-sans text-xs text-stone">{{ __('messages.visit_date') }}</span>
                                <span class="font-sans text-xs font-medium text-ink">
                                    {{ \Carbon\Carbon::parse($flow['tgl_kunjungan'])->isoFormat('D MMMM YYYY') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-sans text-xs text-stone">{{ __('messages.booking_code') }}</span>
                                <span class="font-sans text-xs font-semibold text-forest">{{ $flow['kode_booking'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-amber-50 border border-amber-200/60 rounded-xl px-4 py-3.5 flex items-start gap-2.5">
                        <span class="material-symbols-outlined text-amber-500 text-sm mt-0.5 flex-shrink-0">info</span>
                        <p class="font-sans text-xs text-stone leading-relaxed">
                            {{ __('messages.visitor_data_note') }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
