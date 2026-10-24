@php
    $columnPhotos = collect($columnPhotos)->unique('url')->values();
    $columnsEnglish = app()->getLocale() === 'en';
@endphp
<div class="bj-photo-columns {{ $columnPhotos->isEmpty() ? 'bj-photo-columns--empty' : '' }}" data-photo-columns role="region" aria-label="{{ $columnsEnglish ? 'Bangkiang Jaran photo gallery' : 'Galeri foto Bangkiang Jaran' }}">
    <div class="bj-photo-columns__viewport" data-columns-viewport tabindex="0" aria-label="{{ $columnsEnglish ? 'Photo collection' : 'Kumpulan foto' }}">
        <div class="bj-photo-columns__source" data-columns-source>
            @forelse($columnPhotos as $columnPhoto)
                <figure><img src="{{ $columnPhoto['url'] }}" alt="{{ $columnPhoto['caption'] }}" width="706" height="1020" loading="lazy" decoding="async"><p hidden>{{ $columnsEnglish ? 'This photo could not be loaded.' : 'Foto ini belum dapat dimuat.' }}</p></figure>
            @empty
                <p>{{ $columnEmptyMessage ?? ($columnsEnglish ? 'Photographs have not been added yet.' : 'Dokumentasi foto belum tersedia.') }}</p>
            @endforelse
        </div>
        <div class="bj-photo-columns__lanes" data-columns-lanes aria-hidden="true" inert hidden></div>
    </div>
    <div class="bj-photo-columns__controls" data-columns-controls hidden>
        <button type="button" data-columns-pause aria-pressed="false" data-play-label="{{ $columnsEnglish ? 'Play photo motion' : 'Putar gerak foto' }}" data-pause-label="{{ $columnsEnglish ? 'Pause photo motion' : 'Jeda gerak foto' }}"><span class="material-symbols-outlined" aria-hidden="true">pause</span><span data-columns-button-label>{{ $columnsEnglish ? 'Pause photo motion' : 'Jeda gerak foto' }}</span></button>
    </div>
</div>
