<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class RegistrationOtpMail extends Mailable
{
    public function __construct(public string $name, public string $code) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Kode verifikasi akun Bangkiang Jaran');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.registration-otp', text: 'emails.registration-otp-text');
    }
}
