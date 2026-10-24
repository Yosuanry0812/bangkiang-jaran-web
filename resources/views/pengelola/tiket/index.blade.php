@extends('pengelola.layouts.admin')
@section('title', __('messages.manage_tickets_title'))
@section('page_title', __('messages.manage_tickets_title'))
@section('content')
<header class="admin-page-head"><div><h1>{{ __('messages.manage_tickets_title') }}</h1><p>Kelola jenis tiket, harga masuk, dan ketersediaannya.</p></div><a class="admin-btn" href="{{ route('pengelola.tiket.create') }}"><span class="material-symbols-outlined" aria-hidden="true">add</span>{{ __('messages.add_ticket') }}</a></header>
<section class="admin-panel">
    <div class="admin-panel-head"><div><h2>Daftar tiket</h2><p>Harga per tiket dalam Rupiah</p></div><span class="admin-count">{{ $tiket->count() }} jenis</span></div>
    <div class="admin-table-wrap">
    <table class="admin-table admin-table--responsive">
        <caption class="sr-only">Daftar tiket masuk Bangkiang Jaran</caption>
        <thead><tr><th scope="col">{{ __('messages.th_ticket_name') }}</th><th scope="col">{{ __('messages.th_price') }}</th><th scope="col">{{ __('messages.th_total_sold') }}</th><th scope="col">{{ __('messages.th_status') }}</th><th scope="col">{{ __('messages.th_action') }}</th></tr></thead>
        <tbody>
        @forelse($tiket as $t)
        <tr>
            <td data-label="{{ __('messages.th_ticket_name') }}"><strong>{{ $t->nama_tiket }}</strong><small>{{ $t->kategori === 'kendaraan' ? 'Kendaraan' : 'Tiket pengunjung' }}</small></td>
            <td data-label="{{ __('messages.th_price') }}" class="admin-money">Rp {{ number_format($t->harga, 0, ',', '.') }}</td>
            <td data-label="{{ __('messages.th_total_sold') }}">{{ $t->total_terjual ?? 0 }} tiket</td>
            <td data-label="{{ __('messages.th_status') }}"><span class="admin-status {{ $t->status === 'aktif' ? '' : 'admin-status--neutral' }}">{{ $t->status === 'aktif' ? __('messages.active') : __('messages.inactive') }}</span></td>
            <td data-label="{{ __('messages.th_action') }}" class="admin-cell-wide"><div class="admin-row-actions">
                <a class="admin-text-action" href="{{ route('pengelola.tiket.edit', $t->id_tiket) }}"><span class="material-symbols-outlined" aria-hidden="true">edit</span>{{ __('messages.edit') }}<span class="sr-only"> {{ $t->nama_tiket }}</span></a>
                <form method="POST" action="{{ route('pengelola.tiket.destroy', $t->id_tiket) }}" onsubmit="return confirm(@js(__('messages.delete_confirm')))">@csrf @method('DELETE')
                    <button type="submit" class="admin-text-action admin-text-action--danger"><span class="material-symbols-outlined" aria-hidden="true">delete</span>{{ __('messages.delete') }}<span class="sr-only"> {{ $t->nama_tiket }}</span></button>
                </form>
            </div></td>
        </tr>
        @empty
        <tr><td colspan="5"><div class="admin-empty"><span class="material-symbols-outlined" aria-hidden="true">confirmation_number</span><h3>{{ __('messages.no_tickets_yet') }}</h3><p>Tambahkan jenis tiket agar pengunjung dapat melakukan pemesanan.</p><a class="admin-btn" href="{{ route('pengelola.tiket.create') }}">{{ __('messages.add_ticket') }}</a></div></td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    @if(method_exists($tiket, 'links') && $tiket->hasPages())<div class="admin-pagination">{{ $tiket->links() }}</div>@endif
</section>
@endsection
