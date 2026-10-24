@php
    $statusEnglish = app()->getLocale() === 'en';
    $approved = $booking->isApproved();
    $cancelled = $booking->status === 'dibatalkan';
    $rejected = $booking->pembayaran?->status === 'ditolak';
    $stateLabel = $approved ? ($statusEnglish ? 'Successful' : 'Berhasil') : ($cancelled ? ($statusEnglish ? 'Cancelled' : 'Dibatalkan') : ($rejected ? ($statusEnglish ? 'Receipt declined' : 'Bukti ditolak') : ($booking->pembayaran ? ($statusEnglish ? 'Awaiting approval' : 'Menunggu persetujuan') : ($statusEnglish ? 'Awaiting payment' : 'Menunggu pembayaran'))));
@endphp
<span class="bj-payment-state {{ $approved ? 'is-success' : (($cancelled || $rejected) ? 'is-declined' : 'is-pending') }}" role="status">{{ $stateLabel }}</span>
