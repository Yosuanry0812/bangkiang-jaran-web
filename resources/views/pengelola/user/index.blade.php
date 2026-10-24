@extends('pengelola.layouts.admin')
@section('title', __('messages.manage_users_title'))
@section('page_title', __('messages.manage_users_title'))
@section('content')
<header class="admin-page-head"><div><h1>Pengguna</h1><p>Daftar akun wisatawan dan aktivitas pemesanannya.</p></div></header>
<section class="admin-panel">
    <div class="admin-panel-head"><div><h2>Wisatawan terdaftar</h2><p>{{ request('search') ? 'Hasil pencarian: ' . request('search') : 'Akun terbaru ditampilkan lebih dulu' }}</p></div><span class="admin-count">{{ method_exists($users, 'total') ? $users->total() : $users->count() }} akun</span></div>
    <form method="GET" action="{{ route('pengelola.user.index') }}" class="admin-search admin-user-search" role="search">
        <label class="admin-field" for="user-search"><span class="sr-only">Cari nama, username, atau email</span><input id="user-search" type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama, username, atau email…" autocomplete="off"></label>
        <button type="submit" class="admin-btn"><span class="material-symbols-outlined" aria-hidden="true">search</span>{{ __('messages.search_btn') }}</button>
        @if(request('search'))<a class="admin-btn admin-btn--quiet" href="{{ route('pengelola.user.index') }}">{{ __('messages.reset') }}</a>@endif
    </form>
    <div class="admin-table-wrap">
    <table class="admin-table admin-table--responsive">
        <caption class="sr-only">Daftar akun wisatawan</caption>
        <thead><tr><th scope="col">Pengguna</th><th scope="col">Email</th><th scope="col">Nomor telepon</th><th scope="col">Bergabung</th><th scope="col">Pemesanan</th></tr></thead>
        <tbody>
        @forelse($users as $u)
        <tr>
            <td data-label="Pengguna"><div class="admin-person"><span class="admin-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}</span><div><strong>{{ $u->name }}</strong><small>{{ '@' . $u->username }}</small></div></div></td>
            <td data-label="Email">{{ $u->email }}</td>
            <td data-label="Nomor telepon">{{ $u->phone ?: '-' }}</td>
            <td data-label="Bergabung">{{ $u->created_at?->format('d M Y') ?? '-' }}</td>
            <td data-label="Pemesanan"><span class="admin-count">{{ $u->pemesanan_count ?? 0 }}</span></td>
        </tr>
        @empty
        <tr><td colspan="5"><div class="admin-empty"><span class="material-symbols-outlined" aria-hidden="true">person_search</span><h3>{{ request('search') ? 'Pengguna tidak ditemukan' : __('messages.no_users') }}</h3><p>{{ request('search') ? 'Coba nama, username, atau email yang lain.' : 'Akun wisatawan akan muncul setelah melakukan pendaftaran.' }}</p></div></td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    @if(method_exists($users, 'links') && $users->hasPages())<div class="admin-pagination">{{ $users->appends(request()->only('search'))->links() }}</div>@endif
</section>
@endsection
