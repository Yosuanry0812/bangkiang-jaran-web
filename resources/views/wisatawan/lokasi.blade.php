@extends('layouts.app')

@section('content')
<div class="bg-background min-h-screen flex flex-col">
    {{-- Hero Title --}}
    <section class="w-full pt-xl pb-lg px-gutter max-w-container-max mx-auto text-center">
        <h1 class="font-display text-display-mobile md:text-display-lg text-on-background mb-4">Find Your Serenity</h1>
        <p class="font-body text-body-lg text-on-surface-variant max-w-2xl mx-auto">Nestled in the lush landscapes of Gianyar, Bangkiang Jaran is your perfect escape into nature's embrace.</p>
    </section>

    {{-- Interactive Map Section --}}
    <section class="w-full px-gutter max-w-container-max mx-auto mb-xl relative">
        <div class="w-full h-[500px] md:h-[600px] rounded-2xl overflow-hidden card-shadow relative">
            <div class="absolute inset-0 bg-surface-variant flex items-center justify-center">
                <iframe
                    src="https://www.google.com/maps?q=-8.511979,115.328486&output=embed&z=15"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy">
                </iframe>
            </div>
            {{-- Map Overlay UI --}}
            <div class="absolute top-md left-md right-md flex justify-between items-start pointer-events-none">
                <div class="bg-surface/80 backdrop-blur-md rounded-xl p-4 flex items-center gap-sm pointer-events-auto card-shadow">
                    <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">location_on</span>
                    <div>
                        <p class="font-body text-label-md text-on-surface">Bangkiang Jaran Waterfall</p>
                        <p class="font-body text-caption text-on-surface-variant">Gianyar, Bali</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Floating Info Card --}}
        <div class="relative mt-[-80px] md:mt-[-100px] mx-auto max-w-4xl z-10 px-4">
            <div class="bg-surface rounded-2xl card-shadow p-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-lg text-center md:text-left divide-y md:divide-y-0 md:divide-x divide-outline-variant/30">
                    <div class="flex flex-col items-center md:items-start pt-4 md:pt-0 md:px-sm first:pt-0">
                        <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-sm text-primary">
                            <span class="material-symbols-outlined">route</span>
                        </div>
                        <h3 class="font-body text-label-md text-outline mb-xs">Distance</h3>
                        <p class="font-body text-body-md text-on-surface font-medium">Kira-kira 15km dari Ubud</p>
                    </div>
                    <div class="flex flex-col items-center md:items-start pt-6 md:pt-0 md:px-lg">
                        <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-sm text-primary">
                            <span class="material-symbols-outlined">schedule</span>
                        </div>
                        <h3 class="font-body text-label-md text-outline mb-xs">Time</h3>
                        <p class="font-body text-body-md text-on-surface font-medium">30-45 menit berkendara</p>
                    </div>
                    <div class="flex flex-col items-center md:items-start pt-6 md:pt-0 md:pl-lg">
                        <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-sm text-primary">
                            <span class="material-symbols-outlined">directions_car</span>
                        </div>
                        <h3 class="font-body text-label-md text-outline mb-xs">Route</h3>
                        <p class="font-body text-body-md text-on-surface font-medium">Akses mudah melalui jalan aspal</p>
                    </div>
                </div>
                <div class="mt-lg flex justify-center border-t border-outline-variant/30 pt-md">
                    <a href="https://www.google.com/maps?q=-8.511979,115.328486" target="_blank"
                       class="bg-primary-container text-white font-body text-label-md px-8 py-4 rounded-xl flex items-center gap-sm hover:-translate-y-1 transition-transform shadow-sm group">
                        <span class="material-symbols-outlined group-hover:animate-pulse">map</span>
                        Buka di Google Maps
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
