@php($collagePhotos = collect($collagePhotos)->unique('url')->take(3)->values())
<div class="bj-photo-collage bj-photo-collage--{{ $collagePhotos->count() }}" role="group" aria-label="{{ app()->getLocale() === 'en' ? 'Views of Bangkiang Jaran' : 'Sudut Bangkiang Jaran' }}">
    @foreach($collagePhotos as $collageIndex => $collagePhoto)
        <figure class="bj-photo-collage__photo bj-photo-collage__photo--{{ $collageIndex + 1 }}">
            @include('partials.photo-slideshow', ['slideshowPhotos' => $rotationPhotos ?? $collagePhotos, 'slideshowPhoto' => $collagePhoto, 'slideshowIndex' => $collageIndex])
        </figure>
    @endforeach
</div>
