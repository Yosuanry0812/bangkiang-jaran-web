<?php

namespace App\Mail;

use App\Models\Pemesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PembayaranValid extends Mailable
{
    use Queueable, SerializesModels;

    public $pemesanan;

    public function __construct(Pemesanan $pemesanan)
    {
        $this->pemesanan = $pemesanan;
    }

    public function build()
    {
        return $this->subject('Pembayaran Diverifikasi - Tiket Siap Digunakan')
                    ->view('emails.pembayaran-valid');
    }
}
