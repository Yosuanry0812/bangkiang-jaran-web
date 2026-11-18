@extends('pengelola.layouts.admin')
@section('title', __('messages.manage_tickets_title'))
@section('page_title', __('messages.manage_tickets_title'))
@section('content')
<header class="admin-page-head"><div><h1>{{ __('messages.manage_tickets_title') }}</h1><p>{{ __('panel.kelola_jenis_tiket_harga_34cd') }}</p></div><a class="admin-btn" href="{{ route('pengelola.tiket.create') }}"><span class="material-symbols-outlined" aria-hidden="true">add</span>{{ __('messages.add_ticket') }}</a></header>
<section class="admin-panel">
    <div class="admin-panel-head"><div><h2>{{ __('panel.daftar_tiket_f711') }}</h2><p>{{ __('panel.harga_per_tiket_dalam_1a25') }}</p></div><span class="admin-count">{{ trans_choice('ui.types_count', $tiket->count()) }}</span></div>
    <div class="admin-table-wrap">
    <table class="admin-table admin-table--responsive">
        <caption class="sr-only">{{ __('panel.daftar_tiket_masuk_bangkiang_3bde') }}</caption>
        <thead><tr><th scope="col">{{ __('messages.th_ticket_name') }}</th><th scope="col">{{ __('messages.th_price') }}</th><th scope="col">{{ __('messages.th_total_sold') }}</th><th scope="col">{{ __('messages.th_status') }}</th><th scope="col">{{ __('messages.th_action') }}</th></tr></thead>
        <tbody>
        @forelse($tiket as $t)
        <tr>
            <td data-label="{{ __('messages.th_ticket_name') }}"><strong>{{ $t->nama_tiket }}</strong><small>{{ __('panel.tiket_pengunjung_06db') }}</small></td>
            <td data-label="{{ __('messages.th_price') }}" class="admin-money">Rp {{ number_format($t->harga, 0, ',', '.') }}</td>
            <td data-label="{{ __('messages.th_total_sold') }}">{{ trans_choice('ui.tickets_n', $t->total_terjual ?? 0) }}</td>
            <td data-label="{{ __('messages.th_status') }}"><span class="admin-status {{ $t->status === 'aktif' ? '' : 'admin-status--neutral' }}">{{ $t->status === 'aktif' ? __('messages.active') : __('messages.inactive') }}</span></td>
            <td data-label="{{ __('messages.th_action') }}" class="admin-cell-wide"><div class="admin-row-actions">
                <a class="admin-text-action" href="{{ route('pengelola.tiket.edit', $t->id_tiket) }}"><span class="material-symbols-outlined" aria-hidden="true">edit</span>{{ __('messages.edit') }}<span class="sr-only"> {{ $t->nama_tiket }}</span></a>
                <form method="POST" action="{{ route('pengelola.tiket.destroy', $t->id_tiket) }}" onsubmit="return confirm(@js(__('messages.delete_confirm')))">@csrf @method('DELETE')
                    <button type="submit" class="admin-text-action admin-text-action--danger"><span class="material-symbols-outlined" aria-hidden="true">delete</span>{{ __('messages.delete') }}<span class="sr-only"> {{ $t->nama_tiket }}</span></button>
                </form>
            </div></td>
        </tr>
        @empty
        <tr><td colspan="5"><div class="admin-empty"><span class="material-symbols-outlined" aria-hidden="true">confirmation_number</span><h3>{{ __('messages.no_tickets_yet') }}</h3><p>{{ __('panel.tambahkan_jenis_tiket_agar_0160') }}</p><a class="admin-btn" href="{{ route('pengelola.tiket.create') }}">{{ __('messages.add_ticket') }}</a></div></td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    @if(method_exists($tiket, 'links') && $tiket->hasPages())<div class="admin-pagination">{{ $tiket->links() }}</div>@endif
</section>
@endsection
