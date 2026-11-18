<?php

namespace App\Mail;

use App\Models\Pemesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class TransactionalMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $pemesanan;

    public $tries = 5;

    public $timeout = 60;

    public function backoff(): array
    {
        return [60, 300, 900, 1800];
    }

    public function __construct(Pemesanan $pemesanan)
    {
        $this->pemesanan = $pemesanan;
        $user = $pemesanan->user;
        $this->locale(auth()->id() && $user && auth()->id() === $user->id ? app()->getLocale() : ($user?->locale ?: config('app.locale')));
        $this->onConnection(config('queue.transactional_connection', 'database'));
        $this->onQueue('mail');
        $this->afterCommit();
    }

    public function failed(Throwable $exception): void
    {
        Log::warning('Pengiriman email transaksi gagal.', [
            'mail' => static::class,
            'exception' => $exception::class,
        ]);
    }
}
