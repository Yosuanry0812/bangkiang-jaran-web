<?php

namespace App\Mail;

class PembayaranDitolak extends TransactionalMail
{
    public function build()
    {
        return $this->subject('Pembayaran Ditolak - Silakan Upload Ulang')
                    ->view('emails.pembayaran-ditolak');
    }
}
