@extends('layouts.app')

@section('title', 'Tiket Masuk — Bangkiang Jaran')

@section('content')

{{-- ══════════════════════════════════════════ --}}
{{--  HERO                                      --}}
{{-- ══════════════════════════════════════════ --}}
<section class="relative min-h-screen flex items-center overflow-hidden">
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1503785640985-62d183d5cfe7?auto=format&fit=crop&w=1920&q=80"
             class="w-full h-full object-cover" alt="Bangkiang Jaran">
        <div class="absolute inset-0" style="background:linear-gradient(to right,rgba(0,0,0,0.72) 0%,rgba(0,0,0,0.38) 60%,rgba(0,0,0,0.18) 100%);"></div>
        <div class="absolute inset-0" style="background:linear-gradient(to top,rgba(0,0,0,0.45) 0%,transparent 55%);"></div>
    </div>
    <div class="relative z-10 w-full max-w-6xl mx-auto px-gutter py-32 lg:py-44">
        <div class="max-w-xl" data-aos="fade-up" data-aos-duration="900">
            <p class="font-sans text-[11px] tracking-[0.18em] uppercase text-white/50 mb-8">{{ __('messages.online_booking') }}</p>
            <h1 class="font-serif text-[clamp(3rem,7vw,5.5rem)] leading-[1.0] text-white mb-7">
                {{ __('messages.plan_your_journey') }}
            </h1>
            <p class="font-sans text-[15px] text-white/60 mb-10 leading-relaxed max-w-sm">
                {{ __('messages.plan_your_journey_desc') }}
            </p>
            <div class="flex flex-wrap items-center gap-3">
                @auth
                <a href="{{ route('wisatawan.pemesanan.create') }}"
                   class="inline-flex items-center gap-2 bg-white text-gray-900 font-sans text-sm font-medium px-6 py-3 rounded-full hover:bg-gray-100 transition-colors">
                    {{ __('messages.book_ticket') }} <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
                @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 bg-white text-gray-900 font-sans text-sm font-medium px-6 py-3 rounded-full hover:bg-gray-100 transition-colors">
                    {{ __('messages.login_to_book') }} <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
                @endauth
                <a href="#tiket-list"
                   class="inline-flex items-center gap-2 border border-white/25 text-white/80 font-sans text-sm px-6 py-3 rounded-full hover:border-white/50 hover:text-white transition-colors">
                    {{ __('messages.view_price') }}
                </a>
            </div>
            <div class="flex flex-wrap gap-x-6 gap-y-8 mt-14 pt-10 border-t border-white/10">
                <div>
                    <p class="font-sans text-[11px] text-white/40 tracking-widest uppercase mb-1">{{ __('messages.operating_hours_label') }}</p>
                    <p class="font-sans text-sm text-white/80">{{ __('messages.operating_hours') }}</p>
                </div>
                <div class="w-px bg-white/10 hidden md:block"></div>
                <div>
                    <p class="font-sans text-[11px] text-white/40 tracking-widest uppercase mb-1">{{ __('messages.location') }}</p>
                    <p class="font-sans text-sm text-white/80">{{ __('messages.address') }}</p>
                </div>
                <div class="w-px bg-white/10 hidden md:block"></div>
                <div>
                    <p class="font-sans text-[11px] text-white/40 tracking-widest uppercase mb-1">{{ __('messages.visitors') }}</p>
                    <p class="font-sans text-sm text-white/80">{{ __('messages.visitors_value') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════ --}}
{{--  TICKET LIST                               --}}
{{-- ══════════════════════════════════════════ --}}
<section id="tiket-list" class="py-24 md:py-32 px-gutter bg-white">
    <div class="max-w-6xl mx-auto">
        <div class="mb-16" data-aos="fade-up">
            <p class="font-sans text-[11px] tracking-[0.15em] uppercase text-gray-400 mb-4">{{ __('messages.ticket_price') }}</p>
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <h2 class="font-serif text-4xl md:text-6xl text-gray-900 leading-[1.05]">{{ __('messages.select_your_ticket') }}</h2>
                <p class="font-sans text-sm text-gray-400 max-w-xs leading-relaxed md:text-right">
                    {{ __('messages.ticket_price_desc') }}
                </p>
            </div>
            <div class="mt-8 h-px bg-gray-100"></div>
        </div>

        @if(isset($tikets) && $tikets->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($tikets as $t)
            <div class="group flex flex-col bg-white border border-gray-150 rounded-2xl p-7 hover:border-gray-300 hover:shadow-sm transition-all duration-300"
                 data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                <div class="flex items-center justify-between mb-8">
                    <span class="font-sans text-xs text-gray-300 tabular-nums">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="inline-flex items-center gap-1.5 font-sans text-xs text-gray-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> {{ __('messages.available') }}
                    </span>
                </div>
                <h3 class="font-serif text-2xl text-gray-900 mb-1 leading-snug">{{ $t->nama_tiket }}</h3>
                <p class="font-sans text-sm text-gray-400 mb-8">{{ __('messages.per_person_day') }}</p>
                <div class="mt-auto">
                    <div class="flex items-baseline gap-1.5 mb-7">
                        <span class="font-sans text-sm text-gray-400">Rp</span>
                        <span class="font-serif text-4xl text-gray-900 tracking-tight">{{ number_format($t->harga, 0, ',', '.') }}</span>
                    </div>
                    <div class="h-px bg-gray-100 mb-7"></div>
                    <ul class="space-y-2.5 mb-8">
                        <li class="flex items-center gap-2.5 font-sans text-sm text-gray-500">
                            <span class="material-symbols-outlined text-sm text-gray-300">check</span>
                            {{ __('messages.facility_access_entry') }}
                        </li>
                        <li class="flex items-center gap-2.5 font-sans text-sm text-gray-500">
                            <span class="material-symbols-outlined text-sm text-gray-300">check</span>
                            {{ __('messages.facility_valid_date_one_day') }}
                        </li>
                        <li class="flex items-center gap-2.5 font-sans text-sm text-gray-500">
                            <span class="material-symbols-outlined text-sm text-gray-300">check</span>
                            {{ __('messages.email_confirmation') }}
                        </li>
                    </ul>
                    <div class="flex gap-2.5">
                        <a href="{{ route('tiket.detail', $t->id_tiket) }}"
                           class="flex-1 text-center font-sans text-sm py-3 rounded-xl border border-gray-200 text-gray-500 hover:border-gray-400 hover:text-gray-900 transition-all duration-200">
                            {{ __('messages.detail') }}
                        </a>
                        @auth
                        <a href="{{ route('wisatawan.pemesanan.create', ['id_tiket' => $t->id_tiket]) }}"
                           class="flex-1 text-center font-sans text-sm font-medium py-3 rounded-xl bg-gray-900 text-white hover:bg-gray-700 transition-all duration-200">
                            {{ __('messages.book') }}
                        </a>
                        @else
                        <a href="{{ route('login') }}"
                           class="flex-1 text-center font-sans text-sm font-medium py-3 rounded-xl bg-gray-900 text-white hover:bg-gray-700 transition-all duration-200">
                            {{ __('messages.book') }}
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @else
        <div class="text-center py-24" data-aos="fade-up">
            <span class="material-symbols-outlined text-5xl text-gray-200 block mb-4">confirmation_number</span>
            <p class="font-serif text-xl text-gray-400 mb-2">{{ __('messages.no_tickets') }}</p>
            <p class="font-sans text-sm text-gray-400">{{ __('messages.no_tickets_desc') }}</p>
        </div>
        @endif
    </div>
</section>

{{-- ══════════════════════════════════════════ --}}
{{--  MENGAPA PESAN ONLINE                      --}}
{{-- ══════════════════════════════════════════ --}}
<section class="py-24 md:py-32 px-gutter bg-white border-t border-gray-100">
    <div class="max-w-6xl mx-auto">
        <div class="mb-16" data-aos="fade-up">
            <p class="font-sans text-[11px] tracking-[0.15em] uppercase text-gray-400 mb-4">{{ __('messages.advantages') }}</p>
            <h2 class="font-serif text-3xl md:text-5xl text-gray-900">{{ __('messages.why_book_online') }}</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-100">
            <div class="py-10 md:py-0 md:pr-12" data-aos="fade-up" data-aos-delay="0">
                <p class="font-sans text-[11px] tracking-[0.12em] uppercase text-gray-300 mb-6">01</p>
                <h3 class="font-serif text-xl text-gray-900 mb-3">{{ __('messages.fast_easy_process') }}</h3>
                <p class="font-sans text-sm text-gray-500 leading-relaxed">{{ __('messages.fast_easy_desc') }}</p>
            </div>
            <div class="py-10 md:py-0 md:px-12" data-aos="fade-up" data-aos-delay="80">
                <p class="font-sans text-[11px] tracking-[0.12em] uppercase text-gray-300 mb-6">02</p>
                <h3 class="font-serif text-xl text-gray-900 mb-3">{{ __('messages.secure_reliable') }}</h3>
                <p class="font-sans text-sm text-gray-500 leading-relaxed">{{ __('messages.secure_reliable_desc') }}</p>
            </div>
            <div class="py-10 md:py-0 md:pl-12" data-aos="fade-up" data-aos-delay="160">
                <p class="font-sans text-[11px] tracking-[0.12em] uppercase text-gray-300 mb-6">03</p>
                <h3 class="font-serif text-xl text-gray-900 mb-3">{{ __('messages.anytime_support') }}</h3>
                <p class="font-sans text-sm text-gray-500 leading-relaxed">{{ __('messages.anytime_support_desc') }}</p>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════ --}}
{{--  CARA PESAN                                --}}
{{-- ══════════════════════════════════════════ --}}
<section class="py-24 md:py-32 px-gutter border-t border-gray-100" style="background:#F8F7F5;">
    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-start">
            <div data-aos="fade-right">
                <p class="font-sans text-[11px] tracking-[0.15em] uppercase text-gray-400 mb-5">{{ __('messages.how_to_book') }}</p>
                <h2 class="font-serif text-3xl md:text-5xl text-gray-900 leading-[1.1] mb-14">{{ __('messages.three_steps_title') }}</h2>
                <div class="divide-y divide-gray-200">
                    <div class="flex gap-5 sm:gap-7 py-9">
                        <span class="font-serif text-[2.75rem] leading-none select-none mt-0.5 tabular-nums" style="color:#E5E1DC;">1</span>
                        <div class="pt-1">
                            <h4 class="font-serif text-lg text-gray-900 mb-2">{{ __('messages.step_one_title') }}</h4>
                            <p class="font-sans text-sm text-gray-500 leading-relaxed">{{ __('messages.step_one_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex gap-5 sm:gap-7 py-9">
                        <span class="font-serif text-[2.5rem] sm:text-[2.75rem] leading-none select-none mt-0.5 tabular-nums" style="color:#E5E1DC;">2</span>
                        <div class="pt-1">
                            <h4 class="font-serif text-lg text-gray-900 mb-2">{{ __('messages.step_two_title') }}</h4>
                            <p class="font-sans text-sm text-gray-500 leading-relaxed">{{ __('messages.step_two_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex gap-5 sm:gap-7 py-9">
                        <span class="font-serif text-[2.5rem] sm:text-[2.75rem] leading-none select-none mt-0.5 tabular-nums" style="color:#E5E1DC;">3</span>
                        <div class="pt-1">
                            <h4 class="font-serif text-lg text-gray-900 mb-2">{{ __('messages.step_three_title') }}</h4>
                            <p class="font-sans text-sm text-gray-500 leading-relaxed">{{ __('messages.step_three_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lg:sticky lg:top-28" data-aos="fade-left" data-aos-delay="100">
                <div class="relative rounded-2xl overflow-hidden aspect-[4/5]">
                    <img src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=800&q=80"
                         class="w-full h-full object-cover" alt="Bangkiang Jaran" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-8">
                        <p class="font-serif text-white text-xl leading-snug">{{ __('messages.experience_cta_title') }}</p>
                        <p class="font-sans text-sm text-white/60 mt-2">{{ __('messages.location_desc_short') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════ --}}
{{--  FAQ                                       --}}
{{-- ══════════════════════════════════════════ --}}
<section class="py-24 md:py-32 px-gutter border-t border-gray-100" style="background:#F8F7F5;" x-data="{open: null}">
    <div class="max-w-3xl mx-auto">
        <div class="mb-16" data-aos="fade-up">
            <p class="font-sans text-[11px] tracking-[0.15em] uppercase text-gray-400 mb-4">FAQ</p>
            <h2 class="font-serif text-3xl md:text-5xl text-gray-900">{{ __('messages.faq_title') }}</h2>
        </div>
        @php $faqs = [
            ['q'=>__('messages.faq_q1'),'a'=>__('messages.faq_a1')],
            ['q'=>__('messages.faq_q2'),'a'=>__('messages.faq_a2')],
            ['q'=>__('messages.faq_q3'),'a'=>__('messages.faq_a3')],
            ['q'=>__('messages.faq_q4'),'a'=>__('messages.faq_a4')],
            ['q'=>__('messages.faq_q5'),'a'=>__('messages.faq_a5')],
        ]; @endphp
        <div class="divide-y divide-gray-200" data-aos="fade-up">
            @foreach($faqs as $i => $faq)
            <div>
                <button class="w-full flex items-center justify-between py-6 text-left group"
                        @click="open === {{ $i }} ? open = null : open = {{ $i }}">
                    <span class="font-serif text-lg text-gray-900 pr-8 group-hover:text-gray-600 transition-colors">{{ $faq['q'] }}</span>
                    <span class="flex-shrink-0 text-gray-400 transition-transform duration-300" :class="{'rotate-45': open === {{ $i }}}">
                        <span class="material-symbols-outlined text-xl">add</span>
                    </span>
                </button>
                <div x-show="open === {{ $i }}" x-collapse>
                    <p class="font-sans text-sm text-gray-500 leading-relaxed pb-6">{{ $faq['a'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
