@php($collagePhotos = collect($collagePhotos)->unique('url')->take(3)->values())
<div class="bj-photo-collage bj-photo-collage--{{ $collagePhotos->count() }}" role="group" aria-label="{{ app()->getLocale() === 'en' ? 'Views of Bangkiang Jaran' : 'Sudut Bangkiang Jaran' }}">
    @foreach($collagePhotos as $collageIndex => $collagePhoto)
        <figure class="bj-photo-collage__photo bj-photo-collage__photo--{{ $collageIndex + 1 }}">
            <img src="{{ $collagePhoto['url'] }}" alt="{{ $collagePhoto['caption'] }}" width="706" height="1020" loading="lazy" decoding="async">
            <p hidden>{{ app()->getLocale() === 'en' ? 'This photo could not be loaded.' : 'Foto ini belum dapat dimuat.' }}</p>
        </figure>
    @endforeach
</div>
