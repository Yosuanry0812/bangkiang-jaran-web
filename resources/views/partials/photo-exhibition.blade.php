@php
    $exhibitionPhotos = collect($exhibitionPhotos)->values();
    $exhibitionEnglish = app()->getLocale() === 'en';
    $exhibitionCount = $exhibitionPhotos->count();
@endphp
<div class="bj-exhibition {{ $exhibitionCount < 2 ? 'bj-exhibition--still' : '' }} {{ $exhibitionCount === 0 ? 'bj-exhibition--empty' : '' }}" data-photo-exhibition role="region" aria-label="{{ $exhibitionLabel }}" @if($exhibitionCount > 1) aria-roledescription="{{ $exhibitionEnglish ? 'carousel' : 'karusel' }}" @endif>
    <div class="bj-exhibition__viewport">
        <div class="bj-exhibition__scene" data-exhibition-scene @if($exhibitionCount > 1) tabindex="0" aria-label="{{ $exhibitionEnglish ? 'Photo gallery. Use the left and right arrow keys to browse.' : 'Galeri foto. Gunakan tombol panah kiri dan kanan untuk berpindah.' }}" @endif>
            @if($exhibitionCount < 3)
                <div class="bj-exhibition__mount bj-exhibition__mount--left" aria-hidden="true"></div>
                <div class="bj-exhibition__mount bj-exhibition__mount--right" aria-hidden="true"></div>
            @endif
            @forelse($exhibitionPhotos as $exhibitionIndex => $exhibitionPhoto)
                <figure class="bj-exhibition__print {{ $exhibitionIndex === 0 ? 'is-current' : '' }}" data-exhibition-print role="group" aria-label="{{ ($exhibitionIndex + 1) . ' / ' . $exhibitionCount }}">
                    <div class="bj-exhibition__image">
                        <img src="{{ $exhibitionPhoto['url'] }}" alt="{{ $exhibitionPhoto['caption'] }}" loading="lazy" width="706" height="1020" draggable="false">
                        <p class="bj-exhibition__unavailable" hidden>{{ $exhibitionEnglish ? 'This photo could not be loaded.' : 'Foto ini belum dapat dimuat.' }}</p>
                    </div>
                    <figcaption><span>{{ $exhibitionPhoto['caption'] }}</span><span aria-hidden="true">{{ str_pad($exhibitionIndex + 1, 2, '0', STR_PAD_LEFT) }}</span></figcaption>
                    @if($exhibitionCount > 1)
                        <button class="bj-exhibition__select" type="button" data-exhibition-select="{{ $exhibitionIndex }}" tabindex="-1" aria-label="{{ ($exhibitionEnglish ? 'View photo: ' : 'Lihat foto: ') . $exhibitionPhoto['caption'] }}"></button>
                    @endif
                </figure>
            @empty
                <div class="bj-exhibition__print bj-exhibition__waiting">
                    <div><h3>{{ $exhibitionEnglish ? 'Restaurant photos' : 'Foto restoran' }}</h3><p>{{ $exhibitionEnglish ? 'Restaurant photographs have not been added yet.' : 'Dokumentasi restoran belum tersedia.' }}</p></div>
                    <p>{{ $exhibitionEnglish ? 'Bakbakan, Gianyar, Bali' : 'Desa Bakbakan, Gianyar, Bali' }}</p>
                </div>
            @endforelse
        </div>
    </div>
    @if($exhibitionCount > 1)
        <div class="bj-exhibition__navigation" data-exhibition-navigation hidden>
            <div class="bj-exhibition__position"><span data-exhibition-counter aria-live="polite" aria-atomic="true">1 / {{ $exhibitionCount }}</span><p>{{ $exhibitionEnglish ? 'Drag or choose a photo' : 'Geser atau pilih foto' }}</p></div>
            <div class="bj-exhibition__arrows">
                <button type="button" data-exhibition-prev aria-label="{{ $exhibitionEnglish ? 'Previous photo' : 'Foto sebelumnya' }}"><span class="material-symbols-outlined" aria-hidden="true">west</span></button>
                <button type="button" data-exhibition-next aria-label="{{ $exhibitionEnglish ? 'Next photo' : 'Foto berikutnya' }}"><span class="material-symbols-outlined" aria-hidden="true">east</span></button>
            </div>
        </div>
        <div class="bj-exhibition__thumbnails" data-exhibition-thumbnails hidden aria-label="{{ $exhibitionEnglish ? 'Choose a photo' : 'Pilih foto' }}">
            @foreach($exhibitionPhotos as $exhibitionIndex => $exhibitionPhoto)
                <button type="button" data-exhibition-thumb="{{ $exhibitionIndex }}" aria-pressed="{{ $exhibitionIndex === 0 ? 'true' : 'false' }}" aria-label="{{ ($exhibitionEnglish ? 'View photo: ' : 'Lihat foto: ') . $exhibitionPhoto['caption'] }}"><img src="{{ $exhibitionPhoto['url'] }}" alt="" width="64" height="64" loading="lazy" draggable="false"></button>
            @endforeach
        </div>
    @endif
</div>
