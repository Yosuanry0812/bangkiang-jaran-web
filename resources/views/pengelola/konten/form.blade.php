@extends('pengelola.layouts.admin')
@section('title', isset($konten) ? 'Edit Konten' : 'Tambah Konten')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-stone-800">{{ isset($konten) ? 'Edit Konten' : 'Tambah Konten' }}</h1>
            <p class="text-sm text-stone-500 mt-0.5">{{ isset($konten) ? 'Ubah konten halaman' : 'Buat konten halaman baru' }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6 max-w-2xl">
        <form action="{{ isset($konten) ? route('pengelola.konten.update', $konten->id_konten) : route('pengelola.konten.store') }}" method="POST">
            @csrf
            @if (isset($konten))
                @method('PUT')
            @endif

            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-1.5">Judul</label>
                    <input type="text" name="judul" value="{{ old('judul', $konten->judul ?? '') }}" required
                        class="w-full border border-stone-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-stone-50/50 text-stone-800 placeholder-stone-400">
                    @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-1.5">Jenis</label>
                    <select name="jenis" required
                        class="w-full border border-stone-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-stone-50/50 text-stone-800">
                        <option value="sejarah" {{ old('jenis', $konten->jenis ?? '') == 'sejarah' ? 'selected' : '' }}>Sejarah</option>
                        <option value="info" {{ old('jenis', $konten->jenis ?? '') == 'info' ? 'selected' : '' }}>Info</option>
                        <option value="umum" {{ old('jenis', $konten->jenis ?? '') == 'umum' ? 'selected' : '' }}>Umum</option>
                    </select>
                    @error('jenis') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-1.5">Isi</label>
                    <textarea name="isi" rows="8" required
                        class="w-full border border-stone-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-stone-50/50 text-stone-800 placeholder-stone-400">{{ old('isi', $konten->isi ?? '') }}</textarea>
                    @error('isi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
                        <span class="material-symbols-outlined text-base">{{ isset($konten) ? 'save' : 'add_circle' }}</span>
                        {{ isset($konten) ? 'Update' : 'Simpan' }}
                    </button>
                    <a href="{{ route('pengelola.konten.index') }}" class="bg-stone-200 hover:bg-stone-300 text-stone-700 px-6 py-2.5 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
                        <span class="material-symbols-outlined text-base">arrow_back</span>
                        Kembali
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection