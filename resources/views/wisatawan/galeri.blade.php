@extends('layouts.app')
@section('nav-mode', 'light')

@section('content')
<div class="bg-background min-h-screen">
    <div class="max-w-container-max mx-auto px-gutter py-xl">
        <header class="mb-xl text-center md:text-left max-w-3xl">
            <h1 class="font-display text-display-mobile md:text-display-lg text-primary mb-md">{{ __('messages.gallery_title') }}</h1>
            <p class="font-body text-body-lg text-on-surface-variant">{{ __('messages.gallery_desc') }}</p>
        </header>

        <section class="grid grid-cols-1 md:grid-cols-4 grid-rows-[auto] gap-md md:gap-lg">
            @foreach($galeri as $i => $media)
            <div class="group relative overflow-hidden rounded-xl cursor-pointer bg-surface-container card-shadow
                {{ $i == 0 ? 'md:col-span-2 md:row-span-2' : '' }}"
                style="{{ $i == 0 ? '' : 'height: 300px;' }} min-height: 250px;">
                <div class="absolute inset-0 w-full h-full bg-cover bg-center transition-transform duration-700 group-hover:scale-105"
                     style="background-image: url('{{ asset('storage/' . $media->file) }}')">
                </div>
                <div class="absolute inset-0 bg-primary/0 group-hover:bg-primary/20 transition-colors duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100 backdrop-blur-[2px]">
                    <span class="material-symbols-outlined text-white text-[48px] drop-shadow-md">zoom_in</span>
                </div>
            </div>
            @endforeach
        </section>

        @if($galeri->count() > 6)
        <div class="mt-xl flex justify-center">
            <button class="font-body text-label-md border-[1.5px] border-primary text-primary px-lg py-sm rounded-xl hover:bg-primary/5 transition-colors duration-300">
                {{ __('messages.load_more') }}
            </button>
        </div>
        @endif
    </div>
</div>
@endsection
