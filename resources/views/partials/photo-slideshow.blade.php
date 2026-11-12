@php($slideshowPhotos = collect($slideshowPhotos)->unique('url')->take(12)->values())
<div class="bj-photo-slideshow" data-photo-slideshow data-slideshow-photos="{{ $slideshowPhotos->toJson() }}" data-slideshow-index="{{ $slideshowIndex ?? 0 }}">
    <img src="{{ $slideshowPhoto['url'] }}" alt="{{ $slideshowPhoto['caption'] }}" width="1200" height="800" loading="lazy" decoding="async">
    <p hidden>{{ app()->getLocale() === 'en' ? 'This photo could not be loaded.' : 'Foto ini belum dapat dimuat.' }}</p>
    @include('partials.photo-link', ['photoUrl' => $slideshowPhoto['url'], 'photoCaption' => $slideshowPhoto['caption']])
</div>
