@extends('pengelola.layouts.admin')
@section('title', __('messages.add_media'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-gray-500-800">{{ __('messages.add_media') }}</h1>
            <p class="text-sm text-gray-500-500 mt-0.5">{{ __('messages.manage_gallery_desc') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6 max-w-2xl">
        <form action="{{ route('pengelola.galeri.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500-500 mb-1.5">{{ __('messages.file_image') }}</label>
                    <div class="relative">
                        <input type="file" name="file" id="fileInput" required onchange="previewFile(event)"
                            accept="image/*"
                            class="w-full border border-gray-300-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-stone-50/50 text-gray-500-800 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-emerald-100 file:text-emerald-700 hover:file:bg-emerald-200 file:cursor-pointer">
                    </div>
                    @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    <div class="mt-3" id="previewFoto" style="display:none">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500-500 mb-2">{{ __('messages.preview') }}</p>
                        <img id="previewImg" class="max-h-48 rounded-xl border border-gray-300-200" alt="Preview">
                    </div>
                    <p class="text-xs text-gray-500-400 mt-1.5">{{ __('messages.format_hint') }}</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500-500 mb-1.5">Tipe Galeri <span class="text-red-400">*</span></label>
                    <select name="tipe" required
                        class="w-full border border-gray-300-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-stone-50/50 text-gray-500-800">
                        <option value="wisata" {{ old('tipe') === 'wisata' ? 'selected' : '' }}>Wisata Bangkiang Jaran</option>
                        <option value="restoran" {{ old('tipe') === 'restoran' ? 'selected' : '' }}>Restoran / Kuliner</option>
                    </select>
                    @error('tipe') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500-500 mb-1.5">{{ __('messages.description_label') }}</label>
                    <input type="text" name="keterangan" value="{{ old('keterangan') }}"
                        class="w-full border border-gray-300-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-stone-50/50 text-gray-500-800 placeholder-stone-400">
                    @error('keterangan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
                        <span class="material-symbols-outlined text-base">upload</span>
                    {{ __('messages.save') }}
                    </button>
                    <a href="{{ route('pengelola.galeri.index') }}" class="bg-stone-200 hover:bg-stone-300 text-gray-500-700 px-6 py-2.5 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
                        <span class="material-symbols-outlined text-base">arrow_back</span>
                        {{ __('messages.back') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function previewFile(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('previewImg');
            preview.src = e.target.result;
            document.getElementById('previewFoto').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush
@endsection
