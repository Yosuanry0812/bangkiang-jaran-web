<?php

namespace App\Mail;

class PemesananBerhasil extends TransactionalMail
{
    public function build()
    {
        return $this->subject('Pemesanan Tiket Berhasil - ' . $this->pemesanan->kode_booking)
                    ->view('emails.pemesanan-berhasil');
    }
}
