@extends('pengelola.layouts.admin')
@section('title', isset($tiket) ? __('messages.edit_ticket') : __('messages.add_ticket'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-stone-800">{{ isset($tiket) ? __('messages.edit_ticket') : __('messages.add_ticket') }}</h1>
            <p class="text-sm text-stone-500 mt-0.5">{{ isset($tiket) ? __('messages.edit_ticket_desc') : __('messages.add_ticket_desc') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6 max-w-2xl">
        <form action="{{ isset($tiket) ? route('pengelola.tiket.update', $tiket->id_tiket) : route('pengelola.tiket.store') }}" method="POST">
            @csrf
            @if (isset($tiket))
                @method('PUT')
            @endif

            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-1.5">{{ __('messages.ticket_name') }}</label>
                    <input type="text" name="nama_tiket" value="{{ old('nama_tiket', $tiket->nama_tiket ?? '') }}" required
                        class="w-full border border-stone-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-stone-50/50 text-stone-800 placeholder-stone-400">
                    @error('nama_tiket') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-1.5">{{ __('messages.price_label') }}</label>
                    <input type="number" name="harga" value="{{ old('harga', $tiket->harga ?? '') }}" required
                        class="w-full border border-stone-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-stone-50/50 text-stone-800 placeholder-stone-400">
                    @error('harga') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-1.5">{{ __('messages.th_status') }}</label>
                    <select name="status" required
                        class="w-full border border-stone-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-stone-50/50 text-stone-800">
                            <option value="aktif" {{ old('status', $tiket->status ?? '') == 'aktif' ? 'selected' : '' }}>{{ __('messages.active_option') }}</option>
                            <option value="nonaktif" {{ old('status', $tiket->status ?? '') == 'nonaktif' ? 'selected' : '' }}>{{ __('messages.inactive_option') }}</option>
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
                        <span class="material-symbols-outlined text-base">{{ isset($tiket) ? 'save' : 'add_circle' }}</span>
                        {{ isset($tiket) ? __('messages.update') : __('messages.save') }}
                    </button>
                    <a href="{{ route('pengelola.tiket.index') }}" class="bg-stone-200 hover:bg-stone-300 text-stone-700 px-6 py-2.5 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
                        <span class="material-symbols-outlined text-base">arrow_back</span>
                        {{ __('messages.back') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection