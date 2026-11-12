<?php

namespace App\Mail;

class PembayaranValid extends TransactionalMail
{
    public function build()
    {
        return $this->subject('Pembayaran Diverifikasi - Tiket Siap Digunakan')
                    ->view('emails.pembayaran-valid');
    }
}
