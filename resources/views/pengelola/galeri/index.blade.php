@extends('pengelola.layouts.admin')
@section('title', __('messages.manage_gallery_title'))
@section('page_title', __('messages.manage_gallery_title'))
@section('content')
<header class="admin-page-head"><div><h1>{{ __('messages.manage_gallery_title') }}</h1><p>Dokumentasi destinasi dan kuliner, dalam satu koleksi.</p></div><a class="admin-btn" href="{{ route('pengelola.galeri.create') }}"><span class="material-symbols-outlined" aria-hidden="true">add_photo_alternate</span>{{ __('messages.add_media') }}</a></header>
<nav class="admin-tabs" aria-label="Kategori galeri">
    @foreach(['' => 'Semua media', 'wisata' => 'Wisata Bangkiang Jaran', 'restoran' => 'Restoran / Kuliner'] as $type => $label)
    <a href="{{ route('pengelola.galeri.index', $type ? ['tipe' => $type] : []) }}" @if((request('tipe') ?? '') === $type) aria-current="page" @endif>{{ $label }}</a>
    @endforeach
</nav>
<div class="admin-gallery">
    @forelse($galeri as $g)
    <article class="admin-media">
        <div class="admin-media-image">
            <span class="material-symbols-outlined" aria-hidden="true">image</span><small>Foto tidak tersedia</small>
            <img src="{{ asset('storage/' . $g->file) }}" alt="{{ $g->keterangan }}" loading="lazy" decoding="async" onerror="this.remove()">
        </div>
        <div class="admin-media-body">
            <small>{{ $g->tipe === 'restoran' ? 'Restoran / Kuliner' : 'Wisata Bangkiang Jaran' }}</small>
            <h2>{{ $g->keterangan ?: 'Dokumentasi Bangkiang Jaran' }}</h2>
            <div class="admin-media-footer"><time>{{ $g->created_at ? $g->created_at->format('d M Y') : '' }}</time>
                <form method="POST" action="{{ route('pengelola.galeri.destroy', $g->id_galeri) }}" onsubmit="return confirm(@js(__('messages.delete_confirm_msg')))">@csrf @method('DELETE')
                <button type="submit" class="admin-text-action admin-text-action--danger"><span class="material-symbols-outlined" aria-hidden="true">delete</span>{{ __('messages.delete') }}<span class="sr-only"> {{ $g->keterangan }}</span></button></form>
            </div>
        </div>
    </article>
    @empty
    <div class="admin-panel col-span-full"><div class="admin-empty"><span class="material-symbols-outlined" aria-hidden="true">photo_library</span><h2>{{ __('messages.no_media') }}</h2><p>Unggah dokumentasi asli untuk melengkapi galeri destinasi atau restoran.</p><a class="admin-btn" href="{{ route('pengelola.galeri.create') }}">{{ __('messages.add_media') }}</a></div></div>
    @endforelse
</div>
@if(method_exists($galeri, 'links') && $galeri->hasPages())<div class="admin-pagination">{{ $galeri->links() }}</div>@endif
@endsection
