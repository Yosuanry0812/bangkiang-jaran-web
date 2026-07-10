@extends('pengelola.layouts.admin')
@section('title', __('messages.manage_tickets_title'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-stone-800">{{ __('messages.manage_tickets_title') }}</h1>
            <p class="text-sm text-stone-500 mt-0.5">{{ __('messages.manage_tickets_desc') }}</p>
        </div>
        <a href="{{ route('pengelola.tiket.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
            <span class="material-symbols-outlined text-base">add</span>
            {{ __('messages.add_ticket') }}
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-200 bg-stone-50 text-left">
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_ticket_name') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_price') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_total_sold') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_status') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_action') }}</th>
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
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">{{ __('messages.active') }}</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">{{ __('messages.inactive') }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('pengelola.tiket.edit', $t->id_tiket) }}" class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-800 text-sm font-medium transition-colors">
                                    <span class="material-symbols-outlined text-base">edit</span>
                                    {{ __('messages.edit') }}
                                </a>
                                <form method="POST" action="{{ route('pengelola.tiket.destroy', $t->id_tiket) }}" onsubmit="return confirm('{{ __('messages.delete_confirm') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 text-red-500 hover:text-red-700 text-sm font-medium transition-colors">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                        {{ __('messages.delete') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-10 text-center text-stone-400">{{ __('messages.no_tickets_yet') }}</td></tr>
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