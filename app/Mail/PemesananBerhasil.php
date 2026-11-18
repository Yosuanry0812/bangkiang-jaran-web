<?php

namespace App\Mail;

class PemesananBerhasil extends TransactionalMail
{
    public function build()
    {
        return $this->subject(__('mail.subject_created', ['code' => $this->pemesanan->kode_booking]))
                    ->view('emails.pemesanan-berhasil');
    }
}
