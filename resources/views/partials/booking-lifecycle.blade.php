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
        @if($pemesanan->pembayaran?->bukti_bayar)<a href="{{ route('payment.proof', $pemesanan->pembayaran->id_bayar) }}" target="_blank" rel="noopener noreferrer">{{ __('messages.payment_proof') }}</a>@endif
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
