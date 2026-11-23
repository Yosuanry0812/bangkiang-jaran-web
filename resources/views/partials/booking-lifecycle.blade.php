<section class="bj-operations no-print" aria-labelledby="booking-timeline-title">
    <p role="status"><strong>{{ $pemesanan->statusLabel() }}</strong></p>
    @if($pemesanan->status === 'pending' && $pemesanan->pembayaran?->status === 'pending')
        <p role="status">{{ __('operations.review_protected') }}</p>
    @elseif($pemesanan->status === 'pending' && $pemesanan->expires_at)
        <p>{{ __('operations.deadline') }}: <strong><time datetime="{{ $pemesanan->expires_at->toIso8601String() }}">{{ $pemesanan->expires_at->format('d/m/Y H:i') }} {{ config('app.timezone') }}</time></strong></p>
        <p>{{ __('operations.time_remaining') }}: <span data-booking-countdown="{{ $pemesanan->expires_at->timestamp }}" data-server-now="{{ now()->timestamp }}" data-expired-label="{{ __('operations.deadline_reached') }}">{{ $pemesanan->expires_at->format('d/m/Y H:i') }}</span></p>
    @endif
    @if($pemesanan->pembayaran?->rejection_reason)
        <div class="bj-operations-notice"><strong>{{ __('operations.rejection_reason') }}</strong><p class="bj-preserve-lines">{{ $pemesanan->pembayaran->rejection_reason }}</p></div>
    @endif
    <div class="bj-operations-actions">
        @if($pemesanan->isApproved())<a href="{{ route('eticket.download', $pemesanan->id_pemesanan) }}">{{ __('operations.download_ticket') }}</a>@endif
        @if($pemesanan->pembayaran?->bukti_bayar)<a href="{{ route('payment.proof', $pemesanan->pembayaran->id_bayar) }}" data-proof-modal data-proof-type="{{ strtolower(pathinfo($pemesanan->pembayaran->bukti_bayar, PATHINFO_EXTENSION)) === 'pdf' ? 'pdf' : 'image' }}" target="_blank" rel="noopener noreferrer">{{ __('messages.payment_proof') }}</a>@endif
        @if(auth()->user()?->isWisatawan() && (int) auth()->id() === (int) $pemesanan->id_user && $pemesanan->canCancel())
        <form method="POST" action="{{ route('wisatawan.pemesanan.cancel', $pemesanan->id_pemesanan) }}" data-booking-confirm="{{ __('operations.cancel_confirm') }}">@csrf<button type="submit" class="bj-operations-danger">{{ __('operations.cancel_booking') }}</button></form>
        @endif
    </div>
    <h2 id="booking-timeline-title">{{ __('operations.timeline') }}</h2>
    <ol class="bj-booking-timeline">
        @forelse($pemesanan->events as $event)
        <li><strong>{{ __('operations.event_'.$event->event) }}</strong><time datetime="{{ $event->created_at->toIso8601String() }}">{{ $event->created_at->format('d/m/Y H:i') }}</time>@if($event->description)<p class="bj-preserve-lines">{{ $event->description }}</p>@endif</li>
        @empty<li><time>{{ $pemesanan->created_at?->format('d/m/Y H:i') }}</time><p>{{ __('operations.timeline_empty') }}</p></li>@endforelse
    </ol>
</section>

@if($pemesanan->pembayaran?->bukti_bayar)
<dialog id="proof-dialog" aria-label="{{ __('messages.payment_proof') }}" style="border:0;border-radius:16px;padding:0;max-width:min(92vw,720px);width:100%;box-shadow:0 20px 60px rgba(0,0,0,.35)">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid #e5e7eb">
        <strong>{{ __('messages.payment_proof') }}</strong>
        <button type="button" id="proof-close" aria-label="Tutup" style="font-size:22px;line-height:1;padding:2px 10px;border-radius:8px;cursor:pointer">&times;</button>
    </div>
    <div id="proof-body" style="padding:12px;max-height:80vh;overflow:auto;text-align:center"></div>
</dialog>
<script>
(function () {
    var dlg = document.getElementById('proof-dialog');
    if (!dlg || typeof dlg.showModal !== 'function') return;
    var body = document.getElementById('proof-body');
    document.addEventListener('click', function (e) {
        var a = e.target.closest('[data-proof-modal]');
        if (!a) return;
        e.preventDefault();
        body.innerHTML = '';
        var el;
        if (a.dataset.proofType === 'pdf') {
            el = document.createElement('iframe');
            el.style.cssText = 'width:100%;height:75vh;border:0';
        } else {
            el = document.createElement('img');
            el.style.cssText = 'max-width:100%;max-height:75vh;margin:0 auto;border-radius:8px';
            el.alt = a.textContent.trim();
        }
        el.src = a.href;
        body.appendChild(el);
        dlg.showModal();
    });
    document.getElementById('proof-close').addEventListener('click', function () { dlg.close(); });
    dlg.addEventListener('click', function (e) { if (e.target === dlg) dlg.close(); });
    dlg.addEventListener('close', function () { body.innerHTML = ''; });
})();
</script>
@endif
