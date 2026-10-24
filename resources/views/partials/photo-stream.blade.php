@php
    $streamPhotos = collect($streamPhotos)->values();
    $streamEnglish = app()->getLocale() === 'en';
    $streamRows = $streamPhotos->count() > 3 ? [$streamPhotos->filter(fn ($photo, $key) => $key % 2 === 0), $streamPhotos->filter(fn ($photo, $key) => $key % 2 !== 0)] : [$streamPhotos];
@endphp
<div class="bj-photo-stream {{ $streamPhotos->count() < 2 ? 'bj-photo-stream--static' : '' }}" data-photo-stream role="region" aria-label="{{ $streamLabel }}">
    @if($streamPhotos->isEmpty())
        <div class="bj-stream-empty"><span class="material-symbols-outlined" aria-hidden="true">photo_library</span><p>{{ $streamEnglish ? 'Restaurant photographs have not been added yet.' : 'Dokumentasi restoran belum tersedia.' }}</p></div>
    @else
        @foreach($streamRows as $rowIndex => $streamRow)
        <div class="bj-photo-stream__viewport" data-stream-viewport tabindex="{{ $streamPhotos->count() > 1 ? '0' : '-1' }}" aria-label="{{ $streamEnglish ? 'Scroll horizontally to browse photos' : 'Geser mendatar untuk melihat foto' }}">
            <div class="bj-photo-stream__track" data-stream-track data-stream-direction="{{ $rowIndex % 2 ? '-1' : '1' }}">
                <div class="bj-photo-stream__set" data-stream-set>
                    @foreach($streamRow as $streamPhoto)<figure><img src="{{ $streamPhoto['url'] }}" alt="{{ $streamPhoto['caption'] }}" loading="lazy" width="706" height="1020" draggable="false"><figcaption>{{ $streamPhoto['caption'] }}</figcaption></figure>@endforeach
                </div>
                @if($streamPhotos->count() > 1)
                <div class="bj-photo-stream__set" aria-hidden="true">@foreach($streamRow as $streamPhoto)<figure><img src="{{ $streamPhoto['url'] }}" alt="" loading="lazy" width="706" height="1020" draggable="false"><figcaption>{{ $streamPhoto['caption'] }}</figcaption></figure>@endforeach</div>
                @endif
            </div>
        </div>
        @endforeach
        @if($streamPhotos->count() > 1)
        <div class="bj-photo-stream__controls"><p>{{ $streamEnglish ? 'Follow the photos as you scroll. You can also drag.' : 'Foto mengikuti ritme scroll. Bisa digeser juga.' }}</p><div><button type="button" data-stream-prev aria-label="{{ $streamEnglish ? 'Previous photos' : 'Foto sebelumnya' }}"><span class="material-symbols-outlined" aria-hidden="true">arrow_back</span></button><button type="button" data-stream-pause aria-pressed="false" data-play-label="{{ $streamEnglish ? 'Play photo stream' : 'Putar aliran foto' }}" data-pause-label="{{ $streamEnglish ? 'Pause photo stream' : 'Jeda aliran foto' }}" aria-label="{{ $streamEnglish ? 'Pause photo stream' : 'Jeda aliran foto' }}"><span class="material-symbols-outlined" aria-hidden="true">pause</span></button><button type="button" data-stream-next aria-label="{{ $streamEnglish ? 'Next photos' : 'Foto berikutnya' }}"><span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></button></div></div>
        @endif
    @endif
</div>
