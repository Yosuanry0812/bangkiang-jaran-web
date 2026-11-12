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

    public function __construct(Pemesanan $pemesanan)
    {
        $this->pemesanan = $pemesanan;
        $this->onConnection(config('queue.transactional_connection', 'background'));
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
