<?php

namespace App\Mail;

class PembayaranDitolak extends TransactionalMail
{
    public function build()
    {
        return $this->subject(__('mail.subject_rejected'))
                    ->view('emails.pembayaran-ditolak');
    }
}
