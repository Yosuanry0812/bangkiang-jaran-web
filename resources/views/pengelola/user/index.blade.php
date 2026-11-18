@extends('pengelola.layouts.admin')
@section('title', __('messages.manage_users_title'))
@section('page_title', __('messages.manage_users_title'))
@section('content')
<header class="admin-page-head"><div><h1>{{ __('panel.pengguna_a354') }}</h1><p>{{ __('panel.daftar_akun_wisatawan_dan_9e07') }}</p></div></header>
<section class="admin-panel">
    <div class="admin-panel-head"><div><h2>{{ __('panel.wisatawan_terdaftar_b82b') }}</h2><p>{{ request('search') ? __('ui.search_results', ['q' => request('search')]) : __('ui.latest_first') }}</p></div><span class="admin-count">{{ trans_choice('ui.accounts_count', method_exists($users, 'total') ? $users->total() : $users->count()) }}</span></div>
    <form method="GET" action="{{ route('pengelola.user.index') }}" class="admin-search admin-user-search" role="search">
        <label class="admin-field" for="user-search"><span class="sr-only">{{ __('panel.cari_nama_username_atau_6ebe') }}</span><input id="user-search" type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('panel.cari_nama_username_atau_977d') }}" autocomplete="off"></label>
        <button type="submit" class="admin-btn"><span class="material-symbols-outlined" aria-hidden="true">search</span>{{ __('messages.search_btn') }}</button>
        @if(request('search'))<a class="admin-btn admin-btn--quiet" href="{{ route('pengelola.user.index') }}">{{ __('messages.reset') }}</a>@endif
    </form>
    <div class="admin-table-wrap">
    <table class="admin-table admin-table--responsive">
        <caption class="sr-only">{{ __('panel.daftar_akun_wisatawan_c42e') }}</caption>
        <thead><tr><th scope="col">{{ __('panel.pengguna_a354') }}</th><th scope="col">Email</th><th scope="col">{{ __('panel.pendaftaran_430a') }}</th><th scope="col">{{ __('panel.verifikasi_email_ce8e') }}</th><th scope="col">{{ __('panel.nomor_telepon_e50f') }}</th><th scope="col">{{ __('panel.bergabung_5b89') }}</th><th scope="col">{{ __('panel.pemesanan_f431') }}</th></tr></thead>
        <tbody>
        @forelse($users as $u)
        <tr>
            <td data-label="{{ __('panel.pengguna_a354') }}"><div class="admin-person"><span class="admin-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}</span><div><strong>{{ $u->name }}</strong><small>{{ '@' . $u->username }}</small></div></div></td>
            <td data-label="Email">{{ $u->email }}</td>
            <td data-label="{{ __('panel.pendaftaran_430a') }}">{{ $u->registrationMethodLabel() }}</td>
            <td data-label="{{ __('panel.verifikasi_email_ce8e') }}">{{ $u->emailVerificationLabel() }}</td>
            <td data-label="{{ __('panel.nomor_telepon_e50f') }}">{{ $u->phone ?: '-' }}</td>
            <td data-label="{{ __('panel.bergabung_5b89') }}">{{ $u->created_at?->format('d M Y') ?? '-' }}</td>
            <td data-label="{{ __('panel.pemesanan_f431') }}"><span class="admin-count">{{ $u->pemesanan_count ?? 0 }}</span></td>
        </tr>
        @empty
        <tr><td colspan="7"><div class="admin-empty"><span class="material-symbols-outlined" aria-hidden="true">person_search</span><h3>{{ request('search') ? __('ui.user_not_found') : __('messages.no_users') }}</h3><p>{{ request('search') ? __('ui.user_try_other') : __('ui.user_empty') }}</p></div></td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    @if(method_exists($users, 'links') && $users->hasPages())<div class="admin-pagination">{{ $users->appends(request()->only('search'))->links() }}</div>@endif
</section>
@endsection
