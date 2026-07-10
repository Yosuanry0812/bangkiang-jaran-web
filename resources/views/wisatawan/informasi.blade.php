@extends('layouts.app')

@section('content')
<div class="bg-background min-h-screen">
    {{-- ═══════════════════════════════════════════════════════════════
         HEADER
    ════════════════════════════════════════════════════════════════ --}}
    <section class="max-w-container-max mx-auto px-gutter md:px-lg py-xl mt-lg">
        <div class="text-center md:text-left">
            <h1 class="font-display text-display-mobile md:text-display-lg text-primary mb-md">Sejarah &amp; Informasi</h1>
            <p class="font-body text-body-lg text-on-surface-variant max-w-2xl">Discover the roots of Bangkiang Jaran, a hidden gem in Gianyar, where nature and local heritage intertwine beautifully.</p>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════
         NARRATIVE BENTO GRID — show only 2 items
    ════════════════════════════════════════════════════════════════ --}}
    @if(isset($kontenList) && $kontenList->count() > 0)
    <section class="max-w-container-max mx-auto px-gutter pb-xl space-y-lg">
        @foreach($kontenList->take(2) as $item)
        @php $isSejarah = $item->jenis == 'sejarah'; $isSecond = $loop->index == 1; @endphp

        <div class="grid grid-cols-1 md:grid-cols-12 gap-lg">
            {{-- Image block first (only for sejarah, second item) --}}
            @if($isSejarah && $isSecond)
             <div class="md:col-span-7 h-[400px] md:h-auto rounded-2xl overflow-hidden card-shadow relative">
                <img class="absolute inset-0 w-full h-full object-cover"
                     src="{{ asset('images/sejarah-bangkiang-waterfall.webp') }}"
                     alt="Sejarah Bangkiang Waterfall">
            </div>
            @endif

            {{-- Text block --}}
            <div class="@if($isSejarah && !$isSecond) md:col-span-7 @elseif($isSejarah && $isSecond) md:col-span-5 @else md:col-span-12 @endif bg-surface-container-lowest rounded-2xl p-lg card-shadow flex flex-col justify-center">
                <h2 class="font-display text-headline-md text-on-background mb-md">
                    {{ $item->judul }}
                </h2>
                <div class="font-body text-body-md text-on-surface-variant space-y-md">
                    @if($isSejarah)
                        @php $paras = array_filter(array_map('trim', explode("\n", $item->isi))); @endphp
                        @foreach($paras as $p)
                        <p>{{ $p }}</p>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Image block (only for sejarah, NOT second item) --}}
            @if($isSejarah && !$isSecond)
            <div class="md:col-span-5 h-[400px] md:h-auto rounded-2xl overflow-hidden card-shadow relative">
                <img class="absolute inset-0 w-full h-full object-cover"
                     src="{{ asset('images/sejarah-bangkiang-waterfall.webp') }}"
                     alt="Bangkiang Jaran Waterfall">
            </div>
            @endif
        </div>
        @endforeach
    </section>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════
         LOKASI
    ════════════════════════════════════════════════════════════════ --}}
    <section class="max-w-container-max mx-auto px-gutter pb-xl">
        <div class="text-center mb-lg">
            <h2 class="font-display text-headline-md text-primary mb-sm">Lokasi</h2>
            <p class="font-body text-body-md text-on-surface-variant">Desa Bakbakan, Kecamatan Gianyar, Kabupaten Gianyar, Bali</p>
        </div>
        <div class="rounded-2xl overflow-hidden card-shadow h-[350px]">
            <iframe
                src="https://www.google.com/maps?q=-8.511979,115.328486&output=embed&z=15"
                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy">
            </iframe>
        </div>
    </section>
</div>
@endsection
