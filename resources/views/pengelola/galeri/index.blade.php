@extends('pengelola.layouts.admin')
@section('title', __('messages.manage_gallery_title'))
@section('page_title', __('messages.manage_gallery_title'))
@section('content')
<header class="admin-page-head"><div><h1>{{ __('messages.manage_gallery_title') }}</h1></div><a class="admin-btn" href="{{ route('pengelola.galeri.create') }}"><span class="material-symbols-outlined" aria-hidden="true">add_photo_alternate</span>{{ __('messages.add_media') }}</a></header>
<nav class="admin-tabs" aria-label="{{ __('panel.kategori_galeri_1e60') }}">
    @foreach(['' => __('ui.gallery_all'), 'wisata' => __('ui.gallery_tourism'), 'restoran' => __('ui.gallery_resto'), 'sejarah' => __('ui.gallery_history')] as $type => $label)
    <a href="{{ route('pengelola.galeri.index', $type ? ['tipe' => $type] : []) }}" @if((request('tipe') ?? '') === $type) aria-current="page" @endif>{{ $label }}</a>
    @endforeach
</nav>
<div class="admin-gallery">
    @forelse($galeri as $g)
    <article class="admin-media">
        <div class="admin-media-image">
            <span class="material-symbols-outlined" aria-hidden="true">image</span><small>{{ __('panel.foto_tidak_tersedia_ad6a') }}</small>
            <img src="{{ \App\Models\Galeri::variantUrl($g->file, 480) }}" alt="" loading="lazy" decoding="async" onerror="this.remove()">
        </div>
        <form class="admin-media-delete" method="POST" action="{{ route('pengelola.galeri.destroy', $g->id_galeri) }}" onsubmit="return confirm(@js(__('messages.delete_confirm_msg')))">@csrf @method('DELETE')
            <button type="submit" title="{{ __('messages.delete') }}" aria-label="{{ __('messages.delete') }}"><span class="material-symbols-outlined" aria-hidden="true">delete</span></button>
        </form>
        <div class="admin-media-body"><small>{{ match($g->tipe) { 'restoran' => __('ui.gallery_resto'), 'sejarah' => __('ui.gallery_history'), default => __('ui.gallery_tourism') } }}</small></div>
    </article>
    @empty
    <div class="admin-panel col-span-full"><div class="admin-empty"><span class="material-symbols-outlined" aria-hidden="true">photo_library</span><h2>{{ __('messages.no_media') }}</h2><a class="admin-btn" href="{{ route('pengelola.galeri.create') }}">{{ __('messages.add_media') }}</a></div></div>
    @endforelse
</div>
@if(method_exists($galeri, 'links') && $galeri->hasPages())<div class="admin-pagination">{{ $galeri->links() }}</div>@endif
@endsection
