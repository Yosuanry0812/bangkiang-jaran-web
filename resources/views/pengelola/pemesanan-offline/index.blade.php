@extends('pengelola.layouts.admin')
@section('title', 'Pemesanan Offline — Bangkiang Jaran')
@section('page_title', 'Pemesanan Offline')
@section('content')
<div x-data="offlineOrder({{ Js::from($tiketList->pluck('harga', 'id_tiket')) }}, {{ Js::from($tiketList->pluck('nama_tiket', 'id_tiket')) }}, {{ Js::from(old('tickets', [])) }})">
    <header class="admin-page-head"><div><h1>Pemesanan offline</h1><p>Catat kunjungan wisatawan yang membeli tiket di lokasi.</p></div><a href="{{ route('pengelola.pemesanan-offline.riwayat') }}" class="admin-btn admin-btn--quiet"><span class="material-symbols-outlined" aria-hidden="true">history</span>Riwayat offline</a></header>
    <div class="admin-flow" aria-label="Tahap pemesanan"><span aria-current="step"><b>1</b>Pilih tiket</span><span><b>2</b>Data pengunjung</span><span><b>3</b>Tiket terbit</span></div>
    @if(session('error') || $errors->any())
    <div class="admin-alert admin-alert--error" role="alert">{{ session('error') }}<ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form method="POST" action="{{ route('pengelola.pemesanan-offline.store') }}">
    @csrf
    <div class="admin-booking-grid">
        <div class="admin-booking-fields">
            <section class="admin-panel admin-date"><label class="admin-field" for="offline-date">Tanggal kunjungan<input id="offline-date" type="date" name="tgl_kunjungan" value="{{ old('tgl_kunjungan', $tanggal) }}" required></label><p class="admin-note mt-3">Tiket berlaku pada tanggal kunjungan yang dipilih.</p></section>
            <section class="admin-panel">
                <div class="admin-panel-head"><div><h2>Pilih jenis tiket</h2><p>Masukkan jumlah atau gunakan tombol tambah dan kurang.</p></div></div>
                @forelse($tiketList as $t)
                <div class="admin-ticket-row">
                    <div><h3 id="ticket-name-{{ $t->id_tiket }}">{{ $t->nama_tiket }}</h3><p>Rp{{ number_format($t->harga, 0, ',', '.') }} / {{ $t->kategori === 'kendaraan' ? 'kendaraan' : 'orang' }}</p></div>
                    <div class="admin-qty">
                        <button type="button" @click="decrement({{ $t->id_tiket }})" :disabled="qty({{ $t->id_tiket }}) === 0" aria-label="Kurangi {{ $t->nama_tiket }}"><span class="material-symbols-outlined" aria-hidden="true">remove</span></button>
                        <input type="number" min="0" step="1" inputmode="numeric" name="tickets[{{ $t->id_tiket }}]" :value="qty({{ $t->id_tiket }})" @input="setQty({{ $t->id_tiket }}, $event.target.value)" @change="$event.target.value = qty({{ $t->id_tiket }})" aria-labelledby="ticket-name-{{ $t->id_tiket }}">
                        <button type="button" @click="increment({{ $t->id_tiket }})" aria-label="Tambah {{ $t->nama_tiket }}"><span class="material-symbols-outlined" aria-hidden="true">add</span></button>
                    </div>
                </div>
                @empty
                <div class="admin-empty"><span class="material-symbols-outlined" aria-hidden="true">confirmation_number</span><h3>Tidak ada tiket aktif</h3><p>Aktifkan tiket terlebih dahulu untuk membuat pemesanan.</p><a class="admin-btn" href="{{ route('pengelola.tiket.index') }}">Kelola tiket</a></div>
                @endforelse
            </section>
            <p class="admin-note">Harga mengikuti tarif tiket yang tersimpan. Periksa jumlah dan tanggal sebelum melanjutkan.</p>
        </div>
        <aside class="admin-order-aside">
            <section class="admin-panel">
                <div class="admin-panel-head"><div><h2>Ringkasan pesanan</h2><p><span x-text="quantity()">0</span> tiket dipilih</p></div><span class="material-symbols-outlined" aria-hidden="true">receipt_long</span></div>
                <div class="admin-order-body">
                    <div class="admin-empty" x-show="selectedTickets().length === 0"><span class="material-symbols-outlined" aria-hidden="true">confirmation_number</span><h3>Belum ada tiket dipilih</h3><p>Pilih jumlah tiket untuk melihat rincian pesanan.</p></div>
                    <div x-show="selectedTickets().length > 0" x-cloak>
                        <template x-for="item in selectedTickets()" :key="item.id"><div class="admin-order-item"><div><strong x-text="item.nama"></strong><small x-text="item.qty + ' × Rp' + item.harga.toLocaleString('id-ID')"></small></div><span x-text="'Rp' + (item.qty * item.harga).toLocaleString('id-ID')"></span></div></template>
                    </div>
                    <div class="admin-order-totals" aria-live="polite" aria-atomic="true"><div><span>Jumlah tiket</span><span x-text="quantity()">0</span></div><div><strong>Total pembayaran</strong><strong class="admin-order-total" x-text="'Rp' + total().toLocaleString('id-ID')">Rp0</strong></div></div>
                    <button type="submit" class="admin-btn w-full" :disabled="selectedTickets().length === 0">Lanjut ke data pengunjung<span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span></button>
                </div>
            </section>
            <p>Pesanan offline langsung aktif setelah data pengunjung disimpan. Pembayaran dicatat sebagai <strong>Lunas</strong>.</p>
        </aside>
    </div>
    </form>
</div>
@endsection
@push('scripts')
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
