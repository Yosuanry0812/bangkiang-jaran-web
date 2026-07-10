@extends('pengelola.layouts.admin')
@section('title', 'Kelola Konten')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-stone-800">Kelola Konten</h1>
            <p class="text-sm text-stone-500 mt-0.5">Atur konten halaman Bangkiang Jaran</p>
        </div>
        <a href="{{ route('pengelola.konten.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
            <span class="material-symbols-outlined text-base">add</span>
            Tambah Konten
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-200 bg-stone-50 text-left">
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">Judul</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">Jenis</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">Isi</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">Status</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($konten as $k)
                    <tr class="border-b border-stone-100 hover:bg-stone-50 transition-colors">
                        <td class="py-3.5 px-4 font-medium text-stone-800">{{ $k->judul }}</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-teal-100 text-teal-700">{{ ucfirst($k->jenis) }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-stone-500 max-w-xs truncate">{{ Str::limit(strip_tags($k->isi), 100) }}</td>
                        <td class="py-3.5 px-4">
                            @if ($k->deleted_at)
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Dihapus</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Aktif</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('pengelola.konten.edit', $k->id_konten) }}" class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-800 text-sm font-medium transition-colors">
                                    <span class="material-symbols-outlined text-base">edit</span>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('pengelola.konten.destroy', $k->id_konten) }}" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 text-red-500 hover:text-red-700 text-sm font-medium transition-colors">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-10 text-center text-stone-400">Belum ada konten</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (method_exists($konten, 'links'))
        <div class="p-4 border-t border-stone-200">
            {{ $konten->links() }}
        </div>
        @endif
    </div>
</div>
@endsection