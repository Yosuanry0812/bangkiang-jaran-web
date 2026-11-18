<?php

namespace App\Mail;

class PembayaranValid extends TransactionalMail
{
    public function build()
    {
        return $this->subject(__('mail.subject_valid'))
                    ->view('emails.pembayaran-valid');
    }
}
