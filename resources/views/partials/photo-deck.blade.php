@php
    $deckPhotos = collect($deckPhotos)->values();
    $deckEnglish = app()->getLocale() === 'en';
@endphp
<div class="bj-photo-deck {{ $deckPhotos->count() < 2 ? 'bj-photo-deck--single' : '' }}" data-photo-deck role="region" aria-roledescription="{{ $deckEnglish ? 'carousel' : 'karusel' }}" aria-label="{{ $deckLabel }}">
    <div class="bj-photo-deck__stage" data-deck-stage @if($deckPhotos->count() > 1) tabindex="0" aria-label="{{ $deckEnglish ? 'Use left and right arrows to browse photos' : 'Gunakan panah kiri dan kanan untuk melihat foto' }}" @endif>
        @forelse($deckPhotos as $deckIndex => $deckPhoto)
            <figure class="bj-photo-deck__slide" data-deck-slide role="group" aria-roledescription="slide" aria-label="{{ $deckIndex + 1 }} / {{ $deckPhotos->count() }}">
                <img src="{{ $deckPhoto['url'] }}" alt="{{ $deckPhoto['caption'] }}" loading="lazy" width="706" height="1020" draggable="false">
                <figcaption>{{ $deckPhoto['caption'] }}</figcaption>
            </figure>
        @empty
            <div class="bj-photo-deck__empty"><span class="material-symbols-outlined" aria-hidden="true">photo_library</span><h3>{{ $deckEnglish ? 'A glimpse of the restaurant, soon.' : 'Sekilas suasana restoran, segera hadir.' }}</h3><p>{{ $deckEnglish ? 'Restaurant photographs have not been added yet.' : 'Dokumentasi restoran belum tersedia.' }}</p></div>
        @endforelse
    </div>
    @if($deckPhotos->count() > 1)
        <div class="bj-photo-deck__controls">
            <span data-deck-counter aria-live="polite" aria-atomic="true">1 / {{ $deckPhotos->count() }}</span>
            <p>{{ $deckEnglish ? 'Drag to explore' : 'Geser untuk menjelajah' }}</p>
            <button type="button" data-deck-prev aria-label="{{ $deckEnglish ? 'Previous photo' : 'Foto sebelumnya' }}"><span class="material-symbols-outlined" aria-hidden="true">arrow_back</span></button>
            <button type="button" data-deck-pause aria-label="{{ $deckEnglish ? 'Pause slideshow' : 'Jeda peragaan foto' }}" aria-pressed="false" data-play-label="{{ $deckEnglish ? 'Play slideshow' : 'Putar peragaan foto' }}" data-pause-label="{{ $deckEnglish ? 'Pause slideshow' : 'Jeda peragaan foto' }}"><span class="material-symbols-outlined" aria-hidden="true">pause</span></button>
            <button type="button" data-deck-next aria-label="{{ $deckEnglish ? 'Next photo' : 'Foto berikutnya' }}"><span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></button>
        </div>
    @endif
</div>
