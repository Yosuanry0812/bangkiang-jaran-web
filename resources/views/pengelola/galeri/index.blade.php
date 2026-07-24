@extends('pengelola.layouts.admin')
@section('title', __('messages.manage_gallery_title'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-gray-500-800">{{ __('messages.manage_gallery_title') }}</h1>
            <p class="text-sm text-gray-500-500 mt-0.5">{{ __('messages.manage_gallery_desc') }}</p>
        </div>
        <a href="{{ route('pengelola.galeri.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
            <span class="material-symbols-outlined text-base">add_photo_alternate</span>
            {{ __('messages.add_media') }}
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        @forelse ($galeri ?? [] as $g)
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden group">
            <div class="relative overflow-hidden">
                <img src="{{ asset('storage/' . $g->file) }}" alt="{{ $g->keterangan }}" class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-500"
                     onerror="this.onerror=null; this.src='https://via.placeholder.com/300x200?text=No+Image'">
            </div>
            <div class="p-4">
                <p class="text-sm text-gray-500-600 truncate">{{ $g->keterangan }}</p>
                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-300-100">
                    <span class="text-xs text-gray-500-400">{{ $g->created_at ? \Carbon\Carbon::parse($g->created_at)->format('d/m/Y') : '' }}</span>
                    <form method="POST" action="{{ route('pengelola.galeri.destroy', $g->id_galeri) }}" onsubmit="return confirm('{{ __('messages.delete_confirm_msg') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1 text-red-500 hover:text-red-700 text-xs font-medium transition-colors">
                            <span class="material-symbols-outlined text-sm">delete</span>
                            {{ __('messages.delete') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 text-center">
            <span class="material-symbols-outlined text-5xl text-gray-500-300 mb-3 block">photo_library</span>
            <p class="text-gray-500-400">{{ __('messages.no_media') }}</p>
        </div>
        @endforelse
    </div>
    @if (method_exists($galeri, 'links'))
    <div class="mt-4">
        {{ $galeri->links() }}
    </div>
    @endif
</div>
@endsection
