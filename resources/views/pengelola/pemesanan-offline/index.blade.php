@extends('pengelola.layouts.admin')
@section('title', __('ui.t_offline'))
@section('page_title', __('ui.t_offline_short'))
@section('content')
<div x-data="offlineOrder({{ Js::from($tiketList->pluck('harga', 'id_tiket')) }}, {{ Js::from($tiketList->pluck('nama_tiket', 'id_tiket')) }}, {{ Js::from(old('tickets', [])) }})">
    <header class="admin-page-head"><div><h1>{{ __('panel.pemesanan_offline_13e7') }}</h1><p>{{ __('panel.catat_kunjungan_wisatawan_ya_dc9d') }}</p></div><a href="{{ route('pengelola.pemesanan-offline.riwayat') }}" class="admin-btn admin-btn--quiet"><span class="material-symbols-outlined" aria-hidden="true">history</span>{{ __('panel.riwayat_offline_66eb') }}</a></header>
    <div class="admin-flow" aria-label="{{ __('panel.tahap_pemesanan_0929') }}"><span aria-current="step"><b>1</b>{{ __('panel.pilih_tiket_8f11') }}</span><span><b>2</b>{{ __('panel.data_pengunjung_512a') }}</span><span><b>3</b>{{ __('panel.tiket_terbit_c2f0') }}</span></div>
    @if(session('error') || $errors->any())
    <div class="admin-alert admin-alert--error" role="alert">{{ session('error') }}<ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form method="POST" action="{{ route('pengelola.pemesanan-offline.store') }}">
    @csrf
    <div class="admin-booking-grid">
        <div class="admin-booking-fields">
            <section class="admin-panel admin-date"><label class="admin-field" for="offline-date">{{ __('panel.tanggal_kunjungan_0dff') }}<input id="offline-date" type="date" name="tgl_kunjungan" value="{{ old('tgl_kunjungan', $tanggal) }}" required></label><p class="admin-note mt-3">{{ __('panel.tiket_berlaku_pada_tanggal_e415') }}</p></section>
            <section class="admin-panel">
                <div class="admin-panel-head"><div><h2>{{ __('panel.pilih_jenis_tiket_4a4b') }}</h2><p>{{ __('panel.masukkan_jumlah_atau_gunakan_94f2') }}</p></div></div>
                @forelse($tiketList as $t)
                <div class="admin-ticket-row">
                    <div><h3 id="ticket-name-{{ $t->id_tiket }}">{{ $t->nama_tiket }}</h3><p>Rp{{ number_format($t->harga, 0, ',', '.') }} / {{ $t->kategori === 'kendaraan' ? __('ui.unit_vehicle') : __('ui.unit_person') }}</p></div>
                    <div class="admin-qty">
                        <button type="button" @click="decrement({{ $t->id_tiket }})" :disabled="qty({{ $t->id_tiket }}) === 0" aria-label="{{ __('ui.decrease', ['name' => $t->nama_tiket]) }}"><span class="material-symbols-outlined" aria-hidden="true">remove</span></button>
                        <input type="number" min="0" step="1" inputmode="numeric" name="tickets[{{ $t->id_tiket }}]" :value="qty({{ $t->id_tiket }})" @input="setQty({{ $t->id_tiket }}, $event.target.value)" @change="$event.target.value = qty({{ $t->id_tiket }})" aria-labelledby="ticket-name-{{ $t->id_tiket }}">
                        <button type="button" @click="increment({{ $t->id_tiket }})" aria-label="{{ __('ui.increase', ['name' => $t->nama_tiket]) }}"><span class="material-symbols-outlined" aria-hidden="true">add</span></button>
                    </div>
                </div>
                @empty
                <div class="admin-empty"><span class="material-symbols-outlined" aria-hidden="true">confirmation_number</span><h3>{{ __('panel.tidak_ada_tiket_aktif_5431') }}</h3><p>{{ __('panel.aktifkan_tiket_terlebih_dahu_60c5') }}</p><a class="admin-btn" href="{{ route('pengelola.tiket.index') }}">{{ __('panel.kelola_tiket_e512') }}</a></div>
                @endforelse
            </section>
            <p class="admin-note">{{ __('panel.harga_mengikuti_tarif_tiket_b289') }}</p>
        </div>
        <aside class="admin-order-aside">
            <section class="admin-panel">
                <div class="admin-panel-head"><div><h2>{{ __('panel.ringkasan_pesanan_7157') }}</h2><p><span x-text="quantity()">0</span> {{ __('panel.tiket_dipilih_5e2d') }}</p></div><span class="material-symbols-outlined" aria-hidden="true">receipt_long</span></div>
                <div class="admin-order-body">
                    <div class="admin-empty" x-show="selectedTickets().length === 0"><span class="material-symbols-outlined" aria-hidden="true">confirmation_number</span><h3>{{ __('panel.belum_ada_tiket_dipilih_66c2') }}</h3><p>{{ __('panel.pilih_jumlah_tiket_untuk_8c4c') }}</p></div>
                    <div x-show="selectedTickets().length > 0" x-cloak>
                        <template x-for="item in selectedTickets()" :key="item.id"><div class="admin-order-item"><div><strong x-text="item.nama"></strong><small x-text="item.qty + ' × Rp' + item.harga.toLocaleString('id-ID')"></small></div><span x-text="'Rp' + (item.qty * item.harga).toLocaleString('id-ID')"></span></div></template>
                    </div>
                    <div class="admin-order-totals" aria-live="polite" aria-atomic="true"><div><span>{{ __('panel.jumlah_tiket_334d') }}</span><span x-text="quantity()">0</span></div><div><strong>{{ __('panel.total_pembayaran_3222') }}</strong><strong class="admin-order-total" x-text="'Rp' + total().toLocaleString('id-ID')">Rp0</strong></div></div>
                    <button type="submit" class="admin-btn w-full" :disabled="selectedTickets().length === 0">{{ __('panel.lanjut_ke_data_pengunjung_cf33') }}<span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></button>
                </div>
            </section>
            <p>{!! __('ui.offline_note', ['status' => '<strong>'.e(__('panel.lunas_3928')).'</strong>']) !!}</p>
        </aside>
    </div>
    </form>
</div>
@endsection
@push('scripts')
<script defer src="{{ asset('js/booking-operations.js') }}"></script>
<script>
function offlineOrder(prices, names, previous) {
    const tickets = {};
    Object.keys(prices).forEach(id => { tickets[id] = Math.max(0, Math.floor(Number(previous[id]) || 0)); });
    return {
        tickets, prices, names,
        qty(id) { return this.tickets[id] || 0; },
        setQty(id, value) { const amount = Number(value); this.tickets[id] = Number.isSafeInteger(amount) && amount > 0 ? amount : 0; },
        increment(id) { this.setQty(id, this.qty(id) + 1); },
        decrement(id) { this.setQty(id, this.qty(id) - 1); },
        selectedTickets() {
            return Object.entries(this.tickets).filter(([, qty]) => qty > 0).map(([id, qty]) => ({
                id: Number(id), nama: this.names[id] || 'Tiket', qty, harga: Number(this.prices[id]) || 0,
            }));
        },
        quantity() { return this.selectedTickets().reduce((sum, item) => sum + item.qty, 0); },
        total() { return this.selectedTickets().reduce((sum, item) => sum + item.qty * item.harga, 0); },
    };
}
</script>
@endpush
