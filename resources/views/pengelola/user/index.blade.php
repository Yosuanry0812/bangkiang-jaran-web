@extends('pengelola.layouts.admin')
@section('title', __('messages.manage_users_title'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-stone-800">{{ __('messages.manage_users_title') }}</h1>
            <p class="text-sm text-stone-500 mt-0.5">{{ __('messages.manage_users_desc') }}</p>
        </div>
    </div>

    <form method="GET" action="{{ route('pengelola.user.index') }}" class="flex gap-3">
        <div class="relative flex-1 max-w-xs">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-base">search</span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search_user') }}"
                class="w-full border border-stone-300 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-stone-50/50 text-stone-800 placeholder-stone-400">
        </div>
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
            <span class="material-symbols-outlined text-base">search</span>
            {{ __('messages.search_btn') }}
        </button>
        @if (request('search'))
        <a href="{{ route('pengelola.user.index') }}" class="bg-stone-200 hover:bg-stone-300 text-stone-700 px-4 py-2.5 rounded-xl text-sm font-medium inline-flex items-center gap-2 transition-all">
            <span class="material-symbols-outlined text-base">close</span>
            {{ __('messages.reset') }}
        </a>
        @endif
    </form>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-stone-200 bg-stone-50 text-left">
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_name') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_username') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_email') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_phone') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_register_date') }}</th>
                        <th class="text-xs font-semibold uppercase tracking-wider text-stone-500 py-3.5 px-4">{{ __('messages.th_total_bookings') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users ?? [] as $u)
                    <tr class="border-b border-stone-100 hover:bg-stone-50 transition-colors">
                        <td class="py-3.5 px-4 font-medium text-stone-800">{{ $u->name }}</td>
                        <td class="py-3.5 px-4 text-stone-700">{{ $u->username }}</td>
                        <td class="py-3.5 px-4 text-stone-600">{{ $u->email }}</td>
                        <td class="py-3.5 px-4 text-stone-600">{{ $u->phone ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-stone-500 text-xs">{{ $u->created_at ? \Carbon\Carbon::parse($u->created_at)->format('d/m/Y') : '-' }}</td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-teal-100 text-teal-700 text-sm font-semibold">{{ $u->pemesanan_count ?? 0 }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-10 text-center text-stone-400">{{ __('messages.no_users') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (method_exists($users ?? [], 'links'))
    <div class="mt-4">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection