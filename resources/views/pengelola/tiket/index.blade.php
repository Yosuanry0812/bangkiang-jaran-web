@extends('pengelola.layouts.admin')
@section('title', 'Kelola Tiket')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-stone-800">Kelola Tiket</h1>
            <p class="text-sm text-stone-500 mt-0.5">Atur jenis dan harga tiket masuk</p>
        </div>
        <a href="{{ route('pengelola.tiket.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
            <span class="material-symbols-outlined text-base">add</span>
            Tambah Tiket
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-200 bg-stone-50 text-left">
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">Nama Tiket</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">Harga</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">Total Terjual</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">Status</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tiket ?? [] as $t)
                    <tr class="border-b border-stone-100 hover:bg-stone-50 transition-colors">
                        <td class="py-3.5 px-4 font-medium text-stone-800">{{ $t->nama_tiket }}</td>
                        <td class="py-3.5 px-4 text-stone-700">Rp {{ number_format($t->harga, 0, ',', '.') }}</td>
                        <td class="py-3.5 px-4 text-stone-600">{{ $t->total_terjual ?? 0 }}</td>
                        <td class="py-3.5 px-4">
                            @if ($t->status == 'aktif')
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Aktif</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('pengelola.tiket.edit', $t->id_tiket) }}" class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-800 text-sm font-medium transition-colors">
                                    <span class="material-symbols-outlined text-base">edit</span>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('pengelola.tiket.destroy', $t->id_tiket) }}" onsubmit="return confirm('Yakin ingin menghapus?')">
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
                    <tr><td colspan="5" class="py-10 text-center text-stone-400">Belum ada tiket</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if (method_exists($tiket, 'links'))
        <div class="p-4 border-t border-stone-200">
            {{ $tiket->links() }}
        </div>
        @endif
    </div>
</div>
@endsection